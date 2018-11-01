<?php

use K\Encoder;
use K\Reader;
use K\Rules\AddEvens;
use K\Rules\Clean;
use K\Rules\Dirty;
use K\Rules\RemoveEvens;
use K\Rules\Reverse;
use K\Writer;
use PHPUnit\Framework\TestCase;

class Kata1Test extends TestCase
{
    public function tearDown()
    {
        @unlink(__DIR__ . '/assets/out.txt');
        @unlink(__DIR__ . '/assets/foo.txt');
        @unlink(__DIR__ . '/assets/superSecretCode.txt');
    }

    public function testCleanRule(): void
    {
        $rule = new Clean(3, 3);
        $this->assertEquals('12345', $rule->apply('***12345***'));
    }

    public function testReverseRule(): void
    {
        $rule = new Reverse();
        $this->assertEquals('12345', $rule->apply('54321'));
    }

    public function testRemoveEvensRule(): void
    {
        $rule = new RemoveEvens();
        $this->assertEquals('135', $rule->apply('12345'));
    }

    public function getDataProviderDirtyRule(): array
    {
        return [
            ['input' => 123, 'begin' => 3, 'end' => 3],
            ['input' => 12345, 'begin' => 1, 'end' => 2],
            ['input' => 1234, 'begin' => 3, 'end' => 0],
            ['input' => 123456, 'begin' => 0, 'end' => 3],
            ['input' => 1, 'begin' => 0, 'end' => 0],
        ];
    }

    /** @dataProvider getDataProviderDirtyRule */
    public function testDirtyRule($input, $begin, $end): void
    {
        $rule = new Dirty($begin, $end);
        $out  = $rule->apply($input);
        $this->assertEquals(strlen($input) + $begin + $end, strlen($out));
        $this->assertEquals($input, substr($out, $begin, strlen($input)));
    }

    public function testAddEvens(): void
    {
        $rule = new AddEvens();
        $out  = $rule->apply('135');

        $this->assertEquals(5, strlen($out));
        $this->assertEquals(1, $out[0]);
        $this->assertEquals(3, $out[2]);
        $this->assertEquals(5, $out[4]);
    }

    public function testAddEvens2(): void
    {
        $rule = new AddEvens();
        $out  = $rule->apply('1');

        $this->assertEquals(1, strlen($out));
    }

    public function testEncoder(): void
    {
        $encoder = new Encoder('135');
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddEvens());
        $encoder->appendRule(new Dirty(3, 3));

        $encoded = $encoder->get();
        $decoder = new Encoder($encoded);
        $decoder->appendRule(new Clean(3, 3));
        $decoder->appendRule(new RemoveEvens());
        $decoder->appendRule(new Reverse());
        $this->assertEquals('135', $decoder->get());
    }

    public function testReadFile(): void
    {
        \file_put_contents(__DIR__ . '/assets/foo.txt', 'Hola');
        $reader = new Reader(__DIR__ . '/assets/foo.txt');
        $this->assertEquals('Hola', $reader->read());
    }

    public function testWriteFile(): void
    {
        $writer = new Writer(__DIR__ . '/assets/out.txt');
        $this->assertTrue($writer->write('Hola'));
    }

    public function testWriteReadFile(): void
    {
        $data = \uniqid('text', true);
        $path = __DIR__ . '/assets/out.txt';

        $writer = new Writer($path);
        $this->assertTrue($writer->write($data));

        $reader = new Reader($path);
        $this->assertEquals($data, $reader->read());
    }

    public function testEncodeImage(): void
    {
        $reader  = new Reader(__DIR__ . '/assets/k1/finalImage.jpg');
        $encoder = new Encoder($reader->read());
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddEvens());
        $encoder->appendRule(new Dirty(3, 3));
        $writer = new Writer(__DIR__ . '/assets/superSecretCode.txt');

        $txt = $encoder->get();
        $this->assertTrue($writer->write($txt));
        $this->assertEquals(filesize(__DIR__ . '/assets/k1/X54J79-1.txt'), strlen($txt));
    }

    public function testDecodeFile(): void
    {
        $reader  = new Reader(__DIR__ . '/assets/k1/X54J79-1.txt');
        $encoder = new Encoder($reader->read());
        $encoder->appendRule(new Clean(3, 3));
        $encoder->appendRule(new RemoveEvens());
        $encoder->appendRule(new Reverse());

        $img = $encoder->get();
        $filename = __DIR__ . '/assets/k1/finalImage.jpg';
        $this->assertEquals(filesize($filename), strlen($img));
        $this->assertEquals(file_get_contents($filename), $img);
    }
}