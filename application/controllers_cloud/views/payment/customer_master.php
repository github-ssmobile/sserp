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
    /*background-color: #fff;*/
    box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
    background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
    color: #fff;
    line-height: 63px;
    position: absolute;
    border-radius: 50% 50%;
    bottom: 0px;
    right: 0px;
    /*border: 5px solid #176391;*/
    /* opacity: 0.3; */
    opacity: 1;
    transition: all 0.4s;
}
.floatingButton .fas {
    font-size: 20px !important;
}
.floatingButton.open,
.floatingButton:hover,
.floatingButton:focus,
.floatingButton:active {
    opacity: 1;
    color: #fff;
    box-shadow: 0px 15px 20px rgba(23, 98, 145, 0.4);
    transform: translateY(-7px);
}
.floatingButton .fas {
    transform: rotate(0deg);
    transition: all 0.4s;
}
.floatingButton.open .fas {
    transform: rotate(270deg);
}
</style>
<div class="floatingButtonWrap">
    <div class="floatingButtonInner">
        <a href="#" class="floatingButton" data-toggle="modal" data-target="#customer_form">
            <i class="fas fa fa-plus icon-default"></i>
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
    $(document).on("change", "#customer_gst", function (event) {
        let regTest = /[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9A-Za-z]{1}[Z]{1}[0-9a-zA-Z]{1}/.test($(this).val());
        if(regTest){
            let a=65,b=55,c=36;
            return Array['from'](g).reduce((i,j,k,g)=>{
               p=(p=(j.charCodeAt(0)<a?parseInt(j):j.charCodeAt(0)-b)*(k%2+1))>c?1+(p-c):p;
               return k<14?i+p:j==((c=(c-(i%c)))<10?c:String.fromCharCode(c+b));
            },0);
        }
        alert(regTest);
    });
    $(document).on("click", "#customer_submit", function (event) {
        var $form = $('.customer_form_submit');
        if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
            event.preventDefault();
            alert("Fill Mandatory fields !!");
            return false;
        }else{
            swal({
                title: "Alert: You have not added Customer GSTIN",
                text: "Do you want to proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#E84848',
                confirmButtonText: 'Yes, Proceed!',
                closeOnConfirm: true,
            },
            function(){
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
                                    $('#cust_oldcontact').val(customer.customer_contact);
                                    $('#cust_fname').val(customer.customer_fname);
                                    $('#cust_lname').val(customer.customer_lname);
                                    $('#gst_no').val(customer.customer_gst);
                                    $('#cust_pincode').val(customer.customer_pincode);
                                    $('#cust_idstate').val(customer.idstate);
                                    $('#cust_state').val(customer.customer_state);
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
                                $('#address').val('');
                                $('#cust_oldcontact').val('');
                                alert(data.msg);
                                return false;
                            }
                        }
                    });
                }
            });
        }
    });
});
</script>
<div class="modal fade" id="customer_form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a href="" class="popup-close" data-dismiss="modal" aria-label="Close">x</a>
                <h4 class="modal-title text-center"><span class="fa fa-handshake-o" style="font-size: 28px"></span> Create Customer
                    <a href="<?php echo base_url('Sale/customer_list') ?>" target="_blank" class="btn btn-warning btn-floating waves-effect pull-right" style="line-height: 10px"><i class="mdi mdi-table fa-lg"></i></a>
                </h4>
            </div>
            <div class="modal-body">
                <form class="customer_form_submit">
                    <label class="col-md-3">Name<span class="red-text">*</span></label>
                    <div class="col-md-9" style="padding: 0">
                        <div class="col-md-6">
                            <input type="hidden" id="customer_latitude" name="customer_latitude" />
                            <input type="hidden" id="customer_longitude" name="customer_longitude" />
                            <input type="hidden" name="iduser" value="<?php echo $_SESSION['id_users'] ?>" />
                            <input type="hidden" name="idbranch" value="<?php echo $_SESSION['idbranch'] ?>" />
                            <input type="text" class="form-control input-sm required" placeholder="First Name" name="customer_fname" required=""/>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control input-sm required" placeholder="Last Name" name="customer_lname" required=""/>
                        </div>
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Contact<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm required" id="en_customer_contact" placeholder="Customer Contact" name="customer_contact" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Email Id</label>
                    <div class="col-md-9">
                        <input type="email" class="form-control input-sm" placeholder="Customer Email Id" name="email_id"/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">GSTIN</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm" placeholder="Customer GSTIN" name="customer_gst" id="customer_gst" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Address<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm required" placeholder="Customer Address" name="customer_address" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Pincode<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm required" placeholder="Customer Pincode" name="customer_pincode" id="customer_pincode" required="" pattern="^[0-9]{6}$" />
                    </div>
                    <!--<img class="img" width="50" src="<?php // echo base_url('assets/images/loader.gif') ?>" style="display: none" />-->
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">City<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm required" placeholder="Customer City" name="customer_city" id="customer_city" required="" />
                        <!--<input type="text" class="form-control input-sm required" placeholder="Customer City" name="customer_city" id="customer_city" required="" onfocus="blur()" />-->
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">District<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control input-sm required" placeholder="Customer Disctrict" name="customer_district" id="customer_district" required="" />
                        <!--<input type="text" class="form-control input-sm required" placeholder="Customer Disctrict" name="customer_district" id="customer_district" required="" onfocus="blur()" />-->
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">State<span class="red-text">*</span></label>
                    <div class="col-md-9">
                        <!--<input type="text" class="form-control input-sm required" placeholder="Customer State" name="customer_state" id="customer_state" required="" />-->
                        <select class="form-control input-sm required" placeholder="Customer State" name="customer_state" id="customer_state" required="">
                            <?php foreach ($state_data as $state){ ?>
                            <option><?php echo $state->state_name ?></option>
                            <?php } ?>
                        </select>
                        <!--<input type="text" class="form-control input-sm required" placeholder="Customer State" name="customer_state" id="customer_state" required="" onfocus="blur()" />-->
                    </div><div class="clearfix"></div><hr>
                    <a class="pull-left btn btn-warning waves-effect waves-orange gradient1" data-dismiss="modal">Close</a>
                    <a id="customer_submit" class="btn btn-info pull-right waves-effect gradient2"> Save</a><div class="clearfix"></div>
                </form>
            </div>
        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- modal -->
<script>
$(document).ready(function(){
    $('.floatingButton').on('click',
        function(e){
            e.preventDefault();
            $(this).toggleClass('open');
            if($(this).children('.fas').hasClass('fa-plus'))
            {
                $(this).children('.fas').removeClass('fa-plus');
                $(this).children('.fas').addClass('fa-close');
                $(this).children('.fas').addClass('');
            } 
            else if ($(this).children('.fas').hasClass('fa-close')) 
            {
                $(this).children('.fas').removeClass('fa-close');
                $(this).children('.fas').addClass('fa-plus');
            }
//            $('.floatingMenu').stop().slideToggle();
        }
    );
    $(this).on('click', function(e) {
        var container = $(".floatingButton");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && $('.floatingButtonWrap').has(e.target).length === 0) 
        {
            if(container.hasClass('open'))
            {
                container.removeClass('open');
            }
            if (container.children('.fas').hasClass('fa-close')) 
            {
                container.children('.fas').removeClass('fa-close');
                container.children('.fas').addClass('fa-plus');
            }
//            $('.floatingMenu').hide();
        }
    });
});
</script>