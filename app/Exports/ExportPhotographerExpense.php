<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportPhotographerExpense implements FromArray, WithHeadings
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
                    'Booking ID',
                    'Date of Event',
                    'Venue Name',
                    'ID',
                    'Photographer ID',
                    'Photographer Name',
                    'Total Expense',
                ];
       
    }
}
