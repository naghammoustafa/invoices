<?php

namespace App\Http\Controllers;

use App\Model\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Unique;
use Symfony\Contracts\Service\Attribute\Required;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $section = Section::all();
        //  dd(  $section);
        return view('Section.section', compact('section'));
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
        // //return $request;
        //  $input = $request->all();

        //  //تأكد من السجل ان كان موجود
        //  $b_exists = Section::where('section_name', '=', $input['section_name'])->exists();

        //  if ($b_exists) 
        //  {
        //      session()->flash('error', 'القسم مسجل مسبقا');
        //      return redirect('/section');
        //  } 
        //  else
        //   {
        //$validated = $request->validated();

        $validateData = $request->validate(
            [
                'section_name' => 'required|unique:sections|max:255',
                'descreption'  => 'required',
            ],
            [
                'section_name.required' => 'يرجى ادخال القسم',
                'section_name.unique' =>  'الاسم موجود مسبقا',
                'descreption.required'  => 'يرجى ادخال الوصف',

            ]
        );

        Section::create([
            'section_name' => $request->section_name,
            'descreption'  => $request->descreption,
            'created_by'  => (Auth::user()->name),

        ]);
        session()->flash('add', 'تم اضافة القسم');
        return redirect('/section');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //return $request;
        $id = $request->id;
        $this->validate(
            $request,
            [
                'section_name' => 'required|unique:sections|max:255' . $id,  //اذا ماعدلت الاسم مايطلع عندي غلط 
                'descreption'  => 'required',
            ],

            [
                'section_name.required' => 'يرجى ادخال القسم',
                'section_name.unique' => 'الاسم موجود مسبقا',
                'descreption.required'  => 'يرجى ادخال الوصف',

            ]
        );

        $section = Section::find($id);

        $section->update([
            'section_name' => $request->section_name,
            'descreption' => $request->descreption,
        ]);
        session()->flash('edit', 'تم تعديل القسم');
        return redirect('/section');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Section  $section
     * @return \Illuminate\Http\Response
     */



    public function destroy(Request $request)
    {
        $id = $request->id;
        //   dd($id );
        Section::find($id)->delete();
        session()->flash('delete', 'تم حذف القسم بنجاح');
        return redirect('/section');
    }
}
