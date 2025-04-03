# CS-490-Capstone

Home assessment tool for the Habitat for Humanity Aging in Place Program, Des Moines IA.
This is used to annotate home layouts with information that may be needed by contractors.

## Production Installation

In order to use this project, you will need both an Apache PHP server and a MySQL database.
Then the following steps can be taken:
1. Copy the contents of this repository into the `htdocs` directory within your Apache server.
2. Import the necessary tables into database using `hfh.sql`.
3. Configure the database connection in `php_scripts\db.php`, particularly the username and password.

All other dependencies are included in the repository.

## Developer Installation

Developers will need to have [XAMPP Control Panel](https://www.apachefriends.org) installed on their computers.
Then the following steps can be taken:
1. Clone this repository either directly into the `htdocs` directory or into some directory `htdocs\<name>`. This is usually found in `C:\xampp\htdocs`.
3. Start the XAMPP Apache and MySQL modules.
2. Create a new database and import the necessary tables using `hfh.sql`.
3. Configure the database connection in `php_scripts\db.php`, particularly the username, password, and dbname. The "Localhost Database Variables" often work by default.
4. Connect to the web app using either `http://localhost/` or `http://localhost/<name>/`, depending on your choices in step 1.

## Use

A link to a full user manual can be found in the navbar.
