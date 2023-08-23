<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepositsExport implements FromCollection, WithHeadings
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $records = $this->query->get();
        $result = array();
        foreach($records as $deposits){
            $result[] = array(
                'name' => $deposits->ofUser->first_name,
                'email' => $deposits->ofUser->email,
                'date' => Carbon::parse($deposits->created_at)->format('Y-m-d'),
                'channel' =>  strtoupper($deposits->channel),
                'gateway' => strtoupper($deposits->gateway),
                'payment_id' => $deposits->payment_id,
                'amount' =>  number_format((float)$deposits->amount, 2, '.', ''),
                'payment_charges' => $deposits->payment_charges,
                'status' => $deposits->status,
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Date',
            'Deposit Method',
            'Payment Gateway',
            'Transaction ID',
            'Deposit Amount',
            'Payment Charges',
            'Status',
        ];
    }
}
