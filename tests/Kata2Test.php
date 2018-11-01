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

class Kata2Test extends TestCase
{
    public function tearDown()
    {
        @unlink(__DIR__ . '/assets/superSecretCode.txt');
        @unlink(__DIR__ . '/assets/superSecretCode2.txt');
        @unlink(__DIR__ . '/assets/X54J79-2.jpg');
    }

    public function getDataProviderRemovePositionRule(): array
    {
        return [
            ['position' => 2, 'expected' => '1245', 'input' => '12345'],
            ['position' => 3, 'expected' => '1235679', 'input' => '123456789'],
            ['position' => 1, 'expected' => '13579', 'input' => '123456789'],
            ['position' => 4, 'expected' => '12346789', 'input' => '123456789'],
            ['position' => 2, 'expected' => '54321', 'input' => '54x32x1'],
        ];
    }

    /** @dataProvider getDataProviderRemovePositionRule */
    public function testRemovePositionRule($position, $expected, $input)
    {
        $rule = new RemovePosition($position);
        $this->assertEquals($expected, $rule->apply($input));
    }

    public function getDataProviderAddPositionRule(): array
    {
        return [
            ['position' => 2, 'expected' => '13x5', 'input' => '135'],
            ['position' => 3, 'expected' => '123x456x789', 'input' => '123456789'],
            ['position' => 1, 'expected' => '1x2x3x4x5x6x7x8x9', 'input' => '123456789'],
            ['position' => 4, 'expected' => '1235x679', 'input' => '1235679'],
        ];
    }

    /** @dataProvider getDataProviderAddPositionRule */
    public function testAddPositionRule($position, $expected, $input)
    {
        $rule = new AddPosition($position, 'x');
        $this->assertEquals($expected, $rule->apply($input));
    }

    public function testEncoder(): void
    {
        $encoder = new Encoder('12345');
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(2, 'x'));
        $encoder->appendRule(new Dirty(3, 3));

        $encoded = $encoder->get();
        $decoder = new Encoder($encoded);
        $decoder->appendRule(new Clean(3, 3));
        $decoder->appendRule(new RemovePosition(2));
        $decoder->appendRule(new Reverse());
        $this->assertEquals('12345', $decoder->get());
    }

    public function testEncodeImage(): void
    {
        $reader  = new Reader(__DIR__ . '/assets/k2/finalImage2.jpg');
        $encoder = new Encoder($reader->read());
        $encoder->appendRule(new Reverse());
        $encoder->appendRule(new AddPosition(5));
        $encoder->appendRule(new Dirty(100, 100));
        $writer = new Writer(__DIR__ . '/assets/superSecretCode2.txt');

        $txt = $encoder->get();
        $this->assertTrue($writer->write($txt));
        $this->assertEquals(filesize(__DIR__ . '/assets/k2/X54J79-2.txt'), strlen($txt));
    }

    public function testDecodeFile(): void
    {
        $reader  = new Reader(__DIR__ . '/assets/k2/X54J79-2.txt');
        $encoder = new Encoder($reader->read());
        $encoder->appendRule(new Clean(100, 100));
        $encoder->appendRule(new RemovePosition(5));
        $encoder->appendRule(new Reverse());

        $img    = $encoder->get();
        $writer = new Writer(__DIR__ . '/assets/X54J79-2.jpg');
        $writer->write($img);

        $filename = __DIR__ . '/assets/k2/finalImage2.jpg';
        $this->assertEquals(filesize($filename), strlen($img));
        $this->assertStringEqualsFile($filename, $img);
    }
}