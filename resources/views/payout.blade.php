@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Withdraw Funds</div>
                    <div class="card-body">
                        <form action="{{ route('payout') }}" method="POST" id="payment-form">
                            @csrf
                            <div class="form-group">
                                <label for="card-number">Card Number</label>
                                <div id="card-number"></div> <!-- Stripe.js will place the card number input here -->
                            </div>
                            <div class="form-group">
                                <label for="card-expiry">Card Expiry</label>
                                <div id="card-expiry"></div> <!-- Stripe.js will place the card expiry input here -->
                            </div>
                            <div class="form-group">
                                <label for="card-cvc">Card CVC</label>
                                <div id="card-cvc"></div> <!-- Stripe.js will place the card CVC input here -->
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submit-button">Withdraw</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');

        var elements = stripe.elements();
        var cardNumberElement = elements.create('cardNumber');
        var cardExpiryElement = elements.create('cardExpiry');
        var cardCvcElement = elements.create('cardCvc');

        cardNumberElement.mount('#card-number');
        cardExpiryElement.mount('#card-expiry');
        cardCvcElement.mount('#card-cvc');

        var form = document.getElementById('payment-form');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(cardNumberElement).then(function(result) {
                if (result.error) {
                    // Show error in your form
                } else {
                    // Send the token to your server
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', result.token.id);
                    form.appendChild(hiddenInput);

                    form.submit();
                }
            });
        });
    </script>
@endpush
