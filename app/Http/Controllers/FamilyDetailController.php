<?php

namespace App\Http\Controllers;

use App\Models\FamilyDetail;
use App\Models\Jemaat;
use App\Models\Family;
use Illuminate\Http\Request;

class FamilyDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FamilyDetail $familyDetail, $jemaat_id)
    {
        $jemaat = Jemaat::find($jemaat_id);
        $family = Family::get();
        return view("Family/assign",compact('jemaat','family'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FamilyDetail $familyDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FamilyDetail $familyDetail)
    {
        // dd($request);   
        // dd($familyDetail);
        FamilyDetail::upsert([
            [
                'jemaat_id' => $request['jemaat_id'], 
                'family_id' => $request['families'], 
                'role' => $request['role']
            ]
        ],uniqueBy: ['jemaat_id','family_id'],update:['family_id','role']);

        return redirect()->route('jemaat.index')->with('success','Data Jemaat Berhasil Diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FamilyDetail $familyDetail)
    {
        //
    }
}
