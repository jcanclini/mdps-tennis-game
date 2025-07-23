<?php

namespace Tennis\UI;

class ViewIO
{
    public function read(string $prompt = '> '): string
    {
        $input = '';
        do {
            $input = trim(readline($prompt));
        } while (!is_string($input));

        return $input;
    }

    public function writeLine(string $message = ""): void
    {
        echo $message . PHP_EOL;
    }

    public function write(string $message = ""): void
    {
        echo $message;
    }
}
