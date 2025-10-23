@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Order")    
@section('title-page', "Order")   
@section('main')
<div class="row mb-5">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Menunggu</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="log-in"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="wait"></h1>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Di Proses</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="loader"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="process"></h1>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Di Hidangkan</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="bell"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="serve"></h1>
               
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Selesai</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="check-circle"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1" id="done"></h1>
            </div>
        </div>
    </div>
</div>
<div class="card flex-fill w-100">
    <div class="card flex-fill">
        <div class="card-header">
            <h5 class="card-title mb-0">Orders</h5>
        </div>
        <table class="table table-hover my-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Table Number</th>
                    <th class="d-none d-md-table-cell">Order Code</th>
                    <th class="d-none d-md-table-cell">Total Price</th>
                    <th class="d-none d-md-table-cell">Payment Method</th>
                    <th class="d-none d-md-table-cell">Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="orderList">
                {{-- <tr>
                    <td>Project Apollo</td>
                    <td>01/01/2021</td>
                    <td>31/06/2021</td>
                    <td class="d-none d-md-table-cell">31/06/2021</td>
                    <td class="d-none d-md-table-cell">31/06/2021</td>
                    <td class="d-none d-md-table-cell">31/06/2021</td>
                    <td class="d-none d-md-table-cell"><span class="badge bg-success">Selesai</span></td>
                    <td><button class="btn btn-info btn-sm rounded"><i class="align-middle" data-feather="edit"></i></button></td>
                </tr> --}}
            </tbody>
        </table>
        <div id="pagination" class="mt-3"></div>
    </div>
</div>
@endsection
@push('script')
<script>
    $(document).ready(function() {
        function loadData(page = 1) {
            $.ajax({
                url: `/order/data?page=${page}`,
                method: 'GET',
                beforeSend: function() {
                    $('#orderList').html(`
                        <tr><td colspan="8" class="text-center py-5 text-muted">Memuat data...</td></tr>
                    `);
                },
                success: function(response) {
                    if (response.data && response.data.length) {
                        renderOrders(response.data);
                        renderPagination(response); // 🔥 render tombol pagination
                    } else {
                        $('#orderList').html(`
                            <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada pesanan.</td></tr>
                        `);
                        $('#pagination').html(''); // kosongkan pagination
                    }

                    if (typeof feather !== 'undefined') feather.replace();
                },
                error: function() {
                    $('#orderList').html(`
                        <tr><td colspan="8" class="text-center text-danger py-5">Gagal memuat data.</td></tr>
                    `);
                }
            });
        }
        function renderPagination(response) {
            let html = `<nav><ul class="pagination justify-content-center">`;

            if (response.prev_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page - 1}">&laquo;</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
            }

            // Loop halaman
            for (let i = 1; i <= response.last_page; i++) {
                html += `
                    <li class="page-item ${i === response.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            if (response.next_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page + 1}">&raquo;</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
            }

            html += `</ul></nav>`;

            $('#pagination').html(html);
        }

        $(document).on('click', '.page-link[data-page]', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadData(page);
        });





        // function loadData(){
        //     $.ajax({
        //         url: '/order/data',
        //         method: 'GET',
        //         success: function(response) {
        //             if (response.data && response.data.length) {
        //                 renderOrders(response.data);
        //             } else {
        //                 $('#orderList').html(`
        //                     <tr>
        //                         <td colspan="8" class="text-center py-5">
        //                             <div class="text-muted fw-semibold">
        //                                 <i class="align-middle" data-feather="inbox"></i> Tidak ada pesanan saat ini.
        //                             </div>
        //                             <div class="small text-secondary mt-2">
        //                                 Semua pesanan akan muncul di sini setelah pelanggan memesan.
        //                             </div>
        //                         </td>
        //                     </tr>
        //                 `);

        //                 if (typeof feather !== 'undefined') feather.replace();
        //             }
        //         },
        //         error: function() {
        //                 $('#orderList').html(`
        //                     <tr>
        //                         <td colspan="8" class="text-center py-5">
        //                             <div class="text-danger fw-bold">
        //                                 <i class="align-middle" data-feather="alert-circle"></i>
        //                                 Gagal memuat data pesanan.
        //                             </div>
        //                             <div class="text-muted mt-2 small">Periksa koneksi atau coba lagi nanti.</div>
        //                             <button class="btn btn-sm btn-outline-danger mt-3" onclick="loadData()">
        //                                 <i class="align-middle" data-feather="refresh-cw"></i> Coba Lagi
        //                             </button>
        //                         </td>
        //                     </tr>
        //                 `);

        //                 if (typeof feather !== 'undefined') feather.replace();
        //         }
        //     });
        // }
        function renderOrders(orders) {
                let html = '';

            orders.forEach(order => {
                let badgeClass = '';
                switch (order.status) {
                    case 'menunggu': badgeClass = 'bg-warning'; break;
                    case 'diproses': badgeClass = 'bg-secondary'; break;
                    case 'dihidangkan': badgeClass = 'bg-primary'; break;
                    case 'selesai': badgeClass = 'bg-success'; break;
                    default: badgeClass = 'bg-danger';
                }

                // <td>${new Date(order.created_at).toLocaleDateString('id-ID')}</td>
                // <td class="d-none d-md-table-cell">${order.total ? 'Rp ' + Number(order.total).toLocaleString('id-ID') : '-'}</td>
                html += `
                    <tr>
                        <td>${order.name ?? '-'}</td>
                        <td>${order.phone ?? '-'}</td>
                        <td>${order.table_number ?? '-'}</td>
                        <td class="d-none d-md-table-cell">${order.order_code ?? '-'}</td>
                        <td class="d-none d-md-table-cell">Rp ${Number(order.total_price || 0).toLocaleString('id-ID')}</td>
                        <td class="d-none d-md-table-cell">${order.payment_method ?? '-'}</td>
                        <td class="d-none d-md-table-cell">
                            <span class="badge ${badgeClass} text-uppercase">${order.status ?? '-'}</span>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm rounded btn-edit" data-id="${order.id}">
                                <i class="align-middle" data-feather="edit"></i>
                            </button>
                            <button class="btn btn-primary btn-sm rounded btn-edit" data-id="${order.id}">
                                <i class="align-middle" data-feather="eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#orderList').html(html);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        





        function loadOrderInfo() {
            $.ajax({
                url: '/order/data/info',
                method: 'GET',
                beforeSend: function() {
                    $('#wait, #process, #serve, #done').html('<span class="text-muted">...</span>');
                },
                success: function(response) {
                    if (response) {
                        const waitCount = response.wait ? response.wait.length : 0;
                        const processCount = response.process ? response.process.length : 0;
                        const serveCount = response.serve ? response.serve.length : 0;
                        const doneCount = response.done ? response.done.length : 0;

                        $('#wait').text(waitCount);
                        $('#process').text(processCount);
                        $('#serve').text(serveCount);
                        $('#done').text(doneCount);
                    } else {
                        $('#wait, #process, #serve, #done').html('<span class="text-muted">0</span>');
                    }

                    if (typeof feather !== 'undefined') feather.replace();
                },
                error: function() {
                    $('#wait, #process, #serve, #done').html(`
                        <span class="text-danger fw-bold">!</span>
                    `);
                    console.error("Gagal memuat data status pesanan.");
                }
            });
        }

        loadData();
        loadOrderInfo();
        Echo.channel(`order-event`)
        .listen('OrderEvent', (e) => {
            loadData();
            loadOrderInfo();
        });
    });

</script>
@endpush
