<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-account-network fa-lg"></span> Vendor</h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Create Vendor"></a></div><div class="clearfix"></div><hr>
<script>
    $(document).ready(function(){
        $('#brand').change(function(){
            $('#brands').val($(this).val());
        });
        $('#create_vendor').click(function(){
            if($('#brands').val() == ''){
                alert('Select vendor has brand','warning');
                return false;
            }
        });
    });
    $(document).ajaxStart(function() {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function() {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    $(document).ready(function(){
        $('#pincode').change(function(){
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
                            $('#state').val(post.State);
                            $('#district').val(post.District);
                            $('#city').val(post.Block);
                        }else{
                            alert ('Invalid Pincode');
                        }
                    });
                }
            });
        });
        $('#gpincode').change(function(){
            var gpincode = $(this).val();
            $.ajax({
                url: "https://api.postalpincode.in/pincode/"+gpincode,
                success:function(data)
                {
                    $(data).each(function (index, item) {
                        var result = item.Status;
                        if(result == 'Success'){
                        var postoffice = item.PostOffice;
                        var post = postoffice[postoffice.length-1];
                            $('#gstate').val(post.State);
                            $('#gdistrict').val(post.District);
                            $('#gcity').val(post.Block);
                        }else{
                            alert ('Invalid Pincode');
                        }
                    });
                }
            });
        });
    });
</script>
<div id="purchase">
    <form  class="">
        <div class="col-md-8 col-md-offset-2" >
            <article role="login" class="" style="padding: 15px; border-radius: 10px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Vendor</h4></center><hr>
                <?php foreach ($vendor_data as $vendor){ ?>
                    <label class="col-md-3 col-md-offset-1">Vendor</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="vendor" value="<?php echo $vendor->vendor_name; ?>" placeholder="Enter Vendor Name" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Contact</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="contact" value="<?php echo $vendor->vendor_contact; ?>" placeholder="Enter Contact Number" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Email</label>
                    <div class="col-md-7">
                        <input type="email" class="form-control" name="email" value="<?php echo $vendor->vendor_email; ?>"  placeholder="Enter Email" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">GST</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="gst" id="gst"  value="<?php echo $vendor->vendor_gst; ?>"   placeholder="Enter GST" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Delivery Days</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" name="delivery_days" value="<?php echo $vendor->delivery_days; ?>" placeholder="Enter Delivery Days" />

                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Address</label>
                    <div class="col-md-7">
                        <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required="" ><?php echo $vendor->vendor_address; ?></textarea>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Pincode</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="pincode" name="pincode" value="<?php echo $vendor->pincode; ?>" placeholder="Enter Pincode" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">State</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="state" readonly="" value="<?php echo $vendor->state; ?>" name="state" placeholder="State" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">District</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="district"   readonly="" name="district" placeholder="District" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">City</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="city" readonly="" value="<?php echo $vendor->city; ?>" name="city" placeholder="City" />
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="<?php echo $vendor->active ?>"><?php if($vendor->active == 1){ echo 'Active'; } elseif($vendor->active == 0){ echo 'In Active'; } ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Brands</label>
                    <div class="col-md-7">
                        <select data-placeholder="Select Multiple Brands" multiple class="chosen-select" required="" id="brand" style="min-width: 330px">
                            <?php foreach ($brand_data as $branch){ ?>
                            <option value="<?php echo $branch->id_brand ?>"><?php echo $branch->brand_name ?></option>
                            <?php } ?>
                        </select>
                    </div><div class="clearfix"></div><hr>
                    <input type="hidden" name="brands" id="brands" />
                    <input type="hidden" name="id" value="<?php echo $vendor->id_vendor?>" />
                    <button type="submit" formmethod="POST" id="create_vendor" formaction="<?php echo base_url() ?>Master/edit_vendor" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
                    <div class="clearfix"></div>
                <?php } ?>
            </article><div class="clearfix"></div><br>
        </div>
    </form>
    
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
    <script>
        $('#gst').change(function (event) {
            var regExp = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}/; 
            var txtgst = $(this).val(); 
            if (txtgst.length == 15 ) { 
             if( txtgst.match(regExp) ){ 
           //   alert('PAN match found');
             }
             else {
              alert('GST is Invalid... Correct pattern is - 22AAAAA0000A1Z0');
              document.getElementById('gst').focus();
              event.preventDefault(); 
              return true;
             } 
            } 
            else { 
                  alert('Please enter 15 digits for a valid GST number');
                  event.preventDefault(); 
                  return true;
            } 
        });
    </script>
<?php include __DIR__.'../../footer.php'; ?>