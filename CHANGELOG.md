# Changelog
All notable changes to `:vips` will be documented in this file.

## 0.1.2 - 2016-10-02

### Added
- Image::set and Image::get methods, handy for properties whose names do not
  confirm to PHP conventions
- type annotations [Kleis Auke Wolthuizen]
- libvips draw calls now work
- logging, see Vips\Image::setLogging()
- exceptions on error

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
