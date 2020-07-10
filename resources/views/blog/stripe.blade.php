<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>
{{--<form action="{{url('user/checkout')}}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="stripe">
    <input type="hidden" name="software_id" value="">
    <input type="hidden" name="interval" value="monthly">
    <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="pk_test_i9biHQKP1fkcgLX0ffjBHVWx00OVUS8BZr"
            data-image="{{asset('assets/images/logo-auth.svg')}}"
            data-name="MTCore"
            data-description="Monthly Subscription"
            data-amount="4151"
            data-label="Sign Me Up!">
    </script>
</form>--}}
<form action="{{route('stripe.post')}}" method="POST">
    @csrf
    <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key={{env('STRIPE_KEY')}}
            data-amount="1999"
            data-name="Stripe Demo"
            data-description="Online course about integrating Stripe"
            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
            data-locale="auto"
            data-currency="usd">
    </script>
</form>
</body>
</html>