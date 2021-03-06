# Magic Helper

> Helping you with your [Magic the Gathering](https://magic.wizards.com/) cards

_currently in developpment!_

This software is designed to help you manage your collection of MTG cards: inventory, complex search, deck building, etc...

Data used in this project come from the [Scryfall API](https://scryfall.com/docs/api)

This project is build using PHP 7.4 and Symfony 4

## Installation (dev)

copy .env.dist to .env.dev  
download a card bulk file from [Scryfall](https://scryfall.com/docs/api/bulk-data) and place it under var/scryfallData.json

install dependencies

```sh
php composer.phar install
```

build the database if needed

```sh
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

populate the databse with scryfall data

```sh
php bin/console scryfall:update
```

start dev server

```sh
symfony server:start
```

<!--
## Usage example

A few motivating and useful examples of how your product can be used. Spice this up with code blocks and potentially more screenshots.

_For more examples and usage, please refer to the [Wiki][wiki]._

## Development setup

Describe how to install all development dependencies and how to run an automated test-suite of some kind. Potentially do this for multiple platforms.

```sh
make install
npm test
```

## Release History

* 0.2.1
  * CHANGE: Update docs (module code remains unchanged)
* 0.2.0
  * CHANGE: Remove `setDefaultXYZ()`
  * ADD: Add `init()`
* 0.1.1
  * FIX: Crash when calling `baz()` (Thanks @GenerousContributorName!)
* 0.1.0
  * The first proper release
  * CHANGE: Rename `foo()` to `bar()`
* 0.0.1
  * Work in progress

## Meta

Your Name – [@YourTwitter](https://twitter.com/dbader_org) – YourEmail@example.com

Distributed under the XYZ license. See ``LICENSE`` for more information.

[https://github.com/yourname/github-link](https://github.com/dbader/)

## Contributing

1. Fork it (<https://github.com/yourname/yourproject/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

[npm-image]: https://img.shields.io/npm/v/datadog-metrics.svg?style=flat-square
[npm-url]: https://npmjs.org/package/datadog-metrics
[npm-downloads]: https://img.shields.io/npm/dm/datadog-metrics.svg?style=flat-square
[travis-image]: https://img.shields.io/travis/dbader/node-datadog-metrics/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/dbader/node-datadog-metrics
[wiki]: https://github.com/yourname/yourproject/wiki
-->
