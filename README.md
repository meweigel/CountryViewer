Project Country Viewer
======================

This project is a Web Application allowing a user to view various aspects of a world database
according to the view and search parameters selected. It is written using AngularJS, and php
on the server backend. The database used is MySQL. The LAMP stack used is XAMPP.

Table formation (number and types of columns) is dynamic.  


How To
------
* Install the XAMPP open source LAMP stack on Linux
* Start XAMPP 
* sudo cp -R CountryViewer/ /opt/lampp/htdocs/countryView
* Restart Apache Web Server
* Load the world_MySQL.sql data into MySQL (MariaDB) - use
  http://localhost/phpmyadmin/index.php
* Open a web browser (only tested on Google Chrome and Firefox)
* Enter http://localhost/countryView/countryView.html in the
  address bar.


Future goals:
-------------
* Extend the world database to include new tables.
* Get it working with PostgreSql.
* Create server side code using nodejs.
* Create server side code using JAVA jsp.
* Create server side code using Python.

