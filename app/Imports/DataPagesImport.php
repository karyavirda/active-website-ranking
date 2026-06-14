<?php

namespace App\Imports;

use App\Models\DataPages;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini

class DataPagesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek apakah ID sudah ada di database
        $exists = DataPages::where('id', $row['id'])->exists();

        if ($exists) {
            // Jika ada, kembalikan null (data akan dilewati/tidak di-insert)
            return null;
        }

        return new DataPages([
            //

            // Sesuaikan dengan nama header di Excel
            'id' => $row['id'],
            'subdomain' => $row['subdomain'],
            'judul' => $row['judul'],
            'created_at' => $row['created_at'],
        ]);
    }
}
