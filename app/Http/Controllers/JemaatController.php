<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jemaat;

class JemaatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jemaats = Jemaat::get();
        return view('Jemaat/index',compact('jemaats'));
        // return view('jemaat',compact('jemaats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Jemaat/add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);

        $jemaat = [
            "name" => $request['name'],
            "jenis_kelamin" => $request['jenis_kelamin'],
            "address" => $request['address'],
            "birth_place" => $request['birth_place'],
            "birth_date" => $request['birth_date'],
            "mobile_no" => $request['mobile_no'],
            "email" => $request['email'],
            "marital_status" => $request['marital_status'],
            "marriage_date" => $request['marriage_date'],
            "spouse_name" => $request['spouse_name'],
            "member_type" => $request['member_type'],
            "baptise_status" => $request['baptise_status'],
            "previous_church" => $request['previous_church'],
            "remark" => $request['remark']
        ];
        Jemaat::create($jemaat);
        return redirect()->route('jemaat.index')->with('success','Data Jemaat Berhasil Masuk.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
