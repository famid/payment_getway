<?php


namespace App\Http\Services\Payment;


use App\Http\Repository\MandateRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class MandateService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $mollieApiService;
    protected $mandateRepository;

    /**
     * CustomerService constructor.
     * @param MollieApiService $mollieApiService
     * @param MandateRepository $mandateRepository
     */
    public function __construct(MollieApiService $mollieApiService,
                                MandateRepository $mandateRepository) {
        $this->mollieApiService = $mollieApiService;
        $this->mandateRepository = $mandateRepository;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    public function getMandateData () {
        try {
            $mandateData = $this->mandateRepository->getUserMandateData(Auth::id());
            if (is_null($mandateData)) {

                return ['success' => false, 'data' => []];
            }

            return ['success' => true, 'data' => $mandateData];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    public function getMandateId ($customerId) {
        try {
            $mandateData = $this->getMandateData();

            if(!$mandateData['success']) return  $mandateData;
            $createMandate=$this->mollieApiService->createMandate($customerId,$mandateData['data']);

            if(!$createMandate['success']) return $createMandate;

            return ['success' => true, 'data' => $createMandate['mandate']->id];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

}