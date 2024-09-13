<?php
include_once("ServiceProvider/Class_Lib/Events_Manager.php");
include_once("ServiceProvider/Class_Lib/Ticket_Manager.php");
$event_manager=new Events_Manager();
$event=$event_manager->getRecord($_GET["event_id"]);
$ticket_Manager=new Ticket_Manager();
$tickets=$ticket_Manager->getAvailableTickets($_GET["event_id"]);
$max_seats=$event["max_seats"];
$ticketsSold=$tickets["tickets_sold"];

$max_tickets=$max_seats-$ticketsSold;
?>
<script src="https://js.stripe.com/v3/"></script>
<div>
    <form id="payment-form" method="POST">
        <!-- Card details fields -->
        <input id="api_call" type="hidden" name="api_function_call" value="buy_ticket">
        <input id="event_id" type="hidden" name="event_id" value="<?php echo($_GET["event_id"])?>">
        
        <div class="label_input">
            <label>Number Of Tickets to purchase. Seats Availiable <?php echo($max_tickets)?></label>
            <input id="number_of_tickets" required type="number" name="number_of_tickets" min="1" max="<?php echo($max_tickets)?>" step="1">
        </div>
        <div class="label_input">
            <label>Cost per Ticket</label>
            <input id="cost_per_ticket" readonly type="text" value="<?php echo($event["charge"])?>"></input>
        </div>
        <div class="label_input">
            <label>Cost After fee's</label>
            <input id="total_cost" readonly type="text">
        </div>
        <div>
            Card Information
        </div>
        <div id="card-element">
        </div>
        <div id="card-errors" style="color:red">
        </div>

        <button type="submit">Buy ticket</button>
    </form>
    <div>4000000000000077 instant</div>
</div>
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
    var success=false;

function checkAvailableTickets(){
    return new Promise((resolve,reject)=>{
    var data={
        event_id:$("#event_id").val(),
        api_function_call:"check_ticket_availability",
        number_of_tickets:$("#number_of_tickets").val()
    };

    $.ajax({
            type: 'POST',
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data:  data, 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            var errorValue=false;
            success=data;
            resolve(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
            reject(false);
        });
    },1000);
}

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
        .done(async function(data) {
            console.log(data);
            var response=JSON.parse(data);
            var clientSecret= response.clientSecret;

            await checkAvailableTickets();

            if(success==true){
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
                            //!!!! this section of code is not secure and i would never use it in live production
                            //a stripe a web hook should be used but i am running low on time.
                            insertTicket();
                            alert("Payment succeeded");
                           window.location.href="http://localhost/project/trustyticket?action=tickets";
                        }
                    }
                });
            }
            else{
                alert("The number of tickets you are attempting to purchase are no longer available");
                window.location.href="http://localhost/project/trustyticket?action=tickets&sub=buy&event_id=<?php echo($_GET["event_id"])?>:"+checkAvailableTickets();
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
function insertTicket(){
    var data={
        event_id:$("#event_id").val(),
        api_function_call:"insert_ticket",
        number_of_tickets:$("#number_of_tickets").val()
    };

    $.ajax({
            type: 'POST',
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data:  data, 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            var errorValue=false;
            console.log(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
}


$(document).ready(function() {
    var cost=$("#cost_per_ticket").val();
    var totalCost=$("#total_cost");
    $('#number_of_tickets').change(function() {
        var numberOfTickets = $(this).val();
        var intialCost=numberOfTickets*cost;
        var stripeFlatFee=.30;
        var ourFee=(intialCost)*.10;
        var total=intialCost+stripeFlatFee+ourFee;
        totalCost.val(total.toFixed(2));
    });
});
</script>