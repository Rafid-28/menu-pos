@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Buat Pesanan Baru</h1>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pilih Produk</h6>
            </div>
            <div class="card-body">
                @foreach ($products as $product)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                    <div>
                        <strong>{{ $product->name }}</strong><br>
                        <small>Rp {{ number_format($product->price) }}</small>
                    </div>
                    <button class="btn btn-sm btn-success">Tambah</button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Keranjang Pesanan</h6>
            </div>
            <div class="card-body">
                <p>Isi keranjang akan muncul di sini.</p>
                <div class="form-group">
                    <label>Total:</label>
                    <input type="text" class="form-control" value="Rp 0" readonly>
                </div>
                <button class="btn btn-block btn-primary">Proses Pesanan</button>
            </div>
        </div>
    </div>
</div>
@endsection