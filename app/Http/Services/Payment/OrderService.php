<?php


namespace App\Http\Services\Payment;

use App\Http\Repository\OrderRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $orderRepository;

    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * @param $data
     * @return array
     */
    public function saveInitialOrder($data) :array{
        try {
            $data = $this->prepareOrderData($data);
            $createOrderResponse = $this->orderRepository->create($data);
            return !$createOrderResponse ? $this->errorResponse :
                ['success' => true, 'data' => $createOrderResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function prepareOrderData ($data) :array {

        return [
            'user_id' => Auth::id(),
            'payment_date' => Carbon::now(),
            'amount' => $data['amount'], // FORM DATA -> request
            'currency' => $data['currency'], // FORM DATA -> request
            'status' => PENDING,// CORE CONSTANT
            'payment_method' => MOLLIE
        ];
    }

    /**
     * @param $orderId
     * @param $paymentId
     * @return bool
     */
    public function updatePaymentId($orderId, $paymentId) :bool {
        try {
            $updateOrderResponse = $this->orderRepository->updatePaymentId($orderId,$paymentId);

            return !$updateOrderResponse ? false : true;
        } catch (Exception $e) {

            return  false;
        }
    }

    /**
     * @param $paymentId
     * @param $status
     * @return bool
     */
    public function updatePaymentStatus ($paymentId,$status) :bool {
        try {
            $completeOrderResponse = $this->orderRepository->updatePaymentStatus($paymentId,$status);

            return !$completeOrderResponse ? false : true;
        } catch (Exception $e) {

            return  false;
        }
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getPaymentId ($orderId) :array {
        try {
            $paymentId = $this->orderRepository->getPaymentId($orderId);
            return empty($paymentId) ? ['success' => false,'data' => []] :
                ['success' => true,'data' => $paymentId];

        } catch (Exception $e) {
            return ['success' => false,'data' => []];
        }
    }
}