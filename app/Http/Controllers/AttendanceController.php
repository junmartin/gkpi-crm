<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Jemaat;
use App\Models\Ibadah;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $attendance = Attendance::get();
        $ibadah = Ibadah::get();
        $attendance = Attendance::attendanceSummary()->get();
        return view('Attendance/index',compact('attendance','ibadah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Attendance/add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);

        try {
            DB::beginTransaction();

            $attd = [];

            foreach($request['jemaat'] as $jem){
                $attd = [
                    'sermon_date' => $request['sermon_date'],
                    'ibadah_id' => $request['ibadah_id'],
                    'ibadah_name' => $request['ibadah_name'],
                    'jemaat_id' => $jem,
                    'attendance' => 1
                ];
                Attendance::create($attd);
            }

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('attendance.index')->with('success','Data Attendance Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('attendance.index')->with('error','Data Attendance Gagal Masuk.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        dd($attendance);
    }

    public function adjustment($sermon_date,$ibadah_id)
    {
        // echo "sermon: ". $sermon_date;
        // echo "<br>ibadah: ". $ibadah_id;
        
        $ibadah = Ibadah::get();
        $jemaats = Jemaat::get();
        $attds = Attendance::where("sermon_date",$sermon_date)->where("ibadah_id",$ibadah_id)->get();
        // echo "<pre>";
        // var_dump($attds[0]);
        // echo "</pre>";

        return view('Attendance/edit',compact('attds','ibadah','jemaats','sermon_date','ibadah_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    public function adjustment_update(Request $request, String $sermon_date, String $ibadah_id)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            Attendance::where('sermon_date', $request['sermon_date'])
                        -> where('ibadah_id', $request['ibadah_id'])
                        -> delete();

            $attd = [];

            foreach($request['jemaat'] as $jem){
                $attd = [
                    'sermon_date' => $request['sermon_date'],
                    'ibadah_id' => $request['ibadah_id'],
                    'ibadah_name' => $request['ibadah_name'],
                    'jemaat_id' => $jem,
                    'attendance' => 1
                ];
                Attendance::create($attd);
            }


            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('attendance.index')->with('success','Data Attendance Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('attendance.index')->with('error','Data Attendance Gagal Masuk.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
