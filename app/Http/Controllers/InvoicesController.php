<?php

namespace App\Http\Controllers;

use App\Model\invoices;
use App\Model\invoices_details;
use App\Model\invoices_attachment;
use App\Model\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exports\InvoicesExport;
use App\Notifications\Addinvoice;
use App\User;
//use Illuminate\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;


class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = invoices::all();
        return view('Invoices.invoices', compact('invoice'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $section = Section::all();
        return view('Invoices.invoices_add', compact('section'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = invoices::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoices_attachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachment/' . $invoice_number), $imageName);
        }

        // ارسال للايميل
        //$user = User::first();
        //$user-> notify(new Addinvoice($invoice_id));  //'طريقة 2
        // Notification::send($user, new Addinvoice($invoice_id));

        //اشعار قبل الارسال
        // $user = User::get();  ارسال الاشعارات لجميع المستخدمين
        $user = User::find(Auth::user()->id);   //ارسال اشعارات للمستخدم يلي ضاف بس
        $invoices = invoices::latest()->first();
        Notification::send($user, new \App\Notifications\addinvoicesnew($invoices));
        // $user->notify(new \App\Notifications\addinvoicesnew($invoices));

        // event(new MyEventClass('hello world'));

        session()->flash('add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { //جبت الاقسام لان الفاتورة بتضمن اقسام
        $invoices = invoices::where('id', $id)->first();
        $section = Section::all();
        return view('invoices.invoice_edit', compact('section', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $invoice_id)
    {
        // $invoices = invoices::findOrFail($request->invoice_id);  //هي بس اذا في بارامتر الريكوست
        // $invoices->update([
        //     'invoice_number' => $request->invoice_number,
        //     'invoice_Date' => $request->invoice_Date,
        //     'Due_date' => $request->Due_date,
        //     'product' => $request->product,
        //     'section_id' => $request->Section,
        //     'Amount_collection' => $request->Amount_collection,
        //     'Amount_Commission' => $request->Amount_Commission,
        //     'Discount' => $request->Discount,
        //     'Value_VAT' => $request->Value_VAT,
        //     'Rate_VAT' => $request->Rate_VAT,
        //     'Total' => $request->Total,
        //     'note' => $request->note,
        // ]);
        $invoices = invoices::find($invoice_id);
        $invoices->update($request->all());

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = invoices::where('id', $invoice_id)->first();
        $details = invoices_attachment::where('invoice_id', $invoice_id)->first();
        $id_page = $request->id_page;

        if (!$id_page == 2) {  //اذا ماكان جاي من زر الارشيف حذفو نهائي

            if (!empty($details->invoice_number)) {
                //  Storage::disk('public_uploads') ->delete($details -> invoice_number.'/'.$details ->file_name);  //حذف مرفق واحد
                Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);  //حذف الصورة مع المجلد
            }

            $invoice->forceDelete();   //حذف نهائي
            // $invoice->Delete();   //حذف بس من الجدول بس بتضل بقاعدة البيانات
            session()->flash('delet');
            return redirect('invoices');
        } else {     //اذا جاي من زر الارشيف لاتحذف بشكل نهائي
            $invoice->Delete();
            session()->flash('archiv');
            return redirect('invoices');
        } //بحسن استغني عن الشرط واعمل تلبع منفصل
    }

    public function getProduct($id)
    //مشان يجبلي المنتجات حسب القسم بحيث اذا كان رقم القسم نقسو الرقم لي جاي عطيني اسم المنتج ورقمو
    {
        $product = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($product);
    }

    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'value_status' => 3,
                'status' => $request->status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }

    public function invoice_paid()
    {
        $invoice = invoices::where('value_status', 1)->get();
        return view('Invoices.invoices_paid', compact('invoice'));
    }

    public function invoice_unpaid()
    {
        $invoice = invoices::where('value_status', 3)->get();
        return view('invoices.invoices_unpaid', compact('invoice'));
    }

    public function invoice_partial()
    {
        $invoice = invoices::where('value_status', 2)->get();
        return view('invoices.invoices_Partial', compact('invoice'));
    }

    public function print_invoice($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.Print_invoice', compact('invoices'));
    }

    public function export()
    {

        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function MarkAsRead_all(Request $request)
    {
        $userUnreadNotification = Auth()->user()->unreadNotifications;

        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }


    public function unreadNotifications_count()

    {
        return auth()->user()->unreadNotifications->count();
    }

    public function unreadNotifications()

    {
        foreach (auth()->user()->unreadNotifications as $notification) {

            return $notification->data['title'];
        }
    }
}
