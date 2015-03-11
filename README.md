About
=====

Pwx allows you to set up your own password exchange service to share passwords
via a temporary link.

You will be responsible to secure your environment. The author of this software
takes no responsiblity for any damage as a result of using this software.

Example
============

http://pwx.michaelthessel.com

Installation
============

php composer.phar install

Add the database table:
```
CREATE TABLE `credentials` (
  `hash` varchar(10) NOT NULL DEFAULT '',
  `userName` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

Copy app/config.php.sample app/config.php and adjust values according to your
environment.
