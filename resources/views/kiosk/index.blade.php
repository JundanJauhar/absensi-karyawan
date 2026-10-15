<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kiosk Absensi</title>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        * {
            box-sizing: border-box
        }

        html,
        body {
            margin: 0;
            width: 100%;
            min-height: 100%;
            background: #07111f;
            color: #f8fafc;
            font-family: Segoe UI, Arial, sans-serif
        }

        main {
            width: 100%;
            margin: 0;
            padding: 0
        }

        .camera {
            position: relative;
            width: 100%;
            height: 78vh;
            min-height: 420px;
            background: #000;
            overflow: hidden
        }

        .camera video,
        .camera canvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1)
        }

        .camera canvas {
            position: absolute;
            inset: 0
        }

        .status {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 20px;
            text-align: center;
            font-size: 20px;
            padding: 0 16px
        }

        .name {
            position: absolute;
            z-index: 2;
            color: #22c55e;
            font-size: 20px;
            font-weight: 700;
            text-shadow: 0 1px 4px #000
        }

        .retry {
            display: none;
            position: absolute;
            left: 50%;
            bottom: 70px;
            transform: translateX(-50%);
            z-index: 4;
            padding: 10px 18px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            cursor: pointer
        }

        .recent {
            width: 100%;
            margin: 0;
            background: #0f1d31;
            padding: 18px 24px
        }

        .recent h2 {
            margin: 0 0 12px
        }

        .recent table {
            width: 100%;
            border-collapse: collapse
        }

        .recent th,
        .recent td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #26364e
        }

        .muted {
            color: #94a3b8
        }

        @media(max-width:600px) {
            .camera {
                height: 68vh;
                min-height: 360px
            }

            .recent {
                padding: 14px 10px;
                overflow-x: auto
            }

            .recent table {
                min-width: 560px
            }
        }
    </style>
</head>

<body>
    <main>
        <div class="camera"><video id="video" autoplay muted playsinline></video><canvas id="overlay"></canvas>
            <div id="name" class="name"></div><button id="retry" class="retry" type="button" onclick="start()">Coba Lagi</button>
            <div id="status" class="status"></div>
        </div>
        <section class="recent">
            <h2>10 Absensi Terbaru</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                    </tr>
                </thead>
                <tbody id="recent"></tbody>
            </table>
        </section>
    </main>
    <script>
        const employees = @json($employeeProfiles);
        const MODEL_URL = '{{ asset("models") }}',
            video = document.getElementById('video'),
            canvas = document.getElementById('overlay');
        let busy = false,
            lastId = null,
            lastAt = 0;
        const distance = (a, b) => Math.sqrt(a.reduce((s, v, i) => s + (v - b[i]) ** 2, 0));
        async function start() {
            const status = document.getElementById('status'),
                retry = document.getElementById('retry');
            retry.style.display = 'none';
            try {
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    throw new Error('Kamera browser hanya dapat digunakan melalui HTTPS. Buka kiosk di localhost pada komputer server atau gunakan alamat HTTPS.');
                }
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser ini tidak menyediakan akses kamera.');
                }
                await Promise.all([faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL), faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL), faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)]);
                if (video.srcObject) video.srcObject.getTracks().forEach(track => track.stop());
                video.srcObject = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    },
                    audio: false
                });
                await video.play();
                scan();
                refresh();
            } catch (error) {
                console.error('Kiosk camera error:', error);
                status.textContent = error.message || 'Kamera tidak dapat dibuka. Periksa izin kamera browser.';
                retry.style.display = 'block';
            }
        }
        async function scan() {
            try {
                if (video.readyState < 2) {
                    setTimeout(scan, 500);
                    return;
                }
                const d = await faceapi.detectAllFaces(video, new faceapi.SsdMobilenetv1Options({
                    minConfidence: .6
                })).withFaceLandmarks(true).withFaceDescriptors();
                const dims = faceapi.matchDimensions(canvas, video, true);
                const out = faceapi.resizeResults(d, dims);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById('name').textContent = '';
                out.forEach(x => {
                    const candidates = employees.filter(e => Array.isArray(e.descriptor) && e.descriptor.length === 128);
                    const best = candidates.map(e => ({
                        e,
                        score: distance(Array.from(x.descriptor), e.descriptor)
                    })).sort((a, b) => a.score - b.score)[0];
                    if (best && best.score < .55) {
                        const b = x.detection.box;
                        const label = document.getElementById('name');
                        label.style.left = `${b.x}px`;
                        label.style.top = `${Math.max(4,b.y-30)}px`;
                        label.textContent = best.e.name;
                        faceapi.draw.drawDetections(canvas, [x]);
                        if (!busy && (lastId !== best.e.id || Date.now() - lastAt > 10000)) {
                            busy = true;
                            lastId = best.e.id;
                            lastAt = Date.now();
                            const photoCanvas = document.createElement('canvas');
                            photoCanvas.width = video.videoWidth || 640;
                            photoCanvas.height = video.videoHeight || 480;
                            photoCanvas.getContext('2d').drawImage(video, 0, 0, photoCanvas.width, photoCanvas.height);
                            const photoBase64 = photoCanvas.toDataURL('image/jpeg', 0.85);
                            fetch('{{ route("kiosk.attendance") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify({
                                    karyawan_id: best.e.id,
                                    confidence: Math.max(0, 1 - best.score),
                                    photo_base64: photoBase64
                                })
                            }).then(r => r.json()).then(r => {
                                if (r.success) {
                                    document.getElementById('status').textContent = `${r.event}: ${r.employee.nama} — ${r.divisi} — ${r.time}`;
                                    refresh();
                                    setTimeout(() => document.getElementById('status').textContent = '', 3000);
                                }
                            }).finally(() => setTimeout(() => busy = false, 2500));
                        }
                    }
                });
                setTimeout(scan, 180);
            } catch (error) {
                console.error('Face detection error:', error);
                setTimeout(scan, 1000);
            }
        }
        async function refresh() {
            const r = await fetch('{{ route("kiosk.recent") }}');
            const rows = await r.json();
            document.getElementById('recent').innerHTML = rows.map(x => `<tr><td>${x.name}</td><td>${x.division}</td><td>${x.in||'-'}</td><td>${x.out||'-'}</td></tr>`).join('');
        }
        start().catch(() => document.getElementById('status').textContent = 'Kamera atau model AI tidak tersedia');
    </script>
</body>

</html>