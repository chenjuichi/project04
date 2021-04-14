<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;                //2021-02-25 add, for sanctum
use Illuminate\Support\Facades\Auth;        //2021-02-25 add, for sanctum

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

//2020-02-25 add, past the following from api.php, and modify it for sanctum
//說明: 1.將api.php移入web.php, 省去在/api的prefix(/api/user), 直接使用/user
//     2./user 這個router url, 會使用到'auth'這個middleware
Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});


//2021-02-25 mark, 2021-04-06 enable BlogController
//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/blog', 'BlogController@index')->name('blog_home');
Route::get('/blog/{slug}', 'BlogController@blogSingle')->name('blog_slug');
Route::get('/category/{categoryName}/{id}', 'BlogController@categoryIndex')->name('blog_cat');
Route::get('/tag/{tagName}/{id}', 'BlogController@tagIndex')->name('blog_tag');
Route::get('/blogs', 'BlogController@allBlogs')->name('allblogs');
Route::get('/search', 'BlogController@search')->name('search');

//2020-02-25 add, for auth
Route::group(['middleware' => ['auth']], function () {

    //});
    //Route::middleware('auth')->group(function () {
    Route::resource('/api/admin/users', 'Admin\UsersController', ['except' => ['show', 'create', 'store']]);
    Route::resource('/api/roles', 'Role\RolesController');
    Route::get('/api/customer/roles/{id}/{roleId?}', [
        'as' => 'roles.show',
        'uses' => 'Role\CustomerRolesController@show',
    ]);

    Route::get('/api/customer/admin/users', 'Admin\CustomerUsersController@getAllUsersAndUserRoles');
    Route::post('/api/customer/rolesList', 'Admin\CustomerUsersController@rolesList');
    Route::post('/api/customer/attatchedRole', 'Admin\CustomerUsersController@attatchedRole');
    // tag
    Route::post('/api/create_tag', 'AdminController@addTag');
    Route::get('/api/get_tags', 'AdminController@getTag');
    Route::post('/api/edit_tag', 'AdminController@editTag');
    Route::post('/api/delete_tag', 'AdminController@deleteTag');

    Route::post('/api/upload', 'AdminController@upload');
    Route::post('/api/delete_image', 'AdminController@deleteImage');
    //category
    Route::post('/api/create_category', 'AdminController@addCategory');
    Route::get('/api/get_category', 'AdminController@getCategory');
    Route::post('/api/edit_category', 'AdminController@editCategory');
    Route::post('/api/delete_category', 'AdminController@deleteCategory');
    //user
    Route::post('/api/create_user', 'AdminController@createUser');
    Route::get('/api/get_users', 'AdminController@getUsers');
    Route::post('/api/edit_user', 'AdminController@editUser');
    //permission
    Route::post('/api/create_permission', 'AdminController@addPermission');
    Route::get('/api/get_permissions', 'AdminController@getPermissions');
    Route::post('/api/edit_permission', 'AdminController@editPermission');
    Route::post('/api/assign_permissions', 'AdminController@assignPermission');
    Route::post('/api/select_permissions', 'AdminController@selectPermissions');
    Route::post('/api/check_permission_curds', 'AdminController@checkForPermission');
    //
    Route::post('/api/postPicture', 'AdminController@postPicture');
    // blog
    Route::post('/api/create_blog', 'AdminController@createBlog');
    Route::get('/api/blogsdata', 'AdminController@blogdata'); // get the blogs item
    Route::post('/api/delete_blog', 'AdminController@deleteBlog');
    Route::get('/api/blog_single/{id}', 'AdminController@singleBlogItem');
    Route::post('/api/update_blog/{id}', 'AdminController@updateBlog');

    Route::post('/hello1/hello140', 'AdminController@uploadEditorImage2');
    Route::get('slug','AdminController@slug');
});

Route::group(['middleware' => ['auth', 'admin.check']], function () {
    //  roles routes
    Route::post('/api/create_role', 'AdminController@addRole');
    Route::get('/api/get_roles', 'AdminController@getRoles');

    Route::post('/api/get_roleWithPermissions', 'AdminController@getRoleWithPermissions');
    
    Route::post('/api/edit_role', 'AdminController@editRole');
    Route::post('/api/delete_role', 'AdminController@deleteRole');
    Route::post('/api/assign_roles', 'AdminController@assignRole');
});



//2020-11-12 modify
//Route::get('/', function () {
//    return view('welcome');
//});
//
//參考: https://stackoverflow.com/questions/54434534/how-to-combine-laravel-and-vue-routes
//方法1:
//Route::get('/{any?}', function () {
//    return view('welcome');
//})->where('any', '^(?!api\/)[\/\w\.-]*');
//方法2:
Route::get('/{any?}', 'AdminController@index')->where('any', '^(?!api\/)[\/\w\.-]*');
