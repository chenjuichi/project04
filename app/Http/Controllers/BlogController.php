<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Category;   
use Illuminate\View\View;   //2021-04-08 add, for View Composer
use Illuminate\Http\Request;


class BlogController extends Controller
{
    public function index(Request $request){
        $categories = Category::select('id', 'categoryName')->get();
        //$blogs = Blog::orderBy('id', 'desc')->with(['categories', 'user'])->limit(6)->get(['id', 'title', 'post_excerpt', 'slug', 'user_id', 'featuredImage']);
        $blogs = Blog::orderBy('id', 'desc')->with(['categories', 'user'])->limit(6)->get(['id', 'title', 'post_excerpt', 'slug', 'user_id']);

        return view('home')->with(['categories' => $categories, 'blogs' => $blogs]);
    }

    public function blogSingle(Request $request, $slug) {
        //$categories = Category::select('id', 'categoryName')->get();
        $blog = Blog::where('slug', $slug)->with(['categories','tags' , 'user'])->first(['id', 'title', 'user_id', 'featuredImage', 'post']);
        $category_ids =[];
        foreach ($blog->categories as $cat) {
            array_push($category_ids, $cat->id);
        }
        
        //get related article
        $relatedBlogs = Blog::with('user')->where('id', '!=', $blog->id)->whereHas('categories', function($q) use($category_ids) {
            $q->whereIn('category_id', $category_ids);
        })->limit(5)->orderBy('id', 'desc')->get(['id', 'title', 'slug', 'user_id']);

        //return view('blogsingle')->with(['categories' => $categories, 'blog' => $blog]);
        return view('blogsingle')->with(['blog' => $blog, 'relatedBlogs' => $relatedBlogs]);
    }

    public function categoryIndex(Request $request, $categoryName, $id) {
        $blogs = Blog::with('user')->whereHas('categories', function($q) use($id) {
            $q->where('category_id', $id);
        })->orderBy('id', 'desc')->select('id', 'title', 'slug', 'user_id')->paginate(1);

        //return $blogs;
        return view('category')->with(['categoryName' => $categoryName, 'blogs' => $blogs]);
    }

    public function tagIndex(Request $request, $tagName, $id) {
        $blogs = Blog::with('user')->whereHas('tags', function($q) use($id) {
            $q->where('tag_id', $id);
        })->orderBy('id', 'desc')->select('id', 'title', 'slug', 'user_id')->paginate(1);

        //return $blogs;
        return view('tag')->with(['tagName' => $tagName, 'blogs' => $blogs]);
    }

    public function allBlogs(Request $request) {
        $blogs = Blog::orderBy('id', 'desc')->with(['categories', 'user'])->select('id', 'title', 'post_excerpt', 'slug', 'user_id')->paginate(1);
        return view('blogs')->with(['blogs' => $blogs]);
    }

    public function search(Request $request) {
        $str = $request->str;
        $blogs = Blog::orderBy('id', 'desc')->with(['categories', 'user', 'tags'])->select('id', 'title', 'post_excerpt', 'slug', 'user_id', 'featuredImage');
        $blogs->when($str != '', function($q) use($str) {
            $q->where('title', 'LIKE', "%{ $str }%")
            ->orWhereHas('categories', function ($q) use ($str) {
                $q->where('categoryName', $str);
            })
            ->orWhereHas('tags', function ($q) use ($str) {
                $q->where('tagName', $str);
            });
        });
        //if ($str) {
        //    $blogs->where('title', 'LIKE', "%{ $str }%")
        //    ->orWhereHas('categories', function($q) use($str) {
        //        $q->where('categoryName', $str);
        //    })
        //    ->orWhereHas('tags', function($q) use($str) {
        //        $q->where('tagName', $str);
        //    });
        //}
        return $blogs->get();
    }
}
