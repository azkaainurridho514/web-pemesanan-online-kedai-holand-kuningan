@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Report")    
@section('title-page', "Report")   

@section('main')
<div class="row d-flex justify-content-evenly">
    <!-- CARD SELESAI -->
    <div class="col-lg-6">
        <div class="card flex-fill">
            <div class="card-header">
                <div class="card-title row mb-3 mt-3">
                    <div class="col-lg-12 d-flex flex-wrap gap-2 mb-2 align-items-center">
                        <select id="selectPeriodSelesai" class="form-select" style="max-width: 180px;">
                            <option value="0">Semua</option>
                            <option value="1">Hari ini</option>
                            <option value="2">Satu minggu</option>
                            <option value="3">Satu bulan</option>
                            <option value="4">Satu tahun</option>
                            <option value="5">Custom Range</option>
                        </select>
                        <input type="date" id="startDateSelesai" class="form-control d-none" style="max-width: 180px;">
                        <i data-feather="arrow-right" class="d-none" id="toSelesai"></i>
                        <input type="date" id="endDateSelesai" class="form-control d-none" style="max-width: 180px;">

                        <button id="btnFilterSelesai" class="btn btn-secondary">
                            <i data-feather="filter"></i> Terapkan
                        </button>
                        <button id="btnDownloadSelesai" class="btn btn-primary">
                            <i data-feather="download"></i> Download
                        </button>
                    </div>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="d-none d-xl-table-cell">Name</th>
                        <th class="d-none d-xl-table-cell">Penjualan</th>
                        <th class="d-none d-xl-table-cell">Total</th>
                    </tr>
                </thead>
                <tbody id="reportListSelesai"></tbody>
            </table>
            <div id="paginationSelesai" class="mt-3"></div>
        </div>
    </div>

    <!-- CARD BATAL -->
    <div class="col-lg-6">
        <div class="card flex-fill">
            <div class="card-header">
                <div class="card-title row mb-3 mt-3">
                    <div class="col-lg-12 d-flex flex-wrap gap-2 mb-2 align-items-center">
                        <select id="selectPeriodBatal" class="form-select" style="max-width: 180px;">
                            <option value="0">Semua</option>
                            <option value="1">Hari ini</option>
                            <option value="2">Satu minggu</option>
                            <option value="3">Satu bulan</option>
                            <option value="4">Satu tahun</option>
                            <option value="5">Custom Range</option>
                        </select>
                        <input type="date" id="startDateBatal" class="form-control d-none" style="max-width: 180px;">
                        <i data-feather="arrow-right" class="d-none" id="toBatal"></i>
                        <input type="date" id="endDateBatal" class="form-control d-none" style="max-width: 180px;">

                        <button id="btnFilterBatal" class="btn btn-secondary">
                            <i data-feather="filter"></i> Terapkan
                        </button>
                        <button id="btnDownloadBatal" class="btn btn-primary">
                            <i data-feather="download"></i> Download
                        </button>
                    </div>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="d-none d-xl-table-cell">Name</th>
                        <th class="d-none d-xl-table-cell">Pembatalan</th>
                        <th class="d-none d-xl-table-cell">Total</th>
                    </tr>
                </thead>
                <tbody id="reportListBatal"></tbody>
            </table>
            <div id="paginationBatal" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function () {

    // ----------------- CARD SELESAI -----------------
    $('#selectPeriodSelesai').on('change', function () {
        toggleDateFields('Selesai', $(this).val());
    });

    $('#btnFilterSelesai').on('click', function () {
        loadReportsSelesai(1);
    });

    $('#btnDownloadSelesai').on('click', function () {
        const { dateFilter, extraParams } = getFilterParams('Selesai');
        window.open(`/export/order/download-report?type=selesai&date_filter=${dateFilter}${extraParams}`, '_blank');
    });


    // ----------------- CARD BATAL -----------------
    $('#selectPeriodBatal').on('change', function () {
        toggleDateFields('Batal', $(this).val());
    });

    $('#btnFilterBatal').on('click', function () {
        loadReportsBatal(1);
    });

    $('#btnDownloadBatal').on('click', function () {
        const { dateFilter, extraParams } = getFilterParams('Batal');
        window.open(`/export/order/download-report?type=batal&date_filter=${dateFilter}${extraParams}`, '_blank');
    });


    // ----------------- GENERIC FUNCTIONS -----------------
    function toggleDateFields(type, val) {
        if (val === '5') {
            $(`#startDate${type}, #endDate${type}, #to${type}`).removeClass('d-none');
        } else {
            $(`#startDate${type}, #endDate${type}, #to${type}`).addClass('d-none');
        }
    }

    function getFilterParams(type) {
        const period = $(`#selectPeriod${type}`).val();
        let dateFilter = '';
        let extraParams = '';

        if (period === '1') dateFilter = 'today';
        else if (period === '2') dateFilter = '7days';
        else if (period === '3') dateFilter = '30days';
        else if (period === '4') {
            const yearStart = new Date(new Date().getFullYear(), 0, 1).toISOString().slice(0, 10);
            const yearEnd = new Date(new Date().getFullYear(), 11, 31).toISOString().slice(0, 10);
            dateFilter = 'range';
            extraParams = `&start_date=${yearStart}&end_date=${yearEnd}`;
        } else if (period === '5') {
            const start = $(`#startDate${type}`).val();
            const end = $(`#endDate${type}`).val();
            if (!start || !end) {
                alert('Pilih tanggal mulai dan selesai terlebih dahulu.');
                return { dateFilter: 'all', extraParams: '' };
            }
            dateFilter = 'range';
            extraParams = `&start_date=${start}&end_date=${end}`;
        } else dateFilter = 'all';

        return { dateFilter, extraParams };
    }


    // ----------------- LOAD REPORT FUNCTIONS -----------------
    function loadReportsSelesai(page = 1) {
        const { dateFilter, extraParams } = getFilterParams('Selesai');
        const url = `/order/data-report?status=selesai&page=${page}&date_filter=${dateFilter}${extraParams}`;
        loadReportData(url, '#reportListSelesai', '#paginationSelesai', 'selesai');
    }

    function loadReportsBatal(page = 1) {
        const { dateFilter, extraParams } = getFilterParams('Batal');
        const url = `/order/data-report?status=batal&page=${page}&date_filter=${dateFilter}${extraParams}`;
        loadReportData(url, '#reportListBatal', '#paginationBatal', 'batal');
    }

    function loadReportData(url, tableId, paginationId, type) {
        $.ajax({
            url,
            method: 'GET',
            beforeSend: function () {
                $(tableId).html(`<tr><td colspan="3" class="text-center py-5 text-muted">Memuat data...</td></tr>`);
            },
            success: function (response) {
                if (response.data.data.length) {
                    renderReports(response.data.data, tableId);
                    renderPagination(response.data, paginationId, type);
                } else {
                    $(tableId).html(`<tr><td colspan="3" class="text-center py-5 text-muted">Tidak ada data.</td></tr>`);
                    $(paginationId).html('');
                }
            },
            error: function (e) {
                console.log(e);
                $(tableId).html(`<tr><td colspan="3" class="text-center text-danger py-5">Gagal memuat data.</td></tr>`);
            }
        });
    }

    function renderReports(reports, id) {
        let html = '';
        reports.forEach(report => {
            html += `
                <tr>
                    <td>${report.product_name ?? '-'}</td>
                    <td class="d-none d-xl-table-cell">${report.total_sold ?? 0}</td>
                    <td class="d-none d-xl-table-cell">Rp ${Number(report.total_revenue || 0).toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
        $(id).html(html);
    }

    function renderPagination(pagination, id, type) {
        if (!pagination || pagination.last_page <= 1) {
            $(id).html('');
            return;
        }

        let html = `<nav><ul class="pagination justify-content-center">`;

        html += `<li class="page-item ${!pagination.prev_page_url ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}" data-type="${type}">&laquo;</a>
        </li>`;

        for (let i = 1; i <= pagination.last_page; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}" data-type="${type}">${i}</a>
            </li>`;
        }

        html += `<li class="page-item ${!pagination.next_page_url ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}" data-type="${type}">&raquo;</a>
        </li>`;

        html += `</ul></nav>`;
        $(id).html(html);

        $(`${id} .page-link`).off('click').on('click', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;

            if (type === 'selesai') loadReportsSelesai(page);
            else loadReportsBatal(page);
        });
    }

    // Load awal
    loadReportsSelesai();
    loadReportsBatal();
});
</script>
@endpush
