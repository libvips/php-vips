# Changelog

All notable changes to `:vips` will be documented in this file.

## master

- add getFields() to fetch an array of field names

## 2.2.0 - 2023-08-17

- improve FFI startup [West14, jcupitt]
- revise example use of composer [jcupitt]
- fix bandrank [axkirillov]
- fix FFI startup log error [ganicus]
- add Source, Target, SourceResource, TargetResource, SourceCustom, 
  TargetCustom [L3tum]
- add setProgress and progress example [jcupitt]
- add streaming-custom example [jcupitt]

## 2.1.1 - 2022-11-13

- remove unused vips_error_buffer_copy() declaration to fix compatibility with
  libvips before 8.9 [Waschnick]
- refactor callBase() for maintainability
- work around a php-ffi memory leak in getPspec() [levmv]
- work around a php-ffi memory leak in arrayType()
- better test for disabled ffi

## 2.1.0 - 2022-10-11

- allow "-" as a name component separator [andrews05]
- split FFI into a separate class [kleisauke]
- improve finding of library binary [jcupitt]

## 2.0.3 - 2022-07-04

- Fix on Windows [kleisauke]
- Fix 32-bit support [kleisauke]
- Code cleanups [kleisauke]

## 2.0.2 - 2022-4-14

- Fix extra optional string args on file open

## 2.0.1 - 2022-1-20

- Fix library name on macOS [andrefelipe]

## 2.0.0 - 2022-1-20

Rewritten to use PHP FFI to call into the libvips library rather than a binary
extension. This means php-vips now requires php 7.4 or later.

### Added
- `Interpolate` class

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

### 1.0.9 - 2021-11-20

### Added
- update docs for libvips 8.12

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.8 - 2020-08-29

### Added
- allow type names as type params to Image::setType() -- fixes issue with GType
  on 32-bit platforms

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.7 - 2020-08-28

### Added
- use nullable types and void return type where possible

### Deprecated
- requires php >= 7.1

### Fixed
- fix autodocs for non-static methods

### Remove
- Nothing

### Security
- Nothing

## 1.0.6 - 2020-08-28

### Added
- Image::setType() 
- Utils::typeFromName() 
- Updated autodocs for libvips 8.10

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.5 - 2019-09-26

### Added
- writeToArray() [John Cupitt]

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.4 - 2018-12-22

### Added
- polar() and rect() now work on non-complex images [John Cupitt]
- add crossPhase() [John Cupitt]
- update autodocs [John Cupitt]

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.3 - 2017-06-06

### Added
- add Image::newInterpolator() [Kleis Auke Wolthuizen]
- implement array access interface [John Cupitt]
- add BlendMode and Image::composite [John Cupitt]
- add Config::version() [John Cupitt]
- add Image::newFromMemory() / Image::writeToMemory() [Kleis Auke Wolthuizen]

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 1.0.2 - 2017-04-29

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
