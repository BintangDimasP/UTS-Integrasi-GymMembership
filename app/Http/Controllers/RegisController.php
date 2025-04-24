<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Carbon;

use App\Models\Regis;
use App\Http\Resources\regisResource;

class RegisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regis = Regis::all();
        return new regisResource($regis, 'All Member List', 'success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|integer',
            'package_id' => 'required|integer',
            'join_date' => 'required|date',
    ]);

        if ($validator->fails()) {
            return new regisResource(null, 'Validasi gagal', $validator->errors());
    }

        // get U-S
        $userResponse = Http::get("http://localhost:8001/api/members/{$request->member_id}");
        if (!$userResponse->ok() || !$userResponse['data']) {
            return new regisResource(null, 'Member tidak ditemukan', 'error');
    }
        $user = $userResponse['data'];

        // Get P-S
        $packageResponse = Http::get("http://localhost:8002/api/packages/{$request->package_id}");
        if (!$packageResponse->ok() || !$packageResponse['data']) {
            return new regisResource(null, 'Package tidak ditemukan', 'error');
    }
        $package = $packageResponse['data'];

        $joinDate = Carbon::parse($request->join_date);
        $endDate = $joinDate->copy()->addMonths($package['duration']);

        $regis = Regis::create([
            'member_id'     => $request->member_id,
            'package_id'    => $request->package_id,
            'name'          => $user['name'],
            'email'         => $user['email'],
            'package_name'  => $package['name'],
            'duration'      => $package['duration'],
            'join_date'     => $joinDate,
            'end_date'      => $endDate,
            'status'        => 'Aktif',
        ]);
         return new regisResource($regis, 'Pendaftaran berhasil','success');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $regis = Regis::find($id);

        if ($regis) {
            return new regisResource($regis, 'Data Member Ditemukan', 'success');
        } else {
            return new regisResource(null, 'Data Member Tidak Ditemukan', 'success');
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
