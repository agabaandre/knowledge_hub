<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class ApiClientAuthProvider extends EloquentUserProvider
{
     /**
     * Retrieve a user by the given credentials.
     * Override this method to implement custom logic for username column.
     */
    public function retrieveByCredentials(array $credentials)
    {
        // Do not attempt to retrieve the user if no credentials are provided
        if (empty($credentials) || (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        // Create a new query for the model
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains($key, 'password')) {
                continue;
            }

            // Replace 'custom_username' with your custom column name
            if ($key === 'username') {
                $key = 'api_key';
            }

            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     * Use custom_password for password comparison.
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        // Assume the custom_password attribute holds the hashed password
        return $this->hasher->check($plain, $user->api_secret);
    }
}