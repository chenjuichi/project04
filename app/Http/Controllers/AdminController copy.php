<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Role;
use App\User;
use App\Category;

use App\Permission;

use GuzzleHttp\Client;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\GuzzleException;

class AdminController extends Controller
{
    public function test(Request $request)
    {
        //return response()->json([
        //    'msg' => 'hello, test is ok....'
        //]);

        return 'it is ok!';
    }

    public function index(Request $request)
    {

        // first check if you are loggedin and admin user ...
        //return Auth::check();
        //尚未登入, 轉向登入/註冊路徑
        if (!Auth::check() && $request->path() != 'hello') {
            return redirect('/hello');
        }

        if (!Auth::check() && $request->path() == 'hello') {
            return view('welcome');
        }

        //尚未登入, 轉向登入/註冊路徑        
        ///if (!Auth::check()) {
        ///    return view('welcome');
        ///}

        // you are already logged in... so check for if you are an admin user..
        //已登入, $user=登入使用者名稱, 並確認式否為admin
        $user = Auth::user();
        $path = $request->path();
        //if ($user->userType == 'User') {
        //    return redirect('/login');
        //}
        //if ($request->path() == 'login') {
        //    return redirect('/');
        //}
        //確認$user的身分
        //return $this->checkForPermission($user, $path);

        $permission = json_decode($user->roles[0]->curds);

        //dd("current path:" . "  " . $path);
        //        
        $hasPermission = false;
        foreach ($permission as $p) {
            if ($p->name == $path) {
                //$right = $p->rTag ? 'true' : 'false';
                if ($p->rTag) {
                    $hasPermission = true;
                }
                break;
            }
            //$right = $hasPermission ? 'true' : 'false';
            //dd($p->name . "  " . $right . "  " . $path);
        }

        return view('welcome');
        //
        //if ($hasPermission) {
        //    return view('welcome');
        //} else {
        //    return view('notfound');
        //}
        
    }

    /*
    public function checkForPermission($user, $path)
    //public function checkForPermission(Request $request)
    {
        //$user = Auth::user();
        $permission = json_decode($user->roles[0]->curds);
        $hasPermission = false;
        //$path = $request->path();
        //$path = URL::current();
        //dd($permission[0]->name . "  " . $permission[0]->rTag . "  " . $path);
        
        //if (!$permission) {
        //   return view('welcome');
        //}
        //dd("path: " . $request->path);

        foreach ($permission as $p) {
        ///    dd($p->name . "  " . $p->rTag);
            if ($p->name == $path) {
                if ($p->rTag) {
                    $hasPermission = true;
                }
            }
        }
        dd("$hasPermission = " . $hasPermission);
        //if ($hasPermission) {
        //    return view('welcome');
        //}

        //return view('welcome');
        //return view('notfound');        
    }
    */
    public function addTag(Request $request)
    {
        // validate request
        $this->validate($request, [
            'tagName' => 'required',
        ]);
        return Tag::create([
            'tagName' => $request->tagName,
        ]);
    }

    public function editTag(Request $request)
    {
        // validate request
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName,
        ]);
    }

    public function deleteTag(Request $request)
    {
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    public function getTag()
    {
        return Tag::orderBy('id', 'desc')->get();
    }


    public function imgurOKupload(Request $request)
    {
        $validatedData = $request->validate([
            //'title' => 'required',
            'file' => 'required'
        ]);

        //$title = $request->get('title');
        $file = $request->file('file');
        //$file = $request->file;

        $path = public_path();
        $file_path = $path . '/upload/' . $file->getClientOriginalName();

        $file->move($path . '/upload', $file->getClientOriginalName());

        $client_id = 'e6088d4d7383c59';
        $client = new \GuzzleHttp\Client();

        $gResponse = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Client-ID ' . $client_id,
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'title' => '== welcome ==',
                'description' => 'laravel application upload image into imgur',
                'image' => base64_encode(file_get_contents($file_path))
            ],
        ]);

        $data = json_decode($gResponse->getBody()->getContents());
        //$imgur_Link = $data;
        //dd($data->data->link);

        // Then we send our data to the view
        //return view('welcome')
        //    ->with('imgur_link', $imgur_Link);

        return view('welcome')->with('data', $data->data->link);
        //    ->with('imgur_link', $imgur_Link);

        //return response()->json([
        //    //'success' => 'You have successfully uploaded "' . $file_name . '"',
        //    //'success' => 'You have successfully uploaded "' . $fileNameToStore . '"',
        //   'imgurLink' => $imgurLink,
        //]);

        //return back();
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);

        //$file = $request->file('file');
        $file = $request->file;

        $path = public_path();

        $file->move($path . '/upload', $file->getClientOriginalName());

        $file_path = $path . '/upload/' . $file->getClientOriginalName();
        $client_id = 'e6088d4d7383c59';
        $client = new \GuzzleHttp\Client();
        $gResponse = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Client-ID ' . $client_id,
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'title' => '== welcome ==',
                'description' => 'laravel application upload image into imgur',
                'image' => base64_encode(file_get_contents($file_path))
            ],
        ]);
        $data = json_decode($gResponse->getBody()->getContents());

        $request->session()->put('imageFile', $file->getClientOriginalName());
        //$request->session()->put('imgurURL', $data->data->link);
        //dd($data->data->link);
        return $data->data->link;

        //return response()->json([
        //    'imageFile' => $file->getClientOriginalName(),
        //    'imgurURL' => $data->data->link,
        //]);        
    }

    // upload image from editor.js
    public function uploadEditorImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);
        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('upload'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://fullstack.localhost/upload/$picName",
            ],
        ]);
        return $picName;
    }

    public function deleteImage(Request $request)
    {
        //$fileName = $request->imageName;
        $fileName = $request->session()->get('imageFile');
        $this->deleteFileFromServer($fileName, false);
        $returnString = '"' . $fileName . '" is delete... ';
        return $returnString;
        //return 'done';
    }

    public function addCategory(Request $request)
    {
        // validate request
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function getCategory()
    {
        return Category::orderBy('id', 'desc')->get();
    }

    public function editCategory(Request $request)
    {
        // validate request
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function deleteCategory(Request $request)
    {
        // first delete the original file from the server
        ////$this->deleteFileFromServer($request->iconImage);
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->delete();
    }

    //public function deleteFileFromServer($fileName, $hasFullPath = false)
    //{
    //    if (!$hasFullPath) {
    //        $filePath = public_path() . '/upload/' . $fileName;
    //    }
    //    if (file_exists($filePath)) {
    //        @unlink($filePath);
    //    }
    //
    //    return;
    //}

    //$classnames = Array("MyFAQ", "myFAQ", "FAQList","myClassName", "youHaveAProblem", "MyClassNNName");
    
    public function addRole(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //正規化, 大寫字母拆分為駱駝字串
        $classname_arr = preg_split('/(?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z])/', $request->name);
        $slug = strtolower(implode("-", array_filter($classname_arr)));

        return Role::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);
    }

    public function editRole(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //正規化, 大寫字母拆分為駱駝字串
        $classname_arr = preg_split('/(?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z])/', $request->name);
        $slug = strtolower(implode("-", array_filter($classname_arr)));

        return Role::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function getRoleWithPermissions(Request $request)
    {
        $r = Role::with('permissions')->where('name', $request->name)->get();
        $rr=json_encode($r[0]['curds']);
        $r2 = json_decode($rr);
         
        //dd("getRoleWithPermissions " . $r2 . " " . $request->name);
        
        return response()->json(
            [
                "id" => $r[0]['id'],
                "curds" => $r2,
                "permissions" => $r[0]['permissions'],
            ]
        );
        
    }

    public function deleteRole(Request $request)
    {
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->delete();
    }

    public function assignRole(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'curds' => $request->permission,
        ]);
    }

    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug',$permissions)->get();
    }

    public function selectPermissions(Request $request)
    {        
        $role = Role::where('name', $request->role)->first();
    
        $permissions = Permission::whereIn('name', $request->permissions)->get();

        return $role->permissions()->sync($permissions);
    }

    public function addPermission(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //正規化, 大寫字母拆分為駱駝字串
        $classname_arr = preg_split('/(?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z])/', $request->name);
        $slug = strtolower(implode("-", array_filter($classname_arr)));

        //dd("add permission", $request->name, $request->description);

        return Permission::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function editPermission(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //正規化, 大寫字母拆分為駱駝字串
        $classname_arr = preg_split('/(?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z])/', $request->name);
        $slug = strtolower(implode("-", array_filter($classname_arr)));

        return Permission::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);
    }

    public function deletePermission(Request $request)
    {
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Permission::where('id', $request->id)->delete();
    }

    public function createUser(Request $request)
    {
        // validate request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',  //email必須在users table是唯一的
            'password' => 'bail|required|min:8|max:30',     //assign the bail rule => if the min rule , the max rule will not be checked. 
            'role_id' => 'required',
        ]);

        $password = bcrypt($request->password);

        //$currentRole = Role::where('id', $request->role_id)->first();

        $user = User::create([
            'name' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            //'userType' => $currentRole->name,
        ]);

        //$currentRole = Role::where('id', $request->role_id)->first();
        $user->roles()->sync([$request->role_id]);

        return $user;
    }

    public function editUser(Request $request)
    {
        // validate request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id", //email may changed, but nedd unique
            'password' => 'bail|required|min:8|max:30',
            'role_id' => 'required',
        ]);

        $data = [
            'name' => $request->fullName,
            'email' => $request->email,
            //'userType' => $request->userType,
        ];

        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }

        $user = User::where('id', $request->id)->update($data);

        $userUpdate = User::where('id', $request->id)->first();

        $userUpdate->roles()->detach();

        $userUpdate->roles()->sync([$request->role_id]);

        return $userUpdate;
    }

    public function getUsers()
    {
        //return User::get();
        //return User::where('userType', '==', 'Administrator')->get();
        return User::with('roles')->get();
    }
}
