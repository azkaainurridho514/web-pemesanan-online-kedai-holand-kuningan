@extends('layouts.main-home')

@section('title', "HOME")
@push('style-css')
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Roboto', sans-serif;
        }
        .menu-card {
            border: none;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .menu-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #222;
        }
        .menu-desc {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .menu-price {
            color: #0d6efd;
            font-weight: 600;
        }
        .btn-cart {
            border-radius: 30px;
            font-weight: 500;
            padding: 0.45rem 1rem;
        }
        .search-box {
            max-width: 100%;
            margin: 0 auto 2rem;
        }
  </style>
@endpush
@section('main')    
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">
            ✨ Cita Rasa dari <span class="text-primary">Kedai Holand</span>
        </h2>
        <p class="text-center text-muted fst-italic mb-5">
            “Dari dapur kami, untuk selera terbaikmu.”
        </p>
        <div class="search-box mb-3">
            <form class="d-flex justify-content-center" onsubmit="event.preventDefault(); searchMenu();">
                <div class="input-group">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="form-control form-control-lg shadow-sm" 
                    placeholder="Cari menu makanan..."
                    aria-label="Cari menu makanan"
                >
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                </div>
            </form>
        </div>

        <div class="d-flex flex-wrap gap-3 mb-5 justify-content-center" id="categoryList">
        </div>
        <div class="row" id="productList">

            

        </div>
    </div>
  </section>
@endsection
@push('script-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    function getDataProducts(search = '', category = '') {
        $('#productList').html(`
            <div class="d-flex justify-content-center align-items-center my-5">
                <div class="spinner-border text-secondary" style="width: 4rem; height: 4rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: '/get-data/product',
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
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card menu-card h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="menu-name">${item.name}</h5>
                            <p class="menu-desc mb-2">${item.description || '-'}</p>
                            <p class="menu-price mb-0">${item.price}</p>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-outline-primary btn-cart">+ Keranjang</button>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        $('#productList').html(html);
    }

    function getCategories() {
        $('#categoryList').html(`
            <div class="spinner-border text-primary my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `);

        $.ajax({
            url: '/get-data/category',
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
            <button type="button" class="btn btn-outline-primary active" data-id="">
                Semua
            </button>
        `;

        categories.forEach(cat => {
            html += `
                <button type="button" class="btn btn-outline-primary" data-id="${cat.id}">
                    ${cat.name}
                </button>
            `;
        });

        $('#categoryList').html(html);

        $('#categoryList button').on('click', function() {
            $('#categoryList button').removeClass('active');
            $(this).addClass('active');

            const category = $(this).data('id') || '';
            const search = $('#searchInput').val().trim();
            getDataProducts(search, category);
        });
    }

    window.searchMenu = function() {
        const search = $('#searchInput').val().trim();
        const category = $('#categoryList button.active').data('id') || '';
        getDataProducts(search, category);
    };

    $('#searchInput').on('input', function() {
        if ($(this).val().trim() === '') {
            const category = $('#categoryList button.active').data('id') || '';
            getDataProducts('', category);
        }
    });

    getCategories();
    getDataProducts();
});
</script>
@endpush
