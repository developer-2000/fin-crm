<?php

namespace App\Exports\Reports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesExport implements FromView, WithEvents, ShouldAutoSize
{
    private $reportData;

    public function __construct($reportData, $data)
    {
        $this->reportData = $reportData;
        $this->dataRequest = $data;
    }

    public function view(): View
    {
        $data['products'] = $this->reportData['products'];
        if (!isset( $this->reportData['filters']['product'])) {
            $data['orderProductsData'] = $this->reportData['orderProductsData'];
        }
        $data['redemptionPercents'] = $this->reportData['redemptionPercents'];
        if (isset( $this->reportData['filters']['product'])) {
            $data['ordersForProducts'] = $this->reportData['ordersForProducts'];
        }
        $data['products'] = $this->reportData['products'];
        $data['filters'] = $this->dataRequest->filters;
        $data['statuses'] = $this->reportData['statuses'];

        return view('exports.reports.sales', $data);
    }

    public function serialize()
    {
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->styleCells(
                    'A4:A2000',
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'C4:G2000',
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
            },
        ];
    }
}
