<?php


namespace App\Http\Services\Payment;

use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $orderService;
    protected $mollieApiService;
    protected $customerService;
    protected $subscriptionService;
    protected $mandateService;
    protected $errorMessage;
    protected $errorResponse;

    /**
     * PaymentService constructor.
     * @param OrderService $orderService
     * @param MollieApiService $mollieApiService
     * @param CustomerService $customerService
     * @param SubscriptionService $subscriptionService
     * @param MandateService $mandateService
     */
    public function __construct(OrderService $orderService,
                                MollieApiService $mollieApiService,
                                CustomerService $customerService,
                                SubscriptionService $subscriptionService,
                                MandateService $mandateService) {
        $this->orderService = $orderService;
        $this->mollieApiService = $mollieApiService;
        $this->customerService = $customerService;
        $this->subscriptionService = $subscriptionService;
        $this->mandateService = $mandateService;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * @param array $data
     * @param $type
     * @return array
     */
    public function pay(array $data,$type) :array {
        try {
            DB::beginTransaction();
            $order = $this->orderService->saveInitialOrder($data);
            if(!$order['success']) {
                throw new \Exception($this->errorMessage);
            }// set redirect url accordingly
            //$route = $type == JUST_FRIEND ? route('mollie.justFriend.redirectUrl') : route('mollie.bestFriend
            //.redirectUrl');
            //$this->mollieApiService->setRedirectUrl($route);
            $type = $type === JUST_FRIEND ? 'mollie.justFriend.redirectUrl':'mollie.bestFriend.redirectUrl';
            $payment = $this->mollieApiService->createPayment($data,$order['data']->id,$type);
            if(!$payment['success']) {
                throw new \Exception($this->errorMessage);
            }
            $updateResponse = $this->orderService->updatePaymentId($order['data']->id, $payment['payment']->id);
            if(!$updateResponse) {
                throw new \Exception($this->errorMessage);
            }
            DB::commit();

            return ['success' => true, 'data' => $payment];
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse;
        }
    }

    /**
     * @param $orderId
     * @return bool
     */
    public function makeSubscriptionOfUser ($orderId) : bool {
        try {
            $customerIdResponse = $this->customerService->getCustomerId();

            if(!$customerIdResponse['success']) return false;
            $mandateIdResponse = $this->mandateService->getMandateId($customerIdResponse['data']);

            if(!$mandateIdResponse['success']) return false;

            $createSubscriptionResponse = $this->createSubscriptionProcess($customerIdResponse['data'],
                $mandateIdResponse['data'],$orderId);

            return !$createSubscriptionResponse['success'] ? false : true;

        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @param $customerId
     * @param $mandateId
     * @param $orderId
     * @return array
     */
    public function createSubscriptionProcess($customerId, $mandateId,$orderId) :array{
        try {
            DB::beginTransaction();
            $storeSubscription = $this->subscriptionService->saveInitialSubscription($orderId);
            if (!$storeSubscription['success']) {
                throw new \Exception($this->errorMessage);
            }
            $subscription = $this->mollieApiService->createSubscription($storeSubscription['data']->id,
                $customerId, $mandateId);
            if (!$subscription['success']) {
                throw new \Exception($this->errorMessage);
            }
            $updateSubscription = $this->subscriptionService->updateSubscriptionId($storeSubscription['data']->id,
                $subscription['subscription']->id);
            if (!$updateSubscription) {
                throw new \Exception($this->errorMessage);
            }
            DB::commit();

            return ['success' => true, 'data' => $subscription];
        } catch (Exception $e){
            DB::rollBack();

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function cancelSubscription() : array {
        try {
            $getCustomerIdAndSubscriptionId = $this->getUserCustomerIdAndSubscriptionId();

            if(!$getCustomerIdAndSubscriptionId['success']) return $getCustomerIdAndSubscriptionId;
            $customerId=$getCustomerIdAndSubscriptionId['data'] ['customer_id'];
            $subscriptionId=$getCustomerIdAndSubscriptionId['data'] ['subscription_id'];
            $cancelSubscriptionProcess= $this->cancelSubscriptionProcess($customerId,$subscriptionId);

            return !$cancelSubscriptionProcess ? ['success' => false , 'message' => __('please try again')] :
                ['success' => true, 'message' => __('subscription is canceled successfully')];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    protected function getUserCustomerIdAndSubscriptionId() :array{
        $customerIdResponse = $this->customerService->getUserCustomerId();

        if(!$customerIdResponse['success']) return $customerIdResponse;
        $subscriptionIdResponse = $this->subscriptionService->getUserSubscriptionId();
        if(!$subscriptionIdResponse['success']) return $subscriptionIdResponse;
        $data = [
            'customer_id' => $customerIdResponse['data'],
            'subscription_id' => $subscriptionIdResponse['data']
        ];

        return ['success' => true,'data' => $data ];
    }

    /**
     * @param $customerId
     * @param $subscriptionId
     * @return bool
     */
    protected function cancelSubscriptionProcess ($customerId, $subscriptionId) {
            DB::beginTransaction();
            $cancelSubscriptionResponse = $this->mollieApiService->cancelSubscription($customerId, $subscriptionId);

            if(!$cancelSubscriptionResponse['success']) {
               DB::rollBack();

               return false;
            }
            $updateResponse = $this->updateSubscriptionAndPaymentStatus($cancelSubscriptionResponse);

            if (!$updateResponse) {
                DB::rollBack();

                return false;
            }
            DB::commit();
            return true;
    }

    /**
     * @param $cancelSubscriptionResponse
     * @return bool
     */
    public function updateSubscriptionAndPaymentStatus($cancelSubscriptionResponse) : bool {
        if($cancelSubscriptionResponse['cancelSubscription']->status !== "canceled") {

            return false;
        }
        $updateSubscriptionStatusResponse = $this->subscriptionService->updateSubscriptionStatus
        ($cancelSubscriptionResponse['cancelSubscription']->id, PENDING);

        if (!$updateSubscriptionStatusResponse) return false;
        $updatePaymentStatusResponse = $this->subscriptionService->UpdatePaymentStatusOfSubscription
        ($cancelSubscriptionResponse['cancelSubscription']->id,PENDING);

        if (!$updatePaymentStatusResponse) return false;

        return true;
    }



}