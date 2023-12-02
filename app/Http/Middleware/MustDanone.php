<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MustDanone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idDanone = Auth::user()->role_id;

        if($idDanone != 3){
            return response()->json([
                "message" => "Anda tidak berhak"
            ], 403);
        }

        return $next($request);
    }
}
