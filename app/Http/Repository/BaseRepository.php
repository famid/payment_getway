<?php


namespace App\Http\Repository;


class BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     * @param $model
     */
    public function __construct($model) {
        $this->model = $model;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data) {
        return $this->model->create($data);
    }

    /**
     * @param $where
     * @param $data
     * @return mixed
     */
    public function update($where, $data) {

        return $this->model->where($where)->update($data);
    }

}