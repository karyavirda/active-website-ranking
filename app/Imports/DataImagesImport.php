<?php

namespace App\Imports;

use App\Models\DataImages;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini

class DataImagesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // Cek apakah ID sudah ada di database
        $exists = DataImages::where('id', $row['id'])->exists();

        if ($exists) {
            // Jika ada, kembalikan null (data akan dilewati/tidak di-insert)
            return null;
        }

        return new DataImages([
            // Sesuaikan dengan nama header di Excel
            'id' => $row['id'],
            'subdomain' => $row['subdomain'],
            'nama' => $row['nama'],
            'created_at' => $row['created_at'],
        ]);
    }
}
