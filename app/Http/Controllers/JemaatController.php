<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jemaat;
use App\Models\Family;
use App\Models\Sermon;
use App\Models\SermonAttendance;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JemaatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $param = $request->all();

        $distinct_city = Jemaat::distinct()->pluck('birth_place');
        $distinct_status = Jemaat::distinct()->pluck('member_type');
        $distinct_baptise = Jemaat::distinct()->pluck('baptise_status');
        
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

        // Status Filter
        $where_status_arr = [];
        $status_default = [];
        foreach($distinct_status as $status){
            if( !empty($param['status_'.str_replace(' ','_',$status)])){
                array_push($where_status_arr,$status);
            }
            array_push($status_default,$status);
        }
        if(empty($where_status_arr)){
            $where_status_arr = $status_default;
        }

        // Baptise Filter
        $where_baptise_arr = [];
        $baptise_default = [];
        foreach($distinct_baptise as $baptise){
            if( !empty($param['baptise_'.str_replace(' ','_',$baptise)])){
                array_push($where_baptise_arr,$baptise);
            }
            array_push($baptise_default,$baptise);
        }
        if(empty($where_baptise_arr)){
            $where_baptise_arr = $baptise_default;
        }

        // Age Group Filter
        // 0 - 12 = Sekolah Minggu
        // 12 - 29 = Youth
        // 30 - 55 = Dewasa
        // > 55 = Lansia

        $jemaats = Jemaat::where(function (Builder $query) use ($where_lakilaki, $where_perempuan) {
            $query->where('jenis_kelamin', $where_lakilaki)
                  ->orWhere('jenis_kelamin', $where_perempuan);
        })
        // ->whereIn('birth_place',$where_city_arr)
        ->whereIn('member_type',$where_status_arr)
        ->whereIn('baptise_status',$where_baptise_arr)        
        ->with('family');
        // ->get();

        if (!empty($param['lansia']) || !empty($param['dewasa']) || !empty($param['pemuda']) || !empty($param['sekolah'])) {
            $jemaats->where(function ($query) use ($param) {
                if (!empty($param['lansia'])) {
                    $query->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 55");
                }
                if (!empty($param['dewasa'])) {
                    $query->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 30 AND 55");
                }
                if (!empty($param['pemuda'])) {
                    $query->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 12 AND 30");
                }
                if (!empty($param['sekolah'])) {
                    $query->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 12");
                }
            });
        }

        if( !empty($param['sort'])){

            if ( $param['sort'] == 'age_asc' )
            {
                $jemaats->orderBy('birth_date', 'desc');
            }
            elseif ( $param['sort'] == 'age_desc' )
            {
                $jemaats->orderBy('birth_date', 'asc');
            }
            elseif ( $param['sort'] == 'name_asc' )
            {
                $jemaats->orderBy('name', 'asc');
            }
            elseif ( $param['sort'] == 'name_desc' )
            {
                $jemaats->orderBy('name', 'desc');
            }
         
        }

        $jemaats->orderBy('id', 'desc');
        $jemaats = $jemaats->get();

        // Get the last 20 sermon IDs with ibadah_id = 1
        $sermon_ids = Sermon::where('ibadah_id', 1)
                            ->orderBy('sermon_date', 'desc')
                            ->take(20)
                            ->pluck('id');

        // Get all sermon attendances for those sermons
        $sermon_attendances = SermonAttendance::whereIn('sermon_id', $sermon_ids)
                                                ->get();

        // Calculate attendance for each jemaat
        $attendance_data = [];
        $total_sermons = count($sermon_ids);
        foreach ($jemaats as $jemaat) {
            $attendance_count = $sermon_attendances->where('jemaat_id', $jemaat->id)->count();
            $attendance_percentage = ($total_sermons > 0) ? ($attendance_count / $total_sermons) * 100 : 0;

            $attendance_data[$jemaat->id] = [
                'count' => $attendance_count,
                'total' => $total_sermons,
                'percentage' => round($attendance_percentage)
            ];
        }
        
        return view('Jemaat/index',compact('jemaats','param','distinct_city','distinct_status','distinct_baptise', 'attendance_data'));
        // return view('jemaat',compact('jemaats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sermons = Sermon::orderBy('sermon_date', 'desc')->take(3)->get();
        return view('Jemaat/add', compact('sermons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'pass_photo' => 'max:2048',
            'pass_photo.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $filePath = "";
            if($request->hasFile('pass_photo')) {
                $path = 'jemaat/file/' . $request->file('pass_photo')->getClientOriginalName();
                Storage::disk('public')->put($path,file_get_contents($request->file('pass_photo')));
                $filePath = 'storage/' . $path;
            }

            if (empty($request['nick_name'])) {
                $nick_name = $request['name'];
            }else{
                $nick_name = $request['nick_name'];
            }
            $jemaat = [
                "name" => $request['name'],
                "nick_name" => $nick_name,
                "jenis_kelamin" => $request['jenis_kelamin'],
                "address" => $request['address'],
                "birth_place" => $request['birth_place'],
                "birth_date" => $request['birth_date'],
                "mobile_no" => $request['mobile_no'],
                "email" => $request['email'],
                "marital_status" => $request['marital_status'],
                "marriage_date" => $request['marriage_date'],
                "spouse_name" => $request['spouse_name'],
                "emergency_contact_name" => $request['emergency_contact_name'],
                "emergency_contact_mobile" => $request['emergency_contact_mobile'],
                "emergency_contact_relation" => $request['emergency_contact_relation'],
                "member_type" => $request['member_type'],
                "baptise_status" => $request['baptise_status'],
                "previous_church" => $request['previous_church'],
                "remark" => $request['remark'],
                "pass_photo" => $filePath,
                "create_by" => auth()->id()
            ];
            $new_jemaat = Jemaat::create($jemaat);

            if (isset($request->sermons)) {
                foreach ($request->sermons as $sermon_id) {
                    SermonAttendance::create([
                        'sermon_id' => $sermon_id,
                        'jemaat_id' => $new_jemaat->id,
                        'attendance' => 1,
                    ]);
                }
            }

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('jemaat.index')->with('success','Data Jemaat Berhasil Masuk.');

        } catch (Exception $e){
            Log::info($e);
            DB::rollback();
            return redirect()->route('jemaat.index')->with('error','Data Jemaat Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $family = Family::get();
        $jemaat = Jemaat::with('family')->find($id);
        return view('Jemaat/view', compact('jemaat','family'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $family = Family::get();
        $jemaat = Jemaat::find($id);
        $sermons = Sermon::orderBy('sermon_date', 'desc')->take(10)->get();
        $sermon_attendances = SermonAttendance::where('jemaat_id', $id)->pluck('sermon_id')->toArray();

        return view('Jemaat/edit', compact('jemaat','family', 'sermons', 'sermon_attendances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'pass_photo' => 'max:2048',
            'pass_photo.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        try {
            DB::beginTransaction();
            
            $nick_name = "";
            if (empty($request['nick_name'])) {
                $nick_name = $request['name'];
            }else{
                $nick_name = $request['nick_name'];
            }
    
            $jemaat_updated = [
                "name" => $request['name'],
                "nick_name" => $nick_name,
                "jenis_kelamin" => $request['jenis_kelamin'],
                "address" => $request['address'],
                "birth_place" => $request['birth_place'],
                "birth_date" => $request['birth_date'],
                "mobile_no" => $request['mobile_no'],
                "email" => $request['email'],
                "marital_status" => $request['marital_status'],
                "marriage_date" => $request['marriage_date'],
                "spouse_name" => $request['spouse_name'],
                "emergency_contact_name" => $request['emergency_contact_name'],
                "emergency_contact_mobile" => $request['emergency_contact_mobile'],
                "emergency_contact_relation" => $request['emergency_contact_relation'],
                "member_type" => $request['member_type'],
                "baptise_status" => $request['baptise_status'],
                "previous_church" => $request['previous_church'],
                "remark" => $request['remark'],
                "family_id" => $request['family_id'],
                "role" => $request['role'],
                "update_by" => auth()->id()
                
            ];
            
            $jemaat = Jemaat::findOrFail($id);
            $jemaat->update($jemaat_updated);

            if($request->hasFile('pass_photo')){
                $path = 'jemaat/file/' . $request->file('pass_photo')->getClientOriginalName();
                Storage::disk('public')->put($path, file_get_contents($request->file('pass_photo')));
                $filePath = 'storage/' . $path;

                $jemaat_foto = ["pass_photo" => $filePath];
                $jemaat->update($jemaat_foto);
            }

            // Handle Sermon Attendance
            $last_ten_sermons_ids = Sermon::orderBy('sermon_date', 'desc')->take(10)->pluck('id');
            SermonAttendance::where('jemaat_id', $id)->whereIn('sermon_id', $last_ten_sermons_ids)->delete();

            if (isset($request->sermons)) {
                foreach ($request->sermons as $sermon_id) {
                    if(in_array($sermon_id, $last_ten_sermons_ids->toArray())) {
                        SermonAttendance::create([
                            'sermon_id' => $sermon_id,
                            'jemaat_id' => $id,
                            'attendance' => 1,
                        ]);
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('jemaat.index')->with('success','Data Jemaat Berhasil Diubah.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('jemaat.index')->with('error','Data Jemaat Gagal Diubah.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function template()
    {
        return view('Template/form');
    }
}