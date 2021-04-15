<?php

namespace App\Http\Controllers;

use App\Model\invoices_attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [

            'file_name' => 'mimes:pdf,jpeg,png,jpg',
    
            ], [
                'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            ]);
            
            $image = $request->file('file_name');
            $file_name = $image->getClientOriginalName();
    
            $attachments =  new invoices_attachment();
            $attachments->file_name = $file_name;   //الاسم لي جاي من الجدول نفسو الاسم لي جاي من الجينرال نايم
            $attachments->invoice_number = $request->invoice_number;  //رقم الفاتورة نفسو الفاليو لي بinput
            $attachments->invoice_id = $request->invoice_id;
            $attachments->Created_by = Auth::user()->name;
            $attachments->save();
               
            // move pic حطلي المرفق الجديد مع نفس ملف المرفق القديم حسب رقم الفاتورة
            $imageName = $request->file_name->getClientOriginalName();
            $request->file_name->move(public_path('Attachment/'. $request->invoice_number), $imageName);
            
            session()->flash('Add', 'تم اضافة المرفق بنجاح');
            return back();
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\invoices_attachment  $invoices_attachment
     * @return \Illuminate\Http\Response
     */
    public function show(invoices_attachment $invoices_attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\invoices_attachment  $invoices_attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(invoices_attachment $invoices_attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\invoices_attachment  $invoices_attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_attachment $invoices_attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\invoices_attachment  $invoices_attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoices_attachment $invoices_attachment)
    {
        //
    }
}
