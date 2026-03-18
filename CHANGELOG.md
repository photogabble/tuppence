# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Changed: The minimum supported PHP version increased from ^8 to >=8.4.1
- Changed: Replaced `getRouter` method with property hook, this is a breaking change, if used.
- Changed: Updated PHPUnit from 11.3.3 to ^13.0
- Changed: Update usage of `league/route` from ^5.1 to ^6.2
- Changed: Update usage of `league/container` from ^4.2 to ^5.1

## [2.0.6] – Nov 4, 2025
- Changed: Implicitly marking parameters as nullable is deprecated (9eb31fd)

## [2.0.5] – Nov 4, 2025
- Changed: Updated PHPUnit from 10.5.20 to 11.3.3
- Added: Static helpers `getInstance` and `setInstance`

## [2.0.4] – May 14, 2024
- Changed: Updated PHPUnit from ^9.6 to ^10.5
- Changed: Update usage of `laminas/laminas-diactoros` from ^2.24 to ^3.2

## [2.0.3] – Apr 5, 2023
- Changed: Test helpers have been made available for use external to this project (#16)

## [2.0.2] – Apr 5, 2023
- Changed: Updated PHPUnit from 8.5.32 to 9.6.6
- Added: Add makes requests trait (#15)

## [2.0.0] / [2.0.1] - Jan 30, 2023
- Changed: The minimum supported PHP version increased from ^5 to ^8
- Changed: Updated PHPUnit from 5.7.* to ^8.5
- Changed: Replaced usage of `Zend\Diactoros` with `laminas/laminas-diactoros`
- Changed: Update usage of `league/container` from ^2.2 to ^4.2
- Changed: Update usage of `league/event` from ^2.1 to ^3.0
- Changed: Update usage of `league/route` from ^3.0 to ^5.1

## [1.1.3] – Apr 5, 2018
- Changed: Helper methods for GET/POST/DELETE/PATCH now return the instance of router they modify allowing chaining if needed (e.g., adding middleware.)

## [1.1.2] – Dec 4, 2017
- Fixed: Handle cases where `$request` is null

## [1.1.1] – Dec 4, 2017
- Changed: Exception Handler is now aware of the HTTP Request

## [1.1.0] – Dec 4, 2017
- Added: Exception Handlers

## [1.0.0] – Nov 30, 2017
- First Release

[1.0.0]: https://github.com/photogabble/tuppence/tree/1.0.0
[1.1.0]: https://github.com/photogabble/tuppence/tree/1.1.0
[1.1.1]: https://github.com/photogabble/tuppence/tree/1.1.1
[1.1.2]: https://github.com/photogabble/tuppence/tree/1.1.2
[1.1.3]: https://github.com/photogabble/tuppence/tree/1.1.3
[2.0.0]: https://github.com/photogabble/tuppence/tree/2.0.0
[2.0.1]: https://github.com/photogabble/tuppence/tree/2.0.1
[2.0.2]: https://github.com/photogabble/tuppence/tree/2.0.2
[2.0.3]: https://github.com/photogabble/tuppence/tree/2.0.3
[2.0.4]: https://github.com/photogabble/tuppence/tree/2.0.4
[2.0.5]: https://github.com/photogabble/tuppence/tree/2.0.5
[2.0.6]: https://github.com/photogabble/tuppence/tree/2.0.6
[Unreleased]: https://github.com/photogabble/tuppence/tree/main
