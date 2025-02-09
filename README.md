# Laravel Integration Example

This repository contains an example of integrating the Checker into a Laravel project. This integration example demonstrates how to use the Checker service to prevent users from registering with temporary email addresses.

## Requirements
- Laravel
- Laravel Breeze (or any other Laravel authentication system)

## How to Integrate into Your Laravel Project

To integrate the temporary email checker service into your Laravel project, follow these steps:

### Step 1: Create the Checker Service
Create a new service class in `app/Services/Checker.php`:

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class Checker
{
    /**
     * Check if the given email is temporary (disposable).
     * 
     * @param string $email
     * @return bool
     */
    public static function isTemp(string $email): bool
    {
        $url = rtrim(config('services.checker.url'), '/') . '/api/v1/check';
        $token = config('services.checker.api_token');
        
        $response = Http::withOptions(['verify' => false])
            ->withToken($token)
            ->get($url, ['email' => $email]);
        
        return $response->successful() ? $response->json()['is_temporary'] : false;
    }
    
    // ...
}
```

### Step 2: Register the Service in `config/services.php`
Add the checker service configuration:

```php
return [
    // ...
    'checker' => [
        'url' => env('CHECKER_URL'),
        'api_token' => env('CHECKER_API_TOKEN'),
    ],
    // ...
];
```

### Step 3: Use the Checker Service in Registration
Modify your `RegisteredUserController.php` to check the email before registering a user:

```php
use App\Services\Checker;

if (Checker::isTemp($request->email)) {
    return back()->withInput()->withErrors(['email' => __('Temporary email addresses are not allowed.')]);
}
```

## Usage
Now, when a user tries to register, their email will be checked against the external checker service. If the email is temporary, registration will be blocked with an error message.

## License
This project is open-source and licensed under the [MIT License](LICENSE).

