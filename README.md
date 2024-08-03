<p align="center">
	<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Blog Project

## Overview

This project is a Laravel-based application that implements a clean architecture using the Repository Pattern. It includes features for user login and registration, leveraging Laravel's built-in authentication mechanisms while following best practices for dependency injection, decoupling, testability, scalability, and adhering to the Single Responsibility Principle (SRP).

## Features

- User Login
- User Logout
- User Registration
- Email Verification
- Forgot Password
- Email Reset Password

## Installation

### Prerequisites
- PHP v8.2.12
- Composer
- Laravel v11.19.0
- MySQL or any other supported database

### Steps

1. **Clone the Repository**

```sh
   git clone https://github.com/yourusername/laravel-authentication-project.git
   cd laravel-authentication-project
```

2. **Install Dependencies**

```sh
   composer install
```

3. **Set Up Environment Variables**
Copy the .env.example to .env and configure your database and mail settings.

```sh
   cp .env.example .env
```

4. **Generate Application Key**

```sh
   php artisan key:generate
```

5. **Run the Application**

```sh
   php artisan serve
```

## Design Pattern: Repository Pattern

### Overview

The Repository Pattern is used to abstract the data layer, making our application more modular, testable, and scalable. It provides a clean separation between the data access logic and the business logic.

### Structure

~~~
Interfaces: Define the contract for data operations.
Repositories: Implement the data operations as defined in the interfaces.
Service Classes: Contain business logic and use repositories for data operations.
Controllers: Handle HTTP requests and responses, using service classes for business logic.
~~~

### Implementation

1. **Interfaces**

Define the interface for user operations in `App\Repositories\UserRepositoryInterface.php`.

```php
   <?php

		namespace App\Repositories;

		interface UserRepositoryInterface
		{
				public function attemptLogin(array $credentials): bool;
				public function register(array $data);
				// any interface
		}
```
