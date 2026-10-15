<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanLokasiController extends Controller
{
    /**
     * Tampilkan halaman pengaturan lokasi kantor & geofencing
     */
    public function index()
    {
        $kantorNama   = Pengaturan::get('kantor_nama', 'Kantor Enterprise HR');
        $kantorLat    = Pengaturan::get('kantor_lat', '-7.711583');
        $kantorLong   = Pengaturan::get('kantor_long', '110.395792');
        $kantorRadius = Pengaturan::get('kantor_radius_meter', '40');
        $geofencingAktif = Pengaturan::get('geofencing_aktif', '1');

        return view('admin.pengaturan.lokasi', compact(
            'kantorNama',
            'kantorLat',
            'kantorLong',
            'kantorRadius',
            'geofencingAktif'
        ));
    }

    /**
     * Simpan perubahan pengaturan lokasi & radius kantor
     */
    public function update(Request $request)
    {
        $request->validate([
            'kantor_nama'         => 'required|string|max:150',
            'kantor_lat'          => 'required|numeric|between:-90,90',
            'kantor_long'         => 'required|numeric|between:-180,180',
            'kantor_radius_meter' => 'required|integer|min:10|max:5000',
        ], [
            'kantor_nama.required'         => 'Nama kantor wajib diisi.',
            'kantor_lat.required'          => 'Latitude wajib diisi.',
            'kantor_lat.numeric'           => 'Format Latitude tidak valid.',
            'kantor_long.required'         => 'Longitude wajib diisi.',
            'kantor_long.numeric'          => 'Format Longitude tidak valid.',
            'kantor_radius_meter.required' => 'Radius kantor wajib diisi.',
            'kantor_radius_meter.min'      => 'Radius minimal adalah 10 meter.',
        ]);

        Pengaturan::set('kantor_nama', $request->kantor_nama, 'Nama lokasi kantor');
        Pengaturan::set('kantor_lat', $request->kantor_lat, 'Latitude koordinat pusat kantor');
        Pengaturan::set('kantor_long', $request->kantor_long, 'Longitude koordinat pusat kantor');
        Pengaturan::set('kantor_radius_meter', $request->kantor_radius_meter, 'Radius toleransi absensi dalam meter');
        Pengaturan::set('geofencing_aktif', $request->has('geofencing_aktif') ? '1' : '0', 'Status aktif geofencing');

        return back()->with('success', 'Pengaturan lokasi kantor & radius absensi berhasil diperbarui!');
    }
}
