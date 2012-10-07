[![Build Status](https://secure.travis-ci.org/ryanc/pastebin.png)](http://travis-ci.org/ryanc/pastebin)

Basic Setup
===========

1. Check out the source code.

        git clone https://github.com/ryanc/pastebin.git
        cd pastebin

2. Install [Composer](http://getcomposer.org)

        curl -s http://getcomposer.org/installer | php

3. Install the project dependencies.

        php composer.phar install

4. Bootstrap the database schema.

        sqlite3 db/pastebin.db < sql/schema.sql

5. Assign permissions to folders that need to be writable to the web
   server.

        # For Debian/Ubuntu
        chown -R www-data cache logs db
        # For Redhat/CentOS/Fedora
        chown -R apache cache logs db

6. Configure your web server. Sample configuration files are available
   for both [Apache](https://github.com/ryanc/pastebin/blob/master/puppet/files/etc/apache2/sites-available/pastebin) and [Nginx](https://github.com/ryanc/pastebin/blob/master/puppet/files/etc/nginx/sites-available/pastebin).

Quick Setup
===========

This setup is ideal for development since it uses the vagrant
configuration that has been committed to this Git repository. This
requires that you have [VirtualBox](http://www.virtualbox.org) and [Vagrant](http://vagrantup.com) installed.

1. Check out the source code.

        git clone https://github.com/ryanc/pastebin.git
        cd pastebin

2. Start [Vagrant](http://vagrantup.com) VM.

        vagrant up

3. Bootstrap the database schema.

        sqlite3 db/pastebin.db < sql/schema.sql

4. SSH into VM.

        vagrant ssh
        cd /vagrant

5. Install [Composer](http://getcomposer.org)

        curl -s http://getcomposer.org/installer | php

6. Install the project dependencies.

        php composer.phar install

7. Visit [http://localhost:8080](http://localhost:8080) in your web browser.
