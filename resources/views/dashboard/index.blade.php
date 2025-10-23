@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Dashboard")    
@section('title-page', "Dashboard")    
@section('main')
<div class="row">
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Today's Incomes</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="activity"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="today-income">14.212</h1>
               
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title" id="today-order">Today's Orders</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1">64</h1>
                
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Incomes</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="all-income">2.382</h1>
               
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Orders</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="truck"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="all-order">$21.300</h1>
            </div>
        </div>
    </div>
    
</div>
<div class="card flex-fill w-100">
    <div class="card flex-fill">
        <div class="card-header">
            <h5 class="card-title mb-0">Latest Orders</h5>
        </div>
        <table class="table table-hover my-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="d-none d-xl-table-cell">Total Order</th>
                    <th class="d-none d-xl-table-cell">Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="latest-orders">
                <tr>
                    <td>Project Apollo</td>
                    <td class="d-none d-xl-table-cell">01/01/2021</td>
                    <td class="d-none d-xl-table-cell">31/06/2021</td>
                    <td><span class="badge bg-success">Done</span></td>
                </tr>
                <tr>
                    <td>Project Fireball</td>
                    <td class="d-none d-xl-table-cell">01/01/2021</td>
                    <td class="d-none d-xl-table-cell">31/06/2021</td>
                    <td><span class="badge bg-danger">Cancelled</span></td>
                </tr>
                <tr>
                    <td>Project Hades</td>
                    <td class="d-none d-xl-table-cell">01/01/2021</td>
                    <td class="d-none d-xl-table-cell">31/06/2021</td>
                    <td><span class="badge bg-success">Done</span></td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('script')
    
@endpush

