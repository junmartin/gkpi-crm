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

use Carbon\Carbon;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $param = $request->all();

        $asset_status['new'] = "New";
        $asset_status['use'] = "In Use";
        $asset_status['oos'] = "Out of Service";
        $asset_status['sto'] = "In Storage";
        $asset_status['dis'] = "Disposed";
        $asset_status['los'] = "Lost/Stolen";
        $asset_status['dmg'] = "Damage/Broken";        
        
        $asset_type = AssetType::get();
        $locations = Asset::select('location')->distinct()->whereNotNull('location')->get();

        // Type Filter
        $where_type_arr = [];
        $type_default = [];
        foreach($asset_type as $type) {
            if( !empty($param['type_'.$type['id']])){
                array_push($where_type_arr,$type['id']);
            }
            array_push($type_default, $type['id']);
        }
        if(empty($where_type_arr)){
            $where_type_arr = $type_default;
        }

        // Type Status
        $where_stat_arr = [];
        $stat_default = ['new','use','oos','sto','dis','los','dmg'];
        //$stat_default = [];
        foreach($asset_status as $s => $stat) {
            if( !empty($param['stat_'.$s])){
                array_push($where_stat_arr,$s);
            }
            array_push($type_default, $s);
        }
        if(empty($where_stat_arr)){
            $where_stat_arr = $stat_default;
        }

        // Location Filter
        $where_loc_arr = [];
        $loc_default = [];
        foreach($locations as $loc) {
            $key = 'loc_' . str_replace(' ', '_', $loc['location']);
            if( !empty($param[$key])){
                array_push($where_loc_arr,$loc['location']);
            }
            array_push($loc_default, $loc['location']);
        }
        if(empty($where_loc_arr)){
            $where_loc_arr = $loc_default;
        }
        
        $sort_by = $request->input('sort_by', 'last_input'); // Default to 'last_input'

        $assets_query = Asset::with([
            'asset_type',
            'asset_photo',
            'maintenance' => function($query) {
                $query->orderBy('maint_date','desc');
            }
        ])
        ->whereIn('type_id',$where_type_arr)
        ->whereIn('status',$where_stat_arr)
        ->whereIn('location',$where_loc_arr);

        if ($sort_by == 'first_input') {
            $assets_query->orderBy('id','asc');
        } elseif ($sort_by == 'last_input') {
            $assets_query->orderBy('id','desc');
        } elseif ($sort_by == 'a_z') {
            $assets_query->orderBy('name','asc');
        } elseif ($sort_by == 'z_a') {
            $assets_query->orderBy('name','desc');
        }

        $assets = $assets_query->get()
        ->each(function($asset){
            $lastMaintenance = $asset->maintenance->first();
            return [
                'id' => $asset->id,
                'asset_type' => $asset->asset_type->name ?? "N/A",
                'name' => $asset->name,
                'merk' => $asset->merk,
                'model' => $asset->model,
                'serial_number' => $asset->serial_number,
                'spec' => $asset->spec,
                'acquired_date' => $asset->acquired_date,
                'status' => $asset->status,
                'location' => $asset->location,
                'pic' => $asset->pic,
                'ownership' => $asset->ownership,
                'maintenance_count' => $asset->maintenances?->count(),
                'last_maintenance_date' => $lastMaintenance?->maint_date,
                'suggested_next_service_date' => $lastMaintenance?->next_maint_date
            ];
        });


        // return view('Asset/index',compact('param','assets','asset_type'));
        return view('Asset/index',array_merge(compact('param','asset_type','locations'),['assets'=>collect($assets)]));
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
                'status' => $request['status'],
                'location' => $request['location'],
                'pic' => $request['pic'],
                'ownership' => ($request['ownership'])?$request['ownership']:'GKPI-GP',
                'create_by' => auth()->id()
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
    
    public function edit_status(string $asset_id)
    {
        
        $asset = Asset::with('asset_type')->find($asset_id);
        $asset_type = AssetType::get();
        $maints = AssetMaint::where('asset_id',$asset_id)->get();
        $asset_photos = AssetPhoto::where('asset_id',$asset_id)->get();
        // dd($asset_photos);
        return view('Asset/edit_status', compact('asset','asset_type','maints','asset_photos'));
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
                'update_by' => auth()->id()
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

    public function update_status(Request $request, $asset_id)
    {
        try {
            DB::beginTransaction();
            
            $new_asset = [
                // 'type_id' => $request['type_id'],
                // 'name' => $request['name'],
                // 'merk' => $request['merk'],
                // 'model' => $request['model'],
                // 'serial_number' => $request['serial_number'],
                // 'tipe' => $request['tipe'],
                // 'spec' => $request['spec'],
                // 'acquired_date' => $request['acquired_date'],
                'status' => $request['status'],
                'location' => $request['location'],
                'pic' => $request['pic'],
                'ownership' => $request['ownership'],
                'update_by' => auth()->id()
            ];
            // $post = Asset::findOrFail($asset['id']);
            $post = Asset::findOrFail($asset_id);
            $post->update($new_asset);
            
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

    public function public_report(Request $request)
    {
        $param = $request->all();

        $asset_status['new'] = "New";
        $asset_status['use'] = "In Use";
        $asset_status['oos'] = "Out of Service";
        $asset_status['sto'] = "In Storage";
        $asset_status['dis'] = "Disposed";
        $asset_status['los'] = "Lost/Stolen";
        $asset_status['dmg'] = "Damage/Broken";

        $asset_type = AssetType::get();
        $locations = Asset::select('location')->distinct()->whereNotNull('location')->get();

        // Type Filter
        $where_type_arr = [];
        $type_default = [];
        foreach($asset_type as $type) {
            if( !empty($param['type_'.$type['id']])){
                array_push($where_type_arr,$type['id']);
            }
            array_push($type_default, $type['id']);
        }
        if(empty($where_type_arr)){
            $where_type_arr = $type_default;
        }

        // Type Status
        $where_stat_arr = [];
        $stat_default = ['new','use','oos','sto','dis','los','dmg'];
        //$stat_default = [];
        foreach($asset_status as $s => $stat) {
            if( !empty($param['stat_'.$s])){
                array_push($where_stat_arr,$s);
            }
            array_push($type_default, $s);
        }
        if(empty($where_stat_arr)){
            $where_stat_arr = $stat_default;
        }

        // Location Filter
        $where_loc_arr = [];
        $loc_default = [];
        foreach($locations as $loc) {
            $key = 'loc_' . str_replace(' ', '_', $loc['location']);
            if( !empty($param[$key])){
                array_push($where_loc_arr,$loc['location']);
            }
            array_push($loc_default, $loc['location']);
        }
        if(empty($where_loc_arr)){
            $where_loc_arr = $loc_default;
        }


        $assets = Asset::with([
            'asset_type',
            'created_by',
            'updated_by'
        ])
        ->whereIn('type_id',$where_type_arr)
        ->whereIn('status',$where_stat_arr)
        ->whereIn('location',$where_loc_arr)
        ->orderBy('id','desc')
        ->get();

        $timestamp = Carbon::now()->toDateTimeString();

        return view('Asset/public_report',compact('assets','asset_status','timestamp'));
    }
}
