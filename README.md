# Emergency Response Event Mapping System

## Overview
The Emergency Response Event Mapping System is a web-based platform developed to assist in tracking and managing emergency events handled by police and fire departments. The system includes AI-driven text and category extraction for automated event reporting, improving the accuracy and speed of incident classification. Users can log in, view events, and admins have additional privileges to manage the events database.

## Features
- **AI-Powered Event Reporting:** Automatic text and category extraction using OpenAI's ChatGPT API to categorize events reported by users.
- **User Authentication:** Secure user login and session management to access the system.
- **Event Mapping:** Visual representation of event locations across Greece, using the Google Maps API.
- **Department Selection:** Users can choose between police and fire department events.
- **Admin Control:** Admins have the ability to add, update, and delete events.
- **Mobile Optimization:** Responsive design to support both desktop and mobile views.
- **Planned Enhancements:** Real-time updates, advanced filtering, and role-based access control.

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP, MySQL, Python
- **AI Integration:** OpenAI's ChatGPT API for text and category extraction
- **Database:** MySQL with tables for users, events, and departments
- **Server:** Apache, running on a Linux VM (VirtualBox)
- **Mapping API:** Google Maps API for visualizing event locations

## Installation
To run this project locally, follow these steps:

1. **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/yourprojectname.git
    ```
2. **Set up the environment:**
    - Ensure you have a LAMP stack (Linux, Apache, MySQL, PHP) installed. If you are on Windows, VirtualBox with a Linux VM can be used.
    - Install dependencies via `composer` if applicable.
    - Set up Google Maps API and OpenAI API keys for text extraction and mapping functionalities.

3. **Database setup:**
    - Import the MySQL database from the `/db` folder:
    ```bash
    mysql -u username -p database_name < /path_to/dbfile.sql
    ```
    - Ensure that you have created the required users, tables, and privileges.

4. **Configure the application:**
    - Update the configuration files (such as database connection in `config.php`, API keys in `.env`).

5. **Run the application:**
    - Start your Apache server and navigate to `http://localhost/yourprojectname` or the IP address of your VM.

## Usage
1. Navigate to the `startpage.php` and log in with your credentials or sign up for a new account.
2. Report an event by filling in the description; the system will use AI to extract and categorize the event automatically.
3. Once logged in, users can view event locations and choose between police and fire department reports.
4. Admins can add, edit, or delete events through the admin dashboard.
5. Users and admin can select to see a live map of historical or live events, marked and categorized by category.
