<style>
.floatingButtonWrap {
    display: block;
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999999;
}
.floatingButtonInner {
    position: relative;
}
.floatingButton {
    display: block;
    width: 60px;
    height: 60px;
    text-align: center;
    box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
    background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
    color: #fff;
    line-height: 63px;
    position: absolute;
    border-radius: 50% 50%;
    bottom: 0px;
    right: 0px;
    opacity: 1;
    transition: all 0.4s;
}
.floatingButton .fas {
    font-size: 20px !important;
}
.floatingButton.open,
.floatingButton:hover,
.floatingButton:focus{
    opacity: 1;
    color: #fff;
    box-shadow: 0px 15px 20px rgba(23, 98, 145, 0.4);
    transform: translateY(-7px);
}
</style>
<div class="floatingButtonWrap">
    <div class="floatingButtonInner">
        <a href="<?php echo base_url('Sale/imei_tracker') ?>" target="_blank" class="floatingButton">
            <i class="fas fa fa-search fa-lg icon-default"></i>
        </a>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#customer_pincode').change(function(){
        var pincode = $(this).val();
        $.ajax({
            url: "https://api.postalpincode.in/pincode/"+pincode,
            success:function(data)
            {
                $(data).each(function (index, item) {
                    var result = item.Status;
                    if(result == 'Success'){
                        var postoffice = item.PostOffice;
                        var post = postoffice[postoffice.length-1];
                        $('#customer_state').val(post.State);
                        $('#customer_district').val(post.District);
                        $('#customer_city').val(post.Block);
                        getCoordinates(post.Block);
                    }else{
                        alert ('Invalid Pincode');
                    }
                });
            }
        });
    });
    function getCoordinates(address){
        var API_KEY= 'AIzaSyCGIMNp5mXHPmrhjGyMqswPwcDmpF-YmIM';
        fetch("https://maps.googleapis.com/maps/api/geocode/json?address="+address+'&key='+API_KEY)
          .then(response => response.json())
          .then(data => {
            const latitude = data.results[0].geometry.location.lat;
            const longitude = data.results[0].geometry.location.lng;
            $('#customer_latitude').val(latitude);
            $('#customer_longitude').val(longitude);
//            console.log({latitude, longitude});
          });
    }
    $(document).on("click", "#customer_submit", function (event) {
        var $form = $('.customer_form_submit');
        if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
            event.preventDefault();
            alert("Fill Mandatory fields !!");
            return false;
        }else{
            if (confirm('Do you want to Create New Customer?')) {
                var serialized = $('.customer_form_submit').serialize();
                $.ajax({
                    url: "<?php echo base_url('Sale/save_customer') ?>",
                    method: "POST",
                    data: serialized,
                    dataType: 'json',
                    success: function (data)
                    {
                        if(data.result == 'Success'){
//                            alert(data.state_data.gst_code);
                            $(data.customer_data).each(function (index, customer) {
//                                alert(customer.customer_fname);
                                $('#idcustomer').val(customer.id_customer);
                                $('#cust_mobile').val(customer.customer_contact);
                                $('#cust_fname').val(customer.customer_fname);
                                $('#cust_lname').val(customer.customer_lname);
                                $('#gst_no').val(customer.customer_gst);
                                $('#cust_pincode').val(customer.customer_pincode);
                                $('#cust_idstate').val(customer.idstate);
                                $('#cust_state').val(customer.customer_state);
                                $('#cust_latitude').val(customer.customer_latitude);
                                $('#cust_longitude').val(customer.customer_longitude);
                                $('#address').val(customer.customer_address);
                            });
                            $('.popup-close').trigger('click');
                        }else if(data.result == 'Failed'){
                            $('#idcustomer').val('');
                            $('#cust_mobile').val('');
                            $('#cust_fname').val('');
                            $('#cust_lname').val('');
                            $('#gst_no').val('');
                            $('#cust_idstate').val('');
                            $('#cust_pincode').val('');
                            $('#cust_latitude').val('');
                            $('#cust_longitude').val('');
                            $('#address').val('');
                            alert(data.msg);
                            return false;
                        }
                    }
                });
//                $('.customer_state').trigger('change');
            }
        }
    });
});
</script>