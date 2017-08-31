<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public function getImageUrlAttribute($value)
    {
            if($value === null){
                return 'http://127.0.0.1:8000/images/No_image_available.png';
            }else{
                return asset('storage/'.substr($value,7));
            }


    }


}
