<?php

namespace App\Imports;

use App\Models\DataLogs;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini

class DataLogsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new DataLogs([
            'subdomain' => $row['subdomain'],
            'nama_admin' => $row['nama_admin'],
            'aktivitas' => $row['aktivitas'],
            'activity_date' => $row['activity_date'],
        ]);
    }
}
