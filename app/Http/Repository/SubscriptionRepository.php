<?php


namespace App\Http\Repository;


use App\Http\Models\Subscription;

class SubscriptionRepository Extends BaseRepository
{
    public $model;

    public function __construct(Subscription $subscription) {
        $this->model = $subscription;
        parent::__construct($this->model);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getSubscriptionId($userId) {

        return $this->model::where('user_id',$userId)->orderBy('id','desc')->first()->subscription_id;
    }

    /**
     * @param $id
     * @param $subscriptionId
     * @return mixed
     */
    public function updateSubscriptionId($id, $subscriptionId) {

        return $this->model::where('id',$id)->update(['subscription_id' => $subscriptionId]);
    }

    /**
     * @param $subscriptionId
     * @param $status
     * @return mixed
     */
    public function updateSubscriptionStatus($subscriptionId, $status) {

        return $this->model::where('subscription_id',$subscriptionId)->update(['status' => $status]);
    }

    /**
     * @param $subscriptionId
     * @param $status
     * @return mixed
     */
    public function changePaymentStatus($subscriptionId,$status) {

        return  $this->model::select()
            ->leftJoin('orders',['orders.id' => 'subscriptions.order_id'])
            ->where('subscriptions.subscription_id',$subscriptionId)
            ->update(['orders.status' => $status]);
    }

}