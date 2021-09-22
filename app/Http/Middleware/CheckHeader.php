<?php
namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Contracts\Auth\Guard;

class CheckHeader
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($_SERVER['HTTP_X_LANG'])) {
            App::setLocale($_SERVER['HTTP_X_LANG']);
        }

        return $next($request);
    }
}
