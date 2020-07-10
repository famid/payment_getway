<?php


namespace App\Http\Services\Payment;


class WebhookUrlService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $mollieApiService;
    protected $orderService;
    protected $paymentService;


    /**
     * WebhookUrlService constructor.
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

    public function mollieWebhookUrlUrlOfBestFriendPackage() {

    }

}