<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssetMaint;
use App\Models\AssetMaintenancePhoto;
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
    // public function create(Request $request)
    // {

    //     $asset = Asset::get();
    //     return view('AssetMaint/add',compact('asset'));
    // }

    public function create($asset_id = null)
    {
        $isAssetGiven = !is_null($asset_id);
        $asset_name = "";
        if($isAssetGiven){
            $asset = Asset::find($asset_id);
            $asset_name = $asset ? $asset->name : '';
        }

        
        $asset = Asset::get();
        return view('AssetMaint/add',compact('asset_id','asset_name','isAssetGiven','asset'));
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
                'vendor_name' => $request['vendor_name'],
                'vendor_address' => $request['vendor_address'],
                'vendor_contact' => $request['vendor_contact'],
                'masalah' => $request['masalah'],
                'diagnosa' => $request['diagnosa'],
                'tindakan' => $request['tindakan'],
                'hasil' => $request['hasil'],
                'create_by' => auth()->id()
            ];
            $inserted_maint_asset = AssetMaint::create($asset);

            if($request->hasFile('asset_photo')) {
                foreach($request->file('asset_photo') as $x => $file){
                    $filePath = 'asset_maint/file/' . $file->hashName();
                    // Storage::disk('public')->put($filePath,file_get_contents($request->file('asset_photo')));
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    AssetMaintenancePhoto::create([
                        'asset_maint_id' => $inserted_maint_asset->id,
                        'asset_photo' => $filePath
                    ]);
                }
            }

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
        $asset_photos = AssetMaintenancePhoto::where('asset_maint_id',$assetMaint->id)->get();
        return view('AssetMaint/edit', compact('assetMaint','asset','asset_photos'));
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
                'vendor_name' => $request['vendor_name'],
                'vendor_address' => $request['vendor_address'],
                'vendor_contact' => $request['vendor_contact'],
                'masalah' => $request['masalah'],
                'diagnosa' => $request['diagnosa'],
                'tindakan' => $request['tindakan'],
                'hasil' => $request['hasil'],
                'update_by' => auth()->id()
            ];
            
            $post = AssetMaint::findOrFail($assetMaint['id']);
            $post->update($asset);

            AssetMaintenancePhoto::where('asset_maint_id',$assetMaint['id'])->delete();

            if($request->hasFile('asset_photo')) {
                foreach($request->file('asset_photo') as $x => $file){
                    $filePath = 'asset_maint/file/' . $file->hashName();
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    AssetMaintenancePhoto::create([
                        'asset_maint_id' => $assetMaint['id'],
                        'asset_photo' => $filePath
                    ]);
                }
            }

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
