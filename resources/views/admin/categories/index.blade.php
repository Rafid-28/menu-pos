@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Manajemen Kategori</h1>

{{-- Pesan Alert --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori</h6>
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addCategoryModal">Tambah Kategori</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Jml. Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count ?? $category->products->count() }}</td> 
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" 
                                data-toggle="modal" 
                                data-target="#editCategoryModal"
                                data-id="{{ $category->id }}" 
                                data-name="{{ $category->name }}">Edit</button>
                            
                            {{-- Form Hapus Kategori --}}
                            <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')">Hapus</button>
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

<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Pindahkan Form Create ke sini --}}
            <form action="{{ route('admin.category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Kategori</label>
                        {{-- Hapus old('name') jika tidak ingin nilai form tersimpan setelah validasi error --}}
                        <input type="text" class="form-control" id="name" name="name" required>
                        {{-- Kita bisa menambahkan logika untuk menampilkan error di modal, tapi ini adalah dasar form --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category-name">Nama Kategori</label>
                        <input type="text" class="form-control" id="category-name" name="name" required>
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
        // Logika untuk Edit Kategori (UPDATE)
        $('.edit-btn').on('click', function() {
            var categoryId = $(this).data('id');
            var categoryName = $(this).data('name');
            
            $('#category-name').val(categoryName);
            
            var updateUrl = '{{ url('admin/categories') }}/' + categoryId;
            $('#editCategoryForm').attr('action', updateUrl);
        });
    });
</script>
@endpush