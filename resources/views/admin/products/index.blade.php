@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Manajemen Produk</h1>

{{-- Pesan Alert --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    {{-- Tampilkan error validasi jika ada --}}
    <div class="alert alert-danger">
        Gagal menyimpan produk. Harap periksa input Anda.
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Produk ({{ $products->count() }})</h6>
        {{-- Tombol Tambah Produk (Memicu Modal CREATE) --}}
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addProductModal">Tambah Produk</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Tersedia</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>Rp {{ number_format($product->price) }}</td>
                        <td>
                            <span class="badge badge-{{ $product->is_available ? 'success' : 'danger' }}">
                                {{ $product->is_available ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td>
                            {{-- Tombol Edit Produk (Memicu Modal UPDATE) --}}
                            <button class="btn btn-sm btn-info edit-product-btn" 
                                data-toggle="modal" 
                                data-target="#editProductModal"
                                data-id="{{ $product->id }}" 
                                data-name="{{ $product->name }}"
                                data-category="{{ $product->category_id }}"
                                data-price="{{ $product->price }}"
                                data-available="{{ $product->is_available ? 1 : 0 }}">Edit</button>
                            
                            {{-- Form Hapus Produk --}}
                            <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE') 
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk {{ $product->name }}?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Tambah Produk Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="{{ route('admin.product.store') }}" method="POST">
          @csrf
          <div class="modal-body">
              <div class="form-group">
                  <label for="create_name">Nama Produk</label>
                  <input type="text" class="form-control" id="create_name" name="name" required>
              </div>
              <div class="form-group">
                  <label for="create_category_id">Kategori</label>
                  <select class="form-control" id="create_category_id" name="category_id" required>
                      @foreach ($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label for="create_price">Harga (Rp)</label>
                  <input type="number" class="form-control" id="create_price" name="price" min="0" required>
              </div>
              <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="create_is_available" name="is_available" checked>
                  <label class="form-check-label" for="create_is_available">Tersedia untuk dijual</label>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-primary">Simpan Produk</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProductModalLabel">Edit Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editProductForm" method="POST">
          @csrf
          @method('PUT') {{-- PENTING! Menggunakan method PUT untuk Update --}}
          <div class="modal-body">
              <div class="form-group">
                  <label for="edit_product_name">Nama Produk</label>
                  <input type="text" class="form-control" id="edit_product_name" name="name" required>
              </div>
              <div class="form-group">
                  <label for="edit_product_category">Kategori</label>
                  <select class="form-control" id="edit_product_category" name="category_id" required>
                      @foreach ($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label for="edit_product_price">Harga (Rp)</label>
                  <input type="number" class="form-control" id="edit_product_price" name="price" min="0" required>
              </div>
              <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="edit_product_available" name="is_available">
                  <label class="form-check-label" for="edit_product_available">Tersedia untuk dijual</label>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-product-btn').on('click', function() {
            var productId = $(this).data('id');
            var productName = $(this).data('name');
            var productCategory = $(this).data('category'); // category_id
            var productPrice = $(this).data('price');
            var productAvailable = $(this).data('available');
            
            // 1. Isi input form Modal Edit
            $('#edit_product_name').val(productName);
            $('#edit_product_category').val(productCategory); // Set selected option
            $('#edit_product_price').val(productPrice);
            
            // Set checkbox ketersediaan (0 = false, 1 = true)
            $('#edit_product_available').prop('checked', productAvailable == 1);
            
            // 2. Set action URL untuk form edit (Tujuan: route admin.product.update)
            var updateUrl = '{{ url('admin/products') }}/' + productId;
            $('#editProductForm').attr('action', updateUrl);
        });

        // Optional: Jika terjadi error validasi, modal Tambah Produk akan muncul kembali
        @if ($errors->any() && (old('name') || old('category_id')))
            $('#addProductModal').modal('show');
        @endif
    });
</script>
@endpush