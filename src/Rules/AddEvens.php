<?php

namespace K\Rules;

class AddEvens implements RuleIface
{
    public function apply(string $input): string
    {
        $length = \strlen($input);
        $out    = [];
        for ($i = 0; $i < $length; $i++) {
            $out[] = $input[$i];
            if ($i < ($length - 1)) {
                $out[] = \random_bytes(1);
            }
        }

        return implode(null, $out);
    }
}