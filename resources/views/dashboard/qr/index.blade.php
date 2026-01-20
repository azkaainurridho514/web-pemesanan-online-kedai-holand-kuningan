@extends('layout-dashboard.main')
@section('title', "Kedai Holand | QR Code")    
@section('title-page', "QR Code")   
@section('main')
<div class="card flex-fill w-100">
    <div class="card flex-fill">
        <div class="card-header">
            <div class="card-title d-flex mb-3 mt-3 row justify-content-between">
                <div class="col-lg-6 d-flex gap-2 flex-grow mb-2">
                    <button id="btnAdd" class="col-lg-2 btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah
                    </button>
                </div>
                <div class="col-lg-6 d-flex gap-2 flex-grow mb-2">
                    <input type="text" id="search" class="form-control" placeholder="Cari...">
                    <button id="btnSearch" class="col-lg-2 btn btn-primary">
                        <i data-feather="search"></i> Cari
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-hover my-0" id="tableQrcode">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Qr</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
    const tableBody = $('#tableQrcode tbody');
    function fetchQrcodes(page = 1) {
        const search = $('#search').val();
        $.ajax({
            url: `/api/get-data/qrcode?page=${page}`,
            method: 'GET',
            dataType: 'json',
            data: {
                search,
            },
            beforeSend: function () {
                tableBody.html(`
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
                    tableBody.html(`
                        <tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada data</td></tr>
                    `);
                    return;
                }
                let rows = '';
                $.each(data, function (i, qr) {
                    rows += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>   
                                ${qr.path
                                    ? `<img src="/storage/qr/${qr.path}"
                                            alt="Photo"
                                            class="img-thumbnail img-preview cursor-pointer"
                                            style="width:100px;height:100px;object-fit:cover;"/>`
                                    : `<span class="text-muted">Qr tidak tersedia</span>`
                                }
                            </td>
                            <td>${qr.name}</td>
                            <td>${qr.description ?? '-'}</td>
                            <td>
                                <button class="btn btn-info btn-sm rounded btn-edit" data-id="${qr.id}">
                                    <i data-feather="edit" class="align-middle"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded btn-delete" data-id="${qr.id}">
                                    <i data-feather="trash-2" class="align-middle"></i>
                                </button>
                                <button class="btn btn-warning btn-sm rounded btn-print" data-name="${qr.name}" data-qr="/storage/qr/${qr.path}">
                                    <i data-feather="printer" class="align-middle"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                tableBody.html(rows);
                if (typeof feather !== 'undefined') feather.replace();
                
            },
            error: function (err) {
                console.error(err);
                tableBody.html(`
                    <tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data s</td></tr>
                `);
            }
        });
    }
    $(document).on('click', '.btn-print', function () {
        const qrPath = $(this).data('qr');
        const name = $(this).data('name');

        const printWindow = window.open('', '', 'width=400,height=400');
        printWindow.document.write(`
            <html>
            <head>
                <style>
                    @page {
                        size: 300px 300px;
                        margin: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center; 
                        align-items: center;    
                    }
                    .qr-wrapper {
                        padding: 20px;
                        text-align: center;
                    }
                    .qr-title {
                        margin-bottom: 10px;
                        font-size: 20px;
                        font-weight: bold;
                    }
                    img {
                        width: 200px;
                        height: auto;
                        display: block;
                        margin: 0 auto; /* center image */
                    }

                </style>
            </head>
            <body>
                <div class="qr-wrapper">
                    <div class="qr-title">${name}</div>
                    <img src="${qrPath}" onload="window.print(); window.close();">
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();
    });


    $('#search').on('keypress', function (e) {
        if (e.which === 13) {
            fetchQrcodes();
        }
    });
    $('#search').on('input', function () {
        if ($(this).val().trim() === '') {
            fetchQrcodes();
        }
    });
        $('#btnSearch').on('click', function () {
        fetchQrcodes();
    });

    
    $(document).on('click', '#btnAdd', function () {
        $('#modalMenuLabel').text('Tambah Qrcode');
        $('#modalMenu .modal-body').html(`
            <form id="formQrCode">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Description</label>
                    <textarea class="form-control" id="desc" name="desc"></textarea>
                </div>
            </form>
        `);
        $('#modalMenu .modal-footer').html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="saveQr">Simpan</button>
        `);
        $('#modalMenu').modal('show');
    });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/get-data/qrcode/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#modalMenuLabel').text('Edit Qrcode');
                $('#modalMenu .modal-body').html(`
                    <form id="formQrCode">
                        <input type="hidden" name="id" value="${res.id}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="${res.name}" required>
                        </div>
                        <div class="mb-3">
                            <label for="desc" class="form-label">Description</label>
                            <textarea class="form-control" id="desc" name="desc">${res.description ?? ''}</textarea>
                        </div>
                    </form>
                `);
                $('#modalMenu .modal-footer').html(`
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="updateQrcode">Update</button>
                `);
                $('#modalMenu').modal('show');
            },
            error: function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal mengambil data qrcode',
                    confirmButtonText: 'Coba Lagi'
                });
            }
        });
    });

    $(document).on('click', '#saveQr', function () {
        const formData = $('#formQrCode').serialize();
        $.ajax({
            url: '/api/create-data/qrcode',
            method: 'POST',
            data: formData,
            success: function(res) {
                $('#modalMenu').modal('hide');
                fetchQrcodes();
                 Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data qrcode berhasil ditambahkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                console.log(err)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonText: 'Coba Lagi'
                });
            }
        });
    });

    $(document).on('click', '#updateQrcode', function () {
        const id = $('#formQrCode input[name="id"]').val();
        const formData = $('#formQrCode').serialize();
        $.ajax({
            url: `/api/update-data/qrcode/${id}`,
            method: 'PUT',
            data: formData,
            success: function(res) {
                $('#modalMenu').modal('hide');
                fetchQrcodes(); 
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data qrcode berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                console.log(err)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonText: 'Coba Lagi'
                });
            }
        });
    });
    
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus s?',
            text: 'Data s yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/delete-data/qrcode/${id}`,
                    method: 'DELETE',
                    data: {
                        // _token: $('meta[name="csrf-token"]').attr('content')
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
                            text: res.message || 'Qrcode berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        fetchQrcodes(); 
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
    fetchQrcodes();
});
</script>

@endpush