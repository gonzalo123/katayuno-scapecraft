<?php
include __DIR__ . '/../vendor/autoload.php';

use K\Reader;
use K\Encoder;
use K\Rules\Clean;
use K\Rules\RemovePosition;
use K\Rules\Reverse;
use K\Writer;

function keypad($raw): void
{
    $encoder = new Encoder($raw);
    $encoder->appendRule(new Clean(100, 100));
    $encoder->appendRule(new RemovePosition(2));
    $encoder->appendRule(new Reverse());

    (new Writer(__DIR__ . '/keypad.png'))->write($encoder->get());
}

function getRemovedBytes($raw): string
{
    $cleanRule = new Clean(100, 100);
    $remover = new RemovePosition(2);

    $remover->apply($cleanRule->apply($raw));
    [$begin, $end] = $cleanRule->getRemoved();
    $middle = $remover->getRemoved();

    return $begin . $middle . $end;
}

function kataboom($raw, $fileName): void
{
    $encoder2 = new Encoder($raw);
    $encoder2->appendRule(new Clean(900, 900));
    $encoder2->appendRule(new RemovePosition(2));
    $encoder2->appendRule(new Reverse());

    (new Writer(__DIR__ . '/' . $fileName))->write($encoder2->get());
}

$raw = (new Reader(__DIR__ . '/CPE1704TKS.txt'))->read();

keypad($raw);
kataboom(getRemovedBytes($raw), 'kataboom.png');
kataboom((new Reader(__DIR__ . '/CPE1704TKS-2.txt'))->read(), 'kataboom-2.png');