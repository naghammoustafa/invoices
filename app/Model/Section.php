<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'section_name', 'descreption', 'created_by',
    ];
}
