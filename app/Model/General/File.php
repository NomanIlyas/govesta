<?php

namespace App\Model\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'original_name', 'type', 'url', 'meta', 'extension'
    ];

    protected $appends = ['src'];

    public function getSrcAttribute()
    {
        $src = [];
        if ($this->url) {
            $src = [
                'original' => $this->url,
                'x' => $this->url,
                'x2' => $this->url
            ];
        } else {
            $folder = 'uploads/' . $this->name . '/';
            $s3 = Storage::disk('s3');
            $src = [
                'original' => $s3->url($folder . 'original.' . $this->extension),
                'x' => $s3->url($folder . 'x.jpg'),
                'x2' => $s3->url($folder . '2x.jpg')
            ];
        }
        return $src;
    }
}
