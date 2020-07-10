<?php


namespace App\Http\Repository;


use App\Http\Models\Mandate;

class MandateRepository Extends BaseRepository
{
    public $model;

    /**
     * MandateRepository constructor.
     * @param Mandate $mandata
     */
    public function __construct(Mandate $mandata) {
        $this->model = $mandata;
        parent::__construct($this->model);
    }

    public function getUserMandateData ($userId) {

        return $this->model::where('user_id', $userId)->first();
    }

}