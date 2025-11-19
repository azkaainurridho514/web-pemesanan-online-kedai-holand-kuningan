<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\OrderReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderExportController extends Controller
{
    public function download(Request $request)
    {
        $status = $request->query('type'); 
        $dateFilter = $request->query('date_filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $fileName = "report_{$status}_" . now()->format('Ymd_His') . ".xlsx";

        return Excel::download(new OrderReportExport($status, $dateFilter, $startDate, $endDate), $fileName);
    }
}
