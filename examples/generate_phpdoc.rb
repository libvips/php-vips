#!/usr/bin/env ruby

require 'vips'

# This file generates the phpdoc comments for the magic methods and properties.
# It's in Ruby, since we use the whole of gobject-introspection, not just the
# small bit exposed by php-vips-ext.

# Regenerate docs with something like:
#
#   cd src
#   ../examples/generate_phpdoc.rb 

# gobject-introspection 3.0.7 crashes a lot if it GCs while doing 
# something
GC.disable

Vips::init

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

# Find all the vips enums 
$enums = []
Vips.constants.each do |name|
    const = Vips.const_get name
    if const.respond_to? :superclass and
        const.superclass == GLib::Enum
        $enums << name.to_s
    end
end

def enum?(name)
    return true if $enums.include?(name)

    # is there a "Vips" at the front of the name? remove it and try the
    # enums again
    trim = name.to_s.tap{|s| s.slice!("Vips")}
    return true if $enums.include?(trim)

    # is there a "::" at the front of the name? remove it and try the
    # enums again
    trim.slice! "::"
    return true if $enums.include?(trim)

    return false
end

# map Ruby type names to PHP type names
$to_php_map = {
    "Vips::Image" => "Image",
    "Image" => "Image",
    "Array<Integer>" => "integer[]|integer",
    "Array<Double>" => "float[]|float",
    "Array<Image>" => "Image[]",
    "Integer" => "integer",
    "gint" => "integer",
    "guint64" => "integer",
    "Double" => "float",
    "gdouble" => "float",
    "Float" => "float",
    "String" => "string",
    "Boolean" => "bool",
    "gboolean" => "bool",
    "Vips::Blob" => "string",
    "gchararray" => "string",
    "gpointer" => "string",
}

def type_to_php(type)
    return $to_php_map[type] if $to_php_map.include?(type) 
    return "string" if enum? type

    # no mapping found
    puts "no mapping found for #{type}"
    return ""
end

class Vips::Argument
    def to_php
        type_to_php type
    end
end

def generate_operation(file, op)
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

    file << " * @method "
    file << "static " if not member_x 
    if required_output.length == 0
        file << "void "
    elsif required_output.length == 1
        file << "#{required_output[0].to_php} "
    else 
        # we generate a Returns: block for this case, see below
        file << "array "
    end

    file << "#{nickname}("

    required_input.each do |arg| 
        file << "#{arg.to_php} $#{arg.name}, "
    end
    file << "array $options = []) "

    file << "#{op.description.capitalize}.\n"

    # find any Enums we've referenced and output @see lines for them
    used_enums = []
    (required_output + required_input).each do |arg|
        next if not enum? arg.type
        file << " *     @see #{arg.type} for possible values for $#{arg.name}\n"
    end

    if required_output.length > 1
        file << " *     Return array with: [\n"
        required_output.each do |arg|
            file << " *         '#{arg.name}' => "
            file << "@type #{arg.to_php} #{arg.blurb.capitalize}.\n"
        end
        file << " *     ];\n"
    end
end

def generate_class(file, gtype)
    begin
        # can fail for abstract types
        # can't find a way to get to #abstract? from a gtype
        op = Vips::Operation.new gtype.name
    rescue
        op = nil
    end

    generate_operation(file, op) if op

    gtype.children.each {|x| generate_class file, x}
end

preamble = <<EOF
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
 * @link      https://github.com/jcupitt/php-vips
 */
EOF

class_header = <<EOF
 * @category  Images
 * @package   Jcupitt\\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
EOF

# The image class is the most complex
puts "Generating ImageAutodoc.php ..."
File.open("ImageAutodoc.php", "w") do |file|
    file << preamble 
    file << "\n"
    file << "namespace Jcupitt\\Vips;\n"

    file << "\n"
    file << "/**\n"
    file << " * Autodocs for the Image class.\n"
    file << class_header 
    file << " *\n"

    generate_class file, GLib::Type["VipsOperation"]

# extract_area is in there twice, once as "crop" ... do them by hand
    file << <<EOF
 * @method Image extract_area(integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 * @method Image crop(integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 *
EOF

    # all magic properties
    Vips::Image.properties.each do |name|
        php_name = name.tr "-", "_"
        p = Vips::Image.property name
        file << " * @property #{type_to_php p.value_type.name} "
        file << "$#{php_name} #{p.blurb}\n"
        if enum? p.value_type.name
            php_name = p.value_type.name.tap{|s| s.slice!("Vips")}
            file << " *     @see #{php_name} for possible values\n"
        end
    end

    file << <<EOF
 */
abstract class ImageAutodoc
{
}
EOF
end

# generate all the enums
$enums.each do |name|
    const = Vips.const_get name
    puts "Generating #{name}.php ..."
    File.open("#{name}.php", "w") do |file|
        file << preamble 
        file << "\n"
        file << "namespace Jcupitt\\Vips;\n"
        file << "\n"
        file << "/**\n"
        file << " * The #{name} enum.\n"
        file << class_header 
        file << " */\n"
        file << "abstract class #{name}\n"
        file << "{\n"

        const.values.each do |value|
            next if value.nick == "last" 
            php_name = value.nick.tr("-", "_").upcase
            file << "    const #{php_name} = '#{value.nick}';\n"
        end

        file << "}\n"
    end
end

