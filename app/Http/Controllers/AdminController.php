<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Blog;
use App\Role;
use App\User;

use App\Blogtag;

use App\Category;

use App\Permission;

use App\Blogcategory;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        //dd($permission);
        if ($permission != null) {
            foreach ($permission as $p) {
                if ($p->name == $path) {
                    //$right = $p->rTag ? 'true' : 'false';
                    if ($p->rTag) {
                        $hasPermission = true;
                        break;
                    }
                    break;
                }
                //$right = $hasPermission ? 'true' : 'false';
            //dd($p->name . "  " . $right . "  " . $path);
            }
        }

        //if ($hasPermission) {
            return view('welcome');
        //}
        //return view('notfound');
    }

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

    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);

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
        
        return $data->data->link;
        //dd("upload " . $data->data->link);
    }

    // upload image from editor.js
    public function uploadEditorImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);
        $file = $request->image;
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

        //return $data->data->link;
        
        return response()->json([
            "success" => 1,
            "file" => [
                "url" => $data->data->link,
            ]
        ]);    
    }

    public function deleteImage(Request $request)
    {
        $fileName = $request->session()->get('imageFile');
        $this->deleteFileFromServer($fileName, false);
        $returnString = '"' . $fileName . '" is delete... ';
        return $returnString;
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
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->delete();
    }

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

    // upload image from editor.js
    public function uploadEditorImage2(Request $request)
    {
        
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);
        
        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://172.105.193.109:8000/uploads/$picName",
            ],
        ]);
        //return $picName;
        
        //dd("hello image! ", $picName);
    }

    public function slug()
    {
        $title = 'This is a nice title changed';
        return $title;

        return Blog::create([
            'title' => $title,
            'post' => 'some post',
            'post_excerpt' => 'aead',
            'user_id' => 1,
            'metaDescription' => 'aead',
        ]);
    }

    private function uniqueSlug($title){
        $slug = Str::slug($title, '-');     //generates a URL friendly "slug" from the given string, this is => this-is
        $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
        $newCount = $count > 0 ? ++$count : '';
        return $newCount > 0 ? "$slug-$newCount" : $slug;
    }

    public function createBlog(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        
        $categories = $request->category_id;
        $tags = $request->tag_id;

        //$blogCategories = [];
        //$blogTags = [];

        //正規化, 大寫字母拆分為駱駝字串
        //$classname_arr = preg_split('/(?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z])/', $request->title);
        //$slug = strtolower(implode("-", array_filter($classname_arr)));
        $slug = $this->uniqueSlug($request->title);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => $slug,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);

        $selectedCategories = Category::whereIn('id', $categories)->get();
        $blog->categories()->sync($selectedCategories);

        $selectedTags = Tag::whereIn('id', $tags)->get();
        $blog->tags()->sync($selectedTags);
        
        response()->json(['success' => 'blog create success!'], 200);
        //return 'done';

        //begin a transaction manually and have complete control over commits
  /*
        DB::beginTransaction();
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'slug' => $slug,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);

            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $blog->id]);
            }
            Blogcategory::insert($blogCategories);

            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $blog->id]);
            }
            Blogtag::insert($blogTags);

            DB::commit();          
            return 'done';
        } catch (\Throwable $th) {
            DB::rollback();
            return 'not done';
        }
*/       
    }

    // update blog
    public function updateBlog(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);

        $categories = $request->category_id;
        $tags = $request->tag_id;
        //$blogCategories = [];
        //$blogTags = [];

        $slug = $this->uniqueSlug($request->title);

        $blog = Blog::where('id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);

        $selectedCategories = Category::whereIn('id', $categories)->get();
        $blog->categories()->sync($selectedCategories);

        $selectedTags = Tag::whereIn('id', $tags)->get();
        $blog->tags()->sync($selectedTags);

        response()->json(['success' => 'blog update success!'], 200);
        /*
        DB::beginTransaction();
        try {
            $blog = Blog::where('id', $id)->update([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);

            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $id]);
            }
            // delete all previous categories
            Blogcategory::where('blog_id', $id)->delete();
            Blogcategory::insert($blogCategories);
            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $id]);
            }
            Blogtag::where('blog_id', $id)->delete();
            Blogtag::insert($blogTags);
            DB::commit();
            return 'done';
        } catch (\Throwable $e) {

            DB::rollback();
            return 'not done';
        }
        */
    }

    public function blogdata()
    {
        return Blog::with(['tags', 'categories'])->orderBy('id', 'desc')->get();
    }

    public function deleteBlog(Request $request)
    {
        return Blog::where('id', $request->id)->delete();
    }
    
    public function singleBlogItem(Request $request, $id)
    {
        //dd($id);
        return Blog::with(['tags', 'categories'])->where('id', $id)->get();
    }

}
