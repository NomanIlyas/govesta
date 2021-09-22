<?php

namespace App\Model\Page;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Translatable;

    public $translationModel = 'App\Model\Page\PageTranslation';

    public $translatedAttributes = ['title', 'slug', 'content'];

    protected $fillable = ['id'];
}
