<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BarangExport implements FromCollection, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    public function collection()
    {
        $query = DB::table('barang')
            ->leftJoin('unit', 'barang.unit_id', '=', 'unit.id')
            ->leftJoin(DB::raw('(SELECT barang_id, SUM(saldo_stok) AS total_stok
                    FROM (SELECT barang_id, gudang_id, adjusment_stok AS saldo_stok, "adjusment" AS jenis
                          FROM barang_adjusment_stok) AS tabel
                    GROUP BY barang_id) AS tabel_stok'), 'barang.id', '=', 'tabel_stok.barang_id')
            ->select('barang.*', 'tabel_stok.total_stok', 'unit.satuan')
            ->orderBy('barang.id', 'asc')
            ->get();

        $data = collect([
            ['No', 'Kode Barang', 'Nama Barang', 'Deskripsi', 'Barcode', 'Satuan', 'Stok']
        ]);

        $no = 1;
        foreach ($query as $row) {
            $data->push([
                $no,
                $row->kode_barang,
                $row->nama_barang,
                $row->deskripsi,
                $row->barcode,
                $row->satuan,
                $row->total_stok,
            ]);
            $no++;
        }

        return $data;
    }

    public function map($row): array
    {
        return [
            $row[0],
            $row[1],
            $row[2],
            $row[3],
            $row[4], // Apply format to treat barcode as text
            $row[5],
            $row[6],
        ];
    }

    public function styles($sheet)
    {
        return [
            'E' => [
                'numberFormat' => [
                    'formatCode' => NumberFormat::FORMAT_NUMBER, // Treat barcode as text
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A:G')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->calculateColumnWidths();
            },
        ];
    }
}
