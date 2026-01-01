<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Expense;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FinancialExportService
{
    /**
     * Generate Excel file for financial report
     */
    public function generateReport($startDate, $endDate)
    {
        // Get data
        $orders = Order::with(['customer', 'payments', 'items.service'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'asc')
            ->get();

        // Calculate totals
        $totalRevenue = $orders->sum('total');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Ringkasan
        $this->createSummarySheet($spreadsheet, $totalRevenue, $totalExpenses, $netProfit, $orders->count(), $startDate, $endDate);
        
        // Sheet 2: Transaksi Laundry
        $this->createTransactionsSheet($spreadsheet, $orders);
        
        // Sheet 3: Pengeluaran
        $this->createExpensesSheet($spreadsheet, $expenses);

        return $spreadsheet;
    }

    /**
     * Create Sheet 1: Ringkasan
     */
    private function createSummarySheet($spreadsheet, $totalRevenue, $totalExpenses, $netProfit, $transactionCount, $startDate, $endDate)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');

        // Header
        $sheet->setCellValue('A1', 'RINGKASAN LAPORAN KEUANGAN');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Label');
        $sheet->setCellValue('B' . $row, 'Value');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Pendapatan');
        $sheet->setCellValue('B' . $row, $totalRevenue);
        $sheet->getStyle('B' . $row)->getNumberFormat()
            ->setFormatCode('"Rp "#,##0');

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Pengeluaran');
        $sheet->setCellValue('B' . $row, $totalExpenses);
        $sheet->getStyle('B' . $row)->getNumberFormat()
            ->setFormatCode('"Rp "#,##0');

        $row++;
        $sheet->setCellValue('A' . $row, 'Laba Bersih');
        $sheet->setCellValue('B' . $row, $netProfit);
        $sheet->getStyle('B' . $row)->getNumberFormat()
            ->setFormatCode('"Rp "#,##0');
        
        // Color laba: green if positive, red if negative
        if ($netProfit >= 0) {
            $sheet->getStyle('B' . $row)->getFont()->getColor()->setARGB('FF008000');
        } else {
            $sheet->getStyle('B' . $row)->getFont()->getColor()->setARGB('FFFF0000');
        }
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Jumlah Transaksi');
        $sheet->setCellValue('B' . $row, $transactionCount);

        $row++;
        $sheet->setCellValue('A' . $row, 'Periode Laporan');
        $sheet->setCellValue('B' . $row, $startDate . ' s/d ' . $endDate);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);

        // Borders
        $sheet->getStyle('A3:B' . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Create Sheet 2: Transaksi Laundry
     */
    private function createTransactionsSheet($spreadsheet, $orders)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Transaksi Laundry');

        // Headers
        $headers = ['Tanggal', 'Waktu', 'Nama Pelanggan', 'No Order', 'Metode Pembayaran', 'Total Bayar', 'Status Order'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Header styling
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4A90E2'); // Light blue
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data
        $row = 2;
        foreach ($orders as $order) {
            // Get payment method
            $paymentMethod = '-';
            if ($order->payments->count() > 0) {
                $firstPayment = $order->payments->first();
                $paymentMethod = match($firstPayment->payment_method) {
                    'cash' => 'Tunai',
                    'bank_transfer' => 'Transfer',
                    'e-wallet' => 'QRIS',
                    'card' => 'Kartu',
                    default => ucfirst(str_replace('_', ' ', $firstPayment->payment_method))
                };
            }

            // Order status
            $orderStatus = match($order->status) {
                'received' => 'Diterima',
                'washing' => 'Dicuci',
                'drying' => 'Dikeringkan',
                'ironing' => 'Disetrika',
                'folding' => 'Dilipat',
                'ready' => 'Siap Diambil',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default => ucfirst(str_replace('_', ' ', $order->status))
            };

            $sheet->setCellValue('A' . $row, $order->created_at->format('d-m-Y'));
            $sheet->setCellValue('B' . $row, $order->created_at->format('H:i'));
            $sheet->setCellValue('C' . $row, $order->customer->name ?? '-');
            $sheet->setCellValue('D' . $row, $order->order_number);
            $sheet->setCellValue('E' . $row, $paymentMethod);
            $sheet->setCellValue('F' . $row, $order->total);
            $sheet->setCellValue('G' . $row, $orderStatus);

            // Format currency
            $sheet->getStyle('F' . $row)->getNumberFormat()
                ->setFormatCode('"Rp "#,##0');

            $row++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);

        // Borders
        if ($row > 2) {
            $sheet->getStyle('A1:G' . ($row - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // Auto-filter
        $sheet->setAutoFilter('A1:G' . ($row - 1));
    }

    /**
     * Create Sheet 3: Pengeluaran
     */
    private function createExpensesSheet($spreadsheet, $expenses)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Pengeluaran');

        // Headers
        $headers = ['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Header styling
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF6B6B'); // Light red
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data
        $row = 2;
        foreach ($expenses as $expense) {
            // Translate category
            $category = match($expense->category) {
                'salary' => 'Gaji',
                'utilities' => 'Utilitas',
                'supplies' => 'Persediaan',
                'maintenance' => 'Perawatan',
                'rent' => 'Sewa',
                'other' => 'Lainnya',
                default => ucfirst($expense->category)
            };

            $sheet->setCellValue('A' . $row, \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y'));
            $sheet->setCellValue('B' . $row, $category);
            $sheet->setCellValue('C' . $row, $expense->description ?? '-');
            $sheet->setCellValue('D' . $row, $expense->amount);

            // Format currency
            $sheet->getStyle('D' . $row)->getNumberFormat()
                ->setFormatCode('"Rp "#,##0');

            $row++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(18);

        // Borders
        if ($row > 2) {
            $sheet->getStyle('A1:D' . ($row - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // Auto-filter
        $sheet->setAutoFilter('A1:D' . ($row - 1));
    }

    /**
     * Export to file
     */
    public function export($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        
        // Set active sheet to first sheet
        $spreadsheet->setActiveSheetIndex(0);
        
        // Stream the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
