<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

use Illuminate\Support\Facades\Auth;
use App\Traits\HasRolesAndPermissions;

//use Illuminate\Support\Facades\Log;

class AdminCheck
{
    use HasRolesAndPermissions;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Log::info('hello, inside middleware...');

        //if (($request->path() == '/') || ($request->path() == 'sanctum/csrf-cookie') || ($request->path() == 'login')) {
        //    return $next($request);
        //}

       
        //if (Auth::check()) {
            //    return response()->json([
            //        'msg' => 'You are not allowed to access this route... ',
            //        'url' => $request->path()
            //    ], 403);    //403, 用戶沒有權限訪問此站，伺服器收到請求但拒絕提供服務
            //}
        //}

        if(Auth::check()) { //check if the user is logged in or not
            $user = Auth::user();
            $tt = $user->roles()->get();
            //dd($tt[0]['name']);
            
            if ($tt[0]['slug'] != 'administrator') {
            //if ($user->hasRole('administrator')) {
                    return response()->json([
                    'msg' => 'hello, You are not allowed to access this route... ',
                ], 403);    //403, 用戶沒有權限訪問此站，伺服器收到請求但拒絕提供服務
            }            
        }

        return $next($request);
    }
}
