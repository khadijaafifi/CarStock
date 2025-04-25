<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class verifyCsrfToken
{
    protected $except = [
        '/get-ai-response',
    ];
    
}

