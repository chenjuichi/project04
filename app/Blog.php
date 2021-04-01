<?php

namespace App;

use App\Tag;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //
    //protected $fillable = ['title', 'post', 'post_excerpt', 'slug', 'user_id', 'featuredImage', 'metaDescription'];
    protected $fillable = ['title', 'post', 'post_excerpt', 'slug', 'user_id', 'metaDescription', 'jsonData'];

    public function setSlugAttribute($title){
        $this->attributes['slug'] = $this->uniqueSlug($title);
    }

    private function uniqueSlug($title){
        $slug = Str::slug($title, '-');     //generates a URL friendly "slug" from the given string, this is => this-is
        $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
        $newCount = $count > 0 ? ++$count : '';
        return $newCount > 0 ? "$slug-$newCount" : $slug;
    }

    //public function tag(){
    public function tags(){
        return $this->belongsToMany(Tag::class, 'blogs_tags');
    }

    //public function cat(){
    public function categories(){
        return $this->belongsToMany(Category::class, 'blogs_categories');
    }    
}
