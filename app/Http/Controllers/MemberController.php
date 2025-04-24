<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Member;
use App\Http\Resources\memberResource;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::all();
        return new memberResource($members, 'Membership List', 'success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);
        if ($validator->fails()) {
            return new memberResource(null, 'gagal', $validator->errors());
        }

        $member = Member::create($request->all());
            return new memberResource($member, 'Membership Berhasil Dibuat', 'success');
    }

    /**     
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::find($id);

        if ($member) {
            return new memberResource($member, 'Member Ditemukan', 'success');
        } else {
            return new memberResource(null, 'Member tidak ditemukan', 'error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::find($id);

        if ($member) {
            $member->update($request->all());
            return new memberResource($member, 'Member berhasil diupdate', 'success');
        } else {
            return new memberResource(null, 'Member tidak ditemukan', 'error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::find($id);

        if ($member) {
            $member->delete();
            return new memberResource(null, 'Member berhasil dihapus', 'success');
        } else {
            return new memberResource(null, 'Member tidak ditemukan', 'error');
        }
    }
}
