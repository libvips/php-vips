<?php

namespace Jcupitt\Vips;

class VipsSource extends Connection
{
    /**
     * Make a new source from a file descriptor (a small integer).
     * Make a new source that is attached to the descriptor. For example:
     *  source = pyvips.Source.new_from_descriptor(0)
     * Makes a descriptor attached to stdin.
     * You can pass this source to (for example) :meth:`new_from_source`.
     * @throws Exception
     */
    public static function newFromDescriptor(int $descriptor): self
    {
        $pointer = FFI::vips()->vips_source_new_from_descriptor($descriptor);

        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create source from descriptor $descriptor");
        }

        return new self($pointer);
    }

    /**
     * Make a new source from a filename.
     * Make a new source that is attached to the named file. For example:
     *  source = pyvips.Source.new_from_file("myfile.jpg")
     * You can pass this source to (for example) :meth:`new_from_source`.
     * @throws Exception
     */
    public static function newFromFile(string $filename): self
    {
        $pointer = FFI::vips()->vips_source_new_from_file($filename);

        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create source from filename $filename");
        }

        return new self($pointer);
    }

    /**
     * @TODO Not sure how best to implement this since PHP does not have buffers like Python
     * @throws Exception
     */
    public static function newFromMemory(string $data): self
    {
        $pointer = FFI::vips()->vips_source_new_from_memory($data, strlen($data));

        if (\FFI::isNull($pointer)) {
            throw new Exception("can't create source from memory");
        }

        return new self($pointer);
    }
}
