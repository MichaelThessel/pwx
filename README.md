About
=====

Pwx allows you to set up your own password exchange service to share passwords
via a temporary link.

You will be responsible to secure your environment. The author of this software
takes no responsiblity for any damage as a result of using this software.

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
