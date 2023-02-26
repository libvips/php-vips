<?php

namespace Jcupitt\Vips;

class VipsTarget extends Connection
{
    public static function newToDescriptor(int $descriptor): self
    {
        $pointer = FFI::vips()->vips_target_new_to_descriptor($descriptor);
        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create output target from descriptor $descriptor");
        }

        return new self($pointer);
    }

    public static function newToFile(string $filename): self
    {
        $pointer = FFI::vips()->vips_target_new_to_file($filename);

        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create output target from filename $filename");
        }

        return new self($pointer);
    }

    public static function newToMemory(): self
    {
        $pointer = FFI::vips()->vips_target_new_to_memory();

        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create output target from memory");
        }

        return new self($pointer);
    }
}
