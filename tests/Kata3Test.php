<?php

use K\Rules\RemovePosition;
use K\Rules\AddPosition;
use K\Rules\Reverse;
use K\Rules\Dirty;
use K\Reader;
use K\Encoder;
use K\Writer;
use K\Rules\Clean;
use PHPUnit\Framework\TestCase;

class Kata3Test extends TestCase
{
    public function tearDown()
    {
        @unlink(__DIR__ . '/assets/img2.png');
        @unlink(__DIR__ . '/assets/CPE1704TKS.txt');
        @unlink(__DIR__ . '/assets/kataboom.txt');
        @unlink(__DIR__ . '/assets/secretCode-1.txt');
        @unlink(__DIR__ . '/assets/secretCode-2.txt');
        @unlink(__DIR__ . '/assets/keypad.png');
    }

    public function getDataProviderCleanRemovePositionRule(): array
    {
        return [
            ['begin' => 1, 'end' => 1, 'expected' => ['1', '5'], 'input' => '12345'],
            ['begin' => 3, 'end' => 1, 'expected' => ['123', '9'], 'input' => '123456789'],
            ['begin' => 1, 'end' => 3, 'expected' => ['1', '789'], 'input' => '123456789'],
            ['begin' => 4, 'end' => 2, 'expected' => ['1234', '89'], 'input' => '123456789'],
            ['begin' => 2, 'end' => 3, 'expected' => ['54', '2x1'], 'input' => '54x32x1'],
        ];
    }

    /** @dataProvider getDataProviderCleanRemovePositionRule */
    public function testCleanRemoved($begin, $end, $expected, $input): void
    {
        $rule = new Clean($begin, $end);
        $rule->apply($input);
        $this->assertEquals($expected, $rule->getRemoved());
    }

    public function getDataProviderRemovePositionRule(): array
    {
        return [
            ['position' => 2, 'expected' => '3', 'input' => '12345'],
            ['position' => 3, 'expected' => '48', 'input' => '123456789'],
            ['position' => 1, 'expected' => '2468', 'input' => '123456789'],
            ['position' => 4, 'expected' => '5', 'input' => '123456789'],
            ['position' => 2, 'expected' => 'xx', 'input' => '54x32x1'],
        ];
    }

    /** @dataProvider getDataProviderRemovePositionRule */
    public function testRemoved($position, $expected, $input): void
    {
        $rule = new RemovePosition($position);
        $rule->apply($input);
        $this->assertEquals($expected, $rule->getRemoved());
    }

    public function testEncoder(): void
    {
        $encoder = new Encoder('12345');
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(2, 'x'));
        $encoder->appendRule(new Dirty(3, 3));

        $encoded = $encoder->get();
        $encoded = (new Clean(3, 3))->apply($encoded);
        $remover = new RemovePosition(2);
        $remover->apply($encoded);

        $removed = $remover->getRemoved();
        $this->assertEquals('xx', $removed);
    }

    public function _testCheckRemovedLength(): void
    {
        $reader = new Reader(__DIR__ . '/assets/k3/CPE1704TKS.txt');
        $data   = $reader->read();

        $data    = (new Clean(100, 100))->apply($data);
        $data    = (new Clean(3, 3))->apply($data);
        $remover = new RemovePosition(2);
        $remover->apply($data);

        $removed = $remover->getRemoved();

        $this->assertEquals(10581, strlen($removed));
    }

    public function testExtractData(): void
    {
        $input = "12345123456789012345";

        $begin = substr($input, 0, 5);
        $end   = substr($input, strlen($input) - 5, 5);

        $data = substr($input, 5, strlen($input) - 10);

        $remover = new RemovePosition(2);
        $remover->apply($data);
        $removed = $remover->getRemoved();

        $this->assertEquals('1234567890', $data);
        $this->assertEquals('369', $removed);
        $this->assertEquals('12345', $begin);
        $this->assertEquals('12345', $end);
    }

    public function testEncodeImage1(): void
    {
        $reader       = new Reader(__DIR__ . '/assets/k3/keypad1.png');
        $originalData = $reader->read();
        $this->assertEquals(63500, strlen($originalData));

        $encoder = new Encoder($originalData);
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(2));
        $encoder->appendRule(new Dirty(100, 100));
        $writer = new Writer(__DIR__ . '/assets/secretCode-1.txt');

        $finalFile = $encoder->get();
        $this->assertEquals(95449, strlen($finalFile));
        $this->assertTrue($writer->write($finalFile));

        $this->assertEquals(31949, strlen($finalFile) - strlen($originalData));
    }

    public function testEncodeImage2(): void
    {
        $reader       = new Reader(__DIR__ . '/assets/k3/kataboom.png');
        $originalData = $reader->read();
        $this->assertEquals(20100, strlen($originalData));

        $encoder = new Encoder($originalData);
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(2));
        $encoder->appendRule(new Dirty(900, 900));
        $writer = new Writer(__DIR__ . '/assets/secretCode-2.txt');

        $finalFile = $encoder->get();
        $this->assertEquals(31949, strlen($finalFile));
        $this->assertTrue($writer->write($finalFile));
    }

    public function testHideImage(): void
    {
        $reader       = new Reader(__DIR__ . '/assets/k3/kataboom.png');
        $originalData = $reader->read();
        $this->assertEquals(20100, strlen($originalData));

        $encoder = new Encoder($originalData);
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(2));
        $encoder->appendRule(new Dirty(900, 900));
        $kataboomTxt = $encoder->get();
        $writer      = new Writer(__DIR__ . '/assets/kataboom.txt');
        $this->assertTrue($writer->write($kataboomTxt));

        $this->assertEquals(31949, strlen($kataboomTxt));

        // $kataboomTxt => código 2
        // este código es el que se tiene que usuar para ensuciar el keypad

        $cleanRule = new Clean(100, 100);
        $middle    = $cleanRule->apply($kataboomTxt);
        $this->assertEquals(31749, strlen($middle));
        [$begin, $end] = $cleanRule->getRemoved();
        $this->assertEquals($kataboomTxt, $begin. $middle. $end);


        $reader    = new Reader(__DIR__ . '/assets/k3/keypad1.png');
        $keypadRaw = $reader->read();

        $this->assertEquals(63500, strlen($keypadRaw));

        // procedo a codificar la imagen:

        // dar la vuelta
        $keypadRaw = strrev($keypadRaw);
        // añadir 1 de cada 3
        $length = strlen($keypadRaw);
        $out    = [];
        $pos    = 0;
        for ($i = 0; $i < $length; $i++) {
            $out[] = $keypadRaw[$i];
            if (($i + 1) < $length && ($i + 1) % 2 === 0) {
                $out[] = $middle[$pos];
                $pos++;
            }
        }
        $this->assertEquals(95249, count($out));
        $this->assertEquals(31749, strlen($middle));

        // ensuciar con 100 al principio y al final
        $finalFile = $begin . implode(null, $out) . $end;

        // con esto tengo un fichero codificado que si
        // le aplicamos el algoritmo1 sacamos la imagen del keypad
        $writer = new Writer(__DIR__ . '/../bin/CPE1704TKS.txt');
        $writer->write($finalFile);

        $encoder2 = new Encoder($finalFile);
        $encoder2->appendRule(new Clean(100, 100));
        $encoder2->appendRule(new RemovePosition(2));
        $encoder2->appendRule(new Reverse());
        $img1   = $encoder2->get();
        $writer = new Writer(__DIR__ . '/assets/keypad.png');

        $writer->write($img1);

        $this->processImg1($finalFile);
    }

    private function processImg1($raw)
    {
        // y con los bytes eliminiados obtenemos el codigo2
        $cleanRule = new Clean(100, 100);
        $middle    = $cleanRule->apply($raw);
        [$begin, $end] = $cleanRule->getRemoved();

        $remover = new RemovePosition(2);
        $remover->apply($middle);
        $middle = $remover->getRemoved();

        $removedBytes = $begin . $middle . $end;
        $this->assertEquals(31949, strlen($removedBytes));
        $writer = new Writer(__DIR__ . '/../bin/CPE1704TKS-2.txt');
        $writer->write($removedBytes);

        $encoder2 = new Encoder($removedBytes);
        $encoder2->appendRule(new Clean(900, 900));
        $encoder2->appendRule(new RemovePosition(2));
        $encoder2->appendRule(new Reverse());
        $img2   = $encoder2->get();

        $writer = new Writer(__DIR__ . '/assets/img2.png');
        $writer->write($img2);
    }
}