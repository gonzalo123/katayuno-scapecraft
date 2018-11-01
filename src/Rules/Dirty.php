<?php

namespace K\Rules;

class Dirty implements RuleIface
{
    private $begin;
    private $end;

    public function __construct(int $begin, int $end)
    {
        $this->begin = $begin;
        $this->end   = $end;
    }

    public function apply(string $input): string
    {
        return $this->getDirty($this->begin) . $input . $this->getDirty($this->end);
    }

    private function getDirty($positions)
    {
        $items = [];
        if ($positions > 0) {
            foreach (range(1, $positions) as $i) {
                $items[] = \random_bytes(1);
            }
        }
        
        return implode(null, $items);
    }
}