@extends('layouts.app')

@section('page-title', 'Pengaturan Lokasi Kantor & Geofencing')

@section('breadcrumb')
    <li class="breadcrumb-item active">Radius Lokasi Kantor</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 380px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4 mb-4">
    {{-- FORM PENGATURAN --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>Konfigurasi Area Absensi
                </h6>
                <small class="text-muted">Tentukan titik koordinat GPS dan radius toleransi kehadiran karyawan</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pengaturan.lokasi.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nama Lokasi Kantor:</label>
                        <input type="text" name="kantor_nama" class="form-control @error('kantor_nama') is-invalid @enderror" 
                               value="{{ old('kantor_nama', $kantorNama) }}" required>
                        @error('kantor_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small">Latitude:</label>
                            <input type="number" step="any" name="kantor_lat" id="inputLat" 
                                   class="form-control font-monospace @error('kantor_lat') is-invalid @enderror" 
                                   value="{{ old('kantor_lat', $kantorLat) }}" required>
                            @error('kantor_lat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small">Longitude:</label>
                            <input type="number" step="any" name="kantor_long" id="inputLong" 
                                   class="form-control font-monospace @error('kantor_long') is-invalid @enderror" 
                                   value="{{ old('kantor_long', $kantorLong) }}" required>
                            @error('kantor_long')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small d-flex justify-content-between">
                            <span>Radius Toleransi (Meter):</span>
                            <span class="text-primary fw-bold" id="labelRadius">{{ $kantorRadius }} meter</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="kantor_radius_meter" id="inputRadius" min="10" max="1000" step="5"
                                   class="form-control @error('kantor_radius_meter') is-invalid @enderror" 
                                   value="{{ old('kantor_radius_meter', $kantorRadius) }}" required>
                            <span class="input-group-text">meter</span>
                        </div>
                        <div class="form-text text-muted small">
                            * Rekomendasi: 30–50 meter untuk gedung perkantoran.
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="geofencing_aktif" id="switchGeofence" value="1"
                                   {{ $geofencingAktif ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="switchGeofence">
                                Wajibkan Pembatasan Radius (Geofencing)
                            </label>
                        </div>
                        <small class="text-muted d-block">
                            Jika diaktifkan, karyawan yang berada di luar radius kantor <strong>tidak akan bisa melakukan Clock In / Clock Out</strong>. Matikan jika sedang mode WFH.
                        </small>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan Lokasi
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="ambilLokasiSaatIni()">
                            <i class="bi bi-crosshair me-1"></i> Gunakan Koordinat GPS Saya Saat Ini
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- INTERACTIVE MAP --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-map-fill text-primary me-2"></i>Peta Radius Kehadiran
                    </h6>
                    <small class="text-muted">Visualisasi area kantor & radius absensi</small>
                </div>
                <span class="badge {{ $geofencingAktif ? 'bg-success' : 'bg-secondary' }}">
                    <i class="bi bi-shield-check me-1"></i>{{ $geofencingAktif ? 'Geofencing Aktif' : 'Geofencing Nonaktif' }}
                </span>
            </div>
            <div class="card-body p-4">
                <div id="map"></div>
                <div class="mt-3 p-3 bg-light rounded-3 border small text-muted d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle fs-4 text-primary"></i>
                    <div>
                        <strong>Titik Terkunci:</strong> Lingkaran biru menandai area yang diizinkan untuk absensi. 
                        Anda dapat menggeser pin merah di peta untuk memindahkan titik pusat kantor.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, marker, circle;
    let lat = parseFloat("{{ $kantorLat }}") || -7.711583;
    let lng = parseFloat("{{ $kantorLong }}") || 110.395792;
    let radius = parseInt("{{ $kantorRadius }}") || 40;

    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi peta Leaflet
        map = L.map('map').setView([lat, lng], 18);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker kantor yang dapat digeser (draggable)
        marker = L.marker([lat, lng], { draggable: true }).addTo(map)
            .bindPopup('<b>{{ $kantorNama }}</b><br>Titik Pusat Kantor').openPopup();

        // Lingkaran radius
        circle = L.circle([lat, lng], {
            color: '#1e40af',
            fillColor: '#3b82f6',
            fillOpacity: 0.25,
            radius: radius
        }).addTo(map);

        // Event saat marker digeser
        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            document.getElementById('inputLat').value = position.lat.toFixed(6);
            document.getElementById('inputLong').value = position.lng.toFixed(6);
            circle.setLatLng(position);
        });

        // Event input radius berubah
        const inputRadius = document.getElementById('inputRadius');
        inputRadius.addEventListener('input', function () {
            const val = parseInt(this.value) || 10;
            document.getElementById('labelRadius').textContent = val + ' meter';
            circle.setRadius(val);
        });

        // Event input koordinat diketik manual
        const inputLat = document.getElementById('inputLat');
        const inputLong = document.getElementById('inputLong');

        function updateMapFromInputs() {
            const newLat = parseFloat(inputLat.value);
            const newLng = parseFloat(inputLong.value);
            if (!isNaN(newLat) && !isNaN(newLng)) {
                const newPos = [newLat, newLng];
                marker.setLatLng(newPos);
                circle.setLatLng(newPos);
                map.panTo(newPos);
            }
        }
        inputLat.addEventListener('change', updateMapFromInputs);
        inputLong.addEventListener('change', updateMapFromInputs);
    });

    // Deteksi koordinat GPS admin saat ini
    function ambilLokasiSaatIni() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi Geolocation.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const curLat = position.coords.latitude;
                const curLng = position.coords.longitude;
                document.getElementById('inputLat').value = curLat.toFixed(6);
                document.getElementById('inputLong').value = curLng.toFixed(6);

                const newPos = [curLat, curLng];
                marker.setLatLng(newPos);
                circle.setLatLng(newPos);
                map.setView(newPos, 18);
                marker.bindPopup('<b>Lokasi Terdeteksi</b><br>Koordinat GPS berhasil diperbarui.').openPopup();
            },
            function (error) {
                alert('Gagal mendeteksi lokasi: ' + error.message);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endpush
