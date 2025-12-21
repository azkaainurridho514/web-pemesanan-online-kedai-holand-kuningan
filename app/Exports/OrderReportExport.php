<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class OrderReportExport implements FromCollection, WithHeadings, WithEvents, WithTitle, WithStyles
{
    protected string $status;
    protected ?string $dateFilter;
    protected ?string $startDate;
    protected ?string $endDate;
    protected string $downloadedAt;
    protected $collection;

    public function __construct(
        string $status, 
        ?string $dateFilter = null, 
        ?string $startDate = null, 
        ?string $endDate = null
    ) {
        $this->status = $status;
        $this->dateFilter = $dateFilter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->downloadedAt = now()->format('d M Y H:i');
    }

    
    public function getFilename(): string
    {
        $timestamp = now()->format('Y-F-d-H-i');
        $status = strtolower($this->status);
        return "report_{$status}_{$timestamp}.xlsx";
    }

    
    public function collection()
    {
        if (!$this->collection) {
            if ($this->status === 'batal') {
                $query = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER (ORDER BY orders.created_at DESC) as no'),
                        'products.name as product_name',
                        DB::raw('DATE_FORMAT(orders.created_at, "%d %M %Y %H:%i") as cancelled_at')
                    )
                    ->where('orders.status', 'batal');
            } else {
                $query = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER (ORDER BY SUM(order_items.quantity) DESC) as no'),
                        'products.name as product_name',
                        DB::raw('FORMAT(products.price, 0) as unit_price'),
                        DB::raw('SUM(order_items.quantity) as total_sold'),
                        DB::raw('FORMAT(SUM(order_items.subtotal), 0) as total_revenue'),
                        DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders')
                    )
                    ->where('orders.status', $this->status)
                    ->groupBy('products.id', 'products.name', 'products.price')
                    ->orderByDesc(DB::raw('SUM(order_items.quantity)'));
            }

            $this->applyDateFilter($query);
            $this->collection = $query->get();
        }

        return $this->collection;
    }

    
    protected function applyDateFilter($query): void
    {
        if (!$this->dateFilter) return;

        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('orders.created_at', Carbon::today());
                break;
            case '7days':
                $query->where('orders.created_at', '>=', Carbon::now()->subDays(7));
                break;
            case '30days':
                $query->where('orders.created_at', '>=', Carbon::now()->subDays(30));
                break;
            case 'range':
                if ($this->startDate && $this->endDate) {
                    $query->whereBetween('orders.created_at', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
                }
                break;
        }
    }

    
    public function headings(): array
    {
        if ($this->status === 'batal') {
            return [
                'No',
                'Product Name',
                'Cancelled At'
            ];
        }
        
        return [
            'No',
            'Product Name',
            'Unit Price (Rp)',
            'Total Sold (Qty)',
            'Total Revenue (Rp)',
            'Total Orders'
        ];
    }

    
    public function title(): string
    {
        return 'Sales Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $collection = $this->collection();
                
                $isBatal = $this->status === 'batal';
                $lastCol = $isBatal ? 'C' : 'F';
                
                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'ORDER SALES REPORT');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14]
                ]);
                
                $statusText = strtoupper($this->status);
                $sheet->setCellValue('A2', "Status: {$statusText}");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10]
                ]);
                
                $periodText = $this->getPeriodText();
                $sheet->setCellValue('A3', "Period: {$periodText}");
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 9]
                ]);
                
                $sheet->setCellValue('A4', "Downloaded: {$this->downloadedAt}");
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 9]
                ]);

                $colRange = $isBatal ? range('A', 'C') : range('A', 'F');
                foreach ($colRange as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(5)->setRowHeight(20);

                $highestRow = $sheet->getHighestRow();
                
                $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D3D3D3']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D0D0D0']
                        ]
                    ]
                ]);

                if ($highestRow > 5) {
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'font' => [
                                'bold' => false,
                                'color' => ['rgb' => '000000']
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFFFFF']
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'D0D0D0']
                                ]
                            ]
                        ]);
                    }
                    
                    $sheet->getStyle("A6:A{$highestRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    if (!$isBatal) {
                        $sheet->getStyle("C6:F{$highestRow}")->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                }

                if ($collection->isNotEmpty()) {
                    $summaryRow = $highestRow + 2;
                    
                    if ($isBatal) {
                        $totalProducts = $collection->count();
                        
                        $sheet->mergeCells("A{$summaryRow}:B{$summaryRow}");
                        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true);

                        $summaryRow++;
                        $sheet->getStyle("B{$summaryRow}")->getFont()->setBold(true);
                    } else {
                        $totalSold = $collection->sum('total_sold');
                        $totalRevenue = $collection->sum(function($item) {
                            return (int) str_replace(',', '', $item->total_revenue);
                        });
                        $totalProducts = $collection->count();
                        
                        $sheet->setCellValue("A{$summaryRow}", 'SUMMARY');
                        $sheet->mergeCells("A{$summaryRow}:C{$summaryRow}");
                        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true);

                        $summaryRow++;
                        $sheet->setCellValue("A{$summaryRow}", 'Total Products:');
                        $sheet->setCellValue("B{$summaryRow}", $totalProducts);
                        
                        $summaryRow++;
                        $sheet->setCellValue("A{$summaryRow}", 'Total Items Sold:');
                        $sheet->setCellValue("B{$summaryRow}", number_format($totalSold, 0));
                        
                        $summaryRow++;
                        $sheet->setCellValue("A{$summaryRow}", 'Total Revenue:');
                        $sheet->setCellValue("B{$summaryRow}", 'Rp ' . number_format($totalRevenue, 0));
                        
                        $sheet->getStyle("B{$summaryRow}")->getFont()->setBold(true)->setSize(12);
                    }
                }

                $sheet->freezePane('A6');
            }
        ];
    }

    
    protected function getPeriodText(): string
    {
        if (!$this->dateFilter) {
            return 'All Time';
        }

        return match($this->dateFilter) {
            'today' => Carbon::today()->format('d M Y'),
            '7days' => 'Last 7 Days (' . Carbon::now()->subDays(7)->format('d M') . ' - ' . Carbon::now()->format('d M Y') . ')',
            '30days' => 'Last 30 Days (' . Carbon::now()->subDays(30)->format('d M') . ' - ' . Carbon::now()->format('d M Y') . ')',
            'range' => Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y'),
            default => 'All Time'
        };
    }
}