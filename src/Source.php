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
        $this->pointer = FFI::vips()->cast(FFI::ctypes('VipsSource'), $pointer);
        parent::__construct($pointer);
    }

    /**
     * Make a new source from a file descriptor. For example:
     *
     * ```php
     * $source = Source::newFromDescriptor(0);
     * ```
     *
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
     * Make a new source from a filename. For example:
     *
     * ```php
     * $source = Source::newFromFile("myfile.jpg");
     * ```
     *
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
     * Make a new source from a memory buffer. For example:
     *
     * ```php
     * $source = Source::newFromMemory(file_get_contents("myfile.jpg"));
     * ```
     *
     * You can pass this source to (for example) @see Image::newFromSource()
     * @throws Exception
     */
    public static function newFromMemory(string $data): self
    {
        $blob = FFI::vips()->vips_blob_copy($data, strlen($data));
        if ($blob === null) {
            throw new Exception("can't create source from memory");
        }

        $pointer = FFI::vips()->vips_source_new_from_blob($blob);
        if ($pointer === null) {
            FFI::vips()->vips_area_unref($blob);
            throw new Exception("can't create source from memory");
        }

        $source = new self($pointer);
        FFI::vips()->vips_area_unref($blob);
        return $source;
    }
}
