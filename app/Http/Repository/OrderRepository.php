<?php


namespace App\Http\Repository;


use App\Http\Models\Order;

class OrderRepository Extends BaseRepository
{
    public $model;

    /**
     * OrderRepository constructor.
     * @param Order $customer
     */
    public function __construct(Order $customer) {
        $this->model = $customer;
        parent::__construct($this->model);
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getPaymentId ($orderId) {

        return $this->model::where('id', $orderId)->first()->payment_id;
    }

    /**
     * @param $id
     * @param $PaymentId
     * @return mixed
     */
    public function updatePaymentId($id, $PaymentId) {

        return $this->model::where('id',$id)->update(['payment_id' => $PaymentId]);
    }

    /**
     * @param $paymentId
     * @param $status
     * @return mixed
     */
    public function updatePaymentStatus($paymentId, $status) {

        return $this->model::where('payment_id', $paymentId)->update(['status' => $status]);
    }

}