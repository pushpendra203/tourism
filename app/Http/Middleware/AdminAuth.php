<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Check if the admin is authenticated
         if (Auth::guard('admin')->check()) {
            // If the admin is trying to access the login page, redirect to the dashboard
            if ($request->is('admin')) {
                return redirect('admin/dashboard');
            }
            // Allow the request to proceed
            return $next($request);
        } else {
            // Redirect unauthenticated admins to the login page
            if (!$request->is('admin')) {
                return redirect('admin');
            }
        }
        return $next($request);

    } 
}
