<?php

namespace App\Http\Controllers;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Faq;
//use App\Http\Requests\FaqRequest;

class FaqController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      // code ...
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('faq.settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentationRequest $request)
    {
      $documentation = Documentation::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Documentation was successfully added!',
            'entity' => $documentation
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('faq.settings.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      return view('documentations.edit')
            ->with('documentation', Documentation::find($id))
            ->with('categories', Category::where('entity','documentation')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentationRequest $request)
    {
        $documentation = Documentation::find($request->id);

        if($documentation){
          $documentation->name = $request->name;
          $documentation->text = $request->text;
          $documentation->category_id = $request->category_id;
          $documentation->save();

          return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::find($id);
        $deleteFileService = new DeleteFileService($documentation);

        foreach ($documentation->files as $file) {
          if(! $deleteFileService->delete($file->id))
          return response()->json(
            [
              'success' => false,
              'message' => 'Error delete file '. $file->name
            ]
          );
        }

        $faq->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
      //code ...
    }

}
