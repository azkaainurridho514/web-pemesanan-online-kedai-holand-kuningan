@extends('layout-home.layout-cart')
@section('title', "HOME")
@section('main')
<div class="row d-flex justify-content-center">
    <div class="single-footer-widget">
        <div class="menu-content pb-60">
            <div class="title text-center">
                <h1 class="mb-10">Keranjang anda di kedai holand</h1>
                <p>Silahkan pesan pada system kami.</p>
            </div>
        </div>
    </div>
</div>
<div class="section-top-border w-100">
    <div class="progress-table-wrap">
        <div class="progress-table">
            <div class="table-head">
                <div class="serial">#</div>
                <div class="percentage">Menu</div>
                <div class="country">Deskripsi</div>
                <div class="visit">Harga</div>
                <div class="visit">Aksi</div>
            </div>
            <div class="table-row">
                <div class="serial">1</div>
                <div class="percentage">Nasi Goreng pedas</div>
                <div class="country">Jangan di kasih nasi</div>
                <div class="visit">Rp. 20.000</div>
                <div class="visit d-flex align-items-center justify-content-center">
                    <button type="button" class="genric-btn primary-border radius small" data-id="">-</button>
                    <p class="mx-3 mb-0">1</p>
                    <button type="button" class="genric-btn primary-border radius small" data-id="">+</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection