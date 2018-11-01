<?php

namespace K;

class Writer
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function write($data): bool
    {
        return file_put_contents($this->path, $data);
    }
}