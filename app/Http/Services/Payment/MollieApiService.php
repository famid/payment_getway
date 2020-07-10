<?php


namespace App\Http\Services\Payment;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\MandateMethod;
use Mollie\Laravel\Facades\Mollie;

class MollieApiService
{
    public $redirectUrl;
    public $webhookUrl;
    private $apiKey;
    private $mollie;
    private $name;

    /**
     * MollieApiService constructor.
     * @param MollieApiClient $mollie
     * @throws ApiException
     */
    public function __construct(MollieApiClient $mollie) {
        $this->mollie = $mollie;
        $this->apiKey = config('mollie.apiKey');
        $this->mollie->setApiKey($this->apiKey);
    }

    /**
     * @param $route
     */
    public function setRedirectUrl($route) {
        $this->redirectUrl = $route;
    }

    /**
     * @param $route
     */
    public function setWebhookUrl($route) {
        $this->webhookUrl = $route;
    }

    /**
     * @param $data
     * @param $orderId
     * @param $name
     * @return array
     */
    public function createPayment($data, $orderId,$name) :array{
        try {
            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => $data['currency'], // Type of currency you want to send
                    'value' => $data['amount'], // You must send the correct number of decimals, thus we enforce
                ],
                'description' => 'Order By code hunger',
                'redirectUrl' =>route($name, ['orderId' => $orderId]),
                "metadata" => [
                    "order_id" => $orderId,
                    "user_id" => Auth::id(),
                ],
            ]);

            return ['success' => true, 'payment' => $payment];
        } catch (ApiException $e) {

            return ['success' => false, 'payment' => []];
        }
    }
    public function getPayment($paymentId) {
        try {
            $payment = Mollie::api()->payments()->get($paymentId);;
            return ['success' => true, 'payment' => $payment];
        } catch (ApiException $e) {
            return ['success' => false, 'payment' => []];
        }
    }


    /**
     * @return array
     */
    public function createCustomer() {
        try {
            $customer = $this->mollie->customers->create([
                "name" => Auth::user()->name,
                "email" => Auth::user()->email,
            ]);

            return ['success' => true, 'customer' => $customer];
        } catch (ApiException $e) {

            return ['success' => false, 'customer' => []];
        }
    }

    /**
     * @param $customerId
     * @return array
     */
    public function updateCustomer($customerId) :array{
        $customer = $this->getCustomer($customerId);
        if (!$customer['success']) return $customer;
        try {
            $customer['customer']->name = Auth::user()->name;
            $customer['customer']->email = Auth::user()->email;
            $updateCustomer = $customer['customer']->update();

            return ['success' => true, '$updateCustomer' => $updateCustomer];
        } catch (ApiException $e) {

            return ['success' => true, 'updateCustomer' => [] ];
        }
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getCustomer($customerId) {
        try {
            $customer = $this->mollie->customers->get($customerId);
            return ['success' => true, 'customer' => $customer];
        } catch (ApiException $e) {
            return ['success' => false, 'customer' => []];
        }
    }

    /**
     * @param $customerId
     * @return bool
     */
    public function customerExists($customerId) : bool {
        return $this->getCustomer($customerId)['success'] ? true : false;
    }

    /**
     * @param $customerId
     * @param $data
     * @return array
     */
    public function createMandate($customerId,$data) :array{
        $customer = $this->getCustomer($customerId);
        if (!$customer['success']) return $customer;
        try {
            $mandate = $customer['customer']->createMandate([
                "method" => MandateMethod::DIRECTDEBIT,
                "consumerName" => Auth::user()->name,
                "consumerAccount" => $data->user_account,
                "consumerBic" => $data->user_bic,
                "signatureDate" => Carbon::parse($data->signature_date)->format('Y-m-d'),
                "mandateReference" => $data->mandate_reference,
            ]);

            return ['success' => true, 'mandate' => $mandate];
        } catch (ApiException $e) {

            return ['success' => false, 'mandate' => []];
        }
    }

    /**
     * @param $customerId
     * @param $subscriptionId
     * @param $mandateId
     * @return array
     */
    public function createSubscription($subscriptionId,$customerId, $mandateId): array{
        $customer = $this->getCustomer($customerId);
        if (!$customer['success']) return $customer;
        try {
            $subscription = $customer['customer']->createSubscription([
                "amount" => [
                    "currency" => "EUR",
                    "value" => "100.00",
                ],
                "times" => 4,
                "interval" => "1 days",
                "description" => "Quarterly payment",
                "mandateId" => $mandateId,
                "webhookUrl" => env("APP_LIVE_URL")."/subscription/webhook/".$subscriptionId,
                //"webhookUrl" => route('mollie.webhook', ['subscriptionId' => $subscriptionId]),
            ]);

            return ['success' => true, 'subscription' => $subscription];

        } catch (ApiException $e) {

            return ['success' => false, 'subscription' => []];
        }

    }

    /**
     * @param $customerId
     * @param $subscriptionId
     * @return array
     */
    public function getSubscription($customerId, $subscriptionId) {
        $customer = $this->getCustomer($customerId);
        if (!$customer['success']) return $customer;
        try {
            $subscription = $customer['customer']->getSubscription($subscriptionId);

            return ['success' => true, 'subscription' => $subscription];
        } catch (ApiException $e) {

            return ['success' => false, 'subscription' => []];
        }
    }

    /**
     * @param $customerId
     * @param $subscriptionId
     * @return array
     */
    public function cancelSubscription($customerId, $subscriptionId) :array{
        $customer = $this->getCustomer($customerId);
        if (!$customer['success']) return $customer;
        try {
            $cancelSubscription = $customer['customer']->cancelSubscription($subscriptionId);

            return ['success' => true, 'cancelSubscription' => $cancelSubscription];
        } catch (ApiException $e) {

            return ['success' => false, 'cancelSubscription' => []];

        }
    }

}