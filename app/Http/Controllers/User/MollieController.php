<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\MollieService;
use App\Http\Services\Payment\CallBackUrlService;
use App\Http\Services\Payment\PaymentService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Mollie\Api\Exceptions\ApiException;


class MollieController extends Controller
{
    protected $mollieService;
    protected $paymentService;
    protected $callBackUrlService;


    /**
     * MollieController constructor.
     * @param MollieService $mollieService
     * @param PaymentService $paymentService
     * @param CallBackUrlService $callBackUrlService
     */
    public function  __construct(MollieService $mollieService, PaymentService $paymentService,CallBackUrlService $callBackUrlService) {
        $this->mollieService = $mollieService;
        $this->paymentService = $paymentService;
        $this->callBackUrlService = $callBackUrlService;
    }

    /**
     * Redirect the user to the Order Gateway.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function preparePayment(Request $request) {
        $type = BEST_FRIEND;
        $paymentResponse = $this->paymentService->pay($request->all(),$type);
        if (!$paymentResponse['success']) {

            return redirect(route('viewImage'))->with(['error' => $paymentResponse['message']]);
        }
        $payment = $paymentResponse['data'];
        return redirect($payment['payment']->getCheckoutUrl(), 303);
    }

    /**
     * JustFriend is the package title
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function redirectUrlOfJustFriend(Request $request) {
        $response = $this->callBackUrlService->mollieCallBackUrlOfJustFriendPackage($request->orderId);
        if (!$response['success']) {

            return redirect(route('viewImage'))->with(['error' => $response['message']]);
        }

        return redirect(route('viewImage'))->with(['success' => $response['message']]);
    }

    /**
     * BestFriend is the package title
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function redirectUrlOfBestFriend(Request $request) {
        $response = $this->callBackUrlService->mollieCallBackUrlOfBestFriendPackage($request->orderId);
        if (!$response['success']) {

            return redirect(route('viewImage'))->with(['error' => $response['message']]);
        }

        return redirect(route('viewImage'))->with(['success' => $response['message']]);
    }

    /**
     * Page redirection after the successfully payment
     *
     * @param Request $request
     * @return void
     */
    public function webhook(Request $request) {
        try {

            Log::info('============== Mollie Webhook =============');
            Log::info($request);
        } catch (ApiException $e) {
            Log::info($e);
        } catch (QueryException $e) {
            Log::info($e);
        }
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function cancelSubscription() {

        $response = $this->paymentService->cancelSubscription();
        if (!$response['success']) {

            return redirect(route('viewImage'))->with(['error' => $response['message']]);
        }

        return redirect(route('viewImage'))->with(['success' => $response['message']]);
    }
}
