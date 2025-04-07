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

Developers will need to have
[XAMPP Control Panel](https://www.apachefriends.org)
installed on their computers.
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
├── assets/                         # Various images used by the application
├── fpdf/                           # Library used to generate PDFs
├── images/                         # Various images used by the application
├── php_scripts/
│   ├── add_user.php                # Script to add user
│   ├── bk_failure.php              # Script for login failure
│   ├── bk_password_mismatch.php    # Script for login with a password that does not match
│   ├── bk_password_simple.php      # Script for creation of password not matching requirements
│   ├── bk_success.php              # Script for account creation success
│   ├── db.php                      # Script for connection to the database
│   ├── delete_all_icons.php        # Script for deleting icons from the Home Assessment Tool
│   ├── delete_form.php             # Script for deleting a form for the Home Assessment Form
│   ├── delete_icon.php             # Script for deleting a single icon from the Home Assessment Tool
│   ├── form.php                    # Script for the Home Assessment Form
│   ├── load_icons.php              # Script for loading icons onto the page
│   ├── login_page.php              # Script for login page itself
│   ├── login.php                   # Script for logging in
│   ├── print_to_pdf.php            # Script for printing the Home Assessment Tool page to a PDF
│   ├── save_icon.php               # Script to save progress on the Home Assessment Tool
│   ├── session.php                 # Script for holding login information
│   └── update_status.php           # Script for assessment status output
├── styles/
│   ├── aboutProjectStyle.css       # Project styles
│   ├── aboutUsStyle.css            # Styles for the about us page
│   ├── catalog.css                 # Styles for the catalog, important for the look
│   ├── index.css                   # Styles for index.php
│   ├── indexBGnew.jpg              # Background for many pages
│   ├── indexStyle.css              # Styles for login
│   ├── jquery-ui.structure.css     # Import containing old functions, might cause conflicts with future functions
│   ├── jquery-ui.structure.min.css # Import containing old functions, might cause conflicts with future functions
│   ├── jquery-ui.theme.css         # Import containing old functions, might cause conflicts with future functions
│   ├── jquery-ui-theme.min.css     # Import containing old functions, might cause conflicts with future functions
│   ├── navbar.css                  # Styles for the navbar itself
│   ├── tabToolStyle.css            # Styles for formatting inside the navbar
│   └── toolStyle.css               # Styles for formatting inside the navbar
├── uploads/
│   ├── layouts/                    # Directory for uploaded home layouts
│   └── photos/                     # Directory for uploaded images for icons
├── .gitignore                      # Ignore for GitHub
├── about_project.php               # Contains updates for each week about project progress in 2024-2025
├── about_us.php                    # About page for showing who worked on the project
├── appMenu.php                     # Import for the icons inside of the nav bar
├── bk_test_page.php                # Assessment Tool test
├── catalog.php                     # Contains the HTML for the catalog
├── hfh.sql                         # Database schema
├── homepage.php                    # Old home page; Not in use
├── houseAssesmentTool.php          # Typo; Not used
├── houseAssessmentTool.php         # Contains the assessment form
├── index.php                       # Old main page; Not in use
├── jquery-ui.css                   # Old code; Import containing css functions for use (might cause conflicts, need to dissect)
├── jquery-ui.js                    # Old code; Import containing JS functions for use (might cause conflicts, need to dissect)
├── jquery-ui.min.css               # Old code; Import containing css functions for use (might cause conflicts, need to dissect)
├── jquery-ui.min.js                # Old code; Import containing additional JS for use (might cause conflicts, need to dissect)
├── login_page_failure.php          # Failure page to display error when credentials are not put in correctly
├── login_screen.php                # Login screen for the project; Landing page when first using the software
├── navbar.php                      # Contains the imports of the navbar
├── new_user_failure.php            # Failure case sent here
├── new_user_simple.php             # Simple version of new user; Not in use
├── new_user.php                    # Base page for new user, sent here from login page
├── README.md                       # You are reading this currently
├── script.js                       # Contains the JavaScript for test page, important for icon functions
├── tempHAStyle.css                 # Used for a test version of the Home Assessment Form
└── test_page.php                   # This is the main page for the Home Assessment Tool
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
Icons are placed over the top of the layout with numerical labels.
These can be referenced on the following pages in a table format.

## Contributing

It is recommended to use either
[GitHub Desktop](https://desktop.github.com/download),
[SourceTree](https://www.sourcetreeapp.com),
or another similar application to create branches, commit/push/pull changes, and handle pull requests.
More information about Git commands can be found
[here](https://www.atlassian.com/git/tutorials/atlassian-git-cheatsheet).
