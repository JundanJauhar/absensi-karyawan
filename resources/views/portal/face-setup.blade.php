@extends('layouts.app')

@section('page-title', 'Setup Face ID')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Portal Karyawan</a></li>
    <li class="breadcrumb-item active">Setup Face ID</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Header Info --}}
        <div class="alert bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-4 p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="bi bi-person-bounding-box"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-primary">Registrasi Face ID</h5>
                    <p class="text-secondary mb-2 small">
                        Daftarkan wajah Anda sekali saja sebagai metode verifikasi absensi. Setelah terdaftar, setiap Clock In dan Clock Out wajib melewati verifikasi wajah ini.
                    </p>
                    <div class="d-flex flex-wrap gap-3 small">
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Data wajah tidak dikirim ke server luar</span>
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Proses di browser Anda sendiri</span>
                    </div>
                </div>
            </div>
        </div>

        @if($karyawan->face_descriptor)
        <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-shield-fill-check fs-3"></i>
            <div>
                <strong>Face ID Sudah Terdaftar</strong><br>
                <small class="text-muted">Didaftarkan: {{ $karyawan->face_registered_at?->translatedFormat('d F Y, H:i') ?? '-' }}<br>Anda dapat mendaftarkan ulang jika wajah tidak terdeteksi dengan baik.</small>
            </div>
        </div>
        @else
        <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-3"></i>
            <div><strong>Face ID Belum Terdaftar</strong><br><small>Ikuti langkah-langkah di bawah untuk mendaftarkan wajah Anda.</small></div>
        </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-camera-video text-primary me-2"></i>Registrasi Wajah via Kamera</h6>
            </div>
            <div class="card-body p-4">

                <div class="mb-4 p-3 bg-light rounded-3 border small">
                    <div class="d-flex gap-3 mb-2">
                        <span class="badge bg-primary">1</span>
                        <span>Klik <strong>"Buka Kamera"</strong> dan izinkan akses kamera browser Anda</span>
                    </div>
                    <div class="d-flex gap-3 mb-2">
                        <span class="badge bg-primary">2</span>
                        <span>Hadapkan wajah Anda lurus ke kamera, pastikan cahaya cukup terang</span>
                    </div>
                    <div class="d-flex gap-3 mb-2">
                        <span class="badge bg-primary">3</span>
                        <span>Tunggu hingga kotak deteksi berwarna <span class="text-success fw-bold">hijau</span> dan status berubah menjadi "Wajah Terdeteksi"</span>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge bg-primary">4</span>
                        <span>Klik <strong>"Ambil Foto dan Daftarkan Wajah"</strong></span>
                    </div>
                </div>

                <div class="position-relative mb-4" style="background:#111; border-radius:12px; overflow:hidden; min-height:280px;">
                    <video id="video" autoplay muted playsinline style="width:100%; max-height:360px; display:block; transform:scaleX(-1); border-radius:12px;"></video>
                    <canvas id="overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;transform:scaleX(-1);"></canvas>
                    
                    {{-- Placeholder kamera belum aktif (tanpa class d-flex agar tidak tabrakan dengan style inline) --}}
                    <div id="cameraPlaceholder" style="display:flex; flex-direction:column; align-items:center; justify-content:center; position:absolute;top:0;left:0;right:0;bottom:0;min-height:280px; color:#fff; z-index:2;">
                        <i class="bi bi-camera-video-off mb-2" style="font-size:3rem;opacity:0.5;"></i>
                        <span class="small text-white-50">Kamera belum aktif</span>
                    </div>

                    {{-- Overlay Berhasil --}}
                    <div id="setupSuccessOverlay" style="display:none; flex-direction:column; align-items:center; justify-content:center; position:absolute;top:0;left:0;right:0;bottom:0; background:rgba(16, 185, 129, 0.92); z-index:10; color:#fff; text-align:center;">
                        <i class="bi bi-check-circle-fill mb-2" style="font-size:3.5rem;"></i>
                        <h5 class="fw-bold mb-1">Face ID Berhasil Didaftarkan!</h5>
                        <p class="small mb-0 text-white-75">Sedang mengalihkan ke Portal Absensi...</p>
                    </div>

                    {{-- Detection status badge --}}
                    <div id="detectionBadge" style="position:absolute;bottom:12px;left:0;right:0;text-align:center;display:none;z-index:3;">
                        <span id="detectionText" class="badge bg-secondary fs-6 px-3 py-2">Mendeteksi...</span>
                    </div>
                </div>

                <div id="modelLoadingInfo" class="text-muted small text-center mb-3">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Memuat model AI Face Recognition... (sekali saja)
                </div>
                <div id="modelReadyInfo" class="text-success small text-center mb-3" style="display:none;">
                    <i class="bi bi-check-circle-fill me-1"></i>Model AI siap. Klik "Buka Kamera" untuk memulai.
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button id="btnBukaKamera" class="btn btn-primary btn-lg flex-grow-1" disabled onclick="bukaKamera()">
                        <i class="bi bi-camera-video me-2"></i>Buka Kamera
                    </button>
                    <button id="btnAmbilFoto" class="btn btn-success btn-lg flex-grow-1" disabled onclick="ambilFotoDanDaftarkan()">
                        <i class="bi bi-person-bounding-box me-2"></i>Ambil Foto dan Daftarkan Wajah
                    </button>
                </div>

                <div id="hasilRegistrasi" class="mt-3" style="display:none;"></div>
            </div>
        </div>

        <a href="{{ route('portal.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Portal
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const MODEL_URL = '{{ asset("models") }}';
let detectionInterval = null;
let faceDetected = false;
let activeStream = null;

async function loadModels() {
    try {
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        document.getElementById('modelLoadingInfo').style.display = 'none';
        document.getElementById('modelReadyInfo').style.display = 'block';
        document.getElementById('btnBukaKamera').disabled = false;
    } catch (err) {
        document.getElementById('modelLoadingInfo').innerHTML =
            '<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Gagal memuat model AI: ' + err.message + '</span>';
    }
}

async function bukaKamera() {
    try {
        activeStream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
        });
        const video = document.getElementById('video');
        video.srcObject = activeStream;
        
        // Sembunyikan placeholder kamera belum aktif
        document.getElementById('cameraPlaceholder').style.display = 'none';
        document.getElementById('detectionBadge').style.display = 'block';
        document.getElementById('btnBukaKamera').disabled = true;
        document.getElementById('btnBukaKamera').innerHTML = '<i class="bi bi-camera-video-fill me-2"></i>Kamera Aktif';
        
        video.onloadedmetadata = () => { mulaiDeteksi(); };
    } catch (err) {
        alert('Gagal mengakses kamera: ' + err.message + '\n\nPastikan izin kamera sudah diberikan di browser Anda.');
    }
}

async function mulaiDeteksi() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('overlay');
    const badge = document.getElementById('detectionText');
    const btnAmbil = document.getElementById('btnAmbilFoto');
    
    detectionInterval = setInterval(async () => {
        if (video.readyState !== 4) return;
        const dims = faceapi.matchDimensions(canvas, video, true);
        
        // OPTIMASI CEPAT: hanya deteksi wajah tunggal & landmark, tanpa descriptor berat saat preview
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks(true);

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (detection) {
            faceDetected = true;
            const resized = faceapi.resizeResults(detection, dims);
            const box = resized.detection.box;
            ctx.strokeStyle = '#10b981'; 
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);
            faceapi.draw.drawFaceLandmarks(canvas, resized);

            badge.className = 'badge bg-success fs-6 px-3 py-2';
            badge.textContent = '✅ Wajah Terdeteksi — Siap Didaftarkan';
            btnAmbil.disabled = false;
        } else {
            faceDetected = false;
            badge.className = 'badge bg-secondary fs-6 px-3 py-2';
            badge.textContent = '🔍 Arahkan Wajah ke Kamera...';
            btnAmbil.disabled = true;
        }
    }, 250);
}

async function ambilFotoDanDaftarkan() {
    if (!faceDetected) { 
        alert('Wajah belum terdeteksi. Pastikan wajah Anda terlihat jelas di kamera.'); 
        return; 
    }

    const video = document.getElementById('video');
    const btn = document.getElementById('btnAmbilFoto');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Sidik Wajah...';

    try {
        clearInterval(detectionInterval);

        // Ekstrak 128-float descriptor sekali saja saat tombol diklik
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks(true)
            .withFaceDescriptor();

        if (!detection) {
            throw new Error('Wajah tidak terdeteksi saat pengambilan foto. Pastikan posisi stabil dan coba lagi.');
        }

        const descriptor = Array.from(detection.descriptor);
        const token = document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch('{{ route("portal.save-descriptor") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': token 
            },
            body: JSON.stringify({ face_descriptor: JSON.stringify(descriptor) }),
        });

        const result = await response.json();
        const hasilDiv = document.getElementById('hasilRegistrasi');
        hasilDiv.style.display = 'block';

        if (result.success) {
            // Hentikan kamera
            if (activeStream) {
                activeStream.getTracks().forEach(track => track.stop());
            }

            // Tampilkan status sukses di tombol
            btn.className = 'btn btn-success btn-lg flex-grow-1';
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Berhasil Didaftarkan!';

            // Tampilkan overlay sukses
            document.getElementById('setupSuccessOverlay').style.display = 'flex';
            document.getElementById('detectionBadge').style.display = 'none';

            hasilDiv.innerHTML = `
                <div class="alert alert-success border-0 rounded-3 shadow-sm py-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-shield-fill-check fs-2 text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-1">${result.message}</h6>
                            <div class="small text-muted">Mengalihkan otomatis ke Portal Absensi dalam 2 detik...</div>
                        </div>
                    </div>
                </div>`;

            // Auto redirect setelah 2 detik
            setTimeout(() => {
                window.location.href = '{{ route("portal.index") }}';
            }, 2000);

        } else {
            hasilDiv.innerHTML = '<div class="alert alert-danger border-0 rounded-3"><i class="bi bi-x-circle me-2"></i>' + result.message + '</div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-person-bounding-box me-2"></i>Ambil Foto dan Daftarkan Wajah';
            mulaiDeteksi();
        }
    } catch (err) {
        const hasilDiv = document.getElementById('hasilRegistrasi');
        hasilDiv.style.display = 'block';
        hasilDiv.innerHTML = '<div class="alert alert-danger border-0 rounded-3"><i class="bi bi-x-circle me-2"></i>' + err.message + '</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-person-bounding-box me-2"></i>Coba Lagi';
        mulaiDeteksi();
    }
}

document.addEventListener('DOMContentLoaded', loadModels);
</script>
@endpush
