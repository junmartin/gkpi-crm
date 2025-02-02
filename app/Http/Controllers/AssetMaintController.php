<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssetMaint;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AssetMaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maints = AssetMaint::with('asset')->get();
        return view('AssetMaint/index',compact('maints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $asset = Asset::get();
        return view('AssetMaint/add',compact('asset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();
            
            $asset = [                
                'asset_id' => $request['asset_id'],
                'maint_type' => $request['maint_type'],
                'maint_date' => $request['maint_date'],
                'next_maint_date' => $request['next_maint_date'],
                'maint_title' => $request['maint_title'],
                'desc' => $request['desc'],
                'maint_fee' => $request['maint_fee'],
                'remark' => $request['remark'],
                'create_by' => auth()->id()
            ];
            AssetMaint::create($asset);

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('asset_maint.index')->with('success','Data Asset Maintenance Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset_maint.index')->with('error','Data Asset Maintenance Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetMaint $assetMaint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetMaint $assetMaint)
    {
        $asset = Asset::get();
        return view('AssetMaint/edit', compact('assetMaint','asset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetMaint $assetMaint)
    {
        try {
            DB::beginTransaction();
            
            $asset = [                
                'asset_id' => $request['asset_id'],
                'maint_type' => $request['maint_type'],
                'maint_date' => $request['maint_date'],
                'next_maint_date' => $request['next_maint_date'],
                'maint_title' => $request['maint_title'],
                'desc' => $request['desc'],
                'maint_fee' => $request['maint_fee'],
                'remark' => $request['remark'],
                'update_by' => auth()->id()
            ];
            
            $post = AssetMaint::findOrFail($assetMaint['id']);
            $post->update($asset);

            DB::commit();
            Log::info('Data updated');
            
            return redirect()->route('asset_maint.index')->with('success','Data Asset Maintenance Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset_maint.index')->with('error','Data Asset Maintenance Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetMaint $assetMaint)
    {
        //
    }
}
