<?php

namespace K\Rules;

class RemoveEvens implements RuleIface
{
    public function apply(string $input): string
    {
        $length = strlen($input);
        $out    = [];
        for ($i = 0; $i < $length; $i++) {
            if ($i % 2 === 0) {
                $out[$i] = $input[$i];
            }
        }

        return implode(null, $out);
    }
}