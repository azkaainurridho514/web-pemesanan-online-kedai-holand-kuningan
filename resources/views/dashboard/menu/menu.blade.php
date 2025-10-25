@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Menu")    
@section('title-page', "Menu")    
@section('main')
<div class="card flex-fill w-100">
    <div class="card flex-fill">
        <div class="card-header">
            <div class="card-title d-flex mb-3 mt-3 row justify-content-between">
                <div class="col-lg-6 d-flex gap-2 flex-grow mb-2">
                    <button id="btnAdd" class="col-lg-2 btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah
                    </button>
                    <select id="filterCategory" class="form-select" style="max-width: 180px;"></select>
                    <select id="filterOption" class="form-select" style="max-width: 180px;"></select>
                    <select id="filterAvailable" class="form-select" style="max-width: 180px;">
                        <option value="">Semua Menu</option>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="col-lg-6 d-flex gap-2 flex-grow mb-2">
                    <input type="text" id="search" class="form-control" placeholder="Cari...">
                    <button id="btnSearch" class="col-lg-2 btn btn-primary">
                        <i data-feather="search"></i> Cari
                    </button>
                    <button id="btnReset" class="col-lg-2 btn btn-outline-secondary">
                        <i data-feather="x"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-hover my-0" id="tableProducts">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th class="d-none d-xl-table-cell text-center">Category</th>
                    <th class="d-none d-xl-table-cell text-center">Option</th>
                    <th class="d-none d-xl-table-cell text-center">Available</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="pagination" class="mt-3"></div>
    </div>
</div>

<div class="modal fade" id="modalMenu" tabindex="-1" aria-labelledby="modalMenuLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMenuLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

@endsection
@push('script') 
<script>
$(document).ready(function () {
    function loadProducts(page = 1) {
        const search = $('#search').val();
        const category = $('#filterCategory').val();
        const option = $('#filterOption').val();
        const available = $('#filterAvailable').val();
        console.log(search)
        $.ajax({
            url: `/api/get-data/product-dashboard?page=${page}`,
            method: 'GET',
            dataType: 'json',
            data: {
                search,
                category_id: category,
                option_id: option,
                is_available: available,
            },
            beforeSend: function () {
                $('#tableProducts tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="spinner-border text-primary me-2" role="status"></div>
                            Memuat data...
                        </td>
                    </tr>
                `);
            },
            success: function (res) {
                const data = res.data;

                if (!data || data.length === 0) {
                    $('#tableProducts tbody').html(`
                        <tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada produk ditemukan</td></tr>
                    `);
                    $('#pagination').html('');
                    return;
                }

                let rows = '';
                $.each(data, function (i, product) {
                    rows += `
                        <tr>
                            <td class="text-center">${(res.from ?? 1) + i}</td>
                            <td>${product.name}</td>
                            <td>${product.description ?? '-'}</td>
                            <td>Rp ${product.price.toLocaleString('id-ID')}</td>
                            <td class="text-center">${product.category_name ?? '-'}</td>
                            <td class="text-center">${product.option_name ?? '-'}</td>
                            <td class="text-center">
                                ${product.is_available
                                    ? '<span class="badge bg-success">Tersedia</span>'
                                    : '<span class="badge bg-danger">Tidak Tersedia</span>'}
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm rounded btn-edit" data-id="${product.id}">
                                    <i data-feather="edit" class="align-middle"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded btn-delete" data-id="${product.id}">
                                    <i data-feather="trash-2" class="align-middle"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $('#tableProducts tbody').html(rows);
                if (typeof feather !== 'undefined') feather.replace();
                renderPagination(res);
            },
            error: function (err) {
                console.error(err);
                $('#tableProducts tbody').html(`
                    <tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data produk</td></tr>
                `);
            }
        });
    }

    function renderPagination(response) {
        let html = `<nav><ul class="pagination justify-content-center">`;

        html += response.prev_page_url
            ? `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page - 1}">&laquo;</a></li>`
            : `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;

        for (let i = 1; i <= response.last_page; i++) {
            html += `
                <li class="page-item ${i === response.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        html += response.next_page_url
            ? `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page + 1}">&raquo;</a></li>`
            : `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;

        html += `</ul></nav>`;
        $('#pagination').html(html);
    }

    $('#btnSearch').on('click', function () {
        loadProducts();
    });

    $('#filterCategory, #filterOption, #filterAvailable').on('change', function() {
        loadProducts();
    });

    $('#btnReset').on('click', function () {
        $('#search').val('');
        $('#filterCategory').val('');
        $('#filterOption').val('');
        $('#filterAvailable').val('');
        loadProducts();
    });

    $(document).on('click', '.pagination .page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) loadProducts(page);
    });

    $('#search').on('keypress', function (e) {
        if (e.which === 13) {
            loadProducts();
        }
    });

    $('#search').on('input', function () {
        if ($(this).val().trim() === '') {
            loadProducts();
        }
    });

    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        $('#modalMenu .modal-body').html(`
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3"></div><br>
                Memuat data menu...
            </div>
        `);
        $('#modalMenu #modalMenuLabel').html(`Detail & Update Menu`);
        $('#modalMenu .modal-footer').html(`
            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSaveMenu">Simpan</button>
            </div>
        `);
        $('#modalMenu').modal('show');

        $.getJSON(`/api/get-data/product/${id}`, function(res) {
            if (!res || !res.product) {
                $('#modalMenu .modal-body').html('<p class="text-danger">Data menu tidak ditemukan.</p>');
                return;
            }

            const product = res.product;
            const categories = res.categories;
            const options = res.options;

            let categoryOptions = '<option value="">-- Pilih Kategori --</option>';
            categories.forEach(cat => {
                const selected = product.category_id === cat.id ? 'selected' : '';
                categoryOptions += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });

            let optionOptions = '<option value="">Tidak ada opsi pada menu</option>';
            options.forEach(opt => {
                const selected = product.option_id === opt.id ? 'selected' : '';
                optionOptions += `<option value="${opt.id}" ${selected}>${opt.name}</option>`;
            });

            const html = `
                <form id="formMenu">
                    <input type="hidden" id="menuId" value="${product.id}">

                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" id="menuName" class="form-control" value="${product.name}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select id="menuCategory" class="form-select" required>
                            ${categoryOptions}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opsi</label>
                        <select id="menuOption" class="form-select">
                            ${optionOptions}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" id="menuPrice" class="form-control" value="${product.price}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" id="menuDesc" class="form-control" value="${product.description ?? ""}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="menuStatus" class="form-select">
                            <option value="1" ${product.is_available === 1 ? 'selected' : ''}>Tersedia</option>
                            <option value="0" ${product.is_available === 0 ? 'selected' : ''}>Tidak Tersedia</option>
                        </select>
                    </div>
                </form>
            `;

            $('#modalMenu .modal-body').html(html);
            feather.replace();
        }).fail(() => {
            $('#modalMenu .modal-body').html('<p class="text-danger">Gagal memuat data menu.</p>');
        });
    });

    $(document).on('click', '#btnSaveMenu', function(e) {
        e.preventDefault();

        const id = $('#menuId').val();
        const name = $('#menuName').val().trim();
        const category = $('#menuCategory').val();
        const option = $('#menuOption').val();
        const desc = $('#menuDesc').val();
        const price = $('#menuPrice').val();
        const status = $('#menuStatus').val();

        if (!name || !category || !price || !status) {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Semua field wajib diisi kecuali Opsi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        $.ajax({
            url: `/api/update-data/product/${id}`,
            method: 'PUT',
            data: {
                name: name,
                category_id: category,
                option_id: option || "", 
                price: price,
                description: desc || "",
                is_available: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btnSaveMenu')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            },
            success: function(res) {
                $('#modalMenu').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data menu berhasil diperbarui.',
                    timer: 2000,
                    showConfirmButton: false
                });

                loadProducts(); 
            },
            error: function(err) {
                console.log(err)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonText: 'Coba Lagi'
                });
            },
            complete: function() {
                $('#btnSaveMenu')
                    .prop('disabled', false)
                    .html('Simpan');
            }
        });
    });

    $(document).on('click', '#btnAdd', function() {
        $('#modalMenu .modal-body').html(`
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3"></div><br>
                Menyiapkan form tambah menu...
            </div>
        `);
        $('#modalMenu #modalMenuLabel').html(`Tambah Menu Baru`);
        $('#modalMenu .modal-footer').html(`
            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSaveNewMenu">Simpan</button>
            </div>
        `);
        $('#modalMenu').modal('show');

        $.getJSON(`/api/get-data/form-menu`, function(res) {
            if (!res || !res.categories) {
                $('#modalMenu .modal-body').html('<p class="text-danger">Gagal memuat form tambah menu.</p>');
                return;
            }

            const categories = res.categories;
            const options = res.options || [];

            let categoryOptions = '<option value="">-- Pilih Kategori --</option>';
            categories.forEach(cat => {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });

            let optionOptions = '<option value="">Tidak ada opsi pada menu</option>';
            options.forEach(opt => {
                optionOptions += `<option value="${opt.id}">${opt.name}</option>`;
            });

            const html = `
                <form id="formAddMenu">
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" id="menuName" class="form-control" placeholder="Contoh: Nasi Goreng Spesial" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select id="menuCategory" class="form-select" required>
                            ${categoryOptions}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opsi</label>
                        <select id="menuOption" class="form-select">
                            ${optionOptions}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" id="menuPrice" class="form-control" placeholder="Contoh: 25000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" id="menuDesc" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="menuStatus" class="form-select">
                            <option value="1">Tersedia</option>
                            <option value="0">Tidak Tersedia</option>
                        </select>
                    </div>
                </form>
            `;

            $('#modalMenu .modal-body').html(html);
            feather.replace();
        }).fail(() => {
            $('#modalMenu .modal-body').html('<p class="text-danger">Gagal memuat data kategori dan opsi.</p>');
        });
    });

    $(document).on('click', '#btnSaveNewMenu', function(e) {
        e.preventDefault();

        const name = $('#menuName').val().trim();
        const category = $('#menuCategory').val();
        const option = $('#menuOption').val();
        const desc = $('#menuDesc').val();
        const price = $('#menuPrice').val();
        const status = $('#menuStatus').val();

        if (!name || !category || !price || status === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Semua field wajib diisi kecuali Opsi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const data = {
            name: name,
            category_id: category,
            option_id: option || "",
            price: price,
            description: desc || "",
            is_available: status
        };
        console.log(data)
        

        $('#btnSaveNewMenu').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '/api/create-data/product',
            type: 'POST',
            data: data,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Menu baru berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#modalMenu').modal('hide');
                loadProducts();

            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan menu.'
                });
            },
            complete: function() {
                $('#btnSaveNewMenu').prop('disabled', false).text('Simpan');
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Menu?',
            text: 'Data menu yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/delete-data/product/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Menu berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        loadProducts(); 
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.'
                        });
                    }
                });
            }
        });
    });

    function loadFilter() {
        $.ajax({
            url: '/api/get-data/category',
            method: 'GET',
            success: function (res) {
                let categorySelect = $('#filterCategory');
                categorySelect.html('<option value="">Semua Categori</option>');

                if (res.success && res.data.length > 0) {
                    res.data.forEach(cat => {
                        categorySelect.append(`<option value="${cat.id}">${cat.name}</option>`);
                    });
                }
            },
            error: function () {
                console.error('Gagal memuat kategori.');
            }
        });

        $.ajax({
            url: '/api/get-data/options',
            method: 'GET',
            success: function (res) {
                let optionSelect = $('#filterOption');
                optionSelect.html('<option value="">Semua Opsi</option>');

                if (res.success && res.data.length > 0) {
                    res.data.forEach(opt => {
                        optionSelect.append(`<option value="${opt.id}">${opt.name}</option>`);
                    });
                }
            },
            error: function () {
                console.error('Gagal memuat option.');
            }
        });
    }

    loadFilter();
    loadProducts();
});


</script>
@endpush
