<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportCustomerPayments implements FromArray, WithHeadings
{
    function __construct($data=array()) {
        
        $this->data=$data;
        
      }
    public function array(): array
    {
        return $this->data;
        return $dataArray;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
    public function headings(): array
    {
                return [
                    //'Customer id',
                    'Customer Name',
                    //'Customer Lastname',
                    //'Customer Email',
                    //'Customer Phone',
                    //'Booking ID',
                    'Date of Event',
                    //'Pencile Added by',
                     'Venue Group Name',
                    // 'Venue Group Manager Name',
                    // 'Venue Group Phone',
                    // 'Venue Group City',
                    // 'Photographer Name',
                    // 'Photographer Email',
                    // 'Photographer Phone',
                    // 'Venue Group to Pay',
                    // 'Customer to Pay',
                    'Package Name',
                    'Package Price',
                    //'Extra Price',
                    //'Over Time Cost',
                    'Total Event Cost',
                    //'Paid By Customer',
                    //'Paid By Venue Group',
                    'Total Payment Received',
                    'Due Payment',
                ];
       
    }
}
