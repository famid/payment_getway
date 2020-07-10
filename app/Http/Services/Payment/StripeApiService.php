<?php


namespace App\Http\Services\Payment;

use Illuminate\Http\Request;
use Mollie\Api\Exceptions\ApiException;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Subscription;
use Stripe\WebhookEndpoint;

class StripeApiService
{
    private $secretKey;
    private $stripe;
    private $customer;
    private $charge;
    private $product;
    private $price;
    private $subscription;
    private $webhookEndpoint;

    public function __construct(Stripe $stripe,Customer $customer,Charge $charge,Product $product,Price $price,
                                Subscription $subscription,WebhookEndpoint $webhookEndpoint) {
        $this->stripe = $stripe;
        $this->customer = $customer;
        $this->charge = $charge;
        $this->product = $product;
        $this->price = $price;
        $this->webhookEndpoint = $webhookEndpoint;
        $this->subscription = $subscription;
        $this->secretKey = config('stripe.secretKey');
        $this->stripe->setApikey($this->secretKey);
    }

    public function pay ($data) {
        try {

            $chargeResponse = $this->createCharge($data);

            if(!$chargeResponse['success']) return $chargeResponse;

            return ['success' => true, 'data' => [/*$customerResponse,*/ $chargeResponse]];

        } catch (\Exception $e) {
            dd($e->getMessage());

            return ['success' => false, 'data' => []];

        }

    }

    public function subscription($data) {
        try {
            $customerResponse = $this->createCustomer($data);

            if(!$customerResponse['success']) return $customerResponse;
            $productResponse = $this->createProduct();

            if(!$productResponse['success']) return $productResponse;
            $priceResponse = $this->createPrice($productResponse['product']->id);

            if(!$priceResponse['success']) return $priceResponse;
            $subscriptionResponse= $this->createSubscription($customerResponse['customer']->id,
                $priceResponse['price']->id);

            if(!$subscriptionResponse['success']) return $subscriptionResponse;

            return ['success' => true, 'data' => [$customerResponse, $subscriptionResponse]];
        } catch (ApiErrorException $e) {
            dd($e->getMessage());

            return ['success' => false, 'data' => []];
        }

    }

    /**
     * @return array
     * @throws ApiErrorException
     */
    public function createProduct() {
        try {
            $product= $this->product::create([
                'name' => 'Weekly Car Wash Service',
                'type' => 'service',
            ]);

            return ['success' => true, 'product' => $product];
        } catch (ApiException $e) {

            return ['success' => true, 'product' => []];
        }
    }

    /**
     * @param $productId
     * @return array
     * @throws ApiErrorException
     */
    public function createPrice($productId) {
        try {
            $price = $this->price::create([
                'nickname' => 'Standard Monthly',
                'product' => $productId,
                'unit_amount' => 2000,
                'currency' => 'usd',
                'recurring' => [
                    'interval' => 'month',
                    'usage_type' => 'licensed',
                ],
            ]);
            return ['success' => true, 'price' => $price];
        } catch (ApiException $e) {
            return ['success' => true, 'price' => []];
        }
    }

    /**
     * @param $customerId
     * @param $priceId
     * @return array
     * @throws ApiErrorException
     */
    public function createSubscription($customerId,$priceId) {
        try {
            $subscription =$this->subscription->create([
                'customer' => $customerId,
                'items' => [/*['price' => $priceId]*/],
            ]);

            return ['success' => true, 'subscription' => $subscription];
        } catch (ApiException $e) {

            return ['success' => true, 'subscription' => []];
        }
    }

    /**
     * @param $data
     * @return array
     * @throws ApiErrorException
     */
    public function createCustomer ($data) {
        try {
            $customer = $this->customer::create(array(
                'email' => $data->stripeEmail,
                'source'  => $data->stripeToken
            ));

            return ['success' => true, 'customer' => $customer];

        } catch (ApiException $e) {

            return ['success' => true, 'customer' => []];
        }

    }

    /**
     * @param $data
     * @return array
     * @throws ApiErrorException
     */
    public function createCharge($data) {
        try {
            $charge = Charge::create(array(
               // 'customer' => $customerId,
                'amount'   => 1999,
                'currency' => 'usd'
            ));

            return ['success' => true, 'charge' => $charge];

        } catch (ApiException $e) {
            return ['success' => false, 'charge' => []];
        }
    }

    /**
     * @return array
     * @throws ApiErrorException
     */
    public function  createWebhookEndpoint() {
       try {
           $webhookEndpoint = $this->webhookEndpoint->create([
               'url' => 'https://example.com/my/webhook/endpoint',
               'enabled_events' => [
                   'charge.failed',
                   'charge.succeeded',
               ],
           ]);

           return ['success' => true, 'webhookEndpoint' => $webhookEndpoint];
       } catch (ApiException $e) {

           return ['success' => false, 'webhookEndpoint' => []];
       }
    }

}