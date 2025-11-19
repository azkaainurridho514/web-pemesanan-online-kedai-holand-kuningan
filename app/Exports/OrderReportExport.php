<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class OrderReportExport implements FromCollection, WithHeadings, WithEvents
{
    protected $status;
    protected $dateFilter;
    protected $startDate;
    protected $endDate;
    protected $downloadedAt;

    public function __construct($status, $dateFilter, $startDate, $endDate)
    {
        $this->status = $status;
        $this->dateFilter = $dateFilter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->downloadedAt = now()->format('Y-m-d H:i'); 
    }

    public function collection()
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->where('orders.status', $this->status)
            ->groupBy('products.name')
            ->orderByDesc('total_sold');

        // Filter tanggal
        if ($this->dateFilter) {
            if ($this->dateFilter === 'today') {
                $query->whereDate('orders.created_at', Carbon::today());
            } elseif ($this->dateFilter === '7days') {
                $query->where('orders.created_at', '>=', Carbon::now()->subDays(7));
            } elseif ($this->dateFilter === '30days') {
                $query->where('orders.created_at', '>=', Carbon::now()->subDays(30));
            } elseif ($this->dateFilter === 'range' && $this->startDate && $this->endDate) {
                $query->whereBetween('orders.created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Product Name', 'Total Sold', 'Total Revenue'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Menambahkan informasi download di atas header
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->setCellValue('A1', "Report {$this->status} downloaded at: {$this->downloadedAt}");

                // Merge A1:C1 agar rapi
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->getStyle('A1')->getFont()->setBold(true);
            },
        ];
    }
}
