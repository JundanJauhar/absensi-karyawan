<?php

namespace App\Http\Controllers;

use App\Models\Struktur;
use Illuminate\Http\Request;

class StrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $strukturs = Struktur::all();
        return view('struktur.index', compact('strukturs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $strukturs = Struktur::all();
        return view('struktur.create', compact('strukturs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_struktur' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Struktur::create($request->all());
        return redirect()->route('struktur.index')->with('success', 'Struktur berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $strukturs = Struktur::findOrFail($id);
        return view('struktur.show', compact('strukturs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $strukturs = Struktur::findOrFail($id);
        return view('struktur.edit', compact('strukturs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_struktur' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $strukturs = Struktur::findOrFail($id);
        $strukturs->update($request->all());

        return redirect()->route('struktur.index')->with('success', 'Struktur berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $strukturs = Struktur::findOrFail($id);
        $strukturs->delete();

        return redirect()->route('struktur.index')->with('success', 'Struktur berhasil dihapus.');
    }
}
