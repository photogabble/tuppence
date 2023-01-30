<h1 align="center">Tuppence</h1>
<p align="center"><em>An Incredibly small "framework"</em></p>

<p align="center">
  <a href="https://travis-ci.org/photogabble/tuppence"><img src="https://travis-ci.org/photogabble/tuppence.svg?branch=master" title="master"></a>
  <a href="https://packagist.org/packages/photogabble/tuppence"><img src="https://img.shields.io/packagist/v/photogabble/tuppence.svg" alt="Latest Stable Version"></a>
  <a href="LICENSE"><img src="https://img.shields.io/github/license/photogabble/tuppence.svg" alt="License"></a>
</p>

## About Tuppence

Tuppence is a very small unimposing library that brings together a [dependency injection container](http://container.thephpleague.com/), [router](http://route.thephpleague.com/) and [events](http://event.thephpleague.com/2.0/) all provided by _The League of Extraordinary Packages_.

## Installation

You can install tuppence into your project via composer `composer require photogabble/tuppence`. Alternatively you can create a new project using the [tuppence boilerplate here](https://github.com/photogabble/tuppence-boilerplate).

## 2.0 TODO List
- [x] Target minimum php version of `^8`
- [x] Update usage of PHPUnit from `5.7.*` to `^8.5`
- [ ] Replace Travis-CI with GitHub Actions
- [x] Replace usage of `Zend\Diactoros` with `laminas/laminas-diactoros` as per issue #3
- [x] Update usage of league/container from `^2.2` to `^4.2`
- [x] Update usage of league/event from `^2.1` to `^3.0`
- [x] Update usage of league/route from `^3.0` to `^5.1`
- [ ] Add tests for router DI
- [ ] Add tests for Routing to controllers 

## Not invented here

Tuppence drew a lot of inspiration from Proton by [Alex Bilbie](https://github.com/alexbilbie). Proton's repository has since been deleted.
