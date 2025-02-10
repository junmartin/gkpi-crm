<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\SermonAttendance;
use App\Models\Ibadah;
use App\Models\Jemaat;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SermonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ibadah = Ibadah::get();
        // $sermon = Sermon::withTotalAttendees()->get();
        $sermon = Sermon::withCount('attendee')->orderBy('sermon_date','desc')->get(); // attendance_count
        return view('Sermon/index',compact('sermon','ibadah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ibadah = Ibadah::get();
        $jemaats = Jemaat::get();
        return view('Sermon/add',compact('jemaats','ibadah'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            // INSERT SERMON
            $serm = [
                'sermon_date' => $request['sermon_date'],
                'ibadah_id' => $request['ibadah_id'],
                'ibadah_name' => $request['ibadah_name']
            ];
            $sermon = Sermon::create($serm);
            
            if(!empty($request['jemaat'])){
                // INSERT SERMON ATTENDANCE
            
                $attd = [];
                foreach($request['jemaat'] as $jem){
                    $attd = [
                        'sermon_id' => $sermon->id,
                        'jemaat_id' => $jem,
                        'attendance' => 1
                    ];
                    SermonAttendance::create($attd);
                }
            }
            
            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('sermon.index')->with('success','Data Attendance Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('sermon.index')->with('error','Data Attendance Gagal Masuk.');
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Sermon $sermon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sermon $sermon)
    {
        $ibadah = Ibadah::get();
        $jemaats = Jemaat::get();
        
        $attds = SermonAttendance::where("sermon_id",$sermon->id)->get();
        return view('Sermon/edit',compact('sermon','attds','jemaats','ibadah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sermon $sermon)
    {
        try {
            DB::beginTransaction();

            // UPDATE SERMON
            $new_sermon = [
                'sermon_date' => $request['sermon_date'],
                'ibadah_id' => $request['ibadah_id'],
                'ibadah_name' => $request['ibadah_name']
            ];

            $current = Sermon::findOrFail($sermon->id);
            $current->update($new_sermon);

            // UPDATE SERMON ATTENDANCE (DELETE OLD, INSERT NEW)
            $deleted = DB::table('sermon_attendances')
                            ->where('sermon_id', $sermon->id)
                            ->delete();

            $attd = [];
            foreach($request['jemaat'] as $jem){
                $attd = [
                    'sermon_id' => $sermon->id,
                    'jemaat_id' => $jem,
                    'attendance' => 1
                ];
                SermonAttendance::create($attd);
            }

            DB::commit();
            Log::info('Data saved');
            
            return redirect()->route('sermon.index')->with('success','Data Attendance Berhasil Masuk.');
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return redirect()->route('sermon.index')->with('error','Data Attendance Gagal Masuk.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sermon $sermon)
    {
        //
    }

    public function report() {
        $ibadah = Ibadah::get();
        $jemaats = Jemaat::get();
        $sermon = Sermon::get();
        $attds = SermonAttendance::orderBy('sermon_id','asc')->get();
        return view('Sermon/report',compact('sermon','attds','jemaats','ibadah'));
    }
}
