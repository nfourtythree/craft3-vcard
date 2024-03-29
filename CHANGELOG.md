# Release notes for vCard

## 2.0.0 - 2022-07-18

### Changed
- vCard now requires Craft CMS 4.1.0 or later.

## 1.2.1 - 2021-12-01

### Fixed
- Fixed outdated readme.

## 1.2.0 - 2021-12-01

### Fixed
- Fixed a bug where type was not being set correctly for emails, addresses, phone numbers, and urls. ([#7](https://github.com/nfourtythree/craft3-vcard/issues/7))
- Fixed a bug where the vCard was not downloading correctly.
- Fixed an error that could occur when creating a vCard with multiple URLs. ([#8](https://github.com/nfourtythree/craft3-vcard/pull/8))

## 1.1.0 - 2020-05-20 [CRITICAL]

### Changed
- The previous default salt is no longer a valid salt to be used.
- There is no longer a default salt, one must be set before using the plugin.

### Fixed
- Fixed a vulnerability when the default salt has not been changed. ([#4](https://github.com/nfourtythree/craft3-vcard/issues/4))  

## 1.0.0 - 2019-01-31

### Added
- Initial release
