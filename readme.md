<p align="center">
Laravel Admin
</p>

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

## Installation

> This package requires PHP 7+ and Laravel 8

First, install laravel 8, and make sure that the database connection settings are correct.

``` bash
$ composer require huztw/admin
```

Then run these commands to publish assets and config：

``` bash
$ php artisan admin:publish
```

After run command you can find config file in `config/admin.php`, in this file you can change the install directory,db connection or table names.

At last run following command to finish install.

``` bash
$ php artisan admin:install
```

Open `http://localhost/admin/` in browser,use username `admin` and password `admin` to login.

## Requirements

 - PHP >= 7.0.0
 - Laravel >= 8
 - Fileinfo PHP Extension

## Configurations

The file `config/admin.php` contains an array of configurations, you can find the default configurations in there.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email c0s0c0z0@gmail.com instead of using the issue tracker.

## Credits

- [huztw][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/huztw/admin.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/huztw/admin.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/huztw/admin/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/huztw/admin
[link-downloads]: https://packagist.org/packages/huztw/admin
[link-travis]: https://travis-ci.org/huztw/admin
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/huztw
[link-contributors]: ../../contributors
