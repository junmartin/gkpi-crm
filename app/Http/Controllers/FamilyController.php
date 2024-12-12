<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $families = Family::get();
        return view('Family/index',compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Family/add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $family = [
                'family_name' => $request['family_name']
            ];
            Family::create($family);

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('family.index')->with('success','Data Family Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('family.index')->with('error','Data Family Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Family $family)
    {
        echo 'family edit';
        // $family_data = Family::find($family);
        // return view('Family/edit',compact('family_data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Family $family)
    {
        return view('Family/edit',compact('family'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Family $family)
    {
        try {
            DB::beginTransaction();
            
            $family = [
                'id'    => $family->id,
                'family_name' => $request['family_name']
            ];
            $post = Family::findOrFail($family['id']);
            $post->update($family);
            // Family::update($family);

            DB::commit();
            Log::info('Family Data Update');
            
            return redirect()->route('family.index')->with('success','Data Family Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('family.index')->with('error','Data Family Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Family $family)
    {
        //
    }
}
