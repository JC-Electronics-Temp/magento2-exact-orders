# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/2.0.0.html).

## [Unreleased]

## [1.6.0] - 2024-04-18

### Changed

- Make compatible with Magento 2.4.7

## [1.5.5] - 2024-03-15

### Fixed

- Don't break script when street address is empty

### Added

- Added changelog and readme files
- `created_at` field to attachments database table and model

## [1.5.4] - 2024-02-14

### Fixed

- Adding attachments in different calls now adds the new files on order/invoice

## [1.5.3] - 2024-02-06

### Fixed

- Allow empty postcode and Magento customer ID on orders

## [1.5.2] - 2024-02-06

### Fixed

- Use full class path in files to make sure Magento can map them

## [1.5.1] - 2024-02-02

### Fixed

- Fixed issue with discount amounts from Exact orders 

## [1.5.0] - 2024-01-23

### Added

- Missing exception and check on customer ID

## [1.4.0] - 2024-01-16

### Added

- Github workflows
- Unit tests and copyrights in files
 
### Fixed

- Fix issue with downloading attachments

## [1.3.6] - 2024-01-15

### Fixed

- Fix several small constraint issues

## [1.3.5] - 2024-01-08

### Fixed

- Add missing preferences in `di.xml`

## [1.3.4] - 2024-01-08

### Changed

- Refactor `getList()` functionality for orders 

## [1.3.3] - 2024-01-08

### Added

- Add logic to fetch all invoices by order

## [1.3.2] - 2024-01-03

### Fixed

- Set missing values when creating orders

## [1.3.1] - 2024-01-03

### Fixed

- Fixed broken class name in `di.xml`

## [1.3.0] - 2024-01-03

### Fixed

- Only update attachments on existing orders 

## [1.2.0] - 2023-12-29

### Added

- Implement order item extension attributes

## [1.1.0] - 2023-12-28

### Added

- Implement attachment display on frontend in customer account

## [1.0.7] - 2023-12-28

### Fixed

- Fix breaking error when no company address was set (is now optional)

## [1.0.6] - 2023-12-28

### Fixed

- Fix issues with attachments for API calls

## [1.0.5] - 2023-12-27

### Fixed

- Add missing docblock for API calls

## [1.0.4] - 2023-12-22

### Fixed

- Fix issue with setting order items

## [1.0.3] - 2023-12-22

### Fixed

- Add additional invoice checks for incomplete data
- Add additional order checks for incomplete data

## [1.0.2] - 2023-12-22

### Added

- Fallbacks for incomplete data to import

## [1.0.1] - 2023-12-22

### Fixed

- Fix incorrect parameters for fetching invoices
- Fix import of customer last name

### Added

- Break import when no products are added 

## [1.0.0] - 2023-12-23

### Added 

- Implement migration scripts to import old Dealer4Dealer entities
- Implement order, invoice and shipment logic
- Refactor/implement attachments
- Add custom data to order items
- Implement modifiers per entity type

[unreleased]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.5...HEAD
[1.5.4]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.4...1.5.5
[1.5.4]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.4.0...1.5.0
[1.4.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.6...1.4.0
[1.3.6]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.5...1.3.6
[1.3.5]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.4...1.3.5
[1.3.4]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.3...1.3.4
[1.3.3]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.2...1.3.3
[1.3.2]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.8...1.1.0
[1.0.7]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/JC-Electronics-Temp/magento2-exact-orders/releases/tag/1.0.0