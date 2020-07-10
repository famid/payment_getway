@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card text-white bg-white">
                    @if($data['status']['success'] === false)

                        <div class="card-header card text-white bg-dark"><h6>{{$data['packages'][0]->header}}
                            </h6></div>
                        <div class="card-body">
                            {{--==============================================--}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="flip-card col-md-4">
                                        <div class="flip-card-inner">
                                            <div class="flip-card-front ">
                                                <h5 class="card-title">Just Friend</h5>
                                                <img src="{{asset('image/just_friend.jpg')}}" alt="Avatar"
                                                     style="width:200px;
                                                height:300px;">
                                            </div>
                                            <div class="flip-card-back">
                                                <div class="card text-dark bg-info " style="max-width: 18rem;">
                                                    <div class="card-header" style="background-color: #02d1f5">ONE
                                                        AND ONLY PAYMENT
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{$data['packages'][0]->title}}</h5>
                                                        <p class="card-text">{{$data['packages'][0]->description}}</p>
                                                        <form action="{{route('mollie.payment')}}" method="post">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label class="custom-input-label"
                                                                       for="amount">Amount:</label>
                                                                <input  type="number" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{AMOUNT}}" disabled>
                                                                <input type="hidden" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{AMOUNT}}">
                                                            </div>
                                                            <div class="form-group pt-5">
                                                                <label class="custom-input-label" for="currency">Select
                                                                    Your Currency :</label>
                                                                <select name="currency"
                                                                        class="form-control mt-2 custom-input-field"
                                                                        id="currency">
                                                                    <option value= {{EUR}} >EUR</option>
                                                                    <option value= {{USD}}>USD</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group pt-4">
                                                                <button id="submit" class="btn btn-outline-success">
                                                                    Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flip-card col-md-4">
                                        <div class="flip-card-inner">
                                            <div class="flip-card-front ">
                                                <h5 class="card-title">Best Friend</h5>
                                                <img src="{{asset('image/best_friend.jpg')}}" alt="Avatar"
                                                     style="width:200px;
                                                height:300px;">
                                            </div>
                                            <div class="flip-card-back">
                                                <div class="card text-white bg-dark " style="max-width: 18rem;">
                                                    <div class="card-header" style="background-color: rgb(25,26,26)">
                                                        MONTHLY SUBSCRIPTION
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title">Best Friend</h5>
                                                        <p class="card-text">Some quick example text to build on the
                                                            card title and make up the bulk of the card's content.</p>
                                                        <form action="{{route('mollie.payment')}}" method="post">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label class="custom-input-label"
                                                                       for="amount">Amount:</label>
                                                                <input type="number" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{SUBSCRIPTION_AMOUNT}}" disabled>
                                                                <input type="hidden" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{SUBSCRIPTION_AMOUNT}}" >
                                                            </div>
                                                            <div class="form-group pt-5">
                                                                <label class="custom-input-label" for="currency">Select
                                                                    Your Currency :</label>
                                                                <select name="currency"
                                                                        class="form-control mt-2 custom-input-field"
                                                                        id="currency">
                                                                    <option value= {{EUR}} >EUR</option>
                                                                    <option value= {{USD}}>USD</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group pt-4">
                                                                <button id="submit" class="btn btn-outline-success">
                                                                    Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flip-card col-md-4">
                                        <div class="flip-card-inner">
                                            <div class="flip-card-front ">
                                                <h5 class="card-title">Brotherhood</h5>
                                                <img src="{{asset('image/brotherhood.png')}}" alt="Avatar"
                                                     style="width:200px;
                                                height:300px;">
                                            </div>
                                            <div class="flip-card-back">
                                                <div class="card text-white bg-danger " style="max-width: 18rem;">
                                                    <div class="card-header" style="background-color: #e32e17">LIFE TIME
                                                        MEMBERSHIP
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title">Brotherhood</h5>
                                                        <p class="card-text">Some quick example text to build on the
                                                            card title and make up the bulk of the card's content.</p>
                                                        <form action="{{route('mollie.payment')}}" method="post">
                                                        @csrf
                                                            <div class="form-group">
                                                                <label class="custom-input-label"
                                                                       for="amount">Amount:</label>
                                                                <input type="number" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{MEMBERSHIP_AMOUNT}}" disabled>
                                                                <input type="hidden" id="amount" min="10"
                                                                       class="form-control custom-input-field"
                                                                       name="amount" value="{{MEMBERSHIP_AMOUNT}}">
                                                            </div>
                                                            <div class="form-group pt-5">
                                                                <label class="custom-input-label" for="currency">Select
                                                                    Your Currency :</label>
                                                                <select name="currency"
                                                                        class="form-control mt-2 custom-input-field"
                                                                        id="currency">
                                                                    <option value= {{EUR}} >EUR</option>
                                                                    <option value= {{USD}}>USD</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group pt-4">
                                                                <button id="submit" class="btn btn-outline-success">
                                                                    Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                        <div class="pt-3">
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


                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{--================================================================--}}
                        </div>

                    @else
                        <div class="card-header card text-white bg-dark"><h6>VIEW YOUR PICTURE </h6></div>
                        <div class="card-body">
                            <div>
                                <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img class="d-block w-100" src="{{asset('image/sundarban.jpg')}}"
                                                 alt="First slide">
                                        </div>
                                        <div class="carousel-item">
                                            <img class="d-block w-100" src="{{asset('image/sundarban1.jpg')}}"
                                                 alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <img class="d-block w-100" src="{{asset('image/sundarban2.jpg')}}"
                                                 alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleFade" role="button"
                                       data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleFade" role="button"
                                       data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-header" style="background-color: #02f56f"><button type="button" class="btn
                        btn-danger align-content-center ">
                                <a  href="{{route("mollie.cancelSubscription")}}">DELETE
                                    SUBSCRIPTION
                                    <i class="fa fa-delete"></i>
                                </a>

                            </button>

                            @endif

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            /*$("#submit").on('click', function(){
                let amount = $("#amount").val();
                let currency = $("#currency").val();
                payment(amount,currency);
                resetInputFields();
            });*/
        });

        /* function payment(amount,currency) {
             $.ajax({
                 url: '{{route('mollie.payment')}}',
                method: 'GET',
                data:{
                    /!*'_token': '{{csrf_token()}}',*!/
                    'amount': amount,
                    'currency': currency
                }
            }).done(function (data) {
                console.log(data);
            }).fail(function (error) {
                console.log(error);
            });
        }

        function resetInputFields() {
            $("#amount , #currency").val('');
        }*/
        //Methods
    </script>
@endsection
