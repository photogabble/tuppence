# Tuppence

[![PHP Composer](https://github.com/photogabble/tuppence/actions/workflows/php.yml/badge.svg)](https://github.com/photogabble/tuppence/actions/workflows/php.yml)
[![Packagist](https://img.shields.io/packagist/v/photogabble/tuppence.svg)](https://packagist.org/packages/photogabble/tuppence)
[![MIT Licensed](https://img.shields.io/github/license/photogabble/tuppence.svg)](LICENSE)

## About Tuppence

Tuppence is a _very small_ micro framework that brings together a [powerful PSR-11 dependency injection container](http://container.thephpleague.com/), a [fast PSR-7 router supporting PSR-15 middleware](http://route.thephpleague.com/) and a [simple and effective PSR-14 event dispatcher](http://event.thephpleague.com/3.0/) all provided by _The League of Extraordinary Packages_.

Tuppence aims to be simple, lightweight and extremly flexible in order to povide the tools needed to _quickly_ write web applications and APIs.

## Installation

Its reccomended to use [Composer](https://getcomposer.org/) to install this framework and all required dependcies:
```
$ composer require photogabble/tuppence
```

Alternatively you can create a new project using the [tuppence boilerplate](https://github.com/photogabble/tuppence-boilerplate) via:

```
$ composer create-project photogabble/tuppence-boilerplate
```

### Requirements
Tuppence 2.0 requires PHP 8.0 or newer.

## 2.0 TODO List
- [x] Target minimum php version of `^8`
- [x] Update usage of PHPUnit from `5.7.*` to `^8.5`
- [x] Replace Travis-CI with GitHub Actions
- [x] Replace usage of `Zend\Diactoros` with `laminas/laminas-diactoros` as per issue #3
- [x] Update usage of league/container from `^2.2` to `^4.2`
- [x] Update usage of league/event from `^2.1` to `^3.0`
- [x] Update usage of league/route from `^3.0` to `^5.1`
- [ ] Add tests for router DI
- [ ] Add tests for Routing to controllers
- [ ] Add documentation

## Not invented here

Tuppence was first created as an update to and in inspiration from Proton by [Alex Bilbie](https://github.com/alexbilbie). It appears that in the many years since then the Proton repository has been deleted.
