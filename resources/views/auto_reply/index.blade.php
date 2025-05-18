@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <div class="container">
        <h3 class="mb-3">Manajemen Auto Reply</h3>

        <!-- Button Tambah -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="openModal()">+ Tambah
            Auto Reply</button>

        <!-- Tabel -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Response</th>
                    <th>Type</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($autoReplies as $reply)
                    <tr>
                        <td>{{ $reply->keyword }}</td>
                        <td>{{ $reply->response }}</td>
                        <td>{{ $reply->type }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                onclick='openModal(@json($reply))'>Edit</button>
                            <form action="{{ route('auto-reply.destroy', $reply->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus?')" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="formModal" action="{{ route('auto-reply.store') }}">
                @csrf
                <input type="hidden" name="_method" id="_method" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFormLabel">Tambah Auto Reply</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Keyword</label>
                            <input type="text" class="form-control" name="keyword" id="keyword" required>
                        </div>
                        <div class="mb-3">
                            <label>Response</label>
                            <textarea class="form-control" name="response" id="response" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Type</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="text">Text</option>
                                <option value="sticker">Sticker</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Script untuk isi modal -->
    <script>
        function openModal(data = null) {
            const form = document.getElementById("formModal");
            const method = document.getElementById("_method");
            const modalLabel = document.getElementById("modalFormLabel");

            if (data) {
                form.action = "/admin/auto-reply/" + data.id;
                method.value = "PUT";
                modalLabel.innerText = "Edit Auto Reply";

                document.getElementById("keyword").value = data.keyword;
                document.getElementById("response").value = data.response;
                document.getElementById("type").value = data.type;
            } else {
                form.action = "{{ route('auto-reply.store') }}";
                method.value = "POST";
                modalLabel.innerText = "Tambah Auto Reply";

                document.getElementById("keyword").value = "";
                document.getElementById("response").value = "";
                document.getElementById("type").value = "text";
            }

            // Ini baris penting agar modal muncul
            var modal = new bootstrap.Modal(document.getElementById('modalForm'));
            modal.show();
        }
    </script>
@endsection
