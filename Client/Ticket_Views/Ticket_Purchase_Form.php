<script src="https://js.stripe.com/v3/"></script>

<form id="payment-form" method="POST">
    <!-- Card details fields -->
     <input type="hidden" name="api_function_call" value="buy_ticket">
     <input type="hidden" name="ticket_id" value="<?php echo($_GET["event_id"])?>">
    <div>
        Card Information
    </div>
    <div id="card-element">
    </div>
    <div id="card-errors">
    </div>

    <button type="submit">Buy ticket</button>
</form>

<script>

    var stripe = Stripe('pk_test_51PHBZMHUca1WAyokXb3XdrwfaspKeZhPg670i95jOuWZ00MWVQKO6zcOhCdA5U5CpfV8mG2Xxu5sajYew9yCP5H600mBkEXJ0F');
    var form = document.getElementById('payment-form');
    
    //This pusts the card information into the form
    var elements = stripe.elements();

    var card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    card.mount('#card-element');



    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var formData=$(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: formData , 
            dataType: 'text',
            encode: true
        })
        .done(function(data) {
            console.log(data);
            var response=JSON.parse(data);
            var clientSecret= response.clientSecret;

            stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card:card
                    }
                }).then(function(result) {
                    if (result.error) {
                        // Show error to your customer
                        var errorElement = document.getElementById('card-errors');
                        switch (result.error.code) {
                            case 'card_declined':
                                errorElement.textContent = 'Your card was declined. Please check with your bank or use a different card.';
                                break;
                            case 'expired_card':
                                errorElement.textContent = 'Your card has expired. Please use a different card.';
                                break;
                            case 'incorrect_cvc':
                                errorElement.textContent = 'The CVC code is incorrect. Please check and try again.';
                                break;
                            case 'processing_error':
                                errorElement.textContent = 'An error occurred while processing your card. Please try again.';
                                break;
                            default:
                                errorElement.textContent = result.error.message;
                                break;
                        }
                    } else {
                        if (result.paymentIntent.status === 'succeeded') {
                            
                            alert('Payment succeeded!');
                        }
                    }
                });
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>