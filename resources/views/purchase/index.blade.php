{{-- =============================================
     FILE: resources/views/purchase/index.blade.php
     =============================================
     Halaman ini menampilkan daftar semua data Purchase.

     FITUR UTAMA:
     1. Tabel data purchase dengan pagination
     2. Tombol Search berdasarkan nomor nota
     3. Tombol Edit & Delete yang memerlukan PASSWORD ATASAN
     4. SweetAlert untuk notifikasi sukses/gagal

     ALUR EDIT:
     Klik Edit → Modal Password → Cek via AJAX → Jika benar → Redirect ke halaman edit

     ALUR DELETE:
     Klik Delete → Modal Password → Cek via AJAX → Jika benar → Konfirmasi SweetAlert → Hapus data
--}}

@extends('layout.master')

@section('title', 'Purchases')

@section('menu')
    @include('layout.menu')
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Header Section: Judul halaman + tombol New Purchase --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 font-weight-bold mb-0">Purchases</h2>
                <p class="text-muted small mb-0">Manage incoming goods and distributor invoices</p>
            </div>
            <div>
                <a href="{{ route('purchase.create') }}" class="btn bg-gradient-dark mb-0">
                    <i class="fas fa-plus me-1"></i> New Purchase
                </a>
            </div>
        </div>

        {{-- Filter/Search Section: Kolom pencarian berdasarkan nomor nota --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('purchase.index') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 bg-light"
                                    placeholder="Search Note Number..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            @if(request('search'))
                                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary w-100 mb-0">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section: Tabel utama yang menampilkan data purchase --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-4 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="5%">#</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Note Number</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Distributor</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Price</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop semua data purchase --}}
                            @forelse($purchases as $index => $purchase)
                                <tr>
                                    {{-- Nomor urut (berdasarkan halaman pagination) --}}
                                    <td class="ps-4 text-secondary text-xs font-weight-bold">
                                        {{ $purchases->firstItem() + $index }}
                                    </td>

                                    {{-- Kolom Nomor Nota --}}
                                    <td>
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div class="avatar avatar-sm me-3 bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-file-invoice text-white"></i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm font-weight-bold text-primary">
                                                    {{ $purchase->note_number }}
                                                </h6>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Tanggal --}}
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $purchase->purchase_date->format('d M Y') }}
                                        </p>
                                    </td>

                                    {{-- Kolom Distributor --}}
                                    <td>
                                        @if($purchase->distributor)
                                            <span class="text-xs font-weight-bold text-dark">
                                                <i class="fas fa-building me-1 text-secondary"></i>
                                                {{ $purchase->distributor->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-xxs">General / No Distributor</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Total Harga --}}
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0 text-success">
                                            Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                                        </p>
                                    </td>

                                    {{-- Kolom Actions: Tombol View, Edit, dan Delete --}}
                                    <td class="align-middle text-center">
                                        {{-- Tombol Detail/View (langsung ke halaman show) --}}
                                        <a href="{{ route('purchase.show', $purchase->id) }}"
                                           class="text-secondary font-weight-bold text-xs me-2"
                                           data-bs-toggle="tooltip" title="View Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>

                                        {{-- =============================================
                                             TOMBOL EDIT (Memerlukan Password Atasan)
                                             =============================================
                                             Saat diklik, akan muncul modal SweetAlert
                                             meminta password atasan. Jika benar, user
                                             diarahkan ke halaman edit. --}}
                                        <a href="#" onclick="askPasswordForEdit(event, '{{ $purchase->id }}')"
                                           class="text-secondary font-weight-bold text-xs me-2"
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>

                                        {{-- =============================================
                                             TOMBOL DELETE (Memerlukan Password Atasan)
                                             =============================================
                                             Saat diklik, akan muncul modal SweetAlert
                                             meminta password atasan. Jika benar, tampilkan
                                             konfirmasi "Are you sure want to delete?"
                                             Jika user konfirmasi, data dihapus. --}}
                                        <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-inline"
                                            id="delete-form-{{ $purchase->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" onclick="askPasswordForDelete(event, '{{ $purchase->id }}', '{{ $purchase->note_number }}')"
                                               class="text-secondary font-weight-bold text-xs"
                                               data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                {{-- Tampilan jika data kosong --}}
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <i class="fas fa-shopping-cart fa-3x text-secondary mb-3 opacity-5"></i>
                                            <p class="text-sm text-secondary mb-0">No purchase history found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination Links --}}
            @if ($purchases->hasPages())
                <div class="card-footer border-0 d-flex justify-content-center pt-3 pb-3">
                    {{ $purchases->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Library SweetAlert2 untuk popup/modal --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /**
         * =============================================
         * NOTIFIKASI SUKSES (Session Flash Message)
         * =============================================
         * Jika ada session 'success' dari controller (redirect()->with('success',...)),
         * tampilkan popup hijau selama 3 detik
         */
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        /**
         * =============================================
         * FUNGSI: askPasswordForEdit()
         * =============================================
         * Dipanggil saat user klik tombol EDIT.
         *
         * ALUR:
         * 1. Tampilkan popup SweetAlert dengan input password
         * 2. User isi password → kirim ke server via AJAX (fetch)
         * 3. Server cek di PurchaseController@checkBossPassword
         * 4. Jika password BENAR (success: true):
         *    - Tampilkan popup "Nice! Your password is correct!"
         *    - Lalu redirect ke halaman edit: /purchase/{id}/edit
         * 5. Jika password SALAH (success: false):
         *    - Tampilkan popup error "Wrong password!"
         *
         * @param {Event} event - Event dari klik link
         * @param {string} id - ID purchase yang mau diedit
         */
        function askPasswordForEdit(event, id) {
            // Cegah link default (href="#")
            event.preventDefault();

            // Tampilkan modal input password menggunakan SweetAlert
            Swal.fire({
                title: 'Password required!',                    // Judul modal
                text: 'Write your boss\'s password:',           // Deskripsi
                input: 'password',                              // Tipe input = password (tersembunyi)
                inputPlaceholder: 'Enter password',             // Placeholder
                showCancelButton: true,                         // Tampilkan tombol Cancel
                confirmButtonText: 'OK',                        // Teks tombol konfirmasi
                confirmButtonColor: '#e91e8c',                  // Warna tombol OK (pink)
                cancelButtonText: 'CANCEL',                     // Teks tombol batal
                // Validasi: password tidak boleh kosong
                inputValidator: (value) => {
                    if (!value) {
                        return 'Password cannot be empty!';
                    }
                }
            }).then((result) => {
                // Jika user menekan tombol OK (bukan Cancel)
                if (result.isConfirmed) {
                    // Kirim password ke server via AJAX (POST request)
                    fetch("{{ route('purchase.check-boss-password') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"    // Token CSRF Laravel (keamanan)
                        },
                        body: JSON.stringify({ password: result.value })  // Kirim password sebagai JSON
                    })
                    .then(res => res.json())     // Parse response jadi JSON
                    .then(data => {
                        if (data.success) {
                            // Password BENAR → Tampilkan popup sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Nice!',
                                text: 'Your password is correct!',
                                confirmButtonColor: '#e91e8c',
                            }).then(() => {
                                // Setelah user klik OK → Redirect ke halaman edit
                                window.location.href = '/purchase/' + id + '/edit';
                            });
                        } else {
                            // Password SALAH → Tampilkan popup error
                            Swal.fire({
                                icon: 'error',
                                title: 'Wrong Password!',
                                text: 'The password you entered is incorrect.',
                                confirmButtonColor: '#d33',
                            });
                        }
                    });
                }
            });
        }

        /**
         * =============================================
         * FUNGSI: askPasswordForDelete()
         * =============================================
         * Dipanggil saat user klik tombol DELETE.
         *
         * ALUR:
         * 1. Tampilkan popup SweetAlert dengan input password
         * 2. User isi password → kirim ke server via AJAX
         * 3. Jika password BENAR:
         *    - Tampilkan popup konfirmasi "Are you sure want to delete?"
         *    - Jika user pilih "YES, DELETE IT!" → form delete di-submit
         * 4. Jika password SALAH:
         *    - Tampilkan popup error
         *
         * @param {Event} event - Event dari klik link
         * @param {string} id - ID purchase yang mau dihapus
         * @param {string} note - Nomor nota (untuk info di popup)
         */
        function askPasswordForDelete(event, id, note) {
            // Cegah link default
            event.preventDefault();

            // Tampilkan modal input password
            Swal.fire({
                title: 'Password required!',
                text: 'Write your boss\'s password:',
                input: 'password',
                inputPlaceholder: 'Enter password',
                showCancelButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#e91e8c',
                cancelButtonText: 'CANCEL',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Password cannot be empty!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim password ke server via AJAX
                    fetch("{{ route('purchase.check-boss-password') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ password: result.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Password BENAR → Tampilkan konfirmasi hapus
                            Swal.fire({
                                title: 'Are you sure want to delete?',
                                text: "Your will not be able to recover this data!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#e91e8c',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'YES, DELETE IT!',
                                cancelButtonText: 'CANCEL'
                            }).then((deleteResult) => {
                                if (deleteResult.isConfirmed) {
                                    // User konfirmasi hapus → Submit form DELETE
                                    document.getElementById('delete-form-' + id).submit();
                                }
                            });
                        } else {
                            // Password SALAH → Tampilkan error
                            Swal.fire({
                                icon: 'error',
                                title: 'Wrong Password!',
                                text: 'The password you entered is incorrect.',
                                confirmButtonColor: '#d33',
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
