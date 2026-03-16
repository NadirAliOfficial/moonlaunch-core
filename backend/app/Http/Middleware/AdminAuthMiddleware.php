<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access the admin panel.');
        }

        $admin = DB::table('roles')->where('roles_id', $adminId)->first();

        if (!$admin) {
            session()->forget('admin_id');
            return redirect()->route('admin.login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        $request->merge([
            'admin'      => $admin,
            'admin_role' => $admin->role_type ?? null,
        ]);

        return $next($request);
    }
}
