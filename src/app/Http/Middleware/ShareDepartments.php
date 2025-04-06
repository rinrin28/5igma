<?php

namespace App\Http\Middleware;

use App\Models\Department;
use Closure;
use Illuminate\Http\Request;

class ShareDepartments
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $departments = Department::orderBy('id')->get();
            view()->share('departments', $departments);
        }

        return $next($request);
    }
} 