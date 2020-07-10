<?php

namespace App\Exports;

use App\Blog;
use App\Http\Controllers\User\BlogController;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;


class BlogExport implements FromCollection
{
    protected $blog;
    public function __construct($blog)
    {
        $this->blog = $blog;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->blog;
    }
}
