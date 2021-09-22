<?php

namespace App\Model\Page;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    public $timestamps = false;
    public $table = "pages_translations";
    protected $fillable = ['title', 'slug', 'content', 'locale'];

}
