<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\Payment\StripeApiService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe;

class StripePaymentController extends Controller
{
    protected $stripeApiService;

    public function __construct(StripeApiService $stripeApiService) {

        $this->stripeApiService = $stripeApiService;

    }

    /**
     * success response method.
     *
     * @return Application|Factory|View
     */
    public function stripe()
    {
        return view('blog.stripe');
    }

    /**
     * success response method.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Stripe\Exception\ApiErrorException
     */
    public function stripePost(Request $request) {

       $intent = $this->stripeApiService->subscription($request);

        dd($intent);

        return back();
    }
}

