<?php

namespace Inium\Laraboard\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Inium\Laraboard\App\Board;

class BoardCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraboard:board-create {boardName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default board';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $boardName = $this->argument('boardName');

        $board = factory(Board::class)->create([
            'name' => $boardName,
            'name_ko' => $boardName
        ]);

        $boardRoute = route('board.post.index', ['boardName' => $boardName], false);

        echo "Board \"{$boardName}\" is created and can use {$boardRoute}.\n";
    }
}
