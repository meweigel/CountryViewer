Project Country Viewer
======================

This project is a Web Application allowing a user to view various aspects of a world database
according to the view and search parameters selected. It is written using AngularJS, and php7
on the server backend. The database used is Mariadb. 

Table formation (number and types of columns) is dynamic.  


How To Using XAMPP
------------------
* Install the XAMPP open source LAMP stack on Linux 
* Start XAMPP 
* Start Apache Web Server
* Start Mariadb
* sudo cp -R countryView/ /opt/lampp/htdocs/
* Edit /opt/lampp/htdocs/countryView/dbCredentials.php
  Add your username and password
* Load the world_MySQL.sql data into MySQL (MariaDB) - use
  http://localhost/phpmyadmin/index.php
  Login as root with the assigned root password
* Add a new user: your username and password, and give this user only select privileges
  for the world database
* Open a web browser (only tested on Google Chrome and Firefox)
* Enter http://localhost/countryView/countryView.html in the
  address bar.


How To Using Linux LAMP Stack
-----------------------------
* Please see: https://computingforgeeks.com/how-to-install-lamp-stack-on-fedora/

    sudo dnf -y update
    sudo dnf -y install vim bash-completion curl wget telnet
    sudo setenforce 0
    sudo sed -i 's/^SELINUX=.*/SELINUX=permissive/g' /etc/selinux/config
    sudo dnf -y install httpd
    sudo systemctl start httpd
    sudo systemctl enable httpd
    sudo firewall-cmd --add-service={http,https} --permanent
    sudo dnf -y install php php-cli php-php-gettext php-mbstring php-mcrypt php-mysqlnd php-pear php-curl php-gd php-xml php-bcmath php-zip
    sudo dnf install php-json.x86_64
    sudo dnf install mariadb-server

    sudo vim /etc/my.cnf.d/mariadb-server.cnf

        Set your character set under [mysqld] section

        [mysqld]
        character-set-server=utf8

    sudo systemctl start mariadb
    sudo systemctl enable mariadb
    sudo firewall-cmd --add-service=mysql --permanent
    sudo firewall-cmd --reload

    sudo vim /var/www/html/phpinfo.php

    Add:

    <?php
       // Show all information, defaults to INFO_ALL
       phpinfo();
    ?>

    sudo systemctl reload httpd

    Open added PHP info page http://localhost/phpinfo.php

    sudo dnf install phpmyadmin

* sudo cp -R countryView/ /var/www/html/

* Edit /var/www/html/countryView/dbCredentials.php

  Add the username and password
  
* Perform MariaDB initial settings like setting up a root password, disabling remote root login e.t.c:

  $ mysql_secure_installation 

* Restart Apache Web Server

* Load the world_MySQL.sql data into MySQL (MariaDB) - use
  http://localhost/phpmyadmin/index.php
  
  Login as root with the assigned root password

* Add a new user: the username and password, and give this user only select privileges
  for the world database

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

