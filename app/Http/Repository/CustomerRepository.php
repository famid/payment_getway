<?php


namespace App\Http\Repository;


use App\Http\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerRepository Extends BaseRepository
{
    public $model;

    public function __construct(Customer $customer) {
        $this->model = $customer;
        parent::__construct($this->model);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getCustomerId($userId) {

        return $this->model::where('user_id',$userId)->orderBy('id','desc')->first()->customer_id;
    }

}