# Laravel Blog Project
<!-- # Laravel Authentication Project -->

## Overview

This project is a Laravel-based application that implements a clean architecture using the **Repository Pattern**. It includes features for user login and registration, leveraging Laravel's built-in authentication mechanisms while following best practices for dependency injection, decoupling, testability, scalability, and adhering to the Single Responsibility Principle (SRP).

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
 Interfaces:				Define the contract for data operations.
 Repositories:			Implement the data operations as defined in the interfaces.
 Service Classes:		Contain business logic and use repositories for data operations.
 Controllers:				Handle HTTP requests and responses, using service classes for business logic.
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

2. **Repositories**

Implement the interface in `App\Repositories\UserRepository.php`.

```php
<?php

namespace App\Repositories;

class UserRepository implements UserRepositoryInterface
{
public function attemptLogin(array $credentials): bool
{
  return Auth::attempt($credentials);
}

public function register(array $data)
{
  $user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
  ]);

  Mail::to($user->email)->send(new VerificationEmail($user));

  return $user;
}

// any implementation
}
```

3. **Binding in AppServiceProvider**

Register the binding in `App\Providers\AppServiceProvider.php`.

```php
<?php

namespace App\Providers;

class AppServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
  }
}
```

4. **Service Classes**

Create a service class `App\Services\AuthService.php`.

```php
<?php

namespace App\Services;

class AuthService
{
  protected $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function login(array $credentials): bool
  {
    return $this->userRepository->attemptLogin($credentials);
  }

  public function register(array $data)
  {
    return $this->userRepository->register($data);
  }

  // any service
}
```

5. **Controllers**

Use the service class in `App\Http\Controllers\AuthController.php`.

```php
<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
  protected $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    if ($this->authService->login($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended('/');
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ]);
  }

  public function register(registerUserRequest $request): RedirectResponse
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $this->authService->register($request->only(['name', 'email', 'password']));

    return redirect('/')->with('status', 'verification-link-sent');
  }
}

```

### Benefits

- Decoupling: The business logic is separated from data access logic. You can change the data access logic without affecting the business logic.
- Testability: You can easily mock the repository interface for unit testing.
- Scalability: You can replace the repository implementation with a different data source (e.g., cache, remote API) without changing the service or controller.
- Single Responsibility Principle (SRP): Each class has a single responsibility. For example, the AppServiceProvider only handles binding, and the AuthService only handles business logic.

### Example Use Cases

#### Decoupling:

Changing the data source from database to cache or API without affecting controllers or services.

```php
<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

class CacheUserRepository implements UserRepositoryInterface
{
  public function attemptLogin(array $credentials): bool
  {
    // Implement login logic using cache
  }

  public function register(array $data)
  {
    // Implement register logic using cache
  }
}
```

#### Testability:

Mocking the repository for unit tests to isolate the service logic.

```php
<?php

class AuthServiceTest extends TestCase
{
    public function testLogin()
    {
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('attemptLogin')->with(['email' => 'test@example.com', 'password' => 'password'])->andReturn(true);

        $authService = new AuthService($mockRepo);
        $this->assertTrue($authService->login(['email' => 'test@example.com', 'password' => 'password']));
    }
}
```

#### Scalability:

Switching to a different repository implementation for performance improvements or change using Remote API.

```php
<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class ApiUserRepository implements UserRepositoryInterface
{
    public function attemptLogin(array $credentials): bool
    {
        $response = Http::post('https://api.example.com/login', $credentials);
        return $response->successful();
    }

    public function register(array $data)
    {
        $response = Http::post('https://api.example.com/register', $data);
        return $response->json();
    }
}
```


### SRP:

Keeping each class focused on a single responsibility, improving code readability and maintainability.
By separating the binding configuration into an `AppServiceProvider`, we maintain the SRP principle where each class or file has only one responsibility.

Register the binding in `App\Providers\AppServiceProvider.php`

```php
<?php

namespace App\Providers;

class AppServiceProvider extends ServiceProvider
{
  public function register()
  {
    // binding any interface and implementation repository here
  }
}
```

## Contributing

If you wish to contribute to this project, please fork the repository and submit a pull request with your changes.

## License
This project is licensed under the MIT License.