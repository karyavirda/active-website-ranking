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

        return new DataParts([
            'subdomain' => $row['subdomain'],
            'judul' => $row['judul'],
            'tipe' => $row['tipe'],
            'created_at' => $row['created_at'],
        ]);
    }
}
