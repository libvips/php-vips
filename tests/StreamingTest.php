<?php

namespace Jcupitt\Vips\Test;

use Generator;
use Jcupitt\Vips\Exception;
use Jcupitt\Vips\Image;
use Jcupitt\Vips\VipsSource;
use Jcupitt\Vips\VipsSourceResource;
use Jcupitt\Vips\VipsTarget;
use Jcupitt\Vips\VipsTargetResource;
use PHPUnit\Framework\TestCase;

class StreamingTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function sourceAndTargetProvider(): Generator
    {
        $sources = [
            'File' => fn() => VipsSource::newFromFile(__DIR__ . '/images/img_0076.jpg'),
            'Memory' => fn() => VipsSource::newFromMemory(file_get_contents(__DIR__ . '/images/img_0076.jpg')),
            'Resource' => fn() => new VipsSourceResource(fopen(__DIR__ . '/images/img_0076.jpg', 'rb'))
        ];
        $targets = [
            'File' => fn() => VipsTarget::newToFile(tempnam(sys_get_temp_dir(), 'image')),
            'Memory' => fn() => VipsTarget::newToMemory(),
            'Resource' => fn() => new VipsTargetResource(fopen('php://memory', 'wb+')),
            'Resource(Not Readable)' => fn() => new VipsTargetResource(fopen('php://memory', 'wb'))
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
    public function testFromSourceToTarget(VipsSource $source, VipsTarget $target): void
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
        $source = VipsSource::newFromFile(__DIR__ . '/images/img_0076.jpg');
        $target = VipsTarget::newToFile(tempnam(sys_get_temp_dir(), 'image'));
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
        for ($i = 0; $i < 10; $i++) {
            $filename = tempnam(sys_get_temp_dir(), 'image');
            $source = new VipsSourceResource(fopen(__DIR__ . '/images/img_0076.jpg', 'rb'));
            $target = new VipsTargetResource(fopen($filename, 'wb+'));
            $image = Image::newFromSource($source);
            $image->writeToTarget($target, '.jpg[Q=95]');
            unlink($filename);
            $usage = memory_get_peak_usage(true);
            $diff = $usage - $lastUsage;
            if ($lastUsage !== 0 && $diff > 0) {
                echo "LEAK LEAK LEAK" . PHP_EOL;
            }
            $lastUsage = $usage;
        }
    }

    public function testFromFileToDescriptor(): void
    {
        // NOTE(L3tum): There is no way to get a file descriptor in PHP :)
        // In theory we could use the known fds like stdin or stdout,
        // but that would spam those channels full with an entire image file.
        // Because of that I've chosen to omit this test.
    }
}
