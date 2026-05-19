{{-- =============================================
     FILE: resources/views/purchase/edit.blade.php
     =============================================
     Halaman ini menampilkan FORM EDIT untuk data Purchase.

     FUNGSI:
     - Menampilkan data purchase yang sudah ada di form
     - User bisa mengubah: nomor nota, tanggal, distributor, dan daftar barang
     - Saat submit, data dikirim ke PurchaseController@update

     CATATAN:
     - Sebelum sampai ke halaman ini, user sudah melewati
       verifikasi password atasan (di halaman index)
     - Form menggunakan method PUT (via @method('PUT'))
       karena ini adalah operasi UPDATE, bukan CREATE
--}}

@extends('layout.master')

@section('title', 'Edit Purchase')

@section('menu')
    @include('layout.menu')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border-0 shadow-sm">
                    {{-- Header Card --}}
                    <div class="card-header pb-0 bg-white">
                        <h6 class="font-weight-bolder text-primary">Edit Purchases Data</h6>
                        <p class="text-xs text-muted">Update purchase data and items below.</p>
                    </div>

                    <div class="card-body">
                        {{-- Tampilkan Error Validasi jika ada --}}
                        @if($errors->any())
                            <div class="alert alert-danger text-white text-sm" role="alert">
                                <strong>Oops!</strong> Please fix the errors below.
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- =============================================
                             FORM EDIT PURCHASE
                             =============================================
                             method="POST" + @method('PUT') = Ini cara Laravel
                             mengirim request PUT untuk update data.
                             action mengarah ke route purchase.update --}}
                        <form action="{{ route('purchase.update', $purchase->id) }}" method="POST" id="purchase-form">
                            @csrf
                            @method('PUT') {{-- Penting! Ini mengubah POST jadi PUT --}}

                            {{-- =============================================
                                 BAGIAN 1: HEADER NOTA (Invoice Info)
                                 =============================================
                                 Menampilkan data header: No. Nota, Tanggal, Distributor
                                 Data diisi dari $purchase (data lama) --}}
                            <div class="row mb-4">
                                {{-- Input Nomor Nota --}}
                                <div class="col-md-4 mb-3 position-relative">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Invoice No</label>
                                    <input type="text" class="form-control @error('note_number') is-invalid @enderror"
                                        id="note_number" name="note_number"
                                        value="{{ old('note_number', $purchase->note_number) }}"
                                        placeholder="Ex: INV-2023-001" required>
                                    <div id="note_error" class="invalid-feedback text-xs">
                                        Note Number already exists!
                                    </div>
                                </div>

                                {{-- Input Tanggal Purchase --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Invoice Date</label>
                                    <input type="date" class="form-control" name="purchase_date"
                                        value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                                </div>

                                {{-- Dropdown Distributor --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Distributor</label>
                                    <select class="form-select" name="distributor_id">
                                        <option value="">-- Select Distributor (Optional) --</option>
                                        @foreach ($distributors as $distributor)
                                            {{-- Pilih distributor yang sama dengan data lama --}}
                                            <option value="{{ $distributor->id }}"
                                                {{ old('distributor_id', $purchase->distributor_id) == $distributor->id ? 'selected' : '' }}>
                                                {{ $distributor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="horizontal dark my-4">

                            {{-- =============================================
                                 BAGIAN 2: DAFTAR BARANG (Items List)
                                 =============================================
                                 Menampilkan tabel barang yang bisa diedit.
                                 Data barang lama dimuat dari $purchase->details --}}
                            <h6 class="font-weight-bolder text-dark mb-3">Items List</h6>

                            <div class="table-responsive">
                                <table class="table align-items-center mb-0" id="items-table">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" width="30%">Book</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" width="10%">Quantity</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" width="15%">Purchase Price</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" width="10%">Margin %</th>
                                            <th class="text-uppercase text-success text-xxs font-weight-bolder opacity-7 ps-2" width="15%">New Sell Price</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" width="15%">SubTotal</th>
                                            <th class="text-secondary opacity-7" width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        {{-- =============================================
                                             LOOP DATA BARANG DARI DATABASE
                                             =============================================
                                             Setiap detail barang dari purchase lama
                                             ditampilkan sebagai baris di tabel.
                                             Jika tidak ada detail (purchase baru), tampilkan 1 baris kosong --}}
                                        @forelse($purchase->details as $index => $detail)
                                        <tr class="item-row">
                                            <td>
                                                {{-- Dropdown Pilih Produk --}}
                                                <select class="form-select form-select-sm product-select" name="items[{{ $index }}][product_id]" required>
                                                    <option value="" disabled>Choose Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" data-current-price="{{ $product->price }}"
                                                            {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} (Stok: {{ $product->stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-xs text-muted old-price-display d-none">Current Sell Price: Rp <span>0</span></small>
                                            </td>
                                            <td>
                                                {{-- Input Jumlah Beli --}}
                                                <input type="number" class="form-control form-control-sm qty-input"
                                                    name="items[{{ $index }}][quantity]" min="1"
                                                    value="{{ old("items.$index.quantity", $detail->purchase_amount) }}" required>
                                            </td>
                                            <td>
                                                {{-- Input Harga Beli --}}
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs">Rp</span>
                                                    <input type="number" class="form-control price-input"
                                                        name="items[{{ $index }}][price]" min="0"
                                                        value="{{ old("items.$index.price", $detail->purchase_price) }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- Input Margin Keuntungan (%) --}}
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control margin-input"
                                                        name="items[{{ $index }}][margin]" min="0" max="500"
                                                        value="{{ old("items.$index.margin", $detail->selling_margin) }}" required>
                                                    <span class="input-group-text text-xs">%</span>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- Estimasi Harga Jual (Readonly, dihitung otomatis oleh JavaScript) --}}
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs text-success font-weight-bold">Rp</span>
                                                    <input type="text" class="form-control new-sell-price bg-white text-success font-weight-bold" value="0" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- Subtotal (Readonly, dihitung otomatis oleh JavaScript) --}}
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs">Rp</span>
                                                    <input type="text" class="form-control subtotal-input bg-white" value="0" readonly>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{-- Tombol hapus baris (disabled jika cuma 1 baris) --}}
                                                <button type="button" class="btn btn-link text-danger px-3 mb-0 remove-row"
                                                    {{ count($purchase->details) <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        {{-- Jika tidak ada detail, tampilkan 1 baris kosong --}}
                                        <tr class="item-row">
                                            <td>
                                                <select class="form-select form-select-sm product-select" name="items[0][product_id]" required>
                                                    <option value="" disabled selected>Choose Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" data-current-price="{{ $product->price }}">
                                                            {{ $product->name }} (Stok: {{ $product->stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-xs text-muted old-price-display d-none">Current Sell Price: Rp <span>0</span></small>
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm qty-input" name="items[0][quantity]" min="1" value="1" required></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs">Rp</span>
                                                    <input type="number" class="form-control price-input" name="items[0][price]" min="0" value="0" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control margin-input" name="items[0][margin]" min="0" max="500" value="10" required>
                                                    <span class="input-group-text text-xs">%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs text-success font-weight-bold">Rp</span>
                                                    <input type="text" class="form-control new-sell-price bg-white text-success font-weight-bold" value="0" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text text-xs">Rp</span>
                                                    <input type="text" class="form-control subtotal-input bg-white" value="0" readonly>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-link text-danger px-3 mb-0 remove-row" disabled>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Tombol Tambah Barang + Display Grand Total --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary mb-0" id="add-row-btn">
                                    <i class="fas fa-plus me-1"></i> Add Another Item
                                </button>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-3">Total Payment:</h6>
                                    <h4 class="text-primary font-weight-bolder mb-0" id="grand-total-display">Rp 0</h4>
                                    <input type="hidden" name="total_price" id="grand-total-input" value="0">
                                </div>
                            </div>

                            {{-- Tombol Cancel dan Edit This Purchase --}}
                            <div class="d-flex justify-content-end mt-5">
                                <a href="{{ route('purchase.index') }}" class="btn btn-light m-0 me-2">CANCEL</a>
                                <button type="submit" id="btn-submit" class="btn bg-gradient-danger m-0">
                                    <i class="fas fa-save me-1"></i> EDIT THIS PURCHASE
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         JAVASCRIPT: LOGIKA KALKULATOR & DYNAMIC ROWS
         =============================================
         Script ini menangani:
         1. Kalkulasi otomatis subtotal & grand total
         2. Estimasi harga jual berdasarkan margin
         3. Tambah/Hapus baris barang secara dinamis
         4. Cek nomor nota unik via AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- VARIABEL UTAMA ---
            let itemIndex = {{ count($purchase->details) > 0 ? count($purchase->details) : 1 }};
            const itemsBody = document.getElementById('items-body');
            const addRowBtn = document.getElementById('add-row-btn');
            const grandTotalDisplay = document.getElementById('grand-total-display');
            const grandTotalInput = document.getElementById('grand-total-input');

            // Fungsi format angka ke Rupiah (Rp 1.000.000)
            const formatRupiah = (num) => new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0
            }).format(num);

            /**
             * =============================================
             * FUNGSI: updateCalculations()
             * =============================================
             * Dipanggil setiap kali ada perubahan input (qty, harga, margin).
             * Menghitung ulang: subtotal per baris, harga jual baru, dan grand total.
             */
            function updateCalculations() {
                let grandTotal = 0;
                const rows = document.querySelectorAll('.item-row');

                rows.forEach(row => {
                    // Ambil nilai dari input di setiap baris
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const buyPrice = parseFloat(row.querySelector('.price-input').value) || 0;
                    const margin = parseFloat(row.querySelector('.margin-input').value) || 0;

                    // 1. Hitung Subtotal (Qty × Harga Beli)
                    const subtotal = qty * buyPrice;
                    row.querySelector('.subtotal-input').value = subtotal.toLocaleString('id-ID');
                    grandTotal += subtotal;

                    // 2. Hitung Estimasi Harga Jual Baru (Harga Beli + Margin%)
                    // Rumus: Harga Beli + (Harga Beli × Margin / 100)
                    const marginValue = buyPrice * (margin / 100);
                    const newSellPrice = buyPrice + marginValue;
                    row.querySelector('.new-sell-price').value = newSellPrice.toLocaleString('id-ID');
                });

                // Update tampilan Grand Total
                grandTotalDisplay.innerText = formatRupiah(grandTotal);
                grandTotalInput.value = grandTotal;
            }

            /**
             * FUNGSI: handleProductChange()
             * Saat dropdown produk berubah, tampilkan harga jual lama produk tsb.
             */
            function handleProductChange(selectElement) {
                const row = selectElement.closest('.item-row');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const oldPrice = selectedOption.getAttribute('data-current-price');

                const display = row.querySelector('.old-price-display');
                const priceSpan = display.querySelector('span');

                if(oldPrice) {
                    priceSpan.innerText = parseFloat(oldPrice).toLocaleString('id-ID');
                    display.classList.remove('d-none');
                } else {
                    display.classList.add('d-none');
                }
            }

            /**
             * EVENT: Tombol "Add Another Item"
             * Menambah baris baru di tabel barang dengan meng-clone baris pertama.
             */
            addRowBtn.addEventListener('click', function() {
                const firstRow = itemsBody.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);

                // Reset semua nilai input di baris baru
                newRow.querySelector('.qty-input').value = 1;
                newRow.querySelector('.price-input').value = 0;
                newRow.querySelector('.margin-input').value = 10;
                newRow.querySelector('.subtotal-input').value = 0;
                newRow.querySelector('.new-sell-price').value = 0;
                newRow.querySelector('.old-price-display').classList.add('d-none');

                // Update nama input supaya unik (items[1], items[2], dst.)
                newRow.querySelector('.product-select').name = `items[${itemIndex}][product_id]`;
                newRow.querySelector('.qty-input').name = `items[${itemIndex}][quantity]`;
                newRow.querySelector('.price-input').name = `items[${itemIndex}][price]`;
                newRow.querySelector('.margin-input').name = `items[${itemIndex}][margin]`;

                newRow.querySelector('.remove-row').disabled = false;
                itemsBody.appendChild(newRow);
                itemIndex++;
                attachEvents(newRow);
            });

            /**
             * FUNGSI: attachEvents()
             * Menambahkan event listener ke setiap baris barang
             * (input change & tombol hapus baris)
             */
            function attachEvents(row) {
                const inputs = row.querySelectorAll('input');
                const removeBtn = row.querySelector('.remove-row');
                const productSelect = row.querySelector('.product-select');

                // Setiap input berubah → hitung ulang
                inputs.forEach(input => input.addEventListener('input', updateCalculations));

                // Dropdown produk berubah → tampilkan harga lama
                productSelect.addEventListener('change', function() {
                    handleProductChange(this);
                });

                // Tombol hapus baris
                removeBtn.addEventListener('click', function() {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        row.remove();
                        updateCalculations();
                    }
                });
            }

            // Inisialisasi: Pasang event listener ke semua baris yang sudah ada
            const initialRows = document.querySelectorAll('.item-row');
            initialRows.forEach(row => attachEvents(row));

            // Jalankan kalkulasi pertama kali (untuk menampilkan data yang sudah ada)
            updateCalculations();

            // --- CEK NOMOR NOTA UNIK (Opsional) ---
            const noteInput = document.getElementById('note_number');
            const submitBtn = document.getElementById('btn-submit');
            const noteError = document.getElementById('note_error');

            if(noteInput) {
                noteInput.addEventListener('blur', function() {
                    const val = this.value;
                    if(val.length < 3) return;
                    // Jika nomor nota sama dengan yang lama, skip pengecekan
                    if(val === "{{ $purchase->note_number }}") {
                        noteInput.classList.remove('is-invalid');
                        noteInput.classList.add('is-valid');
                        noteError.style.display = 'none';
                        submitBtn.disabled = false;
                        return;
                    }
                    fetch("{{ route('purchase.check-unique') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ note_number: val })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.exists) {
                            noteInput.classList.add('is-invalid');
                            noteError.style.display = 'block';
                            submitBtn.disabled = true;
                        } else {
                            noteInput.classList.remove('is-invalid');
                            noteInput.classList.add('is-valid');
                            noteError.style.display = 'none';
                            submitBtn.disabled = false;
                        }
                    });
                });
                noteInput.addEventListener('input', function() {
                    this.classList.remove('is-invalid', 'is-valid');
                    noteError.style.display = 'none';
                    submitBtn.disabled = false;
                });
            }
        });
    </script>
@endsection
