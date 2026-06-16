<?php

namespace App\Imports;

use App\Models\Subdomains;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubdomainsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // Cek apakah Subdomain sudah ada di database
        $exists = Subdomains::where('subdomain', $row['subdomain'])->exists();

        if ($exists) {
            // Jika ada, kembalikan null (data akan dilewati/tidak di-insert)
            return null;
        }

        return new Subdomains([
            'subdomain' => $row['subdomain'],
            'paket' => $row['paket']
        ]);
    }
}
