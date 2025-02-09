<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Checker
{
    /**
     * Check if the given email is temporary(disposable).
     * 
     * @param string $email
     * @return bool
     */
    public static function isTemp(string $email): bool
    {
        // Endpoint of the checker service
        $url = rtrim(config('services.checker.url'), '/') . '/api/v1/check';

        // This is the token of the checker service
        $token = config('services.checker.api_token');

        // Ask the checker service if the email is temporary
        $response = Http::withOptions(['verify' => false])->withToken($token)->get($url, ['email' => $email]);

        if ($response->successful()) {
            return $response->json()['is_temporary'];
        }
        
        if ($response->clientError()) {

            // log the error
            logger()->error('Checker service returned a client error. Error: ' . $response->body());
        }

        // log the error
        logger()->error('Checker service returned an unexpected response. Response: ' . $response->body());
        
        // If the checker service returned an unexpected response, we assume the email is not temporary
        // This is a safe assumption because we don't want to block users from signing up
        // If you want to block users from signing up in case of an error, you can return true here
        return false;
    }
}
