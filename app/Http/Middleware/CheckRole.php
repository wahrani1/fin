<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Always return JSON response for API routes
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect('login');
        }

        $user = Auth::user();

        // For debugging
        \Log::info('User type: ' . $user->type);
        \Log::info('Required role: ' . $role);

        // Check role
        $checkMethod = 'is' . ucfirst($role);
        if (method_exists($user, $checkMethod) && $user->$checkMethod()) {
            return $next($request);
        }

        // Always return JSON response for API routes
        if ($request->is('api/*')) {
            return response()->json(['error' => 'Forbidden: Requires ' . $role . ' role'], 403);
        }

        return redirect('dashboard')->with('error', 'You do not have permission to access this resource.');
    }
}
?>
