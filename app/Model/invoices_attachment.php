<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class invoices_attachment extends Model
{
    protected $fillable = [
        'file_name', 'invoice_number', 'Created_by', 'invoice_id'
    ];

}
