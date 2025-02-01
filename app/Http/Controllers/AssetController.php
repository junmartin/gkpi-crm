<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssetMaint;
use App\Models\AssetPhoto;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::with('asset_type')->get();
        return view('Asset/index',compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $asset_type = AssetType::get();
        return view('Asset/add',compact('asset_type'));
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
                'type_id' => $request['type_id'],
                'name' => $request['name'],
                'merk' => $request['merk'],
                'model' => $request['model'],
                'serial_number' => $request['serial_number'],
                'tipe' => $request['tipe'],
                'spec' => $request['spec'],
                'acquired_date' => $request['acquired_date'],
            ];
            $inserted_asset = Asset::create($asset);

            
            if($request->hasFile('asset_photo')) {
                foreach($request->file('asset_photo') as $x => $file){
                    // $filePath[$x] = 'storage/asset/file/' . $file->file('asset_photo')->getClientOriginalName();
                    $filePath = 'asset/file/' . $file->hashName();
                    // Storage::disk('public')->put($filePath,file_get_contents($request->file('asset_photo')));
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    AssetPhoto::create([
                        'asset_id' => $inserted_asset->id,
                        'asset_photo' => $filePath
                    ]);
                }
            }

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('asset.index')->with('success','Data Asset Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset.index')->with('error','Data Asset Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $asset_type = AssetType::get();
        $maints = AssetMaint::where('asset_id',$asset->id)->get();
        $asset_photos = AssetPhoto::where('asset_id',$asset->id)->get();
        // dd($asset_photos);
        return view('Asset/edit', compact('asset','asset_type','maints','asset_photos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        try {
            DB::beginTransaction();
            
            $new_asset = [
                'type_id' => $request['type_id'],
                'name' => $request['name'],
                'merk' => $request['merk'],
                'model' => $request['model'],
                'serial_number' => $request['serial_number'],
                'tipe' => $request['tipe'],
                'spec' => $request['spec'],
                'acquired_date' => $request['acquired_date'],
            ];
            $post = Asset::findOrFail($asset['id']);
            $post->update($new_asset);

            AssetPhoto::where('asset_id',$asset['id'])->delete();

            if($request->hasFile('asset_photo')) {
                foreach($request->file('asset_photo') as $x => $file){
                    // $filePath[$x] = 'storage/asset/file/' . $file->file('asset_photo')->getClientOriginalName();
                    $filePath = 'asset/file/' . $file->hashName();
                    // Storage::disk('public')->put($filePath,file_get_contents($request->file('asset_photo')));
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    AssetPhoto::create([
                        'asset_id' => $asset['id'],
                        'asset_photo' => $filePath
                    ]);
                }
            }

            DB::commit();
            Log::info('Asset Data Update');
            
            return redirect()->route('asset.index')->with('success','Data Asset Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('asset.index')->with('error','Data Asset Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        //
    }
}
