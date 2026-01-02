<?php

if (!function_exists('rupiah')) {
    /**
     * Format number to Rupiah currency
     */
    function rupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('statusLabel')) {
    /**
     * Convert English database status values to Indonesian labels
     *
     * @param string $status
     * @param string $type 'order'|'payment'|'transaction'
     * @return string
     */
    function statusLabel($status, $type = 'order')
    {
        $mappings = [
            'order' => [
                'in_progress' => 'Diproses',
                'ready' => 'Siap Diambil',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ],
            'payment' => [
                'pending' => 'Belum Dibayar',
                'partial' => 'Sebagian',
                'paid' => 'Lunas',
                'refunded' => 'Dikembalikan',
            ],
            'transaction' => [
                'income' => 'Pendapatan',
                'expense' => 'Pengeluaran',
            ],
        ];

        return $mappings[$type][$status] ?? ucfirst($status);
    }
}
