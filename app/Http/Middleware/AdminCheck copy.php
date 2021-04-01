<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

use Illuminate\Support\Facades\Auth;
//use App\Role;
use App\Traits\HasRolesAndPermissions;
//use Illuminate\Database\Eloquent\Model;

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
    //public function handle($request, Closure $next, $role, $permission = null)
    public function handle($request, Closure $next)
    {
        //Log::info('hello, inside middleware...');

        //if (($request->path() == '/') || ($request->path() == 'sanctum/csrf-cookie') || ($request->path() == 'login')) {
        //    return $next($request);
        //}

        //$user = User::find(1);
        //if (Auth::check()) {
            //    return response()->json([
            //        'msg' => 'You are not allowed to access this route... ',
            //        'url' => $request->path()
            //    ], 403);    //403, 用戶沒有權限訪問此站，伺服器收到請求但拒絕提供服務
            //}
        //}
            ////hi, cal this function
            //$obj = new User();
            //$id = Auth::id();
            //$flight = User::where('id', $id)->isActivated;
            //$user =$obj->where('id', $id)->hasRole('administrator');
            //$obj = User::hydrate( $user->toArray() );
            //$model = new User($user);

            //$obj = app(User::class, ['id' => $user->id,
            //                        'name' => $user->name,
            //                        'email' => $user->email,
            //                        'password' => $user->password]);

            //if (!$user || $user instanceof HasRolesAndPermissions && !$user->hasRole()) {
            //dd(method_exists($user,'hasRole'));
            //dd($model->namehasRole('administrator'));
            //dd($request->user()->hasRoleName());
        //}
        //if ($user instanceof HasRolesAndPermissions && !$user->hasRole('administrator')) {
        
            /*
        if (!$user) {
            return response()->json([
                'msg' => 'Hello, You are not allowed to access this route... ',
            ], 403);
        }
        */
        //}
        ////


        //$user = new myUser();
        //$ta = new myTa();
        //$id = Auth::id();
        //$userRole = $user->where('id', $id)->first();
        //$userWithRole = $user->where('id', $id)->first();
        //$userWithRole = $userRole->roles;

        //if ($userRole->roles()->where('name', 'Administrator')->exists()) {
        //}

        //if ($userWithRole[0]->slug != 'administrator') {
        //    return response()->json([
        //        'msg' => 'hello, You are not allowed to access this route... ',
        //    ], 403);    //403, 用戶沒有權限訪問此站，伺服器收到請求但拒絕提供服務
        //}

//** */
        if(Auth::check()) { //check if the user is logged in or not
            $user = Auth::user();
            //if ($user instanceof HasRolesAndPermissions && $user->hasRole('administrator')) {
                $tt = $user->roles()->get();
                dd($tt[0]['name']);
            //} else {
                //dd("hi is not");
           //}
        }

        return $next($request);
    }
}
