<?php

namespace K\Rules;

class Reverse implements RuleIface
{
    public function apply(string $input): string
    {
        return strrev($input);
    }
}