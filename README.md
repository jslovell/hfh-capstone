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

<details>
<summary>Collapse codebase structure</summary>
```md
hfh-capstone/
├── assets/
│   └── # These are various images used by the application.
├── fpdf/
│   └── # This is a dependency used to generate PDF's.
├── images/
│   └── # These are various images used by the application.
├── php_scripts/
│   ├── add_user.php
│   ├── bk_failure.php
│   ├── bk_password_mismatch.php
│   ├── bk_password_simple.php
│   ├── bk_success.php
│   ├── db.php
│   ├── delete_all_icons.php
│   ├── delete_form.php
│   ├── delete_icon.php
│   ├── form.php
│   ├── load_icons.php
│   ├── login_page.php
│   ├── login.php
│   ├── print_to_pdf.php
│   ├── save_icon.php
│   ├── session.php
│   └── update_status.php
├── styles/
│   ├── aboutProjectStyle.css
│   ├── aboutUsStyle.css
│   ├── catalog.css
│   ├── index.css
│   ├── indexBGnew.jpg
│   ├── indexStyle.css
│   ├── jquery-ui.structure.css
│   ├── jquery-ui.structure.min.css
│   ├── jquery-ui.theme.css
│   ├── jquery-ui-theme.min.css
│   ├── navbar.css
│   ├── tabToolStyle.css
│   └── toolStyle.css
├── uploads/
│   ├── layouts/ # Directory for uploaded home layouts.
│   └── photos/ # Directory for uploaded images for icons.
├── .gitignore
├── about_project.php
├── about_us.php
├── appMenu.php
├── bk_test_page.php
├── catalog.php
├── hfh.sql
├── homepage.php
├── houseAssesmentTool.php
├── houseAssessmentTool.php
├── index.php
├── jquery-ui.css
├── jquery-ui.js
├── jquery-ui.min.css
├── jquery-ui.min.js
├── login_page_failure.php
├── login_screen.php
├── navbar.php
├── new_user_failure.php
├── new_user_simple.php
├── new_user.php
├── README.md
├── script.js
├── tempHAStyle.css
└── test_page.php
```
</details>

## Usage

### For Developers

Follow these steps to run the application:
1. Start the XAMPP Apache and MySQL modules.
2. Connect to the application using `http://localhost/hfh-capstone/`.

You may need to create a new account from the home page.
Also a full user manual can be found in the navbar.

### User Management

Currently, there is nothing in place to authorize the creation of new accounts.
Also, the only way to either delete or edit existing accounts is to manually edit the database.
These issues have been marked for future development.

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
