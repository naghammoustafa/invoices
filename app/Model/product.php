<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{

   // protected $reguard =[];

    protected $fillable = [
        'product_name', 'descreption', 'section_id',
    ];

    public function section()
    {
        return $this->belongsTo('App\Model\section');
    }
}
