<?php

namespace Inium\Laraboard\App\Board;

trait RenderTemplateTrait
{
    private function render(string $path, array $param = [])
    {
        return view("laraboard::{$path}", $param);
    }
}
