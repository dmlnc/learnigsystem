<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademyResource;
use App\Http\Resources\FacultyResource;
use App\Models\Academy;
use Illuminate\Http\Request;

class AcademyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = auth()->user();

        return AcademyResource::collection(Academy::where('company_id', $user->company_id)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
       
        $validatedData['company_id'] = auth()->user()->company_id;

        $academy = Academy::create($validatedData);

        return new AcademyResource($academy);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Academy $academy)
    {
        return new AcademyResource($academy);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Academy $academy)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $academy->update($validatedData);

        return new AcademyResource($academy);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Academy $academy)
    {
        $academy->delete();

        return response()->json(null, 204);
    }
}
