<?php


namespace App\Http\Services\Payment;

use App\Http\Repository\CustomerRepository;
use Exception;
use App\Http\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $mollieApiService;
    protected $customerRepository;

    /**
     * CustomerService constructor.
     * @param MollieApiService $mollieApiService
     * @param CustomerRepository $customerRepository
     */
    public function __construct(MollieApiService $mollieApiService,
                                CustomerRepository $customerRepository) {
        $this->mollieApiService = $mollieApiService;
        $this->customerRepository = $customerRepository;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * saveInitialCustomer method save customerInfo in Database without customerId
     * updateCustomerId method save the customerId in Database after create the  customer by createCustomer method
     * @return array
     */
    protected function customerCreateProcess() {
        try {
            DB::beginTransaction();
            $storeCustomer = $this->saveInitialCustomer();

            if (!$storeCustomer['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $customer = $this->mollieApiService->createCustomer();
            if (!$customer['success']) {
                DB::rollBack();

                return $this->errorResponse;
            }
            $updateCustomer = $this->updateCustomerId($storeCustomer['data']->id, $customer['customer']->id);
            if (!$updateCustomer) {
                DB::rollBack();

                return $this->errorResponse;
            }
            DB::commit();

            return ['success' => true, 'data' => $customer['customer']->id];

        } catch (Exception $e) {

            return ['success' => false, 'data' => []];
        }
    }

    /**
     * @return array
     */
    protected function saveInitialCustomer() {
        try {
            $data = $this->prepareCustomerData();
            $createCustomerResponse = Customer::create($data);
            if (!$createCustomerResponse) {

                return $this->errorResponse;
            }

            return ['success' => true, 'data' => $createCustomerResponse];
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
            'payment_method' => STRIPE
        ];
    }

    /**
     * @param int $id
     * @param string $customerId
     * @return bool
     */
    protected function updateCustomerId (int $id, string $customerId) {
        try {
            $where=['id'=>$id];
            $data=['customer_id' => $customerId];
            $updateCustomerResponse = $this->customerRepository->update($where, $data);

            return !$updateCustomerResponse ? false : true;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @return array
     */
    public function getCustomerId (){
        try {
            $customerExistence = $this->getUserCustomerId();
            if (!$customerExistence['success']) {
                $createCustomerResponse = $this->customerCreateProcess();
                return !$createCustomerResponse['success'] ? $this->errorResponse :
                    ['success' => true, 'data' => $createCustomerResponse['data']];
            }

            return ['success' => true, 'data' => $customerExistence['data']];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return array
     */
    public function getUserCustomerId () {
        try {
            $customerId =$this->customerRepository->getCustomerId(Auth::id());
            if(empty($customerId)) {

                return $this->errorResponse;
            }

            return ['success' => true, 'data' => $customerId];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @return bool
     */
    protected function customerIdIsExist () {
        $customerIdResponse = $this->getUserCustomerId();

        return !$customerIdResponse['success'] ? false : true;
    }
}