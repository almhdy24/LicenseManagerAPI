# License Manager API

LicenseManagerAPI enables secure generation and validation of software licenses, ensuring smooth access control for your applications. This repository contains both the backend and frontend components for managing licenses.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Setup Instructions](#setup-instructions)
  - [Backend Setup](#backend-setup)
  - [Frontend Setup](#frontend-setup)
- [Usage](#usage)
  - [Generating a License](#generating-a-license)
  - [Validating a License](#validating-a-license)
  - [Managing Users](#managing-users)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## Features

- **User Management**: Create, update, delete, and list users.
- **License Management**: Generate and validate licenses associated with specific applications.
- **Role-Based Access**: Only active users can generate licenses.
- **Responsive UI**: User-friendly interface to manage licenses and users.

## Tech Stack

### Backend

- **PHP**: Main language for backend logic.
- **SQLite**: Database to store user and license information.
- **FastRoute**: Routing library.
- **Composer**: Dependency manager for PHP.

### Frontend

- **React**: JavaScript library for building user interfaces.
- **Tailwind CSS**: Utility-first CSS framework for styling.
- **SweetAlert2**: Library for beautiful alerts and popups.

## Setup Instructions

### Backend Setup

1. **Clone the Repository**:
    ```bash
    git clone https://github.com/almhdy24/LicenseManagerAPI.git
    cd LicenseManagerAPI/Backend
    ```

2. **Install Dependencies**:
    ```bash
    composer install
    ```

3. **Setup the Database**:
    ```bash
    php setup_db.php
    ```

4. **Run the Backend Server**:
    ```bash
    php -S localhost:8080 -t public
    ```

### Frontend Setup

1. **Navigate to the Project Directory**:
    ```bash
    cd ../Frontend
    ```

2. **Open `index.html` in a Browser**:
    Simply open the `index.html` file located in the `Frontend` directory in your preferred web browser.

## Usage

### Generating a License

1. Navigate to the "Generate License" section.
2. Enter the email of an active user and the application ID.
3. Click "Generate" to create a new license.

### Validating a License

1. Navigate to the "Validate License" section.
2. Enter the license code and the application ID.
3. Click "Validate" to check the validity of the license.

### Managing Users

- **Create User**: Fill in user details in the "Create User" section and click "Create User".
- **Edit User**: Click "Edit" next to a user, modify the details, and click "Update User".
- **Delete User**: Click "Delete" next to a user to remove them.

## API Endpoints

### User Endpoints

- **GET /users**: List all users.
- **GET /users/{id}**: Retrieve a single user by ID.
- **POST /users**: Create a new user.
- **PUT /users/{id}**: Update an existing user.
- **DELETE /users/{id}**: Delete a user.

### License Endpoints

- **POST /generate**: Generate a new license. Requires `email` and `application_id`.
- **GET /validate**: Validate a license. Requires `code` and `application_id`.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.