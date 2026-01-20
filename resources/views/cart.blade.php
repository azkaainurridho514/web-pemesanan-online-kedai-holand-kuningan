@extends('layout-home.layout-cart')
@section('title', "Keranjang")
@push('styles')
    <style>
    #reader {
        position: relative;
        background: #000;
    }

    #reader video {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    #loading {
        color: #666;
    }

    #scanResult .alert {
        text-align: left;
        margin-top: 15px;
    }

    .modal-body {
        padding: 20px;
    }
    </style>
@endpush
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
                <div class="country text-center">Aksi</div>
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
        <button type="button" class="btn-add genric-btn primary radius medium" data-toggle="modal"
         {{-- data-target="#modal-confirm" --}}
         id="btn-order"
         >Pesan</button>
    </div>
</div>
<div class="section-top-border w-100">
    <h4 class="mb-2">History Pesanan Kamu</h4>
    <div class="progress-table-wrap">
        <div class="progress-table">
            <div class="table-head">
                <div class="serial">#</div>
                <div class="percentage">Kode Pesanan</div>
                <div class="percentage">Tanggal</div>
                <div class="percentage">Total Pesanan</div>
                <div class="percentage">Harga</div>
                <div class="visit">Status</div>
            </div>
            <div id="orderTable"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Info pelanggan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="text" name="name" placeholder="Nama" required class="single-input-primary mb-2">
            <input type="number" name="phone" placeholder="No. HP" class="single-input-primary mb-2">
        </div>
        <div class="modal-footer">
            <button type="button" class="genric-btn danger radius medium" data-dismiss="modal">Close</button>
            <button type="button" class="genric-btn primary radius medium" id="btn-checkout">
                Checkout
            </button>
        </div>
        </div>
    </div>
</div>


<!-- Modal Box Scan Camera Cihuy -->
<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanModalLabel">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Camera Preview (Kotak) -->
                <div id="reader" style="width: 100%; max-width: 400px; height: 400px; margin: 0 auto; border: 2px solid #007bff; border-radius: 8px; overflow: hidden;"></div>
                
                <!-- Loading Indicator -->
                <div id="loading" class="mt-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Membuka kamera...</p>
                </div>
                
                <!-- Result -->
                <div id="scanResult" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script-js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
$(document).ready(function () {
    let html5QrCode = null;
    let isScanning = false;
    let scanCallback = null;
    window.openQRScanner = function(callback) {
        console.log('openQRScanner dipanggil');
        scanCallback = callback;
        $('#scanModal').modal('show');
    }
    $('#scanModal').on('shown.bs.modal', function () {
        console.log('Modal dibuka, memulai scanner');
        startQRScanner();
    });
    async function startQRScanner() {
        const name = $('#modal-confirm input[name="name"]').val();
        const phone = $('#modal-confirm input[name="phone"]').val();
        console.log('startQRScanner dipanggil');
        if (isScanning) {
            console.log('Scanner sudah berjalan');
            return;
        }
        const readerElement = document.getElementById('reader');
        const loadingElement = document.getElementById('loading');
        const resultElement = document.getElementById('scanResult');
        if (!readerElement) {
            console.error('Element #reader tidak ditemukan!');
            return;
        }
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" }
            });
            stream.getTracks().forEach(track => track.stop());
        } catch (err) {
            console.error('Izin kamera ditolak:', err);
            resultElement.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Akses kamera ditolak</strong><br>
                    Silakan izinkan kamera di browser.
                </div>
            `;
            return;
        }
        resultElement.innerHTML = '';
        loadingElement.style.display = 'block';
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        html5QrCode = new Html5Qrcode("reader");
        console.log('Memulai kamera...');
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                stopScanner();
                resultElement.innerHTML = `
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="ml-2">Mengecek QR Code...</span>
                `;
                
                $.ajax({
                url: '/api/check-qrcode/'+decodedText,
                method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                }).then(data => {
                    // QR valid
                    if (data.status === 'valid') {
                        console.log("Proses checkout");
                        closeQRModal();
                        checkout(name, phone, data.data.id);
                        return;
                    }

                    // QR pernah dipakai tapi masih bisa dipakai ulang
                    if (data.status === 'used') {
                        stopScanner();
                        resultElement.innerHTML = `
                            <div class="alert alert-warning" role="alert">
                                <strong><i class="fa fa-exclamation-triangle"></i> Peringatan!</strong>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <p class="mb-0">${data.message}</p>
                                    <button id="btn-rescan" class="btn btn-sm btn-primary">Scan Ulang</button>
                                </div>
                            </div>
                        `;

                        const btnRescan = document.getElementById('btn-rescan');
                        if (btnRescan) {
                            btnRescan.addEventListener('click', () => {
                                resultElement.innerHTML = '';
                                startQRScanner();
                            });
                        }
                        return;
                    }
                })
                .catch(error => {
                    // QR salah / tidak aktif
                    console.error('Error:', error);
                    stopScanner();

                    resultElement.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <strong><i class="fa fa-exclamation-triangle"></i> Error!</strong>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="mb-0">${error.message ?? 'QR Salah/Tidak ditemukan.'}</p>
                                <button id="btn-rescan" class="btn btn-sm btn-primary">Scan Ulang</button>
                            </div>
                        </div>
                    `;

                    const btnRescan = document.getElementById('btn-rescan');
                    if (btnRescan) {
                        btnRescan.addEventListener('click', () => {
                            resultElement.innerHTML = '';
                            startQRScanner();
                        });
                    }
                });

                // old =============
                // .then(data => {
                //     console.log("Proses checkout");
                //     closeQRModal();
                //     checkout(
                //         name,
                //         phone, 
                //         data.id
                //     );
                // })
                // .catch(error => {
                //     console.error('Error:', error);
                //     stopScanner();
                //     resultElement.innerHTML = `
                //         <div class="alert alert-danger" role="alert">
                //             <strong><i class="fa fa-exclamation-triangle"></i> Error!</strong>
                //             <div class="d-flex justify-content-between align-items-center">
                //                 <p class="mb-0">Qr Salah/Tidak di temukan.</p>
                //                 <button id="btn-rescan" class="btn btn-sm btn-primary">
                //                     Scan Ulang
                //                 </button>
                //             </div>
                //         </div>
                //     `;
                //     document.getElementById('btn-rescan').addEventListener('click', () => {
                //         resultElement.innerHTML = '';
                //         startQRScanner();
                //     });
                // });
            },
            (errorMessage) => {
                console.log(errorMessage)
            }
        ).then(() => {
            console.log('Scanner berhasil dimulai');
            isScanning = true;
            loadingElement.style.display = 'none';
        }).catch(err => {
            console.error(`Unable to start scanning: ${err}`);
            loadingElement.style.display = 'none';
            resultElement.innerHTML = `
                <div class="alert alert-warning" role="alert">
                    <strong>Gagal membuka kamera!</strong><br>
                    Pastikan Anda sudah memberikan izin akses kamera.<br>
                    <small>Error: ${err}</small>
                </div>
            `;
            isScanning = false;
        });
    }

    function closeQRModal() {
        console.log('Menutup modal QR');
        stopScanner();                 
        $('#scanModal').modal('hide'); 
    }


    function stopScanner() {
        console.log('stopScanner dipanggil');
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                console.log('Scanner stopped');
                isScanning = false;
                html5QrCode.clear();
            }).catch(err => {
                console.error('Error stopping scanner:', err);
            });
        }
    }

    $('#scanModal').on('hidden.bs.modal', function () {
        console.log('Modal ditutup');
        stopScanner();
        document.getElementById('scanResult').innerHTML = '';
        document.getElementById('loading').style.display = 'none';
        scanCallback = null;
    });
    function checkout(name, phone, id){
        $.ajax({
            url: '/cart/checkout',
            type: 'POST',
            data: {
                name: name,
                phone: phone,
                qr_code_id: id,
                // _token: '{{ csrf_token() }}'
            },
            xhrFields: { withCredentials: true },
            beforeSend: function () {
                Swal.fire({
                    title: 'Memproses Pesanan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (res) {
                loadCart();
                loadHistory();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pesanan berhasil dibuat.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            },
            error: function (xhr, status, error) {
                let message = 'Terjadi kesalahan saat checkout.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    if (xhr.responseJSON.errors) {
                        const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                        message = firstError;
                    }
                } 
                else if (xhr.responseText) {
                    message = xhr.responseText;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                console.error('Checkout Error:', {
                    status,
                    error,
                    response: xhr.responseJSON || xhr.responseText
                });
            }
        });
    }
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
            cartItems.forEach((item, index) => {
                const hargaNumber = parseInt(String(item.harga).replace(/[^0-9]/g, '')) || 0;
                const total = hargaNumber * item.qty;
                totalPrice += total;
                html += `
                    <div class="table-row">
                        <div class="serial">${index + 1}</div>
                        <div class="percentage">${item.nama}</div>
                        <div class="country">${item.desc || '-'}</div>
                        <div class="visit">Rp ${hargaNumber.toLocaleString('id-ID')}</div>
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
                    ? new Date(order.date.replace(' ', 'T') + '+07:00').toLocaleString('id-ID', {
                        timeZone: 'Asia/Jakarta',
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
                        <div class="percentage">
                            ${order.count} menu, total pesan ${order.count_item}
                        </div>
                        <div class="percentage">
                            <span class="badge bg-light text-dark">${dateFormatted}</span>
                        </div>
                        <div class="percentage">
                            <span class="badge bg-light text-dark">${totalFormatted}</span>
                        </div>
                        <div class="visit text-success fw-bold">Di Pesan</div>
                    </div>`;
            });

            $('#orderTable').html(html);
        });
    }
    function validateCheckout(){
        const name = $('#modal-confirm input[name="name"]').val();
        const phone = $('#modal-confirm input[name="phone"]').val();
        if (name === '' || phone === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Harap isi semua kolom sebelum melanjutkan!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return; 
        }
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Pesanan',
            html: `
                <div style="text-align:center">
                    <b>Nama:</b> ${name}<br>
                    <b>No HP:</b> ${phone}<br>
                </div>
                <br>
                Apakah data pesanan sudah benar?
            `,
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Sekarang',
            cancelButtonText: 'Periksa Lagi',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal.fire({
                //     title: 'Memproses Pesanan...',
                //     html: 'Harap tunggu, pesanan Anda sedang dikirim.',
                //     allowOutsideClick: false,
                //     didOpen: () => {
                //         Swal.showLoading();
                //     }
                // });
                $('#modal-confirm').modal('hide');
        
                // Tutup SweetAlert loading dulu
                // setTimeout(() => {
                    Swal.close();
                    
                    // Panggil fungsi scan QR Code dengan callback
                    openQRScanner(function(qrData) {
                        // Callback ini dipanggil setelah QR code berhasil di-scan dan valid
                        console.log('QR Code Data:', qrData);
                        
                        // Lanjutkan proses checkout
                        checkout(name, phone, table_number, qrData);
                    });
                // }, 500);
                // checkout(name, phone, table_number);
            }
        });
    }
    function cekQRCode(){

    }

    $(document).on('click', '#btn-checkout', function () {
        validateCheckout();
    });

    $(document).on('click', '#btn-order', function () {
        $.getJSON('/cart/data', function (res) {
            const cartItems = res.cart || [];
            if (!cartItems.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang Kosong',
                    text: 'Silakan tambahkan produk terlebih dahulu sebelum checkout!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return; 
            }
            $('#modal-confirm').modal('show');
        }).done((res)=> {
            console.log(res)
        }).fail((err) => {
            console.log(err)
        });
        
    });

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
                    // _token: '{{ csrf_token() }}'
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
                    data: { 
                        // _token: '{{ csrf_token() }}'
                    },
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

    loadCart();
    loadHistory();

});
</script>
@endpush
