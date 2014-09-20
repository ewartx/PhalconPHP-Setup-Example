Phalcon PHP is a web framework delivered as a C extension providing high performance and lower resource consumption.

PhalconPHP Sample Project Setup Example
=======================================

PhalconPHP + Grunt + Composer + Bower + Git

This is an example of how to combine the use of many popular automation tools with the Phalcon PHP Framework. This setup is intended for building web applications with scalability in mind.

Phalcon PHP
-----------

In this setup I'm using:

- Volt templating engine
- Universal Class Loader is registering the external classes via namespaces (maximizing speed)
- Config files, libary and plugins are pooled into a resources folder for better organization
- Under app/models, added a ModelBase.php which serves the same purpose as the ControllerBase.php but for common model functions
- Extending the PHQL with MysqlExtended plugin so that useful mysql functionalities such as DATE_INTERVAL,  FULLTEXT_MATCH can be supported

Grunt
-----

Grunt has become an essential tool for demanding projects. Look through the Gruntfile.coffee for ideas for your own project.

Notable highlights:

- Gruntfile written in coffeescript
- Preprocessors such as coffeescript, sass /w compass, imagemin
- Package.json separates out development and product dependencies

Composer
--------

Simplistic way to include third party php libraries for your projects with autoloading enabled. I've included PHPMailer in this repo as the example

Bower
-----

Bower has been something I've played around for a while. While its great at syncing down the latest resource packages such as bootstrap, fontawesome, it downloads the entire repository. One of the grunt automation I used often is <a href='https://github.com/Zolmeister/grunt-sails-linker' target='_blank'>Sails-Linker</a>. It auto-inserts the css and script tags in the layout file. The bloated files bower downloads conflicts /w sails-linker. For those who prefers to manually update the css/js links, bower is still a very useful package manager for you.

Git
---

This is essential to any collaborative project for both version control and deployment.

Notable highlights:
- putting .gitkeep in any empty folders, otherwise git won't sync them. This is important for the cache folder that Volt requires
- gitattributes Auto detect text files and perform LF normalization. This is important if you have both mac and window teammates working on the same project
- do not sync sourcemap files
