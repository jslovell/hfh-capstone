# hfh-capstone

Home assessment tool for the Habitat for Humanity Aging in Place Program, Des Moines IA.
This is used to annotate home layouts with information that may be needed by contractors.

## Installation and Setup

### For Production

In order to use this project, you will need both an Apache PHP server and a MySQL database.
Then the following steps can be taken:
1. Copy the contents of this repository into the `htdocs` directory within your Apache server.
2. Import the necessary tables into database using `hfh.sql`.
3. Configure the database connection in `php_scripts\db.php`, particularly the username and password.

All other dependencies are included in the repository.

### For Developers

Developers will need to have [XAMPP Control Panel](https://www.apachefriends.org) installed on their computers.
Then the following steps can be taken:
1. Clone this repository directly into the `htdocs` directory. This is usually found in `C:\xampp\htdocs`.
2. Start the XAMPP Apache and MySQL modules.
3. Open the database admin tool by connecting to `http://localhost/phpmyadmin/`.
4. Create a new database.
5. Import the necessary tables to the new database using `hfh.sql`.
6. Configure the database connection in `php_scripts\db.php`, particularly the username, password, and dbname.
The "Localhost Database Variables" often work by default.

## Codebase Structure

```md
hfh-capstone/
├── assets/
│   └── # These are various images used by the application.
├── fpdf/
│   └── # This is a dependency used to generate PDF's.
├── images/
│   └── # These are various images used by the application.
├── php_scripts/
│   └──
├── styles/
│   └──
├── uploads/
│   ├── layouts/ # Directory for uploaded home layouts.
│   └── photos/ # Directory for uploaded images for icons.
├── file1
└── file2

```

## Usage

For developers, follow these steps to run the application:
1. Start the XAMPP Apache and MySQL modules.
2. Connect to the application using `http://localhost/hfh-capstone/`.

A link to a full user manual can be found in the navbar.

## Code Documentation

### Assessment Tool

The code behind the assessment tool itself (where the user may place icons and annotate over the top of home layouts) is found in `test_page.php` and `script.js`.
In the web browser, information about icons are stored locally as json.
This is generated from database queries, and can be written back to the database.
This json information also configures the placement of each icon on the screen.

### Print to PDF

Within `php_scripts\print_to_pdf.php`, two queries are run to get all of the data for a certain assessment.
The [FPDF Library](https://www.fpdf.org)
is used to generate all necessary text and images from this data.
Icons are placed on top of the layout with numerical labels.
These can be referenced on the following pages in a table format.

## Contributing

It is recommended to use either
[GitHub Desktop](https://desktop.github.com/download),
[SourceTree](https://www.sourcetreeapp.com),
or another similar application to create branches, commit/push/pull changes, and handle pull requests.
More information about Git commands can be found
[here](https://www.atlassian.com/git/tutorials/atlassian-git-cheatsheet).
