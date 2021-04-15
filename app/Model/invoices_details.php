<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class invoices_details extends Model
{
    protected $fillable = [
        'id_Invoice',
        'invoice_number',
        'product',
        'Section',
        'status',
        'value_status',
        'not',
        'user',
        'Payment_Date'
    ];
}
