
<?php
include_once("ServiceProvider/Class_lib/Payment_Manager.php");
$payment_manager=new Payment_Manager();
$accounts=$payment_manager->getAccounts();

?>
<div>
    <form id="target" method="post">
        <input type="hidden" name="api_function_call" value="refund">
        <label>Select one of your accounts the refund needs to go to.</label>
        <select name="stripe_external_bank_account_id">
            <?php foreach($accounts as $account):?>
                <option value="<?php echo $account["stripe_external_bank_account_id"]?>"><?php echo($account["name"])?></option>
            <?php endforeach;?>
        </select>
        <input type="submit" value="Initialize refund">
    </form>
</div>
<script>
    $('#target').submit(function(event) {
        event.preventDefault();
        var apiCall=$("#api_function_call").val();
        var type='POST';
        var formData=new FormData(this);
        var error=$("#error");
        var data={};
        
        $.ajax({
            type: type,
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: formData ,
            processData: false,
            contentType: false, 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            console.log(data);
            var errorValue=false;
            if(data==true){
                alert("Success (Note: Refunds may take up to 10 days to be deposited into your bank account.)");
                window.location.href="http://localhost/project/trustyticket?action=financial";
            }
            else{

            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>