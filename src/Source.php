<?php

namespace Jcupitt\Vips;

class Source extends Connection
{
    /**
     * A pointer to the underlying VipsSource. This is the same as the
     * GObject, just cast to VipsSource to help FFI.
     *
     * @internal
     */
    public \FFI\CData $pointer;

    public function __construct(\FFI\CData $pointer)
    {
        $this->pointer = \FFI::cast(FFI::ctypes('VipsSource'), $pointer);
        parent::__construct($pointer);
    }

    /**
     * Make a new source from a file descriptor (a small integer).
     * Make a new source that is attached to the descriptor. For example:
     *      $source = VipsSource::newFromDescriptor(0)
     * Makes a descriptor attached to stdin.
     * You can pass this source to (for example) @see Image::newFromSource()
     * @throws Exception
     */
    public static function newFromDescriptor(int $descriptor): self
    {
        $pointer = FFI::vips()->vips_source_new_from_descriptor($descriptor);

        if ($pointer === null) {
            throw new Exception("can't create source from descriptor $descriptor");
        }

        return new self($pointer);
    }

    /**
     * Make a new source from a filename.
     * Make a new source that is attached to the named file. For example:
     *      $source = VipsSource::newFromFile("myfile.jpg")
     * You can pass this source to (for example) @see Image::newFromSource()
     * @throws Exception
     */
    public static function newFromFile(string $filename): self
    {
        $pointer = FFI::vips()->vips_source_new_from_file($filename);

        if ($pointer === null) {
            throw new Exception("can't create source from filename $filename");
        }

        return new self($pointer);
    }

    /**
     * Make a new source from a filename.
     * Make a new source that uses the provided $data. For example:
     *      $source = VipsSource::newFromFile(file_get_contents("myfile.jpg"))
     * You can pass this source to (for example) @see Image::newFromSource()
     * @throws Exception
     */
    public static function newFromMemory(string $data): self
    {
        # we need to set the memory to a copy of the data that vips_lib
        # can own and free
        $n = strlen($data);
        $memory = \FFI::new("char[$n]", false, true);
        \FFI::memcpy($memory, $data, $n);
        $pointer = FFI::vips()->vips_source_new_from_memory($memory, $n);

        if ($pointer === null) {
            throw new Exception("can't create source from memory");
        }

        return new self($pointer);
    }
}
