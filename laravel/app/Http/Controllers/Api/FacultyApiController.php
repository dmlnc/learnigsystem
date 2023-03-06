<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyResource;
use App\Models\Academy;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Academy $academy)
    {
        return response([
            'data' => new FacultyResource($academy->faculties),
            'meta' => [
                'name'  => $academy->name,
            ],
        ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Academy $academy)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
       
        $validatedData['academy_id'] = $academy->id;

        $faculty = Faculty::create($validatedData);

        return new FacultyResource($faculty);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Academy $academy, Faculty $faculty)
    {
        return new FacultyResource($faculty);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  Academy $academy, Faculty $faculty)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $faculty->update($validatedData);

        return new FacultyResource($faculty);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Academy $academy, Faculty $faculty)
    {
        $faculty->delete();

        return response()->json(null, 204);
    }
}
