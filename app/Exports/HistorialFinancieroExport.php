<?php

namespace App\Exports;

use App\Models\Reserva;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HistorialFinancieroExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Reserva::with(['cliente', 'servicio'])
            ->where('profesional_id', auth()->id())
            ->where('estado', 'completada')
            ->orderByDesc('fecha')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Cliente',
            'Servicio',
            'Hora Inicio',
            'Hora Fin',
            'Monto',
            'Estado Pago'
        ];
    }

    public function map($reserva): array
    {
        return [
            $reserva->fecha->format('d/m/Y'),
            $reserva->cliente->name ?? 'N/A',
            $reserva->servicio->titulo ?? 'N/A',
            \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i A'),
            \Carbon\Carbon::parse($reserva->hora_fin)->format('g:i A'),
            '$' . number_format($reserva->monto, 0, ',', '.'),
            ucfirst($reserva->estado_pago),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $range = "A1:{$highestColumn}{$highestRow}";

        // Estilos para la cabecera
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2563EB'], // Azul 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estilos para todo el rango de datos (bordes)
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ]
        ]);

        return [];
    }
}
