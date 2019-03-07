<?php
namespace App\Exports\PostRegistry;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class KzExport implements FromView, WithEvents, ShouldAutoSize
{
    private $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function view():View
    {
        $this->serialize();

        return view('exports.post.kz')
                  ->with('orders', $this->orders);
    }

    public function serialize()
    {
        foreach ($this->orders as $order) {
            $target = json_decode($order->getTargetValue->values ?? '', true);
            $address = [];
            $address[] = $target['region']['field_value'] ?? '';
            $address[] = $target['city']['field_value'] ?? '';
            $address[] = $target['district']['field_value'] ?? '';
            $address[] = $target['street']['field_value'] ?? '';
            $address[] = $target['house']['field_value'] ?? '';
            $address[] = $target['flat']['field_value'] ?? '';

            $order->postal_code = $target['postal_code']['field_value']??null;
            $order->address = implode(', ', array_filter($address));
            $order->track = $order->getTargetValue->track??null;
            $order->price = $order->price_total + ((float)$target['cost']['field_value'] ?? 0);
        }
    }

    public function registerEvents(): array
    {
        return [
        AfterSheet::class    => function (AfterSheet $event) {
            $event->sheet->styleCells(
            'B1:B7',
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]
          );
            $event->sheet->styleCells(
            'A9:I100',
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]
          );
            $event->sheet->styleCells(
                'A1:B7',
                [
                    'font' => [
                        'name'      =>  'Calibri',
                        'size'      =>  8,
                    ]
                ]
            );
        },
    ];
    }
}
