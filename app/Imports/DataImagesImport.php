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

        return new DataImages([
            'subdomain' => $row['subdomain'],
            'nama' => $row['nama'],
            'created_at' => $row['created_at'],
        ]);
    }
}
