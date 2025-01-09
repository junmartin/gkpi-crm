<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asset_type = AssetType::get();
        return view('AssetType/index',compact('asset_type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('AssetType/add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $asset_type = [
                'name' => $request['name']
            ];
            AssetType::create($asset_type);

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('asset_type.index')->with('success','Data Asset Type Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset_type.index')->with('error','Data Asset Type Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetType $assetType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetType $assetType)
    {
        return view('AssetType/edit',compact('assetType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetType $assetType)
    {
        try {
            DB::beginTransaction();
            
            $asset_type = [
                'id'    => $assetType->id,
                'name' => $request['name']
            ];
            $post = AssetType::findOrFail($asset_type['id']);
            $post->update($asset_type);
            // Family::update($family);

            DB::commit();
            Log::info('Asset Type Data Update');
            
            return redirect()->route('asset_type.index')->with('success','Data Asset Type Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset_type.index')->with('error','Data Asset Type Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetType $assetType)
    {
        //
    }
}
