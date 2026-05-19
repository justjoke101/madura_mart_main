@extends('layout.master')

@section('title', 'New Transaction')

@section('menu')
    @include('layout.menu')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm">

                {{-- ============================================ --}}
                {{-- BANNER TOTAL BAYAR (Pink/Magenta Gradient) --}}
                {{-- ============================================ --}}
                <div class="card-header pb-0 bg-white">
                    <input type="text"
                        class="form-control fs-1 fw-bold text-white text-center border-0"
                        id="total_bayar_display"
                        style="background: linear-gradient(135deg, #e91e90, #c2185b); border-radius: 12px; padding: 20px; letter-spacing: 2px;"
                        value="Rp,-"
                        disabled>
                </div>

                {{-- ============================================ --}}
                {{-- FORM INPUT AREA --}}
                {{-- ============================================ --}}
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="border border-2 border-primary rounded-3 mt-3 ms-4 me-4 p-4">

                        @if($errors->any())
                            <div class="alert alert-danger text-white mb-4" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('sales.store') }}" method="POST" id="sales-form">
                            @csrf

                            {{-- Hidden: Tanggal Penjualan (Otomatis Hari Ini) --}}
                            <input type="hidden" name="sale_date" value="{{ date('Y-m-d') }}">

                            <div class="row">
                                <div class="col-lg-6 col-md-8">

                                    {{-- INVOICE NO --}}
                                    <div class="mb-3">
                                        <label for="no_nota" class="form-label fw-bold">Invoice No</label>
                                        <input type="text" class="form-control" id="no_nota" name="no_nota"
                                            placeholder="Enter Invoice No"
                                            value="{{ old('no_nota', 'INV-' . date('Ymd') . '-' . str_pad(rand(1,999), 3, '0', STR_PAD_LEFT)) }}"
                                            maxlength="20">
                                    </div>

                                    {{-- PRODUCT SELECT --}}
                                    <div class="mb-3">
                                        <label for="product" class="form-label fw-bold">Product</label>
                                        <select class="form-select" id="product">
                                            <option value="" data-price="0" data-stock="0">Select Product</option>
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->id }}"
                                                    data-price="{{ $prod->price }}"
                                                    data-stock="{{ $prod->stock }}">
                                                    {{ $prod->name }} (Stock: {{ $prod->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- SELLING PRICE (Readonly, otomatis dari produk) --}}
                                    <div class="mb-3">
                                        <label for="harga_jual" class="form-label fw-bold">Selling Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control bg-light" id="harga_jual" value="0" readonly>
                                        </div>
                                    </div>

                                    {{-- SELLING AMOUNT / QTY --}}
                                    <div class="mb-3">
                                        <label for="jumlah_jual" class="form-label fw-bold">Selling Amount</label>
                                        <input type="number" class="form-control" id="jumlah_jual"
                                            placeholder="Enter Selling Amount" value="0" min="1">
                                        <small class="text-muted" id="stock-info"></small>
                                    </div>

                                    {{-- SUBTOTAL --}}
                                    <div class="mb-3">
                                        <label for="subtotal" class="form-label fw-bold">Subtotal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control bg-light" id="subtotal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================ --}}
                            {{-- TOMBOL: ADD ITEM + CANCEL + SAVE --}}
                            {{-- ============================================ --}}
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="px-0 pb-3 d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-success btn-sm mb-0" id="add-item-btn">
                                            <i class="fas fa-plus me-1"></i> Add Item to Cart
                                        </button>
                                        <div>
                                            <a href="{{ route('sales.index') }}" class="btn bg-gradient-secondary me-2">Cancel</a>
                                            <button type="submit" id="btn-save" class="btn bg-gradient-primary" disabled>
                                                Save New {{ $title }} Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden Container: Semua Item yang Sudah Ditambah --}}
                            <div id="hidden-items-container"></div>
                        </form>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- TABEL KERANJANG BELANJA --}}
                {{-- ============================================ --}}
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="cart-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="cart-body">
                                {{-- Rows will be dynamically added here --}}
                                <tr id="empty-cart-row">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-shopping-cart me-2 opacity-5"></i>
                                        No items added yet. Select a product and click "Add Item to Cart".
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================ --}}
    <footer class="footer pt-3">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        © <script>document.write(new Date().getFullYear())</script>,
                        made with <i class="fa fa-heart"></i> by
                        <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                        for a better web.
                    </div>
                </div>
                <div class="col-lg-6">
                    <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================ --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // === DOM Elements ===
    const productSelect  = document.getElementById('product');
    const hargaJualInput = document.getElementById('harga_jual');
    const jumlahInput    = document.getElementById('jumlah_jual');
    const subtotalInput  = document.getElementById('subtotal');
    const totalDisplay   = document.getElementById('total_bayar_display');
    const addItemBtn     = document.getElementById('add-item-btn');
    const btnSave        = document.getElementById('btn-save');
    const cartBody       = document.getElementById('cart-body');
    const emptyRow       = document.getElementById('empty-cart-row');
    const hiddenContainer = document.getElementById('hidden-items-container');
    const stockInfo      = document.getElementById('stock-info');

    let cartItems = []; // Array to track items in cart
    let itemIndex = 0;

    // === Formatter ===
    const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num);

    // === 1. When Product Dropdown Changes ===
    productSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const price = parseInt(selected.getAttribute('data-price')) || 0;
        const stock = parseInt(selected.getAttribute('data-stock')) || 0;

        hargaJualInput.value = formatRupiah(price);
        jumlahInput.value = 1;
        jumlahInput.max = stock;
        subtotalInput.value = formatRupiah(price * 1);

        stockInfo.textContent = stock > 0 ? `Available stock: ${stock}` : 'Out of stock!';
        stockInfo.className = stock > 0 ? 'text-success text-xs' : 'text-danger text-xs';
    });

    // === 2. When Qty Changes ===
    jumlahInput.addEventListener('input', function() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price = parseInt(selected.getAttribute('data-price')) || 0;
        const stock = parseInt(selected.getAttribute('data-stock')) || 0;
        let qty = parseInt(this.value) || 0;

        // Validate against stock
        if (qty > stock && stock > 0) {
            qty = stock;
            this.value = stock;
            Swal.fire({
                icon: 'warning',
                title: 'Stock Limit!',
                text: `Maximum available stock is ${stock}`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        subtotalInput.value = formatRupiah(price * qty);
    });

    // === 3. Add Item to Cart ===
    addItemBtn.addEventListener('click', function() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const productId = productSelect.value;
        const productName = selected.textContent.trim();
        const price = parseInt(selected.getAttribute('data-price')) || 0;
        const stock = parseInt(selected.getAttribute('data-stock')) || 0;
        const qty = parseInt(jumlahInput.value) || 0;

        // --- Validasi ---
        if (!productId) {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please select a product!', timer: 2000, showConfirmButton: false });
            return;
        }
        if (qty <= 0) {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Quantity must be at least 1!', timer: 2000, showConfirmButton: false });
            return;
        }
        if (qty > stock) {
            Swal.fire({ icon: 'error', title: 'Stock Kurang!', text: `Stock only ${stock} left!`, timer: 2000, showConfirmButton: false });
            return;
        }

        // Check if product already in cart
        const existingIndex = cartItems.findIndex(item => item.productId === productId);
        if (existingIndex !== -1) {
            const newQty = cartItems[existingIndex].qty + qty;
            if (newQty > stock) {
                Swal.fire({ icon: 'error', title: 'Stock Kurang!', text: `Total qty (${newQty}) exceeds stock (${stock})!`, timer: 2000, showConfirmButton: false });
                return;
            }
            // Update existing item
            cartItems[existingIndex].qty = newQty;
            cartItems[existingIndex].subtotal = price * newQty;
        } else {
            // Add new item
            cartItems.push({
                productId: productId,
                productName: productName,
                price: price,
                qty: qty,
                subtotal: price * qty
            });
        }

        renderCart();
        resetForm();

        Swal.fire({
            icon: 'success',
            title: 'Added!',
            text: `${productName} x${qty} added to cart`,
            timer: 1500,
            showConfirmButton: false
        });
    });

    // === 4. Render Cart Table & Hidden Inputs ===
    function renderCart() {
        // Clear table body
        cartBody.innerHTML = '';
        hiddenContainer.innerHTML = '';

        if (cartItems.length === 0) {
            cartBody.innerHTML = `
                <tr id="empty-cart-row">
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart me-2 opacity-5"></i>
                        No items added yet.
                    </td>
                </tr>`;
            btnSave.disabled = true;
            totalDisplay.value = 'Rp,-';
            return;
        }

        let grandTotal = 0;

        cartItems.forEach((item, index) => {
            grandTotal += item.subtotal;

            // Table Row (Visual)
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3">
                    <span class="text-sm font-weight-bold">${index + 1}</span>
                </td>
                <td>
                    <button type="button" class="btn btn-link text-danger p-0 mb-0 remove-item" data-index="${index}" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </button>
                </td>
                <td>
                    <span class="text-sm font-weight-bold">${item.productName}</span>
                </td>
                <td>
                    <span class="text-sm">Rp ${formatRupiah(item.price)}</span>
                </td>
                <td>
                    <span class="text-sm font-weight-bold">${item.qty}</span>
                </td>
                <td>
                    <span class="text-sm font-weight-bold text-primary">Rp ${formatRupiah(item.subtotal)}</span>
                </td>
            `;
            cartBody.appendChild(tr);

            // Hidden Inputs (for form submission)
            hiddenContainer.innerHTML += `
                <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            `;
        });

        // Grand Total Row
        const totalRow = document.createElement('tr');
        totalRow.innerHTML = `
            <td colspan="5" class="text-end pe-3">
                <span class="text-sm font-weight-bolder text-uppercase">Grand Total</span>
            </td>
            <td>
                <span class="text-md font-weight-bolder text-success">Rp ${formatRupiah(grandTotal)}</span>
            </td>
        `;
        cartBody.appendChild(totalRow);

        // Update banner
        totalDisplay.value = `Rp ${formatRupiah(grandTotal)},-`;
        btnSave.disabled = false;
    }

    // === 5. Remove Item from Cart ===
    cartBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-item');
        if (!btn) return;

        const index = parseInt(btn.getAttribute('data-index'));
        const item = cartItems[index];

        Swal.fire({
            title: 'Remove Item?',
            text: `Remove ${item.productName} from cart?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove!'
        }).then((result) => {
            if (result.isConfirmed) {
                cartItems.splice(index, 1);
                renderCart();
            }
        });
    });

    // === 6. Reset Form After Adding ===
    function resetForm() {
        productSelect.value = '';
        hargaJualInput.value = '0';
        jumlahInput.value = '0';
        subtotalInput.value = '0';
        stockInfo.textContent = '';
    }

    // === 7. Form Submit Validation ===
    document.getElementById('sales-form').addEventListener('submit', function(e) {
        if (cartItems.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Cart Empty!',
                text: 'Please add at least one item before saving.',
            });
        }
    });
});
</script>
@endsection