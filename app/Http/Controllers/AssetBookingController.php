<?php

namespace App\Http\Controllers;

use App\Models\AssetBooking;
use App\Models\Jemaat;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assetBookings = AssetBooking::with(['jemaat', 'asset'])->get();
        return view('AssetBooking.index', compact('assetBookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jemaats = Jemaat::all();
        $assets = Asset::all();
        return view('AssetBooking.add', compact('jemaats', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jemaat_id' => 'required|exists:jemaats,id',
            'asset_id' => 'required|exists:assets,id',
            'booking_date' => 'required|date',
            'status' => 'required|string',
        ]);

        AssetBooking::create([
            'jemaat_id' => $request->jemaat_id,
            'asset_id' => $request->asset_id,
            'booking_date' => $request->booking_date,
            'status' => $request->status,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect()->route('assetbooking.index')->with('success', 'Asset booking created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assetBooking = AssetBooking::findOrFail($id);
        $jemaats = Jemaat::all();
        $assets = Asset::all();
        return view('AssetBooking.edit', compact('assetBooking', 'jemaats', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'jemaat_id' => 'required|exists:jemaats,id',
            'asset_id' => 'required|exists:assets,id',
            'booking_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $assetBooking = AssetBooking::findOrFail($id);
        $assetBooking->update([
            'jemaat_id' => $request->jemaat_id,
            'asset_id' => $request->asset_id,
            'booking_date' => $request->booking_date,
            'status' => $request->status,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect()->route('assetbooking.index')->with('success', 'Asset booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assetBooking = AssetBooking::findOrFail($id);
        $assetBooking->delete();

        return redirect()->route('assetbooking.index')->with('success', 'Asset booking deleted successfully.');
    }
}
