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

https://pwx.michaelthessel.com

Installation
============

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
app/console orm:schema-tool:create
```

Update
======

If you are updating to a newer version follow these steps.

Update dependencies:
```
# php composer.phar update
```

Update the database schema:
```
app/console orm:schema-tool:update --force
```

Tests
=====

To run the integrated test suite please run
```
# vendor/bin/phpunit
```

Translations
============

Currently:

 * English (en)
 * Spanish (es)
 * and German (de)

are supported by PWX. Please set locale according to your requirements in the
configuration. I'm happy to accept pull requests for additional translations.

Themes
======

PWX supports all [Bootswatch](https://bootswatch.com/) themes. You can adjust
the appearance of the application to your liking by a simple config switch.

Warning
=======

You will be responsible to secure your environment. The author of this software
takes no responsiblity for any damage as a result of using this software.

