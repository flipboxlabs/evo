# EVO: An Environment Manager for Yii2 and CraftCMS
[![Join the chat at https://gitter.im/flipboxlabs/evo](https://badges.gitter.im/flipboxlabs/evo.svg)](https://gitter.im/flipboxlabs/evo?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Version](https://img.shields.io/github/release/flipboxlabs/evo.svg?style=flat-square)](https://github.com/flipboxlabs/evo/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/flipboxlabs/evo/master.svg?style=flat-square)](https://travis-ci.com/flipboxlabs/evo)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/flipboxlabs/evo.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxlabs/evo/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/flipboxlabs/evo.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxlabs/evo)
[![Total Downloads](https://img.shields.io/packagist/dt/flipboxlabs/evo.svg?style=flat-square)](https://packagist.org/packages/flipboxlabs/evo)

## Disclaimer!

This is a *very* experimental package. Use at your own risk.

## Installation

To install, use composer:

```
composer require flipboxlabs/evo
```

## Getting Started
Run `./vendor/bin/evo` for a list of commands. 

### `cloudformation` 
CloudFormation is still in development

### `config`
#### Command: `config/cat`

- Run with `./vendor/bin/evo config`
- Build your environment configurations

#### Command: `config/cat`

- Run with `./vendor/bin/evo config/cat`

### `params`
#### Command: `delete`
- Run with `./vendor/bin/evo params/delete`
- Deletes the parameter from AWS Parameter Store

#### Command: `set`
- Run with `./vendor/bin/evo params/set`
- Sets the parameter from AWS Parameter Store

#### Command: `get`
- Run with `./vendor/bin/evo params/get`
- Gets the parameter from AWS Parameter Store

#### Command: `params/print-dotenv`
- Run with `./vendor/bin/evo params/print-dotenv`
- Prints a .env file to standard output (sdout) that you can write to 
the location of your choosing. There is also an option for setting an output file. 

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/flipboxlabs/evo/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Flipbox Digital](https://github.com/flipbox)

## License

The MIT License (MIT). Please see [License File](https://github.com/flipboxlabs/evo/blob/master/LICENSE) for more information.
