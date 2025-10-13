@extends('layouts.main-home')

@section('title', "HOME")
@push('style-css')
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
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
        <h2 class="text-center mb-4 fw-bold" style="font-family: 'Playfair Display', serif;">
            ✨ Cita Rasa dari <span class="text-primary">Kedai Holand</span>
        </h2>
        <p class="text-center text-muted fst-italic mb-5">
            “Dari dapur kami, untuk selera terbaikmu.”
        </p>
        <div class="search-box mb-5">
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

        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card menu-card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="menu-name">Nasi Goreng Spesial</h5>
                        <p class="menu-desc mb-2">Nasi goreng dengan ayam suwir, telur, dan sayuran segar.</p>
                        <p class="menu-price mb-0">Rp 25.000</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill bg-secondary">Secondary</span>
                        <span class="badge rounded-pill bg-primary">Primary</span>
                        <span class="badge rounded-pill bg-success">Success</span>
                        <span class="badge rounded-pill bg-danger">Danger</span>
                        <span class="badge rounded-pill bg-warning text-dark">Warning</span>
                        <span class="badge rounded-pill bg-info text-dark">Info</span>
                        <span class="badge rounded-pill bg-light text-dark">Light</span>
                        <span class="badge rounded-pill bg-dark">Dark</span>
                    </div>
                     <div class="text-end mt-3">
                        <button class="btn btn-outline-primary btn-cart">+ Keranjang</button>
                    </div>
                </div>
            </div>
            </div>

        </div>
    </div>
  </section>
@endsection
@push('script-js')
    
@endpush