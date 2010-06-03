#Donor Track

Donor Track is a lightweight PHP application for tracking donations to a charity. Donor Track helps you:

* Store up to date contact information
* Keep record of contact interactions
* Track fundraising campaign progress

Donor Track *does not* generate letters or emails, that is a task best left to real word processing applications.

This project is still under heavy development and is not yet feature complete, basic functionality is working though.

##Requirements

Donor Track requires a webserver running PHP and uses a MySQL database. This software is developed and tested using PHP 5 and MySQL 5, earlier versions may work, but I`m not making any effort to support them.

##Installation

1. Extract the distribution to the web server
2. Modify includes/config.php to match the database being used.
3. Navigate to install.php in your web browser and follow the instructions on the page.
4. Delete install.php

Donor Track is now ready for use!

## Development

Donor Track is still under heavy development. Presently my TODO list looks something like:

* Add support for multiple users (UI)
* Rewrite contact searching
* Add limited csv export, based on search results
* Add reporting features

##License

Donor Track is licensed under the [Apache 2.0 License](http://www.apache.org/licenses/LICENSE-2.0).

This software is based on the [Simple Customer](http://www.simplecustomer.com/) project.
