Human friendly URLs tools package for yii2
=======================

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-packagist]
<!--
The extension is a package of tools to implement multilanguage in Yii2 project:
- Automatically redirects the user to the URL selected (automatically or manually) language and remembers the user
selected language
- Automatically collect all new translates into DB
- Has a widget to set a correct hreflang attributes
- Provides a CRUD actions for edit the list of languages and the interface translations
- Has a widget to create language selector (for adminlte theme)
-->
Installation
------------

1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/), run:
    ```bash
    php composer.phar require --prefer-dist xz1mefx/yii2-hfu "~1.0"
    ```
2. Previous action also install the [multilanguage extension][link-multilang-extension],
so if you did not set it earlier you will need to do it **in the first place**

[ico-version]: https://img.shields.io/github/release/xz1mefx/yii2-hfu.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-downloads]: https://img.shields.io/packagist/dt/xz1mefx/yii2-hfu.svg

[link-packagist]: https://packagist.org/packages/xz1mefx/yii2-hfu
[link-multilang-extension]: https://github.com/xZ1mEFx/yii2-multilang
[link-adminlte-extension]: https://github.com/xZ1mEFx/yii2-adminlte
[link-autocomplete-extension]: https://github.com/iiifx-production/yii2-autocomplete-helper
