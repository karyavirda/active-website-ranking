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

        // Cek apakah ID sudah ada di database
        $exists = DataLogs::where('id', $row['id'])->exists();

        if ($exists) {
            // Jika ada, kembalikan null (data akan dilewati/tidak di-insert)
            return null;
        }

        return new DataLogs([
            // Sesuaikan dengan nama header di Excel
            'id' => $row['id'],
            'subdomain' => $row['subdomain'],
            'admin_name' => $row['admin_name'],
            'activity' => $row['activity'],
            'activity_date' => $row['activity_date'],
        ]);
    }
}
