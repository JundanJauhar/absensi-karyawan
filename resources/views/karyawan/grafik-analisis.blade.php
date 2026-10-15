@extends('layouts.app')

@section('page-title', 'Grafik Analisis Karyawan & Presensi')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-bar-chart-line-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Grafik Analisis &amp; Business Intelligence SDM</h5>
                    <small class="text-muted">Visualisasi Real-Time Kehadiran, Denda, Departemen, dan Kontrak Karyawan via Grafana</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                    <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Grafana Terhubung (Port 3000)
                </span>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('grafanaFrame').src = document.getElementById('grafanaFrame').src;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
                <a href="http://{{ request()->getHost() }}:3000/d/emp-hr-absensi-01/dashboard-analitik-presensi-and-karyawan?orgId=1&from=now-30d&to=now" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Layar Penuh
                </a>
            </div>
        </div>

        <div class="card-body p-0 position-relative" style="min-height: 1500px; background-color: #111217;">
            <iframe 
                id="grafanaFrame"
                src="http://{{ request()->getHost() }}:3000/d/emp-hr-absensi-01/dashboard-analitik-presensi-and-karyawan?orgId=1&kiosk&from=now-30d&to=now&refresh=10s" 
                width="100%" 
                height="1600" 
                style="border: none; display: block;" 
                allowfullscreen>
            </iframe>
        </div>
    </div>
</div>
@endsection
