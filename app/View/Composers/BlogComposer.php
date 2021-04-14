<?php

namespace App\View\Composers;

use App\Category;
use Illuminate\View\View;

//use Illuminate\Support\Facades\View;

//use Illuminate\Database\Eloquent\Model;

//class BlogComposer extends Model
class BlogComposer
{
    //public function __construct()
    //{
    //    return "construct function was initialized.";
    //}

    public function compose(View $view)
    {
        $cat = Category::select('id', 'categoryName')->get(['id', 'categoryName']);
        $view->with('cat', $cat);
    }      
}
