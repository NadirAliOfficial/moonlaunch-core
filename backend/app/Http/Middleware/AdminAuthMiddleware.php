<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated using the 'admin' guard
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access the admin panel.');
        }

        // Optional: Fetch admin from database (if needed for more checks)
        $admin = Auth::guard('admin')->user(); // Get the authenticated admin

        if (!$admin) {
            // Admin not found, logout
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        // Store admin info in request for easy access in controllers
        $request->merge([
            'admin' => $admin,
            'admin_role' => $admin->role_type,
        ]);

        return $next($request);
    }
}
