<?php

use Jcupitt\Vips;

class VipsShortcutTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * The original value of pixel (0, 0).
     */
    private $pixel;

    static function map_numeric($value, $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        }
        else if (is_array($value)) {
            array_walk_recursive($value, function (&$item, $key) use ($func) {
                $item = self::map_numeric($item, $func);
            });
        }

        return $value;
    } 

    protected function setUp()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $this->image = Vips\Image::newFromFile($filename, ['shrink' => 8]);
        $this->pixel = $this->image->getpoint(0, 0);
    }

    public function testVipsPow()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            return $value ** 2; 
        });
        $vips = $this->image->pow(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsWop()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            return 2 ** $value; 
        });
        $vips = $this->image->wop(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);
    }

    public function testVipsRemainder()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            return $value % 2; 
        });
        $vips = $this->image->remainder(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            return $value % $value; 
        });
        $vips = $this->image->remainder($this->image)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

    }

    public function testVipsShift()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            return $value << 2; 
        });
        $vips = $this->image->lshift(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            return $value >> 2; 
        });
        $vips = $this->image->rshift(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

    }

    public function testVipsBool()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            return $value & 2; 
        });
        $vips = $this->image->andimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            return $value | 2; 
        });
        $vips = $this->image->orimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            return $value ^ 2; 
        });
        $vips = $this->image->eorimage(2)->getpoint(0, 0);

        $this->assertEquals($vips, $real);

    }

    public function testVipsRelational()
    {
        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value > 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->more(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value >= 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->moreeq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value > $value) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->more($this->image)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value < 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->less(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value <= 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->lesseq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value == 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->equal(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($this->pixel, function ($value) {
            if ($value != 38) {
                return 255;
            }
            else {
                return 0;
            }
        });
        $vips = $this->image->noteq(38)->getpoint(0, 0);
        $this->assertEquals($vips, $real);

    }

    public function testVipsRound()
    {
        $image = $this->image->add([0.1, 1.4, 0.9]);
        $pixel = $image->getpoint(0, 0);

        $real = self::map_numeric($pixel, function ($value) {
            return floor($value);
        });
        $vips = $image->floor()->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($pixel, function ($value) {
            return ceil($value);
        });
        $vips = $image->ceil()->getpoint(0, 0);
        $this->assertEquals($vips, $real);

        $real = self::map_numeric($pixel, function ($value) {
            return round($value);
        });
        $vips = $image->rint()->getpoint(0, 0);
        $this->assertEquals($vips, $real);

    }

    public function testVipsBandand()
    {
        $real = $this->pixel[0] & $this->pixel[1] & $this->pixel[2];
        $vips = $this->image->bandand()->getpoint(0, 0);
        $this->assertEquals(count($vips), 1);
        $this->assertEquals($vips[0], $real);
    }

    public function testVipsIndex()
    {
        $vips = $this->image->invert()[1];
        $this->assertEquals($vips->bands, 1);
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
