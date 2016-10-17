#!/usr/bin/env ruby

require 'vips'

# This file generates the phpdoc comments for the magic methods and properties.
# It's in Ruby, since we use the whole of gobject-introspection, not just the
# small bit exposed by php-vips-ext.

# Regenerate docs with something like:
#
#   ./generate_phpdoc.rb > AutoDocs.php
#
# See https://www.phpdoc.org/docs/latest/references/phpdoc/tags/method.html for
# docs on the @method syntax. We generate something like:
#
# @method [return type] [name]([[type] [parameter]<, ...>]) [<description>]
#
# @method Image embed(Image $in, integer $x, integer $y, integer $width, 
#   integer $height, array $options = []) embed an image in a larger image

# these have hand-written methods, don't autodoc them
$no_generate = %w( 
    bandjoin 
    bandrank 
    ifthenelse
    add
    subtract
    multiply
    divide
    remainder
    extract_area
)

# map Ruby type names to PHP type names
$to_php_map = {
    "Vips::Image" => "Image",
    "Array<Integer>" => "array",
    "Array<Double>" => "array",
    "Array<Image>" => "array",
    "Integer" => "integer",
    "Double" => "float",
    "Float" => "float",
    "String" => "string",
    "Boolean" => "bool",
    "Vips::Blob" => "string",
}

class Vips::Argument
    def to_php
        $to_php_map.include?(type) ? $to_php_map[type] : ""
    end
end

# we need to wrap output at 80 columns ... this output class keeps text as an
# array of strings, starting a new one if the thing we append would take the
# last line over @line_length
class Output
    def initialize(start_prefix = " * ", 
                   cont_prefix = " *     ", 
                   line_length = 80)
        @line_length = line_length
        @start_prefix = start_prefix
        @cont_prefix = cont_prefix
        @lines = []
        @current_line = @start_prefix
    end

    def add(txt)
        if @current_line.length + txt.length > @line_length - 1
            @lines << @current_line
            @current_line = @cont_prefix
        end

        # we could attempt to break txt, but for now assume it'll be under 80
        @current_line += txt
    end

    def get
        @lines << @current_line if @current_line.length > 0
        @lines.join("\n")
    end
end

def generate_operation(op)
    flags = op.flags
    return if (flags & :deprecated) != 0
    nickname = Vips::nickname_find op.gtype

    return if $no_generate.include? nickname

    all_args = op.get_args.select {|arg| not arg.isset}

    # separate args into various categories

    required_input = all_args.select do |arg|
        (arg.flags & :input) != 0 and
        (arg.flags & :required) != 0 
    end

    optional_input = all_args.select do |arg|
        (arg.flags & :input) != 0 and
        (arg.flags & :required) == 0 
    end

    required_output = all_args.select do |arg|
        (arg.flags & :output) != 0 and
        (arg.flags & :required) != 0 
    end

    # required input args with :modify are copied and appended to 
    # output
    modified_required_input = required_input.select do |arg|
        (arg.flags & :modify) != 0 
    end
    required_output += modified_required_input

    optional_output = all_args.select do |arg|
        (arg.flags & :output) != 0 and
        (arg.flags & :required) == 0 
    end

    # optional input args with :modify are copied and appended to 
    # output
    modified_optional_input = optional_input.select do |arg|
        (arg.flags & :modify) != 0 
    end
    optional_output += modified_optional_input

    # find the first input image, if any ... we will be a method of this
    # instance
    member_x = required_input.find do |x|
        x.gtype.type_is_a? GLib::Type["VipsImage"]
    end
    if member_x != nil
        required_input.delete member_x
    end

    out = Output.new
    out.add "@method "
    out.add "static " if not member_x 
    if required_output.length == 0
        out.add "void "
    elsif required_output.length == 1
        out.add "#{required_output[0].to_php} "
    elsif 
        out.add "array("
        out.add required_output.map(&:to_php).join(", ")
        out.add ") "
    end

    out.add "#{nickname}("

    required_input.each do |arg| 
        out.add "#{arg.to_php} $#{arg.name}, "
    end
    out.add "array $options = []) "

    out.add "#{op.description.capitalize}."

    puts out.get
end

def generate_class(gtype)
    begin
        # can fail for abstract types
        # can't find a way to get to #abstract? from a gtype
        op = Vips::Operation.new gtype.name
    rescue
        op = nil
    end

    generate_operation(op) if op

    gtype.children.each {|x| generate_class x}
end

puts <<EOF
<?php

/**
 * This file was generated automatically. Do not edit!
 *
 * PHP version 7
 *
 * LICENSE:
 *
 * Copyright (c) 2016 John Cupitt
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  Images
 * @package   Jcupitt\\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @version   GIT:ad44dfdd31056a41cbf217244ce62e7a702e0282
 * @link      https://github.com/jcupitt/php-vips
 */

/**
 * The vips AutoDocs class. This is extended by Image.
 *
 * @category  Images
 * @package   Jcupitt\\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @version   Release:0.1.2
 * @link      https://github.com/jcupitt/php-vips
 *
EOF

# gobject-introspection 3.0.7 crashes a lot if it GCs while doing 
# something
GC.disable

Vips::init
generate_class GLib::Type["VipsOperation"]

# extract_area is in there twice, once as "crop" ... do them by hand
puts <<EOF
 * @method Image extract_area(integer $left, integer $top, integer $width, 
 *     integer $height, array $options = []) Extract an area from an image.
 * @method Image crop(integer $left, integer $top, integer $width, 
 *     integer $height, array $options = []) Extract an area from an image.
 */
class AutoDocs
{
}

?>
EOF

