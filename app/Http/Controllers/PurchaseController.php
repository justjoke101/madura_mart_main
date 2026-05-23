<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
/**
 * =============================================
 * MENAMPILKAN DAFTAR SEMUA PURCHASE (INDEX)
 * =============================================
 * Fungsi ini mengambil semua data purchase dari database
 * lalu menampilkannya di halaman index dalam bentuk tabel.
 * Mendukung fitur pencarian berdasarkan nomor nota.
 */
public function index(Request $request)
{
    // Query Dasar: Ambil data Purchase beserta relasi Distributor (Eager Loading biar ga N+1 query)
    $query = Purchase::with('distributor');

    // Logika Search: Jika user mengetik di kolom search, filter berdasarkan note_number
    if ($request->has('search') && $request->search != null) {
        $query->where('note_number', 'like', '%' . $request->search . '%');
    }

    // Ambil data terbaru (latest) + Pagination 10 per halaman
    $purchases = $query->latest()->paginate(10)->withQueryString();

    // Kirim data ke view purchase/index.blade.php
    return view('purchase.index', [
        'title' => 'Purchase',
        'purchases' => $purchases
    ]);
}

/**
 * =============================================
 * MENAMPILKAN FORM BUAT PURCHASE BARU (CREATE)
 * =============================================
 * Menampilkan halaman form untuk membuat purchase baru.
 * Memerlukan data distributor dan produk untuk dropdown.
 */
public function create()
{
    // Ambil semua data distributor untuk dropdown pilihan
    $distributors = Distributor::all();
    // Ambil semua produk, urutkan berdasarkan nama (A-Z)
    $products = Product::orderBy('name', 'asc')->get();

    return view('purchase.create', [
        'title' => 'Create Purchase',
        'distributors' => $distributors,
        'products' => $products
    ]);
}

/**
 * =============================================
 * MENYIMPAN DATA PURCHASE BARU KE DATABASE (STORE)
 * =============================================
 * Proses penyimpanan purchase baru:
 * 1. Validasi input
 * 2. Simpan data header purchase (nomor nota, tanggal, distributor)
 * 3. Loop setiap item barang & simpan ke purchase_details
 * 4. Update stok produk & harga jual berdasarkan margin
 * 5. Update grand total di tabel purchases
 * Semua dibungkus dalam DB::transaction agar data konsisten (all or nothing)
 */
public function store(Request $request)
{
    // 1. Validasi semua input yang masuk
    $request->validate([
        'note_number' => 'required|string|max:15|unique:purchases,note_number',
        'purchase_date' => 'required|date',
        'distributor_id' => 'nullable|exists:distributors,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|integer|min:0',
        'items.*.margin' => 'required|numeric|min:0',
    ]);

    try {
        // DB::transaction = jika salah satu proses gagal, SEMUA dibatalkan (rollback)
        DB::transaction(function () use ($request) {

            // A. Simpan Header Purchase (Data induk nota)
            $purchase = Purchase::create([
                'note_number' => $request->note_number,
                'purchase_date' => $request->purchase_date,
                'distributor_id' => $request->distributor_id,
                'total_price' => 0, // Sementara 0, nanti diupdate
            ]);

            $grandTotal = 0;

            // B. Loop setiap barang yang dibeli
            foreach ($request->items as $item) {
                $buyPrice = $item['price'];     // Harga beli satuan
                $qty = $item['quantity'];        // Jumlah beli
                $margin = $item['margin'];       // Margin keuntungan (%)

                // Hitung subtotal per item (Harga Beli x Qty)
                $subtotal = $buyPrice * $qty;
                $grandTotal += $subtotal;

                // Simpan detail barang ke tabel purchase_details
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'purchase_price' => $buyPrice,
                    'purchase_amount' => $qty,
                    'subtotal' => $subtotal,
                    'selling_margin' => $margin,
                ]);

                // C. Update data di Master Product
                $product = Product::findOrFail($item['product_id']);

                // Tambah stok produk
                $product->stock += $qty;

                // Hitung harga jual baru berdasarkan margin
                // Rumus: Harga Jual = Harga Beli + (Harga Beli × Margin%)
                // Contoh: 10.000 + (10.000 × 10/100) = 11.000
                $marginValue = $buyPrice * ($margin / 100);
                $newSellingPrice = $buyPrice + $marginValue;

                // Timpa harga jual lama dengan yang baru
                $product->price = $newSellingPrice;

                $product->save();
            }

            // D. Update Grand Total di tabel purchases
            $purchase->update(['total_price' => $grandTotal]);
        });

        return redirect()->route('purchase.index')
            ->with('success', 'Purchase saved! Stock added & Selling Price updated.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Error saving purchase: ' . $e->getMessage()]);
    }
}

/**
 * =============================================
 * MENAMPILKAN DETAIL PURCHASE (SHOW)
 * =============================================
 * Menampilkan detail lengkap satu purchase:
 * termasuk info distributor dan daftar barang yang dibeli.
 */
public function show($id)
{
    // Eager loading: Ambil Purchase + Distributor + Detail Barang + Nama Produknya
    $purchase = Purchase::with(['distributor', 'details.product'])->findOrFail($id);

    return view('purchase.show', [
        'title' => 'Purchase Details',
        'purchase' => $purchase
    ]);
}

/**
 * ⭐ JELASKAN 3: METHOD EDIT - MENAMPILKAN FORM EDIT PURCHASE */
public function edit($id)
{
    // Cari data purchase berdasarkan ID, beserta detail barangnya
    $purchase = Purchase::with(['details.product', 'distributor'])->findOrFail($id);

    // Ambil data distributor & produk untuk dropdown di form
    $distributors = Distributor::all();
    $products = Product::orderBy('name', 'asc')->get();

    // Kirim data ke view purchase/edit.blade.php
    return view('purchase.edit', [
        'title' => 'Edit Purchase',
        'purchase' => $purchase,
        'distributors' => $distributors,
        'products' => $products
    ]);
}

/**
 * ⭐ JELASKAN 4: METHOD UPDATE - PROSES UPDATE DATA PURCHASE */
public function update(Request $request, $id)
{
    // 1. Validasi input (note_number boleh sama dengan punya sendiri)
    $request->validate([
        'note_number' => 'required|string|max:15|unique:purchases,note_number,' . $id,
        'purchase_date' => 'required|date',
        'distributor_id' => 'nullable|exists:distributors,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|integer|min:0',
        'items.*.margin' => 'required|numeric|min:0',
    ]);

    try {
        DB::transaction(function () use ($request, $id) {

            // Cari purchase yang mau diupdate
            $purchase = Purchase::with('details')->findOrFail($id);

            // A. REVERSE STOCK: Kembalikan stok lama sebelum update
            // Ini penting supaya stok tidak bertambah dobel
            foreach ($purchase->details as $oldDetail) {
                $product = Product::find($oldDetail->product_id);
                if ($product) {
                    // Kurangi stok sejumlah yang dulu ditambahkan
                    $product->stock -= $oldDetail->purchase_amount;
                    $product->save();
                }
            }

            // B. Hapus semua detail lama
            $purchase->details()->delete();

            // C. Update header purchase
            $purchase->update([
                'note_number' => $request->note_number,
                'purchase_date' => $request->purchase_date,
                'distributor_id' => $request->distributor_id,
            ]);

            $grandTotal = 0;

            // D. Simpan detail barang yang baru
            foreach ($request->items as $item) {
                $buyPrice = $item['price'];
                $qty = $item['quantity'];
                $margin = $item['margin'];

                $subtotal = $buyPrice * $qty;
                $grandTotal += $subtotal;

                // Simpan detail baru
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'purchase_price' => $buyPrice,
                    'purchase_amount' => $qty,
                    'subtotal' => $subtotal,
                    'selling_margin' => $margin,
                ]);

                // E. Update stok & harga jual produk
                $product = Product::findOrFail($item['product_id']);
                $product->stock += $qty;

                // Hitung harga jual baru
                $marginValue = $buyPrice * ($margin / 100);
                $product->price = $buyPrice + $marginValue;
                $product->save();
            }

            // F. Update grand total
            $purchase->update(['total_price' => $grandTotal]);
        });

        return redirect()->route('purchase.index')
            ->with('success', 'Purchase updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Error updating purchase: ' . $e->getMessage()]);
    }
}

/**
 * ⭐ JELASKAN 5: METHOD DESTROY - PROSES HAPUS PURCHASE */
public function destroy($id)
{
    $purchase = Purchase::with('details')->findOrFail($id);

    try {
        DB::transaction(function () use ($purchase) {
            // 1. Kembalikan Stok (Reverse Stock)
            foreach ($purchase->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    // Kurangi stok sejumlah yang dulu dibeli
                    $product->stock -= $detail->purchase_amount;
                    $product->save();
                }
            }

            // 2. Hapus Data Purchase (Detail akan terhapus otomatis karena cascade)
            $purchase->delete();
        });

        return redirect()->route('purchase.index')->with('success', 'Purchase deleted and stock reversed successfully.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting purchase: ' . $e->getMessage());
    }
}

/**
 * =============================================
 * CEK NOMOR NOTA UNIK (AJAX)
 * =============================================
 * Dipanggil via AJAX dari form create/edit.
 * Mengecek apakah nomor nota sudah ada di database.
 * Mengembalikan JSON { exists: true/false }
 */
public function checkUniqueNoteNumber(Request $request)
{
    $noteNumber = $request->input('note_number');

    // Cek di database apakah nomor nota sudah ada
    $exists = Purchase::where('note_number', $noteNumber)->exists();

    return response()->json(['exists' => $exists]);
}

/**
 * ⭐ JELASKAN 6: METHOD VERIFIKASI PASSWORD ATASAN (INTI TUGAS) */
public function checkBossPassword(Request $request)
{
    // Ambil password yang diinput user dari modal
    $inputPassword = $request->input('password');

    // Ambil password bos dari file .env (default: 'admin123')
    $bossPassword = env('BOSS_PASSWORD', 'admin123');

    // Cocokkan password
    if ($inputPassword === $bossPassword) {
        // Password BENAR → return success
        return response()->json(['success' => true]);
    } else {
        // Password SALAH → return gagal
        return response()->json(['success' => false]);
    }
}
}
