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
            <div class="card-title row mb-3 mt-3">
                <div class="col-lg-6 d-flex gap-2 mb-2">
                    <select id="filterStatus" class="form-select" style="max-width: 180px;">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="diproses">Diproses</option>
                        <option value="dihidangkan">Dihidangkan</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                    <button id="btnAdd" class="btn btn-primary " data-method='add'>
                        <i data-feather="plus"></i> Buat Order
                    </button>
                </div>
                <div class="col-lg-6 d-flex gap-2 mb-2">
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

<!--  Modal Add Order -->
<div class="modal fade" id="modalAddOrder" tabindex="-1" aria-labelledby="modalAddOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAddOrderLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddOrder">
        <input type="hidden" name="id">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <label>Nama Pelanggan</label>
              <input type="text" class="form-control" name="name">
            </div>
            <div class="col-md-4">
              <label>No. Telepon</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="col-md-4">
              <label>No. Meja</label>
              <input type="text" class="form-control" name="table_number">
            </div>
          </div>

          <hr>
          <h6>Cari Produk</h6>
          <div class="d-flex justify-content-between">
                <div class="col-lg-8 d-flex mb-2 gap-2">
                    <input type="text" id="searchAdd" class="form-control" placeholder="Cari menu...">
                    <button type="button" id="btnSearchAdd" class="col-lg-2 btn btn-primary">
                        <i data-feather="search"></i> Cari
                    </button>
                </div>
                <select id="selectCategory" class="col-lg-2 btn mb-2 ms-2 form-select border-secondary" style="max-width: 180px;"></select>
          </div>
            <div class="row justify-content-between mt-3">
                <div class="col-lg-6 py-1">
                    <h6>Produk</h6>
                    <table class="table table-hover">
                        <thead id="headerProductList"></thead>
                        <tbody id="productList"></tbody>
                    </table>
                </div>
                <div class="col-lg-6 py-1">
                    <h6>Produk yang di pilih</h6>
                    <table class="table table-hover">
                        <thead id="headerProductListSelected"></thead>
                        <tbody id="productListSelected"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Order</button>
        </div>
      </form>
    </div>
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
        let menuItemSelected = []

        $('#search').on('keypress', function (e) {
            if (e.which === 13) {
                loadData();
            }
        });

        $('#search').on('input', function () {
            if ($(this).val().trim() === '') {
                loadData();
            }
        });

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

        function loadDataAdd() {
            const search = $('#searchAdd').val();
            const category = $('#selectCategory').val();
            $.ajax({
                url: `/api/get-data/product?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`,
                method: 'GET',
                beforeSend: function() {
                    $('#productList').html(`
                        <tr><td colspan="8" class="text-center py-5 text-muted">Memuat data...</td></tr>
                    `);
                },
                success: function(response) {
                    if (response.data && response.data.length) {
                        $('#headerProductList').html(
                            `
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            `
                        )
                        renderAddProduct(response.data);
                    } else {
                        $('#productList').html(`
                            <tr><td colspan="8" class="text-center py-5 text-muted">Menu tidak di temukan.</td></tr>
                        `);
                    }

                    if (typeof feather !== 'undefined') feather.replace();
                },
                error: function(err) {
                    $('#productList').html(`
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

        $('#btnSearchAdd').on('click', function() {
            const search = $('#searchAdd').val().trim();
            if (search.length === 0) {
                return;
            }
            loadDataAdd();
        });

        $('#btnReset').on('click', function() {
            $('#search').val('');
            $('#filterStatus').val('');
            loadData(1);
        });

        function renderAddProduct(products) {
            let html = '';
            products.forEach(item => {
                html += `
                   <tr>    
                        <td>${item.name}</td>
                        <td class="d-flex justify-content-end">
                            <button  type="button" class="btn btn-success btn-sm rounded" data-id="${item.id}" data-option_id="${item.option_id}"  
                            data-option='${JSON.stringify(item.option && item.option.items ? item.option.items : [])}'
                            data-option_name="${item.option && item.option.name ? item.option.name : "-"}" data-name="${item.name}" id="addProduct">
                                <i data-feather="plus" class="align-middle"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#productList').html(html);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

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
                            <button class="btn btn-warning btn-sm rounded btn-change-status" data-id="${order.id}">
                                <i data-feather="info" class="align-middle"></i>
                            </button>
                            <button class="btn btn-info btn-sm rounded" id="btnEdit" 
                                    data-id="${order.id}"
                                    data-name="${order.name}"
                                    data-phone="${order.phone}"
                                    data-table_number="${order.table_number}"
                                    >
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

        $(document).on('click', '.btn-change-status', function() {
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

        function addToMenuSelected(id){
            $.getJSON(`/order/${id}`, function(res) {
                $('#headerProductListSelected').html(
                    `
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Option</th>
                            <th class="text-end">Action</th>
                        </tr>
                    `
                )
                let itemIsSelect = []
                if (res.order_items && res.order_items.length) {
                    res.order_items.forEach(item => {
                        itemIsSelect.push({
                            id: item.product.id,
                            name: item.product.name,
                            qty: item.quantity,
                            option_id: item.product.option_id ?? null, 
                            option: item.product.option?.items ?? null,
                            option_name: item.product.option?.name ?? null,
                        });
                    });
                }
                menuItemSelected.unshift(...itemIsSelect);
            }).always(() => {
                renderMenuSelected();
            });
        }

        let categoryList = [];
        $(document).on('click', '#btnAdd, #btnEdit', function() {
            const isEdit = $(this).attr('id') === 'btnEdit'; 
            const id = $(this).data('id');
            $("#searchAdd").val("")
            let title = '';
            menuItemSelected = [];
            if(isEdit){
                addToMenuSelected(id);
                title = 'Edit Order'
            }else{
                renderMenuSelected();
                title = 'Tambah Order'
            }
            $("#modalAddOrderLabel").html(title)
            
            $('#headerProductList').html('');
            $('#productList').html(`
                <tr><td class="text-center">Belum ada</td></tr>
            `);
            
            if (isEdit) {
                $('#formAddOrder input[name="id"]').val($(this).data('id'));
                $('#formAddOrder input[name="name"]').val($(this).data('name'));
                $('#formAddOrder input[name="phone"]').val($(this).data('phone'));
                $('#formAddOrder input[name="table_number"]').val($(this).data('table_number'));
            } else {
                $('#formAddOrder input[name="id"]').val("")
                $('#formAddOrder')[0].reset();
            }
            if (categoryList.length === 0) {
                $.ajax({
                    url: '/api/get-data/category',
                    method: 'GET',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Tunggu sebentar..',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(res) {
                        let categorySelect = $('#selectCategory');
                        categorySelect.html('<option value="">Pilih Category</option>');

                        if (res.success && Array.isArray(res.data) && res.data.length > 0) {
                            categoryList = res.data.map(cat => {
                                categorySelect.append(`<option value="${cat.id}">${cat.name}</option>`);
                                return { id: cat.id, name: cat.name };
                            });
                        }
                    },
                    error: function() {
                        console.error('Gagal memuat kategori.');
                    },
                    complete: function() {
                        Swal.close();
                        $('#modalAddOrder').modal('show');
                    }
                });
            } else {
                let categorySelect = $('#selectCategory');
                categorySelect.html('<option value="">Pilih Category</option>');
                categoryList.forEach(cat => {
                    categorySelect.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
                $('#modalAddOrder').modal('show');
            }
        });

        $('#formAddOrder').on('submit', function(e) {
            e.preventDefault();

            let id = $('#formAddOrder input[name="id"]').val();
            if (!menuItemSelected || menuItemSelected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada produk',
                    text: 'Silakan pilih minimal satu produk sebelum menyimpan!',
                    confirmButtonText: 'OK'
                });
                return;
            }
            let products = menuItemSelected.map(item => ({
                product_id: item.id,
                qty: item.qty,
                note: item.note
            }));

            let formData = {
                name: $('#formAddOrder input[name="name"]').val(),
                phone: $('#formAddOrder input[name="phone"]').val(),
                table_number: $('#formAddOrder input[name="table_number"]').val(),
                products: products
            };

            let isEdit = id !== "" && id !== null;
            let url = isEdit ? `/api/update-data/order/${id}` : `/api/create-data/order`;
            let method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                beforeSend: function() {
                    Swal.fire({
                        title: isEdit ? 'Menyimpan perubahan...' : 'Membuat order...',
                        text: 'Mohon tunggu sebentar...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalAddOrder').modal('hide');
                    $('#formAddOrder')[0].reset();
                    loadData()
                    menuItemSelected = []; 
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
                    });
                }
            });
        });

        function renderMenuSelected(){
            let html = '';
            if(menuItemSelected.length == 0){
                $('#headerProductListSelected').html('')
                html = `
                    <tr>    
                        <td class="text-center">Belum ada</td>
                    </tr>
                `;
            }else{
                menuItemSelected.forEach(item => {
                    html += `
                       <tr>    
                            <td>${item.name}</td>
                            <td style="max-width: 100px;"> <input type="number" class="form-control" id="qty-input" data-id="${item.id}" value="${item.qty}" placeholder="Qty"></td>
                            <td> 
                               ${item.option_id
                                ? `
                                    <select id="selectOption-${item.id}" class="form-select border-secondary">
                                        <option value="">Pilih Opsi</option>
                                        ${item.option.map(opt => `
                                        <option value="${opt.id}">${opt.name}</option>
                                        `).join('')}
                                    </select>
                                `
                                : '-'
                                }

                            </td>
                            <td class="d-flex justify-content-end align-items-center">
                                <button type="button" class="btn btn-danger btn-sm rounded" data-id="${item.id}" id="deleteProduct">
                                    <i data-feather="trash" class="align-middle"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#productListSelected').html(html);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }

        }

        $(document).on('input', '#qty-input', function() {
            const id = $(this).data('id');
            const newQty = parseInt($(this).val()) || 1;
            const item = menuItemSelected.find(p => p.id === id);
            if (item) item.qty = newQty;
        });
        $(document).on('input', '#selectOption', function() {
            const note = $(this).val();
            const id = $(this).data('id');
            const item = menuItemSelected.find(p => p.id === id);
            if (item) item.note = note;
        });

        $(document).on('click', '#deleteProduct', function() {
            let id = $(this).data("id")
            const index = menuItemSelected.findIndex(p => p.id === id);

            if (index !== -1) {
                menuItemSelected.splice(index, 1); 
                renderMenuSelected()
                
            }
        })

        $(document).on('click', '#addProduct', function() {
            let id = $(this).data("id");
            let name = $(this).data("name");
            let option_name = $(this).data("option_name");
            let option_id = $(this).data("option_id");
            let option = $(this).data("option");
            if (typeof option === "string") {
                try {
                    option = JSON.parse(option);
                } catch (e) {
                    option = [];
                }
            }

            if (!Array.isArray(option)) {
                option = [];
            }
            addProductToList(id, name, option_id, option, option_name)
        })

        function addProductToList(id, name, option_id, option, option_name){

            const existingProduct = menuItemSelected.find(p => p.id === id);

            if (!existingProduct) {
                $('#headerProductListSelected').html(
                    `
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Option</th>
                            <th class="text-end">Action</th>
                        </tr>
                    `
                )
                // existingProduct.qty += 1;
                menuItemSelected.push({
                    id: id,
                    name: name,
                    qty: 1,
                    option_id: option_id, 
                    option: option,
                    option_name: option_name,
                });
                renderMenuSelected();
            }else{
                Swal.fire({
                    icon: 'info',
                    title: 'Menu sudah di tambahkan',
                    // text: 'Menu sudah di tambahkan',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        }

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
