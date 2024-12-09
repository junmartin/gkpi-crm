<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jemaat;

use Illuminate\Database\Eloquent\Builder;

class JemaatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $param = $request->all();

        $distinct_city = Jemaat::distinct()->pluck('birth_place');
        
        // Gender Filter
        $where_lakilaki = -1;
        $where_perempuan = -1;
        if( !empty($param['lakilaki'])) {        
            // array_push($where_for_arr,['lakilaki']);
            $where_lakilaki = 0;
        }
        if( !empty($param['perempuan'])) {
            $where_perempuan = 1;
        }
        if($where_lakilaki == -1 && $where_perempuan == -1){
            $where_lakilaki = 0;
            $where_perempuan = 1;
        }

        // City Filter
        $where_city_arr = [];
        $city_default = [];
        foreach($distinct_city as $city){
            if( !empty($param['city_'.str_replace(' ','_',$city)])){
                array_push($where_city_arr,$city);
            }
            array_push($city_default,$city);
        }
        if(empty($where_city_arr)){
            $where_city_arr = $city_default;
        }


        $jemaats = Jemaat::where(function (Builder $query) use ($where_lakilaki, $where_perempuan) {
            $query->where('jenis_kelamin', $where_lakilaki)
                  ->orWhere('jenis_kelamin', $where_perempuan);
        })
        ->whereIn('birth_place',$where_city_arr)
        ->get();

        // dd($jemaats);

        return view('Jemaat/index',compact('jemaats','param','distinct_city'));
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
        $jemaat = Jemaat::find($id);
        return view('Jemaat/edit', compact('jemaat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request);

        $jemaat_updated = [
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
        
        $jemaat = Jemaat::findOrFail($id);
        $jemaat->update($jemaat_updated);

        return redirect()->route('jemaat.index')->with('success','Data Jemaat Berhasil Diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
