@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Option")    
@section('title-page', "Option")    
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
        <table class="table table-hover my-0" id="tableOption">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Name</th>
                    <th>Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalOption" tabindex="-1" aria-labelledby="modalOptionLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalOptionLabel"></h5>
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
    let optionIndex = 0;
    const tableBody = $('#tableOption tbody');
    function fetchOptions() {
        const search = $('#search').val();
        $.ajax({
            url: `/api/get-data/options`,
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
                        <tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada kategori ditemukan</td></tr>
                    `);
                    return;
                }

                let rows = '';
                $.each(data, function (i, data) {
                    rows += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${data.name}</td>
                            <td>${data.items_count ?? '-'}</td>
                            <td>
                                <button class="btn btn-info btn-sm rounded btn-edit" data-id="${data.id}">
                                    <i data-feather="edit" class="align-middle"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded btn-delete" data-id="${data.id}">
                                    <i data-feather="trash-2" class="align-middle"></i>
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
                    <tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data option</td></tr>
                `);
            }
        });
    }

    $('#search').on('keypress', function (e) {
        if (e.which === 13) {
            fetchOptions();
        }
    });
    $('#search').on('input', function () {
        if ($(this).val().trim() === '') {
            fetchOptions();
        }
    });
     $('#btnSearch').on('click', function () {
        fetchOptions();
    });

    
    $(document).on('click', '#btnAdd', function () {
        optionIndex = 0; 
        $('#modalOptionLabel').text('Tambah Option');
        $('#modalOption .modal-body').html(`
            <form id="formOption">
                <div class="mb-3">
                    <label for="optionName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="optionName" name="name" required>
                </div>
                <div id="optionList">
                    <label class="form-label">Options</label>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="addOption">Tambah Option</button>
            </form>
        `);
        $('#modalOption .modal-footer').html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="saveOption">Simpan</button>
        `);
        $('#modalOption').modal('show');
    });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/get-data/options/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#modalOptionLabel').text('Edit Option');
                optionIndex = res.data.items.length || 0;
                $('#modalOption .modal-body').html(`
                    <form id="formOption">
                        <input type="hidden" name="id" value="${res.data.id}">
                        <div class="mb-3">
                            <label for="optionName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="optionName" name="name" value="${res.data.name}" required>
                            </div>
                        <div id="optionList">
                            <label for="" class="form-label">Options</label>
                            ${res.data.items.map((item, index) => `
                            <div class="mb-2 option-item d-flex gap-3" data-index="${index}">
                                <input type="text" class="form-control" name="items[${index}][name]" value="${item.name}" required>
                                <button type="button" class="btn btn-danger btn-sm btn-remove-option">Hapus</button>
                            </div>
                            `).join('')}
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="addOption">Tambah Option</button>
                    </form>
                `);
                $('#modalOption .modal-footer').html(`
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="updateOption">Update</button>
                `);
                $('#modalOption').modal('show');
            },
            error: function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal mengambil data option',
                    confirmButtonText: 'Coba Lagi'
                });
            }
        });
    });

    $(document).on('click', '#addOption', function () {
        $('#optionList').append(`
            <div class="mb-2 option-item d-flex gap-3" data-index="${optionIndex}">
                <input type="text" class="form-control" name="items[${optionIndex}][name]" placeholder="Nama option" required>
                <button type="button" class="btn btn-danger btn-sm btn-remove-option">Hapus</button>
            </div>
        `);
        optionIndex++;
    });
    $(document).on('click', '.btn-remove-option', function () {
        $(this).closest('.option-item').remove();
    });

    $(document).on('click', '#saveOption', function () {
        const formData = $('#formOption').serialize();
        $.ajax({
            url: '/api/create-data/option',
            method: 'POST',
            data: formData,
            success: function(res) {
                $('#modalOption').modal('hide');
                fetchOptions();
                 Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data option berhasil ditambahkan.',
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

    $(document).on('click', '#updateOption', function () {
        const id = $('#formOption input[name="id"]').val();
        const formData = $('#formOption').serialize();
        $.ajax({
            url: `/api/update-data/option/${id}`,
            method: 'PUT',
            data: formData,
            success: function(res) {
                $('#modalOption').modal('hide');
                fetchOptions(); 
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data option berhasil diperbarui.',
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
            title: 'Hapus Option?',
            text: 'Data option yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/delete-data/option/${id}`,
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
                            text: res.message || 'Option berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        fetchOptions(); 
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
    fetchOptions();
});
</script>
@endpush
