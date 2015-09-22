[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0b168ab7-9e4e-4b31-bbf6-e05a52360209/mini.png)](https://insight.sensiolabs.com/projects/0b168ab7-9e4e-4b31-bbf6-e05a52360209)

About
=====

Pwx allows you to set up your own password exchange service to share passwords
via a temporary link.

You will be responsible to secure your environment. The author of this software
takes no responsiblity for any damage as a result of using this software.

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

Install the database dump (MySQL):
```
# cat install/install.sql | mysql -u [user] -h [host] -p [dbname]
```

Create configuration file and adjust according to your environment:
```
# cp app/config.php.sample app/config.php
```

Translations
============

Currently:

 * English (en)
 * Spanish (es)
 * and German (de)

are supported by PWX. Please set locale according to your requirements in the
configuration. I'm happy to accept pull requests for additional translations.
