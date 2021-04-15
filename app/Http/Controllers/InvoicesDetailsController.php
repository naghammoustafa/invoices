<?php

namespace App\Http\Controllers;

use App\Model\invoices;
use App\Model\invoices_attachment;
use App\Model\invoices_details;
use Illuminate\Http\Request;
use file;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice =invoices::all();
        return view('Invoices.invoices_details', compact('invoice'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = invoices::where('id', $id)->first();
        $details = invoices_details::where('id_Invoice', $id)->get();
        $attachment = invoices_attachment::where('invoice_id', $id)->get();

        return view('invoices.invoices_details', compact('invoice', 'details', 'attachment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoices = invoices_attachment::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }


    public function open_file($invoice_number, $file_name)
    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->file($files);
    }

    public function get_file($invoice_number, $file_name)

    {
        $contents = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->download($contents);
    }
}
