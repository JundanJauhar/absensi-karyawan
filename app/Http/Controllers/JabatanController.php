<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = Karyawan::all();
        return view('jabatan.index', compact('jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jabatan.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
            'deskripsi' => 'string',
        ]);

        Karyawan::create($request->all());
        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jabatans = Karyawan::findOrFail($id);
        return view('jabatan.show', compact('jabatans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jabatans = Karyawan::findOrFail($id);
        return view('jabatan.edit', compact('jabatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $jabatans = Karyawan::findOrFail($id);
        $jabatans->update($request->all());

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jabatans = Karyawan::findOrFail($id);
        $jabatans->delete();

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
