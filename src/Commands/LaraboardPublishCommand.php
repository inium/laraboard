<?php

namespace Inium\Laraboard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;

class LaraboardPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "laraboard:publish";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish laraboard components (Model, Controller, Request, Database) and append its route to 'routes/api.php'.";

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->publish();
        $this->appendRoute();

        // route 적용을 위한 optimize 호출 (config clear , route clear)
        $this->call("optimize");

        return 0;
    }

    /**
     * Publish Laraboard files
     *
     * @return void
     */
    protected function publish(): void
    {
        // config
        $this->publishItem(
            __DIR__ . "/../Laraboard/config/laraboard.php",
            config_path("laraboard.php")
        );

        // App
        // Controllers
        $this->publishItem(
            __DIR__ . "/../Laraboard/app/Http/Controllers/Laraboard",
            app_path("Http/Controllers/Laraboard")
        );

        // Requests
        $this->publishItem(
            __DIR__ . "/../Laraboard/app/Http/Requests/Laraboard",
            app_path("Http/Requests/Laraboard")
        );

        // Models
        $this->publishItem(
            __DIR__ . "/../Laraboard/app/Models/Laraboard",
            app_path("Models/Laraboard")
        );

        // Database
        // factories
        $this->publishItem(
            __DIR__ . "/../Laraboard/database/factories/Laraboard",
            database_path("factories/Laraboard")
        );

        // migrations
        $this->publishItem(
            __DIR__ . "/../Laraboard/database/migrations/laraboard",
            database_path("migrations/laraboard")
        );

        // seeders
        $this->publishItem(
            __DIR__ . "/../Laraboard/database/seeders/Laraboard",
            database_path("seeders/Laraboard")
        );

        // test
        $this->publishItem(
            __DIR__ . "/../Laraboard/tests/Feature/Laraboard",
            base_path("tests/Feature/Laraboard")
        );
    }

    /**
     * Append laraboard route to api route
     *
     * @return void
     */
    protected function appendRoute()
    {
        $this->publishItem(
            __DIR__ . "/../Laraboard/routes/laraboard/api.php",
            base_path("routes/laraboard/api.php")
        );

        $to = base_path("routes/api.php");
        $append = "\nrequire_once __DIR__ . \"/laraboard/api.php\";";

        File::append($to, $append);
    }

    /**
     * Publish the given item from and to the given location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishItem($from, $to)
    {
        if ($this->files->isFile($from)) {
            return $this->publishFile($from, $to);
        } elseif ($this->files->isDirectory($from)) {
            return $this->publishDirectory($from, $to);
        }

        $this->components->error("Can't locate path: <{$from}>");
    }

    /**
     * Publish the file to the given path.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishFile($from, $to)
    {
        if ($this->files->exists($to)) {
            $this->components->error("Can't locate path: <{$from}>");
            return;
        }

        $this->createParentDirectory(dirname($to));
        $this->files->copy($from, $to);

        $this->status($from, $to, "file");
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        if ($this->files->exists($to)) {
            $this->components->error("Can't locate path: <{$from}>");
            return;
        }

        $this->files->ensureDirectoryExists($to);
        $this->files->copyDirectory($from, $to);

        $this->status($from, $to, "directory");
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param  string  $directory
     * @return void
     */
    protected function createParentDirectory($directory)
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path() . "/", "", realpath($from));

        $to = str_replace(base_path() . "/", "", realpath($to));

        $this->components->task(
            sprintf("Copying %s [%s] to [%s]", $type, $from, $to)
        );
    }
}
