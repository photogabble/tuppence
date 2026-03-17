# Tuppence

[![PHP Composer](https://github.com/photogabble/tuppence/actions/workflows/php.yml/badge.svg)](https://github.com/photogabble/tuppence/actions/workflows/php.yml)
[![Packagist](https://img.shields.io/packagist/v/photogabble/tuppence.svg)](https://packagist.org/packages/photogabble/tuppence)
[![MIT Licensed](https://img.shields.io/github/license/photogabble/tuppence.svg)](LICENSE)

## About Tuppence

Tuppence is a _very small_ micro framework that brings together a [powerful PSR-11 dependency injection container](http://container.thephpleague.com/), a [fast PSR-7 router supporting PSR-15 middleware](http://route.thephpleague.com/) and a [simple and effective PSR-14 event dispatcher](http://event.thephpleague.com/3.0/) all provided by _The League of Extraordinary Packages_.

Tuppence aims to be simple, lightweight and extremely flexible in order to provide the tools needed to _quickly_ write web applications and APIs.

## Installation

It's recommended to use [Composer](https://getcomposer.org/) to install this framework and all required dependencies:
```
$ composer require photogabble/tuppence
```

Alternatively, you can create a new project using the [tuppence boilerplate](https://github.com/photogabble/tuppence-boilerplate) via:

```
$ composer create-project photogabble/tuppence-boilerplate
```

### Requirements
Tuppence requires PHP 8.4 or newer, and I will not provide support for older versions of PHP.

## 3.0 TODO List
- [ ] Add usage documentation

## Not invented here

Tuppence was first created as an update to and in inspiration from Proton by [Alex Bilbie](https://github.com/alexbilbie). It appears that in the many years since then the Proton repository has been deleted.
