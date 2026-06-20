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

        return new DataPages([
            'subdomain' => $row['subdomain'],
            'judul' => $row['judul'],
            'created_at' => $row['created_at'],
        ]);
    }
}
