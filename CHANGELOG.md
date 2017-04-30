# Changelog
All notable changes to `:vips` will be documented in this file.

## 1.0.1 - 2017-04-29

### Added
- fix minor formatting issues reported by phpcs [John Cupitt]
- add Image::hasAlpha() [Kleis Auke Wolthuizen]
- add Image::findLoad(), Image::findLoadBuffer() [John Cupitt]
- add Image::copyMemory() [John Cupitt]
- add Image::newFromImage() [John Cupitt]
- update docs for libvips 8.5 [John Cupitt]

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.1 

### Added
- oops, mistagged, use 1.0.2

## 1.0.0 - 2016-11-03

### Added
- logging with PSR-3 Logger Interface [Kleis Auke Wolthuizen]
- switch to PSR2 formatting [Kleis Auke Wolthuizen]
- add sig.php example [John Cupitt]
- add Vips\Image::debugLogger() sample logger [John Cupitt]
- added Vips\Config and Vips\Utils 

### Deprecated
- removed `\Enum` from enum names

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 0.1.2 - 2016-10-02

### Added
- Image::set, Image::get methods, handy for properties whose names do not
  confirm to PHP conventions
- add Image::typeof
- add Image::remove
- type annotations [Kleis Auke Wolthuizen]
- libvips draw calls now work
- logging, see Vips\Image::setLogging()
- throw Vips\Exception on error
- much better docs, including automatically-generated docs for magic methods
  and properties

### Deprecated
- now require php >= 7.0.11, fixes #10

### Fixed
- support 2D array args to add() etc. 
- fix bandsplit
- fix ifthenelse with options
- many more phpdoc fixes [Kleis Auke Wolthuizen]

### Remove
- Nothing

### Security
- Nothing

## 0.1.1 - 2016-10-01

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Many phpdoc markup fixes [Kleis Auke Wolthuizen]
- Fix capitalization JCupitt -> Jcupitt

### Remove
- Nothing

### Security
- Nothing

## 0.1.0 - 2016-09-03

### Added
- First commit

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing
