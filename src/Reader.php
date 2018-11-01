<?php

namespace K;

class Reader
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function read(): string
    {
        return file_get_contents($this->path);
    }
}