<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Article;
use Auth;

class Owner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $article = $request->route()->parameter('article');
        if(Auth::guard('api')->check() && (Auth::guard('api')->user()->id == $article->author->id)){
            return $next($request);
        }
        return response()->json([
            'message' => 'Not authorized',
        ], 403);

    }
}
?>