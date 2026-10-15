<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusKaryawan;

class StatusKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statusKaryawans = StatusKaryawan::all();
        return view('status_karyawan.index', compact('statusKaryawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('status_karyawan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        StatusKaryawan::create($request->all());
        return redirect()->route('status_karyawan.index')->with('success', 'Status Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $statusKaryawan = StatusKaryawan::findOrFail($id);
        return view('status_karyawan.show', compact('statusKaryawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $statusKaryawan = StatusKaryawan::findOrFail($id);
        return view('status_karyawan.edit', compact('statusKaryawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $statusKaryawan = StatusKaryawan::findOrFail($id);
        $statusKaryawan->update($request->all());

        return redirect()->route('status_karyawan.index')->with('success', 'Status Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $statusKaryawan = StatusKaryawan::findOrFail($id);
        $statusKaryawan->delete();

        return redirect()->route('status_karyawan.index')->with('success', 'Status Karyawan berhasil dihapus.');
    }
}
