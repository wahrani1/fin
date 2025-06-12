<?php
// Create or locate your AuthenticateWithSanctum middleware
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AuthenticateWithSanctum extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // For API routes, we don't want to redirect, just return null
        // This will cause the middleware to throw an unauthenticated exception
        // which will be caught by the exception handler and converted to a JSON response
        if ($request->is('api/*')) {
            return null;
        }

        return route('login');
    }
}
?>
