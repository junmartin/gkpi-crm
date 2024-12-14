<?php

namespace App\Http\Controllers;

use App\Models\Ibadah;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IbadahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ibadahs = Ibadah::get();
        return view('Ibadah/index',compact('ibadahs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Ibadah/add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $ibadah = [
                "ibadah_name" => $request['ibadah_name'],
                "remark" => $request['remark']
            ];

            Ibadah::create($ibadah);

            DB::commit();
            Log::info("Data ibadah saved.");
            return redirect()->route('ibadah.index')->with('success','Data Ibadah Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('ibadah.index')->with('error','Data Ibadah Gagal Masuk.');
        }   
    }

    /**
     * Display the specified resource.
     */
    public function show(Ibadah $ibadah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ibadah $ibadah)
    {
        return view('Ibadah/edit',compact('ibadah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ibadah $ibadah)
    {
        try {
            DB::beginTransaction();
            
            $ibadah = [
                'id'    => $ibadah->id,
                'ibadah_name' => $request['ibadah_name'],
                'remark' => $request['remark']
            ];
            $post = Ibadah::findOrFail($ibadah['id']);
            $post->update($ibadah);

            DB::commit();
            Log::info('Ibadah Data Update');
            
            return redirect()->route('ibadah.index')->with('success','Data Ibadah Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('ibadah.index')->with('error','Data Ibadah Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ibadah $ibadah)
    {
        //
    }
}
