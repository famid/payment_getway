<?php


namespace App\Http\Services;


use App\Customer;
use App\Models\Order;
use App\Models\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Laravel\Facades\Mollie;

class MollieService
{
    protected $errorResponse;
    protected $errorMessage;

    /**
     * MollieService constructor.
     */
    public function __construct(){
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * @param array $data
     * @return array
     */
    public function pay(array $data) :array {
        try {
            DB::beginTransaction();
            $order = $this->storePaymentInfo($data);

            if(!$order['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $payment = $this->preparePayment($data,$order['data']->id);
            if(!$payment['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $updateResponse = $this->updatePaymentInfo($order['data']->id, $payment['payment']->id);
            if(!$updateResponse['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            DB::commit();

            return ['success' => true, 'message' => 'payment is stored and created successfully', 'data' => $payment];
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function storePaymentInfo($data) :array{
        try {
            $data = $this->prepareData($data);
            $createResponse = Order::create($data);
            if (!$createResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'payment info  is store successfully', 'data' => $createResponse];
        } catch (Exception $e) {

            return ['success' => false, 'message' => [$e->getMessage()]];
        }
    }

    /**
     * Redirect the user to the Order Gateway.
     *
     * @param $data
     * @param $orderId
     * @return array
     */
    public function preparePayment($data,$orderId) :array{
        $userId = Auth::id();
        try {
            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => $data['currency'], // Type of currency you want to send
                    'value' => $data['amount'], // You must send the correct number of decimals, thus we enforce
                ],
                'description' => 'Order By code hunger',
                'redirectUrl' => route('mollie.redirectUrl', ['order_id' => $orderId]),
                "metadata" => [
                    "user_id" => $userId,
                ],
            ]);

            return ['success' => true, 'payment' => $payment];
        } catch (Exception $e) {

            return ['success' => false, 'message' => [$e->getMessage()]];
        }
    }

    /**
     * @param $orderId
     * @param $paymentId
     * @return array
     */
    public function updatePaymentInfo($orderId, $paymentId) :array {
        try {
            $updateResponse = Order::where('id', $orderId)->update(['payment_id' => $paymentId]);
            if (!$updateResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'your payment is successfully done', 'data' => $updateResponse];
        } catch (Exception $e) {

            return ['success' => false, 'message' => [$e->getMessage()]];

        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function prepareData ($data) :array {

        return [
            'user_id' => Auth::id(),
            'payment_date' => Carbon::now(),
            'amount' => $data['amount'], // FORM DATA -> request
            'currency' => $data['currency'], // FORM DATA -> request
            'payment_status' => PENDING// CORE CONSTANT
        ];
    }

    /**
     * Page redirection after the successfully payment
     *
     * @param int $orderId
     * @return array
     */
    public function redirectUrl(int $orderId ) :array{
        try {
            $paymentId = Order::where('id', $orderId)->first()->payment_id;
            $payment = Mollie::api()->payments()->get($paymentId);
            $paymentValidated = $this->validatePayment($payment);
            if (!$paymentValidated['status']) {

                return $paymentValidated;
            }

            $paymentUpdateResponse = Order::where('payment_id', $paymentId)->update(['payment_status' => PAID]);
            if (!$paymentUpdateResponse) {

                return $this->errorResponse;
            }
            $paymentTypeIsSubscription = $this->typeIsSubscription($orderId);

            return $this->subscribe($paymentTypeIsSubscription);

        } catch (ApiException $e) {

            return $this->errorResponse;
        } catch (QueryException $e) {

            return $this->errorResponse;
        }
    }

    protected function validatePayment($payment) {
        if (!$payment->isPaid()) {

            return ['success' => false, 'message' => __("payment is  not received, please try again.")];
        }
        return ['success' => true];


    }

    protected function subscribe($paymentTypeIsSubscription) {
        if ($paymentTypeIsSubscription) {
            $subscriptionResponse = $this->subscription();
            if(!$subscriptionResponse['success']) {

                return ['success' => true, 'message' => __("payment is  received successfully,not subscription")];

            }

            return ['success' => true, 'message' => __("payment is  received successfully and also subscription")];
        }

        return ['success' => true, 'message' => __("payment is  received successfully ")];
    }

    /**
     * @return array
     */
    public function subscription () {
        try {
            DB::beginTransaction();
            $customerIdResponse = $this->customer();

            if(!$customerIdResponse) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $customerId = $customerIdResponse['data'];
            $storeSubscription = $this->storeSubscription();
            if (!$storeSubscription['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $subscription = $this->createSubscription($storeSubscription['data']->id,$customerId);
            if (!$subscription['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $updateSubscription = $this->updateSubscription($storeSubscription['data']->id, $subscription['data']->id);
            if (!$updateSubscription['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            DB::commit();

            return ['success' => true, 'data' => $subscription];
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse;
        }
    }

    /**
     * @param int $id
     * @param string $subscriptionId
     * @return array
     */
    public function updateSubscription (int $id, string $subscriptionId) {
        try {
            $updateSubscriptionResponse = Subscription::where('id',$id)->update(['subscription_id' => $subscriptionId]);
            if (!$updateSubscriptionResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'Subscription info  is update successfully', 'data' =>
                $updateSubscriptionResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param int $subscriptionId
     * @param $customerId
     * @return array|\Mollie\Api\Resources\Subscription
     */
    public function createSubscription(int $subscriptionId, $customerId) {
        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_ayHa7mpnhhauRgyFK4bruquKzKzUCt");
            $customer = $mollie->customers->get($customerId);
            $mandate = $customer->createMandate([
                "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
                "consumerName" => Auth::user()->name,
                "consumerAccount" => "NL55INGB0000000000",
                "consumerBic" => "INGBNL2A",
                "signatureDate" => "2020-06-04",
                "mandateReference" => "YOUR-COMPANY-MD13804",
            ]);
            $subscription = $customer->createSubscription([
                "amount" => [
                    "currency" => "EUR",
                    "value" => "100.00",
                ],
                "times" => 4,
                "interval" => "1 days",
                "description" => "Quarterly payment",
                "mandateId" => $mandate->id,
                "webhookUrl" => env("APP_LIVE_URL")."/subscription/webhook/".$subscriptionId,
                //"webhookUrl" =>  route('mollie.redirectUrl', ['order_id' => $orderId]),
                //"method" => "creditcard",
            ]);

            return ['success' => true, 'data' => $subscription];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function storeSubscription() {
        try {
            $data = $this->prepareSubscriptionData();
            $createCustomerResponse = Subscription::create($data);
            if(!$createCustomerResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'customer info  is store successfully', 'data' => $createCustomerResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function prepareSubscriptionData() {

        return [
            'user_id' => Auth::id(),
            'started_at' => Carbon::now(),
            'ended_at' => Carbon::tomorrow(),
            'status' => PENDING
        ];
    }

    /**
     * @return array
     */
    protected function customer () {
        global $customer;
        try {
            DB::beginTransaction();
            $customerExistence = $this->customerIsExist();
            if(!$customerExistence['success']) {
                $storeCustomer = $this->storeCustomer();
                if (!$storeCustomer['success']) {
                    DB::rollBack();

                    return $this->errorResponse;
                }
                $customer = $this->createCustomer();
                if (!$customer['success']) {
                    DB::rollBack();

                    return $this->errorResponse;
                }
                $updateCustomer = $this->updateCustomer($storeCustomer['data']->id, $customer['customer']->id);
                if (!$updateCustomer['success']) {
                    DB::rollBack();

                    return $this->errorResponse;
                }
            }
            $customerId = $customerExistence['success'] ? $customerExistence['data']: $customer['customer']->id;
            DB::commit();

            return ['success' => true, 'data' => $customerId];
        }catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse;
        }
    }

    /**
     * @param int $id
     * @param string $customerId
     * @return array
     */
    public function updateCustomer (int $id, string $customerId) {
        try {
            $updateCustomerResponse = Customer::where('id',$id)->update(['customer_id' => $customerId]);
            if (!$updateCustomerResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'customer info  is update successfully', 'data' =>
                $updateCustomerResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function createCustomer () {
        $userName = Auth::user()->name;
        $userEmail = Auth::user()->email;
        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_ayHa7mpnhhauRgyFK4bruquKzKzUCt");
            $customer = $mollie->customers->create([
                "name" => $userName,
                "email" => $userEmail,
            ]);

            return ['success' => true, 'customer' => $customer];

        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function storeCustomer () {
        try {
            $data = $this->prepareCustomerData();
            $createCustomerResponse = Customer::create($data);
            if (!$createCustomerResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'message' => 'customer info  is store successfully', 'data' => $createCustomerResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    protected function prepareCustomerData() {

        return [
            'user_id' => Auth::id(),
        ];
    }

    /**
     * @return array
     */
    protected function customerIsExist () {
        try {
            $customerId = Customer::where('user_id',Auth::id())->orderBy('id','desc')->first()->customer_id;
            if(empty($customerId)) {

                return $this->errorResponse;
            }

            return ['success' => true, 'data' => $customerId];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }


    /**
     * @param $orderId
     * @return bool
     */
    protected function typeIsSubscription ($orderId) {
        try {

            $amount = Order::where('id', $orderId)->first()->amount;
            if($amount != SUBSCRIPTION_AMOUNT) {

                return false;
            }
            return true;
        } catch (Exception $e) {

            return false;
        }
    }
}