<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Package;
use App\Http\Resources\packageResource;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        return new packageResource($packages , 'Packages List', 'success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|in:Jawara 1,Jawara 2,Jawara 3,Jawara 4|unique:packages,name',
            'price' => 'required|integer|min:100000',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
        ]);
    
        if ($validator->fails()) {
            return new PackageResource(null, 'Validasi gagal', $validator->errors());
        }

        $durasiMap = [
            'Jawara 1' => 12,
            'Jawara 2' => 6,
            'Jawara 3' => 3,
            'Jawara 4' => 1,
        ];
    
        $duration = $durasiMap[$request->name];
    
        $package = Package::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $duration,
            'level' => $request->level,
        ]);
    
        return new PackageResource($package, 'Package berhasil ditambahkan', 'success');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::find($id);

        if ($package) {
            return new packageResource($package, 'Package Ditemukan', 'success');
        } else {
            return new packageResource(null, 'Package tidak ditemukan', 'error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $package =Package::find($id);

        if ($package) {
            $package->update($request->all());
            return new packageResource($package, 'Package Berhasil Diupdate', 'success');
        } else {
            return new packageResource(null, 'Package tidak ditemukan', 'error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package =Package::find($id);

        if ($package) {
            $package->delete();
            return new packageResource(null, 'Package Berhasil Dihapus', 'success');
        } else {
            return new packageResource(null, 'Package tidak ditemukan', 'error');
        }
    }

    public function ai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget' => 'required|integer|min:0',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
        ]);
    
        if ($validator->fails()) {
            return new packageResource(null, 'Validasi gagal', $validator->errors());
        }
    
        $budget = $request->budget;
        $level = $request->level;
    
        // sccs
        $recommended = Package::where('level', $level)
            ->where('price', '<=', $budget)
            ->orderByDesc('duration')
            ->first();
    
        // partial, budget sesuai tapi level tidak atau sebaliknya
        if (!$recommended) {
            $recommended = Package::where('price', '<=', $budget)
                ->orderByDesc('duration')
                ->first();
    
            if ($recommended) {
                return new packageResource($recommended, 'Tidak ada paket sesuai level, tapi kami temukan paket lain yang sesuai budget.', 'partial');
            }
    
            // gagal
            return new packageResource(null, 'Tidak ada paket yang sesuai dengan budget.', 'not found');
        }
    
        return new packageResource($recommended, 'Paket rekomendasi ditemukan.', 'success');
    
    }
}
