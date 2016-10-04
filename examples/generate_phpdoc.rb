#!/usr/bin/env ruby

require 'vips'

# This file generates the phpdoc comments for the magic methods and properties.
# It's in Ruby, since we use the whole of gobject-introspection, not just the
# small bit exposed by php-vips-ext.

# Regenerate docs with something like:
#
#   ./generate_phpdoc.rb > docs.php
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
}

class Vips::Argument
    def to_php
        $to_php_map.include?(type) ? $to_php_map[type] : ""
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

    print " * @method "
    print "static " if not member_x 
    if required_output.length == 0
        print "void "
    elsif required_output.length == 1
        print "#{required_output[0].to_php} "
    elsif 
        print "array("
        required_output.each do |arg| 
            print required_output.map(&:to_php).join(", ")
        end
        print ") "
    end

    print "#{nickname}("

    required_input.each do |arg| 
        print "#{arg.to_php} $#{arg.name}, "
    end
    print "array $options = []) "

    puts "#{op.description.capitalize}."
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

puts "/**"
puts " * These @method comments were generated automatically. Do not edit!"
puts " */"
puts ""
puts "/**"

# gobject-introspection 3.0.7 crashes a lot if it GCs while doing 
# something
GC.disable

Vips::init
generate_class GLib::Type["VipsOperation"]

puts " */"
