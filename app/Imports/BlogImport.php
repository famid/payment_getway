<?php

namespace App\Imports;

use App\Blog;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BlogImport implements ToModel
{

    /**
     * @param array $row
     * @return Blog|Model|Model[]|null
     */
    public function model(array $row)
    {
        return new Blog([
            'title' => $row[0],
            'description' => $row[1],
            'tags' => ($row[2]),
        ]);
    }
}
