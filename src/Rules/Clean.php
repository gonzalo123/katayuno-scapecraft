<?php

namespace K\Rules;

class Clean implements RuleIface, RemovedIface
{
    private $begin;
    private $end;
    private $removed;

    public function __construct(int $begin, int $end)
    {
        $this->begin   = $begin;
        $this->end     = $end;
        $this->removed = [
            'begin' => [],
            'end'   => [],
        ];
    }

    public function apply(string $input): string
    {
        $length = strlen($input);
        $out    = [];
        for ($i = 0; $i < $length; $i++) {
            if ($i > ($this->begin - 1) && $i < ($length - $this->end)) {
                $out[$i] = $input[$i];
            } else {
                if ($i < $this->begin) {
                    $this->removed['begin'][] = $input[$i];
                } else {
                    $this->removed['end'][] = $input[$i];
                }
            }
        }

        return implode(null, $out);
    }

    public function getRemoved()
    {
        return [
            implode(null, $this->removed['begin']),
            implode(null, $this->removed['end'])
        ];
    }
}