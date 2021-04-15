<?php

namespace App\Http\Controllers;

use App\Model\product;
use App\Model\Section;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $section = section::all();
        $product = product::all();
        return view('product.product', compact('section', 'product'));
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
        // dd( $request->all());
        $validateData = $request->validate(
            [
                'product_name' => 'required|max:225',
                'descreption'  => 'required',
            ],
            [
                'product_name.required' => 'يرجى ادخال المنتج',
                'descreption.required'  => 'يرجى ادخال الوصف',

            ]
        );

        // $product =product::create($request->all());

        $product = product::create([
            'product_name' => $request->product_name,
            'descreption'  => $request->descreption,
            'section_id' => $request->section_id
        ]);

        return redirect('product')->with(['add' => 'تم اضافة المنتج']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //  return $request;
        $id = Section::where('section_name', $request->section_name)->first()->id;
        $product = product::findOrFail($request->id);
        $product->update([
            'product_name' => $request->product_name,
            'descreption'  => $request->descreption,
            'section_id' => $id,
        ]);

        return redirect('product') ->with(['edit' => 'تم تعديل المنتج']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       $product = product::findOrFail($request -> id);
       $product -> delete();
       return redirect('product') ->with(['delete' => 'تم حذف المنتج']);
    }
}
