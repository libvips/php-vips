<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class ShortcutTest extends TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * The original value of pixel (0, 0).
     */
    private $pixel;

    public static function mapNumeric($value, $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        } elseif (is_array($value)) {
            array_walk_recursive($value, function (&$item, $key) use ($func) {
                $item = self::mapNumeric($item, $func);
            });
        }

        return $value;
    }

    protected function setUp(): void
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $this->image = Vips\Image::newFromFile($filename, ['shrink' => 8]);
        $this->pixel = $this->image->getpoint(0, 0);
    }

    protected function tearDown(): void
    {
        unset($this->image);
        unset($this->pixel);
    }

    public function testVipsPow()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value ** 2;
        });
        $vips = $this->image->pow(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsWop()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return 2 ** $value;
        });
        $vips = $this->image->wop(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsRemainder()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value % 2;
        });
        $vips = $this->image->remainder(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value % $value;
        });
        $vips = $this->image->remainder($this->image)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsShift()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value << 2;
        });
        $vips = $this->image->lshift(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value >> 2;
        });
        $vips = $this->image->rshift(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsBool()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value & 2;
        });
        $vips = $this->image->andimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value | 2;
        });
        $vips = $this->image->orimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value ^ 2;
        });
        $vips = $this->image->eorimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsRelational()
    {
        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value > 38 ? 255 : 0;
        });
        $vips = $this->image->more(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value >= 38 ? 255 : 0;
        });
        $vips = $this->image->moreEq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            // $value > $value is always false
            return 0;
        });
        $vips = $this->image->more($this->image)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value < 38 ? 255 : 0;
        });
        $vips = $this->image->less(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value <= 38 ? 255 : 0;
        });
        $vips = $this->image->lessEq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value === 38 ? 255 : 0;
        });
        $vips = $this->image->equal(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($this->pixel, function ($value) {
            return $value !== 38 ? 255 : 0;
        });
        $vips = $this->image->notEq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);
    }

    public function testVipsRound()
    {
        $image = $this->image->add([0.1, 1.4, 0.9]);
        $pixel = $image->getpoint(0, 0);

        $real = self::mapNumeric($pixel, function ($value) {
            return floor($value);
        });
        $vips = $image->floor()->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($pixel, function ($value) {
            return ceil($value);
        });
        $vips = $image->ceil()->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::mapNumeric($pixel, function ($value) {
            return round($value);
        });
        $vips = $image->rint()->getpoint(0, 0);
        $this->assertEquals($vips, $real);
    }

    public function testVipsBandand()
    {
        $real = $this->pixel[0] & $this->pixel[1] & $this->pixel[2];
        $vips = $this->image->bandand()->getpoint(0, 0);
        $this->assertCount(1, $vips);
        $this->assertEquals($vips[0], $real);
    }

    public function testVipsIndex()
    {
        $vips = $this->image->invert()[1];
        $this->assertEquals($vips->bands, 1);
    }

    public function testOffsetSet()
    {
        $base = Vips\Image::newFromArray([1, 2, 3]);
        $image = $base->bandjoin([$base->add(1), $base->add(2)]);

        // replace band with image
        $test = $image->copy();
        $test[1] = $base;
        $avg = $test->avg();
        $this->assertTrue(abs($avg - 2.666) < 0.001);
        $this->assertEquals($test->bands, 3);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 2);
        $this->assertEquals($test[2]->avg(), 4);

        // replace band with constant
        $test = $image->copy();
        $test[1] = 12;
        $this->assertEquals($test->bands, 3);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 12);
        $this->assertEquals($test[2]->avg(), 4);

        // replace band with array
        $test = $image->copy();
        $test[1] = [12, 13];
        $this->assertEquals($test->bands, 4);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 12);
        $this->assertEquals($test[2]->avg(), 13);
        $this->assertEquals($test[3]->avg(), 4);

        // insert at start
        $test = $image->copy();
        $test[-1] = 12;
        $this->assertEquals($test->bands, 4);
        $this->assertEquals($test[0]->avg(), 12);
        $this->assertEquals($test[1]->avg(), 2);
        $this->assertEquals($test[2]->avg(), 3);
        $this->assertEquals($test[3]->avg(), 4);

        // append at end
        $test = $image->copy();
        $test[] = 12;
        $this->assertEquals($test->bands, 4);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 3);
        $this->assertEquals($test[2]->avg(), 4);
        $this->assertEquals($test[3]->avg(), 12);
    }

    public function testOffsetUnset()
    {
        $base = Vips\Image::newFromArray([1, 2, 3]);
        $image = $base->bandjoin([$base->add(1), $base->add(2)]);

        // remove middle
        $test = $image->copy();
        unset($test[1]);
        $this->assertEquals($test->bands, 2);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 4);

        // remove first
        $test = $image->copy();
        unset($test[0]);
        $this->assertEquals($test->bands, 2);
        $this->assertEquals($test[0]->avg(), 3);
        $this->assertEquals($test[1]->avg(), 4);

        // remove last
        $test = $image->copy();
        unset($test[2]);
        $this->assertEquals($test->bands, 2);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 3);

        // remove outside range
        $test = $image->copy();
        unset($test[12]);
        $this->assertEquals($test->bands, 3);
        $this->assertEquals($test[0]->avg(), 2);
        $this->assertEquals($test[1]->avg(), 3);
        $this->assertEquals($test[2]->avg(), 4);
    }

    public function testOffsetUnsetAll()
    {
        $base = Vips\Image::newFromArray([1, 2, 3]);

        // remove all
        $test = $base->copy();
        $this->expectException(\BadMethodCallException::class);
        unset($test[0]);
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
