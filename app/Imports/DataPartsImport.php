<?php

namespace App\Imports;

use App\Models\DataParts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini

class DataPartsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek apakah ID sudah ada di database
        $exists = DataParts::where('id', $row['id'])->exists();

        if ($exists) {
            // Jika ada, kembalikan null (data akan dilewati/tidak di-insert)
            return null;
        }

        return new DataParts([
            // Sesuaikan dengan nama header di Excel
            'id' => $row['id'],
            'subdomain' => $row['subdomain'],
            'judul' => $row['judul'],
            'tipe' => $row['tipe'],
            'created_at' => $row['created_at'],
        ]);
    }
}
