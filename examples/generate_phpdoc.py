#!/usr/bin/env python

from pyvips import Image, Operation, GValue, Error, \
    ffi, values_for_enum, vips_lib, gobject_lib, \
    type_map, type_name, type_from_name, nickname_find

# This file generates the phpdoc comments for the magic methods and properties.
# It's in Python, since we use the whole of FFI, not just the
# small bit exposed by php-vips-ext.

# Regenerate docs with something like:
#
#   cd src
#   python ../examples/generate_phpdoc.py

# this needs pyvips
#
#   pip install --user pyvips

# map a Python gtype to PHP argument type names
gtype_to_php_arg = {
    GValue.gbool_type: 'bool',
    GValue.gint_type: 'integer',
    GValue.gdouble_type: 'float',
    GValue.gstr_type: 'string',
    GValue.refstr_type: 'string',
    GValue.genum_type: 'string',
    GValue.gflags_type: 'integer',
    GValue.gobject_type: 'string',
    GValue.image_type: 'Image',
    GValue.array_int_type: 'integer[]|integer',
    GValue.array_double_type: 'float[]|float',
    GValue.array_image_type: 'Image[]|Image',
    GValue.blob_type: 'string'
}

# php result type names are different, annoyingly, and very restricted
gtype_to_php_result = {
    GValue.gbool_type: 'bool',
    GValue.gint_type: 'integer',
    GValue.gdouble_type: 'float',
    GValue.gstr_type: 'string',
    GValue.refstr_type: 'string',
    GValue.genum_type: 'string',
    GValue.gflags_type: 'integer',
    GValue.gobject_type: 'string',
    GValue.image_type: 'Image',
    GValue.array_int_type: 'array',
    GValue.array_double_type: 'array',
    GValue.array_image_type: 'array',
    GValue.blob_type: 'string'
}

# values for VipsArgumentFlags
_REQUIRED = 1
_INPUT = 16
_OUTPUT = 32
_DEPRECATED = 64
_MODIFY = 128

# for VipsOperationFlags
_OPERATION_DEPRECATED = 8

# some names we might generate are reserved PHP names ... just append a "1".
reserved_php_names = {
    'DEFAULT': 'DEFAULT1',
    'XOR': 'XOR1',
    'AND': 'AND1',
    'OR': 'OR1'
}


def gtype_to_php(gtype, result=False):
    """Map a gtype to PHP type name we use to represent it.
    """

    fundamental = gobject_lib.g_type_fundamental(gtype)

    gtype_map = gtype_to_php_result if result else gtype_to_php_arg

    if gtype in gtype_map:
        return gtype_map[gtype]
    if fundamental in gtype_map:
        return gtype_map[fundamental]
    return '<unknown type>'


def remove_prefix(enum_str):
    prefix = 'Vips'

    if enum_str.startswith(prefix):
        return enum_str[len(prefix):]

    return enum_str


def generate_operation(operation_name):
    op = Operation.new_from_name(operation_name)

    # we are only interested in non-deprecated args
    args = [[name, flags] for name, flags in op.get_args()
            if not flags & _DEPRECATED]

    # find the first required input image arg, if any ... that will be self
    member_x = None
    for name, flags in args:
        if ((flags & _INPUT) != 0 and
                (flags & _REQUIRED) != 0 and
                op.get_typeof(name) == GValue.image_type):
            member_x = name
            break

    required_input = [name for name, flags in args
                      if (flags & _INPUT) != 0 and
                      (flags & _REQUIRED) != 0 and
                      name != member_x]

    required_output = [name for name, flags in args
                       if ((flags & _OUTPUT) != 0 and
                           (flags & _REQUIRED) != 0) or
                       ((flags & _INPUT) != 0 and
                        (flags & _REQUIRED) != 0 and
                        (flags & _MODIFY) != 0)]

    result = ' * @method '
    if member_x is None:
        result += 'static '
    if len(required_output) == 0:
        result += 'void '
    elif len(required_output) == 1:
        result += '{0} '.format(gtype_to_php(op.get_typeof(required_output[0]), True))
    else:
        # we generate a Returns: block for this case, see below
        result += 'array '

    result += '{0}('.format(operation_name)
    for name in required_input:
        gtype = op.get_typeof(name)
        result += '{0} ${1}, '.format(gtype_to_php(gtype), name)

    result += 'array $options = []) '

    description = op.get_description()
    result += description[0].upper() + description[1:] + '.\n'

    # find any Enums we've referenced and output @see lines for them
    for name in required_output + required_input:
        gtype = op.get_typeof(name)
        fundamental = gobject_lib.g_type_fundamental(gtype)

        if fundamental != GValue.genum_type:
            continue

        result += ' *     @see {0} for possible values for ${1}\n'.format(remove_prefix(type_name(gtype)), name)

    if len(required_output) > 1:
        result += ' *     Return array with: [\n'
        for name in required_output:
            gtype = op.get_typeof(name)
            blurb = op.get_blurb(name)
            result += ' *         \'{0}\' => @type {1} {2}\n'.format(name, gtype_to_php(gtype),
                                                                     blurb[0].upper() + blurb[1:])
        result += ' *     ];\n'

    result += ' *     @throws Exception\n'

    return result


preamble = """<?php

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
"""

class_header = """ * @category  Images
 * @package   Jcupitt\\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
"""


def generate_auto_doc(filename):
    all_nicknames = []

    def add_nickname(gtype, a, b):
        nickname = nickname_find(gtype)
        try:
            # can fail for abstract types
            op = Operation.new_from_name(nickname)

            # we are only interested in non-deprecated operations
            if (op.get_flags() & _OPERATION_DEPRECATED) == 0:
                all_nicknames.append(nickname)
        except Error:
            pass

        type_map(gtype, add_nickname)

        return ffi.NULL

    type_map(type_from_name('VipsOperation'), add_nickname)

    # add 'missing' synonyms by hand
    all_nicknames.append('crop')

    # make list unique and sort
    all_nicknames = list(set(all_nicknames))
    all_nicknames.sort()

    # these have hand-written methods, don't autodoc them
    no_generate = [
        'bandjoin',
        'bandrank',
        'ifthenelse',
        'add',
        'subtract',
        'multiply',
        'divide',
        'remainder'
    ]
    all_nicknames = [x for x in all_nicknames if x not in no_generate]

    print('Generating {0} ...'.format(filename))

    with open(filename, 'w') as f:
        f.write(preamble)
        f.write('\n')
        f.write('namespace Jcupitt\\Vips;\n')
        f.write('\n')
        f.write('/**\n')
        f.write(' * Autodocs for the Image class.\n')
        f.write(class_header)
        f.write(' *\n')

        for nickname in all_nicknames:
            f.write(generate_operation(nickname))

        f.write(' *\n')

        # all magic properties
        tmp_file = Image.new_temp_file('%s.v')
        all_properties = tmp_file.get_fields()
        for name in all_properties:
            php_name = name.replace('-', '_')
            gtype = tmp_file.get_typeof(name)
            fundamental = gobject_lib.g_type_fundamental(gtype)

            f.write(' * @property {0} ${1} {2}\n'.format(gtype_to_php(gtype), php_name, tmp_file.get_blurb(name)))

            if fundamental == GValue.genum_type:
                f.write(' *     @see {0} for possible values\n'.format(remove_prefix(type_name(gtype))))

        f.write(' */\n')
        f.write('abstract class ImageAutodoc\n')
        f.write('{\n')
        f.write('}\n')


def generate_enums():
    # otherwise we're missing some enums
    vips_lib.vips_token_get_type()
    vips_lib.vips_saveable_get_type()
    vips_lib.vips_image_type_get_type()

    all_enums = []

    def add_enum(gtype, a, b):
        nickname = type_name(gtype)
        all_enums.append(nickname)

        type_map(gtype, add_enum)

        return ffi.NULL

    type_map(type_from_name('GEnum'), add_enum)

    for name in all_enums:
        gtype = type_from_name(name)
        php_name = remove_prefix(name)

        print('Generating {0}.php ...'.format(php_name))

        with open('{0}.php'.format(php_name), 'w') as f:
            f.write(preamble)
            f.write('\n')
            f.write('namespace Jcupitt\\Vips;\n')
            f.write('\n')
            f.write('/**\n')
            f.write(' * The {0} enum.\n'.format(php_name))
            f.write(class_header)
            f.write(' */\n')
            f.write('abstract class {0}\n'.format(php_name))
            f.write('{\n')

            for value in values_for_enum(gtype):
                php_name = value.replace('-', '_').upper()
                if php_name in reserved_php_names:
                    php_name = reserved_php_names[php_name]
                f.write('    const {0} = \'{1}\';\n'.format(php_name, value))

            f.write('}\n')


generate_auto_doc('ImageAutodoc.php')
generate_enums()
