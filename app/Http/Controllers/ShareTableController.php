<?php

namespace App\Http\Controllers;

use App\Models\ShareTable;
use Illuminate\Http\Request;

class ShareTableController extends Controller
{
    public function index()
    {
        return ShareTable::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'filepond_id' => ['required', 'exists:fileponds'],
            'name' => ['required'],
            'description' => ['required'],
            'type' => ['required'],
            'member_id' => ['required', 'exists:members'],
            'expired_at' => ['required', 'integer'],
        ]);

        return ShareTable::create($data);
    }

    public function show(ShareTable $shareTable)
    {
        return $shareTable;
    }

    public function update(Request $request, ShareTable $shareTable)
    {
        $data = $request->validate([
            'filepond_id' => ['required', 'exists:fileponds'],
            'name' => ['required'],
            'description' => ['required'],
            'type' => ['required'],
            'member_id' => ['required', 'exists:members'],
            'expired_at' => ['required', 'integer'],
        ]);

        $shareTable->update($data);

        return $shareTable;
    }

    public function destroy(ShareTable $shareTable)
    {
        $shareTable->delete();

        return response()->json();
    }
}
