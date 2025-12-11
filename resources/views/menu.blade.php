@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h3>Daftar Menu</h3>
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-4 mb-4"> {{-- Ubah mb-3 jadi mb-4 agar jarak lebih lega --}}
                <div class="card h-100 shadow-sm"> {{-- Tambah shadow-sm untuk efek bayangan --}}
                    
                    {{-- 1. BAGIAN GAMBAR DITAMBAHKAN DI SINI --}}
                    <div style="height: 200px; overflow: hidden;">
                        @if($product->image)
                            <img src="{{ \Storage::url($product->image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $product->name }}" 
                                 style="height: 100%; width: 100%; object-fit: cover;">
                        @else
                            {{-- Placeholder jika tidak ada gambar --}}
                            <img src="https://via.placeholder.com/300x200?text=No+Image" 
                                 class="card-img-top" 
                                 alt="No Image" 
                                 style="height: 100%; width: 100%; object-fit: cover; opacity: 0.5;">
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="text-muted small mb-2">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <h6 class="text-primary font-weight-bold mb-3">Rp {{ number_format($product->price) }}</h6>
                        
                        {{-- Tombol dipaksa ke bawah (mt-auto) agar rata --}}
                        <button class="btn btn-primary btn-sm w-100 mt-auto add-to-cart" 
                            data-id="{{ $product->id }}" 
                            data-name="{{ $product->name }}" 
                            data-price="{{ $product->price }}">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-shopping-cart"></i> Pesanan Anda
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label>Nama Pemesan</label>
                    <input type="text" id="customer_name" class="form-control" placeholder="Nama Pelanggan">
                </div>
                <div class="mb-3">
                    <label>No Meja</label>
                    <input type="number" id="table_number" class="form-control" placeholder="Contoh: 12">
                </div>
                <hr>
                <ul id="cart-items" class="list-group mb-3" style="max-height: 400px; overflow-y: auto;">
                    {{-- Item keranjang masuk sini --}}
                </ul>
                <div class="d-flex justify-content-between font-weight-bold">
                    <span>Total:</span>
                    <span>Rp <span id="cart-total">0</span></span>
                </div>
                <button id="pay-button" class="btn btn-success w-100 mt-3 font-weight-bold">Bayar Sekarang</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];

    // 1. Logic Tambah dari Kartu Menu (Tetap sama)
    $('.add-to-cart').click(function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = $(this).data('price');

        addItemToCart(id, name, price);
    });

    // Fungsi helper untuk menambah item (agar bisa dipakai ulang)
    function addItemToCart(id, name, price) {
        let existing = cart.find(item => item.id === id);
        if(existing) {
            existing.qty++;
        } else {
            cart.push({id: id, name: name, price: price, qty: 1});
        }
        renderCart();
    }

    // 2. Logic Mengurangi Item (BARU)
    // Menggunakan $(document).on karena elemen ini dibuat secara dinamis
    $(document).on('click', '.decrease-item', function() {
        let id = $(this).data('id');
        let item = cart.find(i => i.id === id);

        if (item) {
            item.qty--;
            // Jika qty menjadi 0, hapus dari array cart
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
        }
        renderCart();
    });

    // 3. Logic Menambah Item dari dalam Keranjang (BARU)
    $(document).on('click', '.increase-item', function() {
        let id = $(this).data('id');
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
        }
        renderCart();
    });

    // 4. Render Tampilan Keranjang (DIUPDATE)
    function renderCart() {
        $('#cart-items').empty();
        let total = 0;
        
        if (cart.length === 0) {
            $('#cart-items').append('<li class="list-group-item text-center text-muted">Keranjang kosong</li>');
        }

        cart.forEach((item) => {
            total += item.price * item.qty;
            
            // Tampilan Item Keranjang dengan Tombol +/-
            $('#cart-items').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center lh-condensed">
                    <div style="width: 50%;">
                        <h6 class="my-0 text-truncate" title="${item.name}">${item.name}</h6>
                        <small class="text-muted">@ Rp ${item.price.toLocaleString('id-ID')}</small>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-danger decrease-item py-0 px-2" data-id="${item.id}">-</button>
                        <span class="mx-2 font-weight-bold">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-primary increase-item py-0 px-2" data-id="${item.id}">+</button>
                    </div>

                    <span class="text-muted ml-2" style="min-width: 60px; text-align: right;">
                        ${(item.price * item.qty).toLocaleString('id-ID')}
                    </span>
                </li>
            `);
        });
        $('#cart-total').text(total.toLocaleString('id-ID'));
    }

    $('#pay-button').click(function() {
        if(cart.length === 0) return alert('Keranjang kosong!');
        let name = $('#customer_name').val();
        let table = $('#table_number').val();

        if(!name || !table) return alert('Mohon isi nama dan nomor meja!');

        let btn = $(this);
        btn.prop('disabled', true).text('Memproses...');

        $.ajax({
            url: "{{ route('checkout') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                customer_name: name,
                table_number: table,
                items: cart
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).text('Bayar Sekarang');
                alert("Terjadi kesalahan: " + xhr.status);
            },
            success: function(response) {
                btn.prop('disabled', false).text('Bayar Sekarang');
                
                if (response.snap_token) {
                    window.snap.pay(response.snap_token, {
                        onSuccess: function(result){
                            window.location.href = "/order/receipt/" + response.order_id; 
                        },
                        onPending: function(result){ alert("Menunggu pembayaran!"); },
                        onError: function(result){ alert("Pembayaran gagal!"); },
                        onClose: function(){ alert('Anda menutup popup tanpa menyelesaikan pembayaran'); }
                    });
                } else {
                    alert("Gagal mendapatkan token pembayaran."); 
                }
            },
        });
    });
</script>
@endsection