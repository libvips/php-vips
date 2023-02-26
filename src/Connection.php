<?php

namespace Jcupitt\Vips;

abstract class Connection extends VipsObject
{
    /**
     * Get the filename associated with a connection. Return null if there is no associated file.
     */
    public function filename(): ?string
    {
        $so = \FFI::cast(FFI::ctypes('VipsConnection'), $this->pointer);
        $pointer = FFI::vips()->vips_connection_filename($so);

        if (\FFI::isNull($pointer)) {
            return null;
        }

        return \FFI::string($pointer);
    }

    /**
     * Make a human-readable name for a connection suitable for error messages.
     */
    public function nick(): ?string
    {
        $so = \FFI::cast(FFI::ctypes('VipsConnection'), $this->pointer);
        $pointer = FFI::vips()->vips_connection_nick($so);

        if (\FFI::isNull($pointer)) {
            return null;
        }

        return \FFI::string($pointer);
    }
}
