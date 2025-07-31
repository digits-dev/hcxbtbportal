<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    use Exportable;

    protected $headers;
    protected $title;

    public function __construct($headers, $title = 'Orders Report')
    {
        $this->headers = $headers;
        $this->title = $title;
    }

    public function collection()
    {
        $headers = $this->headers instanceof \Illuminate\Database\Eloquent\Builder
            ? $this->headers->with(['getLines', 'getStatus', 'getCreatedBy', 'getUpdatedBy'])->get()
            : $this->headers;

        $rows = collect();

        foreach ($headers as $header) {
            foreach ($header->getLines as $line) {

                $rows->push([
                    // HEADER DATA
                    $header->getStatus->name ?? '',
                    $header->reference_number ?? '',
                    $header->first_name .' ' . $header->last_name ?? '',
                    $header->delivery_address ?? '',
                    $header->email_address ?? '',
                    $header->contact_details ?? '',
                    $header->has_downpayment ?? '',
                    $header->downpayment_value ?? '',
                    $header->financed_amount ?? '',
                    $header->getCreatedBy->name ?? '',
                    $header->created_at ?? '',
                    // SPACER
                    ' ',
                    // LINE DATA
                    $line->getItem->item_description ?? '',
                    $line->getItem->model ?? '',
                    $line->getItem->actual_color ?? '',
                    $line->getItem->size ?? '',
                    $line->serial_no ?? '',
                    $line->imei ?? '',
                    $line->qty ?? '',

                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Status',
            'Reference Number',
            'Customer Name',
            'Delivery Address',
            'Email Address',
            'Contact Details',
            'Downpayment',
            'Downpayment Value',
            'Financed Amount',
            'Created By',
            'Created Date',
            ' ',
            'Item Description',
            'Model',
            'Color',
            'Storage',
            'Serial Number',
            'IMEI',
            'Quantity',
        ];
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->getFont()->setBold(true); // Headings are now on row 2
        $sheet->getStyle($sheet->calculateWorksheetDimension())
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Insert a row at the top for titles
                $event->sheet->insertNewRowBefore(1, 1);

                // Merge A1 to F1 for 'Header Data'
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->setCellValue('A1', 'Order Data');

                // Merge G1 to I1 for 'Line Data'
                $event->sheet->mergeCells('M1:S1');
                $event->sheet->setCellValue('M1', 'Order Item Details');

                // Apply styles to both titles
                $styleArray = [
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'], // White text
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '000000'], // Black background
                    ],
                ];

                $event->sheet->getStyle('A1:K1')->applyFromArray($styleArray);
                $event->sheet->getStyle('M1:S1')->applyFromArray($styleArray);
            },
        ];
    }
}
