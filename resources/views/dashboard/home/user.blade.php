@extends('layout-dashboard.main')
@section('title', "Kedai Holand | User")    
@section('title-page', "User")    
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
        <table class="table table-hover my-0" id="tableUser">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
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
    const tableBody = $('#tableUser tbody');
    function fetchUsers(page = 1) {
        const search = $('#search').val();
        $.ajax({
            url: `/api/get-data/user?page=${page}`,
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
                        <tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada user ditemukan</td></tr>
                    `);
                    return;
                }

                let rows = '';
                $.each(data, function (i, user) {
                    rows += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.phone}</td>
                            <td>
                                <button class="btn btn-info btn-sm rounded btn-edit" data-id="${user.id}">
                                    <i data-feather="edit" class="align-middle"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded btn-delete" data-id="${user.id}">
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
                    <tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data user</td></tr>
                `);
            }
        });
    }

    $('#search').on('keypress', function (e) {
        if (e.which === 13) {
            fetchUsers();
        }
    });
    $('#search').on('input', function () {
        if ($(this).val().trim() === '') {
            fetchUsers();
        }
    });
     $('#btnSearch').on('click', function () {
        fetchUsers();
    });

    
    $(document).on('click', '#btnAdd', function () {
        $('#modalMenuLabel').text('Tambah user');
        $('#modalMenu .modal-body').html(`
            <form id="formUserAdd">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                 <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="number" class="form-control" id="phone" name="phone" required>
                </div>
                 <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"  required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="align-middle me-2" data-feather="eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </form>
        `);
        $('#modalMenu .modal-footer').html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="saveuser">Simpan</button>
        `);
        $('#modalMenu').modal('show');
    });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/get-data/user/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(user) {
            
                $('#modalMenuLabel').text('Edit User');
                $('#modalMenu .modal-body').html(`
                    <form id="formUserUpdate">
                        <input type="hidden" id="user_id" name="id" value="${user.id}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" value="${user.name}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="${user.email}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="phone" class="form-control" id="phone" name="phone" value="${user.phone}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" value="${user.plain_password || ''}" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="align-middle me-2" data-feather="eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="${user.plain_password || ''}" required>
                        </div>
                    </form>
                `);
                $('#modalMenu .modal-footer').html(`
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="updateUser">Update</button>
                `);
                $('#modalMenu').modal('show');
            },
            error: function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal mengambil data user',
                    confirmButtonText: 'Coba Lagi'
                });
            }
        });
    });

    $(document).on('click', '#togglePassword', function() {
        let passwordInput = $('#password');
        let icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    $(document).on('click', '#saveuser', function () {
        const formData = $('#formUserAdd').serialize();
        $.ajax({
            url: '/api/create-data/user',
            method: 'POST',
            data: formData,
            success: function(res) {
                $('#modalMenu').modal('hide');
                fetchUsers();
                 Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data user berhasil ditambahkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                console.log(xhr);
                
                let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                     errorMessage = '<div>'; 
                    $.each(errors, function(field, messages) {
                        $.each(messages, function(index, message) {
                            errorMessage += `<p class="text-center">${message}</p>`; 
                        });
                    });
                    errorMessage += '</div>'; 
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        html: errorMessage,
                        confirmButtonText: 'OK'
                    });
                } 
                else if (xhr.status === 404) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: xhr.responseJSON?.message || 'User tidak ditemukan.',
                        confirmButtonText: 'OK'
                    });
                }
                else if (xhr.status === 403) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: xhr.responseJSON?.message || 'Anda tidak memiliki akses untuk melakukan ini.',
                        confirmButtonText: 'OK'
                    });
                }
                else if (xhr.status === 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || errorMessage,
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            }   
        });
    });

    $(document).on('click', '#updateUser', function () {
        const id = $('#user_id').val();
        const formData = $('#formUserUpdate').serialize();
        $.ajax({
            url: `/api/update-data/user/${id}`,
            method: 'PUT',
            data: formData,
            success: function(res) {
                $('#modalMenu').modal('hide');
                fetchUsers(); 
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data user berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                console.log(xhr);
                
                let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = '<div>'; 
                    $.each(errors, function(field, messages) {
                        $.each(messages, function(index, message) {
                            errorMessage += `<p class="text-center">${message}</p>`; 
                        });
                    });
                    errorMessage += '</div>'; 
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        html: errorMessage,
                        confirmButtonText: 'OK'
                    });
                } 
                else if (xhr.status === 404) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: xhr.responseJSON?.message || 'User tidak ditemukan.',
                        confirmButtonText: 'OK'
                    });
                }
                else if (xhr.status === 403) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: xhr.responseJSON?.message || 'Anda tidak memiliki akses untuk melakukan ini.',
                        confirmButtonText: 'OK'
                    });
                }
                else if (xhr.status === 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || errorMessage,
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            }    
        });
    });
    
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus user?',
            text: 'Data user yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/delete-data/user/${id}`,
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
                            text: res.message || 'User berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        fetchUsers(); 
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
    fetchUsers();
});
</script>

@endpush
