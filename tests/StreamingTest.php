<?php

namespace Jcupitt\Vips\Test;

use Generator;
use Jcupitt\Vips\Exception;
use Jcupitt\Vips\Image;
use Jcupitt\Vips\Source;
use Jcupitt\Vips\SourceResource;
use Jcupitt\Vips\Target;
use Jcupitt\Vips\TargetResource;
use PHPUnit\Framework\TestCase;

class StreamingTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function sourceAndTargetProvider(): Generator
    {
        $sources = [
            'File' => fn() => Source::newFromFile(__DIR__ . '/images/img_0076.jpg'),
            'Memory' => fn() => Source::newFromMemory(file_get_contents(__DIR__ . '/images/img_0076.jpg')),
            'Resource' => fn() => new SourceResource(fopen(__DIR__ . '/images/img_0076.jpg', 'rb'))
        ];
        $targets = [
            'File' => fn() => Target::newToFile(tempnam(sys_get_temp_dir(), 'image')),
            'Memory' => fn() => Target::newToMemory(),
            'Resource' => fn() => new TargetResource(fopen('php://memory', 'wb+')),
            'Resource (not readable)' => fn() => new TargetResource(fopen('php://memory', 'wb'))
        ];

        foreach ($sources as $sourceName => $source) {
            foreach ($targets as $targetName => $target) {
                yield "$sourceName => $targetName" => [$source(), $target()];
            }
        }
    }

    /**
     * @dataProvider sourceAndTargetProvider
     */
    public function testFromSourceToTarget(Source $source, Target $target): void
    {
        $image = Image::newFromSource($source);
        $image->writeToTarget($target, '.jpg[Q=95]');

        // Try delete temporary file
        if ($target->filename() !== null) {
            @unlink($target->filename());
        }
    }

    /**
     * This test case is extra since it's the easiest to make sure we can "reload" the saved image
     */
    public function testFromFileToFile(): void
    {
        $source = Source::newFromFile(__DIR__ . '/images/img_0076.jpg');
        $target = Target::newToFile(tempnam(sys_get_temp_dir(), 'image'));
        $image = Image::newFromSource($source);
        $image->writeToTarget($target, '.jpg[Q=95]');

        // Make sure we can load the file
        $image = Image::newFromFile($target->filename());
        $image->writeToBuffer('.jpg[Q=95]');
        unlink($target->filename());
    }

    public function testNoLeak(): void
    {
        $lastUsage = 0;
        $leaked = false;
        for ($i = 0; $i < 10; $i++) {
            $filename = tempnam(sys_get_temp_dir(), 'image');
            $source = new SourceResource(fopen(__DIR__ . '/images/img_0076.jpg', 'rb'));
            $target = new TargetResource(fopen($filename, 'wb+'));
            $image = Image::newFromSource($source);
            $image->writeToTarget($target, '.jpg[Q=95]');
            unlink($filename);
            $usage = memory_get_peak_usage(true);
            $diff = $usage - $lastUsage;
            if ($lastUsage !== 0 && $diff > 0) {
                $leaked = true;
            }
            $lastUsage = $usage;
        }

        $this->assertFalse($leaked, 'Streaming leaked memory');
    }

    public function testFromFileToDescriptor(): void
    {
        // NOTE(L3tum): There is no way to get a file descriptor in PHP :)
        // In theory we could use the known fds like stdin or stdout,
        // but that would spam those channels full with an entire image file.
        // Because of that I've chosen to omit this test.
    }
}
