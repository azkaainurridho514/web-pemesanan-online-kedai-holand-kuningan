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
            <div class="card-title d-flex mb-3 mt-3 gap-2">
                <div class="col-lg-6">
                    <select id="filterStatus" class="form-select" style="max-width: 180px;">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="diproses">Diproses</option>
                        <option value="dihidangkan">Dihidangkan</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div class="col-lg-6 d-flex gap-2">
                    <input type="text" id="search" class="form-control" placeholder="Cari nama / meja / metode...">
                    <button id="btnSearch" class="col-lg-2 btn btn-primary">
                        <i data-feather="search"></i> Cari
                    </button>
                    <button id="btnReset" class="col-lg-2 btn btn-outline-secondary">
                        <i data-feather="x"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Table Number</th>
                    <th class="d-none d-xl-table-cell">Order Code</th>
                    <th class="d-none d-xl-table-cell">Total Price</th>
                    <th class="d-none d-xl-table-cell">Payment Method</th>
                    <th class="d-none d-xl-table-cell">Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="orderList"></tbody>
        </table>
        <div id="pagination" class="mt-3"></div>
    </div>
</div>

<!--  Modal Update Status -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUpdateStatusLabel">Ubah Status Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formUpdateStatus">
          <input type="hidden" id="orderId">
          <div class="mb-3">
            <label for="orderStatus" class="form-label">Status</label>
            <select id="orderStatus" class="form-select" required>
              <option value="">-- Pilih Status --</option>
              <option value="menunggu">Menunggu</option>
              <option value="diproses">Diproses</option>
              <option value="dihidangkan">Dihidangkan</option>
              <option value="selesai">Selesai</option>
              <option value="batal">Batal</option>
            </select>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--  Modal Info Pesanan -->
<div class="modal fade" id="modalInfoOrder" tabindex="-1" aria-labelledby="modalInfoOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoOrderLabel">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="orderInfoBody">
        <p class="text-muted">Memuat data...</p>
      </div>
    </div>
  </div>
</div>


@endsection
@push('script')
<script>
    $(document).ready(function() {
        function loadData(page = 1) {
            const search = $('#search').val(); 
            const status = $('#filterStatus').val(); 

            $.ajax({
                url: `/order/data?page=${page}&search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`,
                method: 'GET',
                beforeSend: function() {
                    $('#orderList').html(`
                        <tr><td colspan="8" class="text-center py-5 text-muted">Memuat data...</td></tr>
                    `);
                },
                success: function(response) {
                    if (response.data && response.data.length) {
                        renderOrders(response.data);
                        renderPagination(response); 
                    } else {
                        $('#orderList').html(`
                            <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada pesanan.</td></tr>
                        `);
                        $('#pagination').html(''); 
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

        $('#filterStatus').on('change', function() {
            loadData(1);
        });

        $('#btnSearch').on('click', function() {
            const search = $('#search').val().trim();
            if (search.length === 0) {
                return;
            }
            loadData(1);
        });

        $('#btnReset').on('click', function() {
            $('#search').val('');
            $('#filterStatus').val('');
            loadData(1);
        });

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

                html += `
                    <tr>
                        <td>${order.name ?? '-'}</td>
                        <td>${order.phone ?? '-'}</td>
                        <td>${order.table_number ?? '-'}</td>
                        <td class="d-none d-xl-table-cell">${order.order_code ?? '-'}</td>
                        <td class="d-none d-xl-table-cell">Rp ${Number(order.total_price || 0).toLocaleString('id-ID')}</td>
                        <td class="d-none d-xl-table-cell">${order.payment_method ?? '-'}</td>
                        <td class="d-none d-xl-table-cell">
                            <span class="badge ${badgeClass} text-uppercase">${order.status ?? '-'}</span>
                        </td>
                        <td>
                        <button class="btn btn-info btn-sm rounded btn-edit" data-id="${order.id}">
                            <i data-feather="edit" class="align-middle"></i>
                        </button>
                        <button class="btn btn-primary btn-sm rounded btn-view" data-id="${order.id}">
                            <i data-feather="eye" class="align-middle"></i>
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


        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            $('#orderId').val(id);
            $('#orderStatus').val('');
            $('#modalUpdateStatus').modal('show');
        });

        $('#formUpdateStatus').on('submit', function(e) {
            e.preventDefault();

            const id = $('#orderId').val();
            const status = $('#orderStatus').val();

            if (!status) {
                alert('Pilih status terlebih dahulu.');
                return;
            }

            $.ajax({
                url: `/order/${id}/status`,
                method: 'PUT',
                data: {
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#formUpdateStatus button[type=submit]').prop('disabled', true).text('Menyimpan...');
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'status pesanan berhasil diubah.',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    $('#modalUpdateStatus').modal('hide');
                    loadData(); 
                    loadOrderInfo()
                },
                error: function(err) {
                    alert('Gagal mengubah status pesanan.');
                },
                complete: function() {
                    $('#formUpdateStatus button[type=submit]').prop('disabled', false).text('Simpan');
                }
            });
        });


        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            
            $('#modalInfoOrder').modal('show');
            $('#orderInfoBody').html('<p class="text-muted">Memuat data...</p>');
            
            $.getJSON(`/order/${id}`, function(res) {
                console.log(res)
                if (!res) {
                    $('#orderInfoBody').html('<p class="text-danger">Data pesanan tidak ditemukan.</p>');
                    return;
                }

                const createdAt = new Date(res.created_at).toLocaleString('id-ID');
                const statusBadge = {
                    menunggu: 'bg-warning',
                    diproses: 'bg-info',
                    dihidangkan: 'bg-primary',
                    selesai: 'bg-success',
                    batal: 'bg-danger'
                }[res.status] || 'bg-secondary';

                const items = res.order_items && res.order_items.length
                    ? res.order_items.map((item, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.product == null ? "-" : item.product.name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.note ? `<small class="text-muted">${item.note}</small>` : '-'}</td>
                            <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                        </tr>
                    `).join('')
                    : '<tr><td colspan="5" class="text-center text-muted">Tidak ada item pesanan</td></tr>';

                const totalFormatted = Number(res.total_price).toLocaleString('id-ID');

                $('#orderInfoBody').html(`
                    <div class="mb-3">
                        <div><strong>Order Code:</strong> ${res.order_code}</div>
                        <div><strong>Nama:</strong> ${res.name}</div>
                        <div><strong>Telepon:</strong> ${res.phone}</div>
                        <div><strong>Nomor Meja:</strong> ${res.table_number}</div>
                        <div><strong>Metode Pembayaran:</strong> ${res.payment_method}</div>
                        <div><strong>Status:</strong> <span class="badge ${statusBadge} text-uppercase">${res.status}</span></div>
                        <div><strong>Waktu Pesan:</strong> ${createdAt}</div>
                    </div>
                    <hr>
                    <h6 class="mb-3">Item Pesanan</h6>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Catatan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items}
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <h6><strong>Total:</strong> Rp ${totalFormatted}</h6>
                    </div>
                `);
            }).fail(() => {
                $('#orderInfoBody').html('<p class="text-danger">Gagal memuat data pesanan.</p>');
            });
        });





        // ======================================================
        // ======================================================

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
