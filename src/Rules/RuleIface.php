<?php

namespace K\Rules;

interface RuleIface
{
    public function apply(string $input): string;
}