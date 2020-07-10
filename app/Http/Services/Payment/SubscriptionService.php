<?php


namespace App\Http\Services\Payment;

use App\Http\Repository\SubscriptionRepository;
use Exception;
use App\Http\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    protected $errorResponse;
    protected $errorMessage;
    protected $subscriptionRepository;

    /**
     * SubscriptionService constructor.
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->errorMessage = __('Something went wrong');
        $this->errorResponse = ['success' => false, 'message' => $this->errorMessage, 'data' => []];
    }

    /**
     * @return array
     */
    public function getUserSubscriptionId () :array {
        try {
            $subscriptionId =$this->subscriptionRepository->getSubscriptionId(Auth::id());

            return empty($subscriptionId) ? $this->errorResponse :
                ['success' => true, 'data' => $subscriptionId];
        } catch (QueryException $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param $orderId
     * @return array
     */
    public function saveInitialSubscription($orderId) :array {
        try {
            $data = $this->prepareSubscriptionData($orderId);
            $createSubscriptionResponse = $this->subscriptionRepository->create($data);

            return !$createSubscriptionResponse ? $this->errorResponse :
                ['success' => true, 'data' => $createSubscriptionResponse];
        } catch (Exception $e) {

            return $this->errorResponse;
        }
    }

    /**
     * @param $orderId
     * @return array
     */
    public function prepareSubscriptionData($orderId) :array {

        return [
            'user_id' => Auth::id(),
            'started_at' => Carbon::now(),
            'ended_at' => Carbon::now()->addDays(14),
            'status' => PAID,
            'payment_method' => STRIPE,
            'package_id' => BEST_FRIEND,
            'order_id' => $orderId
        ];
    }

    /**
     * @param int $id
     * @param string $subscriptionId
     * @return bool
     */
    public function updateSubscriptionId(int $id, string $subscriptionId): bool {
        try {
            $updateSubscriptionResponse = $this->subscriptionRepository->updateSubscriptionId($id,$subscriptionId);

            return !$updateSubscriptionResponse ? false : true;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @param string $subscriptionId
     * @param $status
     * @return bool
     */
    public function updateSubscriptionStatus (string $subscriptionId,$status) :bool{
        try {
            $updateSubscriptionResponse = $this->subscriptionRepository->updateSubscriptionStatus($subscriptionId
            ,$status);

            return !$updateSubscriptionResponse ? false : true;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * test purpose
     * @param $subscriptionId
     * @param $status
     * @return mixed
     */
    public function UpdatePaymentStatusOfSubscription($subscriptionId,$status) {
        try {
            $updatePaymentStatusResponse = $this->subscriptionRepository->changePaymentStatus($subscriptionId,$status);
            return !$updatePaymentStatusResponse ? false : true;

        }catch (Exception $e) {

            return false;
        }
    }

    /**
     * @param string $subscriptionId
     * @param $nextExpireDate
     * @return bool
     */
    public function extendSubscription (string $subscriptionId, $nextExpireDate) :bool{
        try {
            $updateSubscriptionResponse = Subscription::where('subscription_id',$subscriptionId)
                ->update(['ended_at' => Carbon::parse($nextExpireDate)]);

            return !$updateSubscriptionResponse ? false : true;
        } catch (Exception $e) {

            return false;
        }
    }
}