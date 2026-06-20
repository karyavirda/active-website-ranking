<?php

namespace App\Imports;

use App\Models\DataNews;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini

class DataNewsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new DataNews([
            'subdomain' => $row['subdomain'],
            'judul' => $row['judul'],
            'created_at' => $row['created_at'],
        ]);
    }
}
