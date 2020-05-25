<?php

namespace Inium\Laraboard\Core\Templates;

trait RenderTemplateTrait
{
    private function render(string $path, array $param = [])
    {
        return view("laraboard::{$path}", $param);
    }
}
