<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class invoices extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number',
        'invoice_Date',
        'Due_date',
        'product',
        'section_id',
        'Amount_collection',
        'Amount_Commission',
        'Discount',
        'Value_VAT',
        'Rate_VAT',
        'Total',
        'status',
        'value_status',
        'note',
        'Payment_Date',
    ];

    protected $dates = ['deleted_at'];

    public function Section()
    {
        return $this->belongsTo('App\Model\section');
    }
}
