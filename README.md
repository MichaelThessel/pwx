[![Build Status](https://travis-ci.org/MichaelThessel/pwx.svg)](https://travis-ci.org/MichaelThessel/pwx)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0b168ab7-9e4e-4b31-bbf6-e05a52360209/mini.png)](https://insight.sensiolabs.com/projects/0b168ab7-9e4e-4b31-bbf6-e05a52360209)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/MichaelThessel/pwx?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

About
=====

PWX allows you to set up your own password exchange service to share passwords
via a temporary link.

For more information please check out my [blog](http://michaelthessel.com/tag/pwx/)

Example
============

https://pwx.io

Installation
============

Download the current stable release [here](https://github.com/MichaelThessel/pwx/archive/v1.0.zip) or clone the repository.

Install dependencies:
```
# php composer.phar install
```

Create configuration file and adjust according to your environment:
```
# cp app/config.php.sample app/config.php
```

Create the database schema:
```
# app/console orm:schema-tool:create
```

Update
======

Download the latest source code.

If you are updating to a newer version follow these steps.

Update dependencies:
```
# php composer.phar update
```

Update the database schema:
```
# app/console orm:schema-tool:update --force
```

Translations
============

Currently:

 * English (en)
 * Spanish (es)
 * Esperanto (eo)
 * and German (de)

are supported by PWX. Please set locale according to your requirements in the
configuration. I'm happy to accept pull requests for additional translations.

You can use a GET parameter when linking to PWX. I.e.

https://example.com?locale=es

This allows for one instance of PWX dynamically being accessed in different
languages.

Themes
======

PWX supports all [Bootswatch](https://bootswatch.com/) themes. You can adjust
the appearance of the application to your liking by a simple config switch.

Developers
==========

PWX uses the [Robo](http://robo.li) task runner. After making changes to the JS
or CSS files run:

```
# vendor/bin/robo build
```

to minify and concatenate the files.

Alternatively you can run:

```
# vendor/bin/robo watch
```

while developing which will automatically generate the minified and
concatenated assets when style or script files are changed.

To run the integrated test suite please run:
```
# vendor/bin/phpunit
```

Warning
=======

You will be responsible to secure your environment. The author of this software
takes no responsiblity for any damage as a result of using this software.
