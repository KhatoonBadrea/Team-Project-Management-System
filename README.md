This project is a Team-Project-Management-System built with Laravel 10 that provides a RESTful APIf for managing Tasks ,We allow addicts to do crud operations on projects and also on the pivot table 
We allow the manager to perform crud operations only within the project in which he is a manager
The user who has been assigned a task can modify the status of this task 
The cover-up could add a note on Tusk in the project he's just working on 
I made middelware so that the last activity of the laser that logs out is set
And I created an event & listener to switch to due date


Key Features:
CRUD Operations on projects:(Create ,update,delete,show) the admin onle can do this operation.
CRUD operation on task: (create,update,delete,show)the  manager can do this operation 
CRUD operation on pivot : (create,update,delete,show)the  admin can do this operation 
Filtering : filter the task by status & priority by use relation & heightpriority $last task & old ask for project.
use services for clean separation of concerns.
Form Requests: Validation is handled by custom form request classes.
event & listener: for dinamic update to due_date 
API Response Service: Unified responses for API endpoints.

Resources: API responses are formatted using Laravel resources for a consistent structure.

### Technologies Used:
- **Laravel 10**
- **PHP**
- **MySQL**
- **XAMPP** (for local development environment)
- **Composer** (PHP dependency manager)
- **Postman Collection**: Contains all API requests for easy testing and interaction with the API.


## Installation

### Prerequisites

Ensure you have the following installed on your machine:
- **XAMPP**: For running MySQL and Apache servers locally.
- **Composer**: For PHP dependency management.
- **PHP**: Required for running Laravel.
- **MySQL**: Database for the project
- **Postman**: Required for testing the requestes.

### Steps to Run the Project

1. Clone the Repository  
   ```bash
   git clone https://github.com/KhatoonBadrea/Team-Project-Management-System
2. Navigate to the Project Directory
   ```bash
   cd books-library
3. Install Dependencies
   ```bash
   composer install
4. Create Environment File
   ```bash
   cp .env.example .env
   Update the .env file with your database configuration (MySQL credentials, database name, etc.).
5. Generate Application Key
    ```bash
    php artisan key:generate
6. Run Migrations
    ```bash
    php artisan migrate
7. Seed the Database
    ```bash
    php artisan db:seed
8. Run the Application
    ```bash
    php artisan serve
9. Interact with the API and test the various endpoints via Postman collection 
    Get the collection from here:https://documenter.getpostman.com/view/37831879/2sAXqqchcs
