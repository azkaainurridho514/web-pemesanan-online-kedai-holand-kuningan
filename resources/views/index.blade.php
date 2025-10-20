@extends('layout-home.main')

@section('title', "HOME")
@section('main')
<div class="row d-flex justify-content-center">
    <div class="single-footer-widget">
        <div class="menu-content pb-60">
            <div class="title text-center">
                <h1 class="mb-10">What kind of Coffee we serve for you</h1>
                <p>Who are in extremely love with eco friendly system.</p>
            </div>
        </div>
    </div>
</div>						
<div class="row d-flex justify-content-between mb-4 align-content-center">
    <div class="single-footer-widget col-md-5 w-full">
        <div class="" id="mc_embed_signup">
            <form class="form-inline" onsubmit="event.preventDefault(); searchMenu();">
                <input class="form-control" id="searchInput" name="search" placeholder="Search..." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search...'" required="" type="text">
                <button class="click-btn btn btn-default"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
                <div style="position: absolute; left: -5000px;">
                    <input name="b_36c4fd991d266f23781ded980_aefe40901a" tabindex="-1" value="" type="text">
                </div>
                <div class="info pt-20"></div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex gap-2 scrollable-row" id="categoryList">
        </div>
    </div>
</div>	

<div class="row" id="productList"></div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>

@endsection
@push('script-js')

<script>
$(document).ready(function() {


    function getDataProducts(search = '', category = '') {
        $('#productList').html(`
            <div class="d-flex justify-content-center align-items-center w-100">
                <div class="spinner-border text-secondary" role="status" style="width: 4rem; height: 4rem;"></div>
            </div>
        `);

        $.ajax({
            url: '/api/get-data/product',
            method: 'GET',
            data: { search, category },
            success: function(response) {
                if (response.success && response.data.length) {
                    renderDataProducts(response.data);
                } else {
                    $('#productList').html(`
                        <div class="text-center text-muted my-5">
                            Tidak ada menu yang cocok.
                        </div>
                    `);
                }
            },
            error: function() {
                $('#productList').html(`
                    <div class="text-center text-danger my-5">
                        Gagal memuat data.
                    </div>
                `);
            }
        });
    }

    function renderDataProducts(data) {
        let html = '';
        data.forEach(item => {
            html += `
            <div class="col-lg-4">
                <div class="single-menu">
                    <h4 class="mb-2">${item.name}</h4>
                    <p class="price">
                        ${item.price}
                    </p>
                    <p>
                        ${item.description || '-'}
                    </p>
                    <div class="mx-1 mt-2 d-flex justify-content-end">
                        <button type="button" class="btn-add genric-btn primary-border radius small" data-id="${item.id}" data-name="${item.name}" data-price="${item.price}" data-option='${JSON.stringify(item.option || {})}' data-toggle="modal" data-target="#modal">Tambah</button>
                    </div>								
                </div>
            </div>`;
        });
        $('#productList').html(html);
    }
    $(document).on('click', '.add-to-cart', function() { 
        const button = $(this);
        const hasOption = String(button.data('has-option')) === "true";
        const product_id = button.data('id'); 
        const nama = button.data('nama');     
        const harga = parseInt(String(button.data('harga')).replace(/[^0-9]/g, '')) || 0;   
        const qty = $('input[name="qty"]').val();
        const desc = $('input[name="desc"]').val();
        const option = $('input[name="option_item"]:checked').val() || null;

        // Validasi input
        if (!qty || qty <= 0) {
            Swal.fire({
                title: "Jumlah belum diisi",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        if (hasOption && !option) {
            Swal.fire({
                title: "Pilih salah satu opsi terlebih dahulu",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        // Ambil cookie cart dulu → supaya tahu ada data sebelumnya atau tidak
        $.getJSON('/cart/data', function (cartResponse) {
            // Kirim ke controller addOrUpdate
            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: product_id,
                    nama: nama,
                    qty: qty,
                    harga: harga,
                    desc: desc || option || "", 
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    Swal.fire({
                        title: res.message,
                        icon: "success",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    console.log('Cart updated:', res.cart);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: "Terjadi kesalahan",
                        text: "Gagal menambahkan ke keranjang",
                        icon: "error"
                    });
                }
            });
        });
    });
    
    $(document).on('click', '.btn-add', function() {
        const productName = $(this).data('name');
        const option = $(this).data('option'); 
        const productId = $(this).data('id');
        const productPrice = $(this).data('price');
        $('#modalLabel').text(productName);

        let optionHtml = '';
        if (option && option.items && option.items.length > 0) {
            optionHtml += `
            <div class="single-input-primary mb-2">
                <div class="d-flex align-items-center flex-wrap">`;

            option.items.forEach(item => {
                optionHtml += `
                    <div class="form-check d-flex align-items-center px-3">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="option_item"
                            id="option_${item.id}"
                            value="${item.name}"
                            style="accent-color: #B68834;"
                        >
                        <label class="form-check-label ms-1" for="option_${item.id}">
                            ${item.name}
                        </label>
                    </div>`;
            });

            optionHtml += `
                </div>
            </div>`;
        }

        $('.modal-body').html(`
            <input type="number" name="qty" placeholder="Jumlah" required class="single-input-primary mb-2">
            ${optionHtml}
            <input type="text" name="desc" placeholder="Catatan" class="single-input-primary">
        `);

        $('.modal-footer').html(`
             <button type="button" class="genric-btn danger radius medium" data-dismiss="modal">Close</button>
            <button type="button" class="genric-btn primary radius medium add-to-cart" 
                data-id="${productId}"
                data-nama="${productName}"
                data-harga="${productPrice}"
                data-has-option="${option && option.items && option.items.length > 0}">
                Simpan
            </button>
        `);
    });
    function getCategories() {
        $('#categoryList').html(`
            <div class="d-flex justify-content-center align-items-center w-100">
                <div class="spinner-border text-secondary" role="status"></div>
            </div>
        `);

        $.ajax({
            url: '/api/get-data/category',
            method: 'GET',
            success: function(response) {
                if (response.success) renderCategories(response.data);
                else $('#categoryList').html(`<p class="text-muted text-center">Tidak ada kategori tersedia.</p>`);
            },
            error: function() {
                $('#categoryList').html(`<p class="text-danger text-center">Gagal memuat kategori.</p>`);
            }
        });
    }

    function renderCategories(categories) {
        let html = `
            <div class="mx-1 mb-2"><button type="button" class="genric-btn primary-border radius small disable" data-id="">Semua</button></div>
        `;

        categories.forEach(cat => {
            html += `
                <div class="mx-1 mb-2"><button type="button" class="genric-btn primary-border radius small" data-id="${cat.id}">${cat.name}</button></div>
            `;
        });

        $('#categoryList').html(html);

        $('#categoryList button').on('click', function() {
            $('#categoryList button').removeClass('disable');
            $(this).addClass('disable');

            const category = $(this).data('id') || '';
            const search = $('#searchInput').val().trim();
            getDataProducts(search, category);
        });
    }

    window.searchMenu = function() {
        const search = $('#searchInput').val().trim();
        const category = $('#categoryList button.disable').data('id') || '';
        getDataProducts(search, category);
    };

    $('#searchInput').on('input', function() {
        if ($(this).val().trim() === '') {
            const category = $('#categoryList button.disable').data('id') || '';
            getDataProducts('', category);
        }
    });

    getCategories();
    getDataProducts();
});
</script>
@endpush
