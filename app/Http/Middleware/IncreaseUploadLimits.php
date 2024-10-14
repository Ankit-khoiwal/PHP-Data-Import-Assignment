<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseUploadLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Increase the upload size and execution limits
        ini_set('upload_max_filesize', '600M');
        ini_set('post_max_size', '600M');
        ini_set('max_execution_time', '800');
        ini_set('max_input_time', '800');
        ini_set('memory_limit', '512M');

        return $next($request);
    }
}
