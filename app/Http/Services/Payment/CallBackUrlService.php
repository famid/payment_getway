<?php


namespace App\Http\Services\Payment;


use Exception;

class CallBackUrlService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $mollieApiService;
    protected $orderService;
    protected $paymentService;

    /**
     * CallBackUrlService constructor.
     * @param MollieApiService $mollieApiService
     * @param OrderService $orderService
     * @param PaymentService $paymentService
     */
    public function __construct(MollieApiService $mollieApiService ,
                                OrderService $orderService,
                                PaymentService $paymentService) {
        $this->mollieApiService = $mollieApiService;
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * @param int $orderId
     * @return array
     */
    public function  mollieCallBackUrlOfJustFriendPackage(int $orderId) {
        try {
            $completePaymentResponse = $this->completePayment($orderId);
            return !$completePaymentResponse ? ['success' => false, 'message' => __('Please try again')] :
                ['success' => true, 'message' => __('payment is paid successfully')];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param int $orderId
     * @return array
     */
    public function  mollieCallBackUrlOfBestFriendPackage(int $orderId) {
        try {
            $completePaymentResponse = $this->completePayment($orderId);
            if (!$completePaymentResponse) {

                return ['success' => false, 'message' => __('Please try again.Something went wrong')];
            }
            $subscriptionResponse = $this->paymentService->makeSubscriptionOfUser($orderId);
            if(!$subscriptionResponse) {

                return ['success' => false, 'message' => __('payment is paid successfully but subscription is failed')];
            }

            return ['success' => true, 'message' => __('payment is paid successfully and subscription is complete')];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param $orderId
     * @return bool
     */
    protected function completePayment ($orderId) {
        $paymentIdResponse = $this->orderService->getPaymentId($orderId);

        if(!$paymentIdResponse['success']) return false;
        $paymentId = $paymentIdResponse['data'];
        $paymentPaidResponse = $this->checkPaymentIsPaid($paymentId);

        if (!$paymentPaidResponse) return false;
        $paymentCompleteResponse = $this->orderService->updatePaymentStatus($paymentId,PAID);

        return !$paymentCompleteResponse ? false : true;
    }

    /**
     * @param $paymentId
     * @return bool
     */
    protected function checkPaymentIsPaid ($paymentId) {
        $payment = $this->mollieApiService->getPayment($paymentId);

        return !$payment['success'] || !$payment['payment']->isPaid() ? false : true;
    }
}