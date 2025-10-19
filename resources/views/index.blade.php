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
