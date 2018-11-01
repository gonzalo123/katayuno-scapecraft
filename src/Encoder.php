<?php

namespace K;

use K\Rules\RuleIface;

class Encoder
{
    private $input;
    private $rules = [];

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function appendRule(RuleIface $rule)
    {
        $this->rules[] = $rule;
    }

    public function get(): string
    {
        foreach ($this->rules as $rule) {
            $this->input = $rule->apply($this->input);
        }

        return $this->input;
    }
}