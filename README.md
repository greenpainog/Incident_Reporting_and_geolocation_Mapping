# Emergency Response Event Mapping System

## Overview
The Emergency Response Event Mapping System is a web-based platform developed to assist in tracking and managing events handled by emergency services, specifically police and fire departments. Users can log in to view events, while admins have enhanced privileges to modify data and oversee operations. The system features an intuitive interface with support for both desktop and mobile platforms.

## Features
- **User Authentication:** Users must create an account and log in to access features. Sessions are maintained even after server restarts.
- **Event Mapping:** Displays real-time event locations across Greece, including street names and incident details.
- **Department Selection:** Users can choose between viewing incidents handled by police or fire departments.
- **Admin Control:** Admin users have the ability to add, update, and delete events.
- **Responsive Design:** Optimized for both desktop and mobile views.
- **Planned Enhancements:** Real-time updates, advanced filtering, improved UI, and role-based access control.

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP, MySQL
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
    - Set up Google Maps API for location mapping.

3. **Database setup:**
    - Import the MySQL database from the `/db` folder:
    ```bash
    mysql -u username -p database_name < /path_to/dbfile.sql
    ```
    - Ensure that you have created the required users, tables, and privileges.

4. **Configure the application:**
    - Update the configuration files (such as database connection in `config.php`).

5. **Run the application:**
    - Start your Apache server and navigate to `http://localhost/yourprojectname` or the IP address of your VM.

## Usage
1. Navigate to the `startpage.php` and log in with your credentials or sign up for a new account.
2. Once logged in, you can select a department (police or fire) to view relevant events.
3. Admin users will have the ability to add, edit, and delete events directly from the dashboard.
