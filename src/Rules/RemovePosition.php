<?php

namespace K\Rules;

class RemovePosition implements RuleIface, RemovedIface
{
    private $position;
    private $removed;

    public function __construct($position)
    {
        $this->position = $position;
    }

    public function apply(string $input): string
    {
        $this->removed = [];
        $length = strlen($input);
        $out    = [];
        $group  = [];
        for ($i = 0; $i < $length; $i++) {
            if (\count($group) === $this->position) {
                $this->removed[] = $input[$i];
                foreach ($group as $item) {
                    $out[] = $item;
                }
                $group = [];
            } else {
                $group[] = $input[$i];
            }
        }

        foreach ($group as $item) {
            $out[] = $item;
        }

        return implode(null, $out);
    }

    public function getRemoved()
    {
        return implode(null, $this->removed);
    }
}