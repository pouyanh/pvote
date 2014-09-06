#### PVote
### Table Of Content
* [What is it](#what-is-it)
* [Installation](#installation)

### <a name="what-is-it"></a>What is it
PVote is Voting application which burst into existence during the 5th ZConf (Zanjan Conference) 2014 to find out the most favoured workshops to be held. Although it can be used for elections taking place over LAN (Local Area Network).

### <a name="installation"></a>Installation
## <a name="requisites"></a>Requisites
* PHP with [Phalcon](http://www.phalconphp.com) extension enabled
* HTTP Server with rewrite extension enabled (Rather [Apache](http://httpd.apache.org/), if another is used you have to write rewrite rules located in .htaccess and front/.htaccess your own)

To make it work you have to:
1. Do a [Composer](https://getcomposer.org/) update
2. Put election candidates in Options.xml (as it is shown in Options.xml.skeleton). By default Options.xml contains ZConf5's workshops as voting options
3. Set root directory of this project as the web server's (or virtual host's) DocumentRoot. Make sure that './var' is writable by http user
4. Enfranchise yourselves