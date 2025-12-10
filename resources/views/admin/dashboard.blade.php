@extends('layout')

@section('content')
<h2>Dashboard Kasir</h2>

<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-target="#orders" data-bs-toggle="tab">Pesanan Masuk</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-target="#menu" data-bs-toggle="tab">Manajemen Menu</button></li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane fade show active" id="orders">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Nama / Meja</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Pesan Kasir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }} (Meja {{ $order->table_number }})</td>
                    <td>
                        <ul>
                            @foreach($order->items as $item)
                                <li>{{ $item->product->name }} x{{ $item->quantity }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'paid' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <form action="{{ route('admin.order.update', $order->id) }}" method="POST">
                        @csrf @method('PUT')
                        <td>
                            <input type="text" name="cashier_note" class="form-control form-control-sm" value="{{ $order->cashier_note }}">
                        </td>
                        <td>
                            <select name="status" class="form-select form-select-sm mb-1">
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="menu">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Tambah Kategori</div>
                    <div class="card-body">
                        <form action="{{ route('admin.category.store') }}" method="POST">
                            @csrf
                            <input type="text" name="name" class="form-control mb-2" placeholder="Nama Kategori" required>
                            <button class="btn btn-primary btn-sm">Simpan</button>
                        </form>
                        <hr>
                        <ul class="list-group">
                            @foreach($categories as $cat)
                                <li class="list-group-item d-flex justify-content-between">
                                    {{ $cat->name }}
                                    <form action="{{ route('admin.category.destroy', $cat->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">X</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Tambah Menu</div>
                    <div class="card-body">
                        <form action="{{ route('admin.product.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="name" class="form-control" placeholder="Nama Menu" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="number" name="price" class="form-control" placeholder="Harga" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <select name="category_id" class="form-control">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary">Simpan Menu</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <table class="table table-sm">
                            <thead><tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @foreach($products as $prod)
                                <tr>
                                    <td>{{ $prod->name }}</td>
                                    <td>{{ $prod->category->name }}</td>
                                    <td>{{ $prod->price }}</td>
                                    <td>
                                        <form action="{{ route('admin.product.destroy', $prod->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection