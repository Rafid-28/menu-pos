@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h3>Daftar Menu</h3>
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="text-muted">{{ $product->category->name }}</p>
                        <h6 class="text-primary">Rp {{ number_format($product->price) }}</h6>
                        <button class="btn btn-primary btn-sm w-100 add-to-cart" 
                            data-id="{{ $product->id }}" 
                            data-name="{{ $product->name }}" 
                            data-price="{{ $product->price }}">
                            Tambah
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">Pesanan Anda</div>
            <div class="card-body">
                <div class="mb-3">
                    <label>Nama Pemesan</label>
                    <input type="text" id="customer_name" class="form-control" placeholder="Budi">
                </div>
                <div class="mb-3">
                    <label>No Meja</label>
                    <input type="text" id="table_number" class="form-control" placeholder="12">
                </div>
                <hr>
                <ul id="cart-items" class="list-group mb-3">
                    </ul>
                <h5>Total: Rp <span id="cart-total">0</span></h5>
                <button id="pay-button" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];

    // Tambah ke cart
    $('.add-to-cart').click(function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = $(this).data('price');

        let existing = cart.find(item => item.id === id);
        if(existing) {
            existing.qty++;
        } else {
            cart.push({id: id, name: name, price: price, qty: 1});
        }
        renderCart();
    });

    function renderCart() {
        $('#cart-items').empty();
        let total = 0;
        cart.forEach(item => {
            total += item.price * item.qty;
            $('#cart-items').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${item.name} x${item.qty}
                    <span>Rp ${item.price * item.qty}</span>
                </li>
            `);
        });
        $('#cart-total').text(total.toLocaleString());
    }

    // Checkout
    $('#pay-button').click(function() {
        if(cart.length === 0) return alert('Keranjang kosong!');
        let name = $('#customer_name').val();
        let table = $('#table_number').val();

        if(!name || !table) return alert('Isi nama dan meja!');

        $.ajax({
            url: "{{ route('checkout') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                customer_name: name,
                table_number: table,
                items: cart
            },
            error: function(xhr, status, error) { // <-- TAMBAHKAN INI
                alert("Terjadi kesalahan pada server saat Checkout. Status: " + xhr.status);
                console.error("AJAX Error Details:", error, xhr.responseText);
            },
            success: function(response) {
                if (response.snap_token) {
                    // Panggil Snap Midtrans
                    window.snap.pay(response.snap_token, {
                        onSuccess: function(result){
                            alert("Pembayaran Berhasil!");
                            window.location.reload();
                        },
                        onPending: function(result){ alert("Menunggu pembayaran!"); },
                        onError: function(result){ alert("Pembayaran gagal!"); }
                    });
                } else {
                    alert("Gagal mendapatkan token pembayaran. Cek log server!"); 
                }
            },
        });
    });
</script>
@endsection