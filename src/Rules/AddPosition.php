<?php

namespace K\Rules;

class AddPosition implements RuleIface
{
    private $position;
    private $fill;

    public function __construct($position, $fill = null)
    {
        $this->position = $position;
        $this->fill     = \is_null($fill) ? \random_bytes(1) : $fill;
    }

    public function apply(string $input): string
    {
        $length = strlen($input);
        $out    = [];
        for ($i = 0; $i < $length; $i++) {
            $out[] = $input[$i];
            if (($i + 1) < $length && ($i + 1) % $this->position === 0) {
                $out[] = $this->fill;
            }
        }

        return implode(null, $out);
    }
}