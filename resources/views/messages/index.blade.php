@extends('vendor.layout.master')
@section('title', 'Kirim Pesan')

@section('content')


    <div class="container py-4">
        <h4 class="mb-3">Kirim Pesan</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="sendForm" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Pilih Device</label>
                        <select name="device_id" id="device_id" class="form-select" required>
                            <option value="">-- pilih --</option>
                            @foreach ($devices as $d)
                                @php
                                    $sub = $d->activeSubscription;
                                    // kalau admin, allowAttachment = true
                                    $allowAttachment =
                                        $isAdmin ?? false
                                            ? true
                                            : $sub && !$sub->is_trial && optional($sub->plan)->allow_attachment;
                                @endphp
                                <option value="{{ $d->id }}" data-allow-attachment="{{ $allowAttachment ? 1 : 0 }}">
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="target_type" class="form-label">Kirim ke</label>
                        <select name="target_type" id="target_type" class="form-select" required>
                            <option value="numbers" selected>Nomor</option>
                            <option value="groups">Grup</option>
                        </select>
                    </div>





                    <div id="numbers_wrap" class="mb-3">
                        <label class="form-label">Nomor Tujuan (pisah koma)</label>
                        <textarea name="numbers" class="form-control" rows="3" placeholder="62812...,62877..."></textarea>
                    </div>

                    <div id="groups_wrap" class="mb-3 d-none">
                        <label class="form-label">Pilih Grup</label>
                        <div id="groups_list" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                            <!-- checkbox grup akan dimuat di sini -->
                        </div>
                        <div class="form-text">Pilih satu atau lebih grup.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Pesan</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran (opsional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control" disabled>
                        <div id="attachmentHelp" class="form-text text-muted">Lampiran nonaktif untuk plan tertentu.</div>
                    </div>

                    <button class="btn btn-primary" type="submit">Kirim</button>
                </form>
            </div>
        </div>

        <div class="mt-4" id="resultBox" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" id="summaryText"></h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                    <th>Message ID / Error</th>
                                </tr>
                            </thead>
                            <tbody id="resultRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen
            const form = document.getElementById('sendForm');
            const deviceSelect = document.getElementById('device_id');
            const attachmentInput = document.getElementById('attachment');
            const attachmentHelp = document.getElementById('attachmentHelp');
            const numbersWrap = document.getElementById('numbers_wrap');
            const groupsWrap = document.getElementById('groups_wrap');
            const groupsSelect = document.getElementById('groups');

            // Bisa radio (tt_numbers/tt_groups) atau dropdown (target_type)
            const ttNumbers = document.getElementById('tt_numbers');
            const ttGroups = document.getElementById('tt_groups');
            const targetTypeSelect = document.getElementById('target_type');

            // Utility: ambil tipe target saat ini
            function getTargetType() {
                if (targetTypeSelect) return targetTypeSelect.value; // dropdown
                if (ttNumbers && ttNumbers.checked) return 'numbers'; // radio
                if (ttGroups && ttGroups.checked) return 'groups'; // radio
                return 'numbers';
            }

            function toggleTargetUI() {
                const type = getTargetType();
                if (!numbersWrap || !groupsWrap) return;

                if (type === 'numbers') {
                    numbersWrap.classList.remove('d-none');
                    groupsWrap.classList.add('d-none');
                } else {
                    numbersWrap.classList.add('d-none');
                    groupsWrap.classList.remove('d-none');
                    loadGroups();
                }
            }

            function applyAttachmentPolicy() {
                if (!deviceSelect || !attachmentInput || !attachmentHelp) return;
                const opt = deviceSelect.options[deviceSelect.selectedIndex];
                const allow = opt ? (opt.getAttribute('data-allow-attachment') === '1') : false;

                attachmentInput.disabled = !allow;
                attachmentHelp.textContent = allow ?
                    'Lampiran diizinkan oleh plan Anda.' :
                    'Lampiran nonaktif untuk plan ini.';
                attachmentHelp.classList.toggle('text-muted', !allow);
            }

            // --- loadGroups dengan AbortController supaya tidak race ---
            let groupsAbort;
            async function loadGroups() {
                if (!deviceSelect) return;
                const deviceId = deviceSelect.value;
                if (!deviceId) return;

                const container = document.getElementById('groups_list');
                container.innerHTML = '<div class="text-muted">Memuat grup...</div>';
                // batalkan request sebelumnya (kalau ada)
                if (groupsAbort) groupsAbort.abort();
                groupsAbort = new AbortController();
                try {
                    // 🔑 Gunakan route RELATIF (param ke-3 = false) agar tidak mixed content
                    const basePath = "{{ route('messages.groups', [], false) }}"; // -> "/messages/groups"
                    const url = `${basePath}?device_id=${encodeURIComponent(deviceId)}`;

                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: groupsAbort.signal
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        container.innerHTML = '<div class="text-danger">Gagal memuat grup</div>';
                        return;
                    }

                    if (!Array.isArray(data.groups) || data.groups.length === 0) {
                        container.innerHTML = '<div class="text-muted">Tidak ada grup di device ini</div>';
                        return;
                    }

                    container.innerHTML = '';
                    data.groups.forEach((g, idx) => {
                        const id = 'group_' + idx;
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('form-check');
                        wrapper.innerHTML = `
          <input class="form-check-input" type="checkbox"
                 name="groups[]" id="${id}" value="${g.wid}">
          <label class="form-check-label" for="${id}">
            ${g.name}
          </label>
        `;
                        container.appendChild(wrapper);
                    });

                } catch (e) {
                    if (e.name === 'AbortError') return; // diabaikan kalau dibatalkan
                    console.error(e);
                    container.innerHTML = '<div class="text-danger">Gagal memuat grup</div>';
                }
            }

            // Listener perubahan tipe target
            if (ttNumbers) ttNumbers.addEventListener('change', toggleTargetUI);
            if (ttGroups) ttGroups.addEventListener('change', toggleTargetUI);
            if (targetTypeSelect) targetTypeSelect.addEventListener('change', toggleTargetUI);

            // Listener device
            if (deviceSelect) {
                deviceSelect.addEventListener('change', () => {
                    applyAttachmentPolicy();
                    if (getTargetType() === 'groups') loadGroups();
                });
            }

            // Init
            applyAttachmentPolicy();
            toggleTargetUI();

            // Submit
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const fd = new FormData(form);

                    // SweetAlert guard (kalau belum di-include)
                    const swal = window.Swal || window.swal || null;
                    if (swal) {
                        swal.fire({
                            title: 'Mengirim...',
                            allowOutsideClick: false,
                            didOpen: () => swal.showLoading()
                        });
                    }

                    try {
                        const postUrl =
                            "{{ route('messages.store', [], false) }}"; // => "/messages/send"
                        const res = await fetch(postUrl, {

                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        const data = await res.json().catch(() => ({}));

                        if (!res.ok || data.success === false) {
                            swal ? swal.fire('Gagal', (data && data.message) || 'Gagal mengirim pesan.',
                                    'error') :
                                alert((data && data.message) || 'Gagal mengirim pesan.');
                            return;
                        }

                        swal ? swal.fire('Selesai', data.summary, 'success') :
                            alert(data.summary);

                        // tampilkan ringkasan
                        const box = document.getElementById('resultBox');
                        const sum = document.getElementById('summaryText');
                        const tbody = document.getElementById('resultRows');

                        if (box && sum && tbody) {
                            box.style.display = '';
                            sum.textContent = data.summary || '';
                            tbody.innerHTML = '';
                            (data.results || []).forEach(r => {
                                const tr = document.createElement('tr');
                                tr.innerHTML =
                                    `<td>${r.to}</td><td>${r.status}</td><td>${r.messageId || r.error || '-'}</td>`;
                                tbody.appendChild(tr);
                            });
                        }

                    } catch (err) {
                        console.error(err);
                        swal ? swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error') :
                            alert('Terjadi kesalahan jaringan.');
                    }
                });
            }
        });
    </script>

@endsection
