<?php

namespace App\Imports;

use App\Models\DataImages;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImagesImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new DataImages([
            'id' => $row[0], // Sesuaikan dengan kolom Excel kamu
            'subdomain' => $row[1],
            'nama' => $row[2],
            'created_at' => $row[3],
        ]);
    }
}
