@extends('layout-home.layout-cart')
@section('title', "Keranjang")
@section('main')

<div class="row d-flex justify-content-center">
    <div class="single-footer-widget">
        <div class="menu-content pb-60">
            <div class="title text-center">
                <h1 class="mb-10">Keranjang anda di Kedai Holand</h1>
                <p>Silahkan pesan melalui sistem kami.</p>
            </div>
        </div>
    </div>
</div>
<div class="section-top-border w-100 mb-5">
    <h4 class="mb-2">Keranjang Kamu</h4>
    <div class="progress-table-wrap">
        <div class="progress-table">
            <div class="table-head">
                <div class="serial">#</div>
                <div class="percentage">Menu</div>
                <div class="country">Deskripsi</div>
                <div class="visit">Total Harga</div>
                <div class="visit text-center">Aksi</div>
                <div class="visit text-center">Hapus</div>
            </div>
            <div id="cartTable"></div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-5">
        <div class="d-flex justify-content-end align-items-center">
            <h4 class="">Total Pesanan : </h4>
            <h6 class="mx-5" id="total-order"></h6>
        </div>
        <button type="button" class="btn-add genric-btn primary radius medium" id="btn-order">Pesan</button>
    </div>
</div>
<div class="section-top-border w-100">
    <h4 class="mb-2">History Pesanan Kamu</h4>
    <div class="progress-table-wrap">
        <div class="progress-table">
            <div class="table-head">
                <div class="serial">#</div>
                <div class="percentage">Kode Pesanan</div>
                <div class="country">Tanggal</div>
                <div class="country">Total Pesanan</div>
                <div class="visit">Status</div>
            </div>
            <div id="orderTable"></div>
        </div>
    </div>
</div>

@endsection
@push('script-js')
<script>
$(document).ready(function () {

    // ✅ Load cart aktif (is_order = false)
    function loadCart() {
        $.getJSON('/cart/data', function (res) {
            const cartItems = res.cart || [];

            if (!cartItems.length) {
                $('#cartTable').html(`
                    <div class="text-center mt-3">
                        <div class="serial w-100">Keranjang masih kosong</div>
                    </div>
                `);
                $('#total-order').text('Rp 0');
                return;
            }

            let totalPrice = 0;
            let html = '';
            console.log(res)
            cartItems.forEach((item, index) => {
                const hargaNumber = parseInt(String(item.harga).replace(/[^0-9]/g, '')) || 0;
                const total = hargaNumber * item.qty;
                totalPrice += total;

                html += `
                    <div class="table-row">
                        <div class="serial">${index + 1}</div>
                        <div class="percentage">${item.nama}</div>
                        <div class="country">${item.desc || '-'}</div>
                        <div class="visit">Rp ${total.toLocaleString('id-ID')}</div>
                        <div class="visit d-flex align-items-center justify-content-center">
                            <button type="button" 
                                class="genric-btn danger-border radius small btn-dec" 
                                data-id="${item.product_id}">-</button>
                            <p class="mx-3 mb-0">${item.qty}</p>
                            <button type="button" 
                                class="genric-btn primary-border radius small btn-inc" 
                                data-id="${item.product_id}">+</button>
                        </div>
                        <div class="visit d-flex align-items-center justify-content-center">
                            <button type="button" 
                                class="genric-btn danger radius small ms-2 btn-remove" 
                                data-id="${item.product_id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>`;
            });

            $('#cartTable').html(html);
            $('#total-order').text(`Rp ${totalPrice.toLocaleString('id-ID')}`);
        });
    }

    // ✅ Load riwayat pesanan (is_order = true)
    function loadHistory() {
        $.getJSON('/cart/history', function (res) {
            if (!res || res.length === 0) {
                $('#orderTable').html(`
                    <div class="text-center mt-3">
                        <div class="serial w-100">Belum ada riwayat pesanan</div>
                    </div>
                `);
                return;
            }

            let html = '';
            res.forEach((order, index) => {
                const totalFormatted = `Rp ${order.total.toLocaleString('id-ID')}`;
                const dateFormatted = order.date
                    ? new Date(order.date).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    })
                    : '-';

                html += `
                    <div class="table-row border-bottom py-2">
                        <div class="serial">${index + 1}</div>
                        <div class="percentage">
                            <strong>${order.order_code || '-'}</strong>
                        </div>
                        <div class="country">
                            ${order.count} item
                        </div>
                        <div class="country">
                            <span class="badge bg-light text-dark">${dateFormatted}</span>
                        </div>
                        <div class="country">
                            <span class="badge bg-light text-dark">${totalFormatted}</span>
                        </div>
                        <div class="visit text-success fw-bold">Di Pesan</div>
                    </div>`;
            });

            $('#orderTable').html(html);
        });
    }


    // ✅ Tombol tambah/kurang/hapus
    $(document).on('click', '.btn-inc', function () {
        const id = $(this).data('id');
        updateQty(id, 1);
    });
    
    $(document).on('click', '.btn-dec', function () {
        const id = $(this).data('id');
        updateQty(id, -1);
    });

    $(document).on('click', '.btn-remove', function () {
        const id = $(this).data('id');
        confirmRemoveItem(id);
    });

    // ✅ Update jumlah
    function updateQty(id, delta) {
        $.getJSON('/cart/data', function (data) {
            const item = data.cart.find(i => i.product_id == id);
            if (!item) return;

            let newQty = item.qty + delta;
            if (newQty <= 0) {
                confirmRemoveItem(id);
                return;
            }

            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: item.product_id,
                    nama: item.nama,
                    qty: delta,
                    harga: item.harga,
                    desc: item.desc,
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    loadCart();
                },
                error: function () {
                    Swal.fire("Gagal", "Tidak bisa memperbarui jumlah", "error");
                }
            });
        });
    }

    // ✅ Konfirmasi hapus
    function confirmRemoveItem(id) {
        Swal.fire({
            title: "Hapus item ini?",
            text: "Item akan dihapus dari keranjang.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/cart/remove/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        Swal.fire({
                            title: "Dihapus!",
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false
                        });
                        loadCart();
                    },
                    error: function () {
                        Swal.fire("Gagal", "Tidak dapat menghapus item", "error");
                    }
                });
            }
        });
    }

    // ✅ Jalankan saat halaman siap
    loadCart();
    loadHistory();

});
</script>

{{-- <script>
$(document).ready(function () {
    function loadCart() {
        $.getJSON('/cart/data', function (res) {
            if (!res || !res.cart || res.cart.length === 0) {
                $('#cartTable').html(`
                    <div class="text-center mt-3">
                        <div class="serial w-100">Keranjang masih kosong</div>
                    </div>
                `);
                $('#total-order').text('Rp 0');
                return;
            }

            let totalPrice = 0;
            let html = '';
            res.cart.forEach((item, index) => {  // <-- res.cart, bukan res
                const hargaNumber = parseInt(String(item.harga).replace(/[^0-9]/g, '')) || 0;
                const total = hargaNumber * item.qty;
                totalPrice += total;

                html += `
                    <div class="table-row">
                        <div class="serial">${index + 1}</div>
                        <div class="percentage">${item.nama}</div>
                        <div class="country">${item.desc || '-'}</div>
                        <div class="visit">Rp ${total.toLocaleString('id-ID')}</div>
                        <div class="visit d-flex align-items-center justify-content-center">
                            <button type="button" 
                                class="genric-btn danger-border radius small btn-dec" 
                                data-id="${item.product_id}">-</button>
                            <p class="mx-3 mb-0">${item.qty}</p>
                            <button type="button" 
                                class="genric-btn primary-border radius small btn-inc" 
                                data-id="${item.product_id}">+</button>
                        </div>
                        <div class="visit d-flex align-items-center justify-content-center">
                            <button type="button" 
                                class="genric-btn danger radius small ms-2 btn-remove" 
                                data-id="${item.product_id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>`;
            });

            $('#cartTable').html(html);
            $('#total-order').text(`Rp ${totalPrice.toLocaleString('id-ID')}`);
        });
    }


    loadCart();

    $(document).on('click', '.btn-inc', function () {
        const id = $(this).data('id');
        updateQty(id, 1);
    });
    
    $(document).on('click', '.btn-dec', function () {
        const id = $(this).data('id');
        updateQty(id, -1);
    });

    $(document).on('click', '.btn-remove', function () {
        const id = $(this).data('id');
        confirmRemoveItem(id);
    });

    function updateQty(id, delta) {
        $.getJSON('/cart/data', function (data) {
            const item = data.cart.find(i => i.product_id == id);
            console.log(item)
            if (!item) return;

            let newQty = item.qty + delta;

            if (newQty <= 0) {
                confirmRemoveItem(id);
                return;
            }

            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: item.product_id,
                    nama: item.nama,
                    qty: delta,
                    harga: item.harga,
                    desc: item.desc,
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    loadCart();
                },
                error: function () {
                    Swal.fire("Gagal", "Tidak bisa memperbarui jumlah", "error");
                }
            });
        });
    }


    function confirmRemoveItem(id) {
        Swal.fire({
            title: "Hapus item ini?",
            text: "Item akan dihapus dari keranjang.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/cart/remove/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        Swal.fire({
                            title: "Dihapus!",
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false
                        });
                        loadCart();
                    },
                    error: function (e) {
                        console.log(e)
                        Swal.fire("Gagal", "Tidak dapat menghapus item", "error");
                    }
                });
            }
        });
    }
});
</script> --}}

@endpush
