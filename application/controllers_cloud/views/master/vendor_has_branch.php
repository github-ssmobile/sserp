<?php include __DIR__.'../../header.php'; ?>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<div class="" id="add_gift">
    <?php echo form_open(); ?>
        <div class="thumbnail col-md-4 col-md-offset-4">
            <h3><center><i class="mdi mdi-account-circle"></i> Add New Scheme</center></h3><hr>
            <div class="col-md-12">
                <input type="text" class="form-control" name="scheme_name" placeholder="Enter Gift Scheme" required=""/>
            </div><div class="clearfix"></div><br>
            <div class="col-md-6">
                <input type="text" name="scheme_min" class="form-control" placeholder="Price From" required="" />
            </div>
            <div class="col-md-6">
                <input type="text" name="scheme_max" class="form-control"  placeholder="Price To" required="" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-6">
                <input type="text" name="date_from" class="form-control datepicker2" placeholder="Date From" required="" />
            </div>
            <div class="col-md-6">
                <input type="text" name="date_to" class="form-control datepicker2" placeholder="Date To" required="" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-12">
                <input type="text" name="remark" class="form-control" placeholder="Enter Remark" />
            </div><div class="clearfix"></div><br>
<!--            <div class="col-md-12">
                <select data-placeholder="Select Multiple Zones" multiple class="chosen-select" name="zone" id="zone" style="min-width: 410px; border-radius: 2px">
                    <option value=""></option>
                    <?php // foreach ($zone_data as $zone){ ?>
                    <option value="<?php // echo $zone->id ?>"><?php // echo $zone->name ?></option>
                    <?php // } ?>
                </select>
            </div><div class="clearfix"></div><br>-->
            <!--<input type="text" name="zones" id="zones" />-->
            <div class="col-md-12">
                <select data-placeholder="Select Multiple Branches" multiple class="chosen-select" name="branch" id="branch" style="min-width: 410px; border-radius: 2px">
                    <option value=""></option>
                    <option value="0">All</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id ?>"><?php echo $branch->name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
            <div class="col-md-6">
                Scheme For Accessories
                <input type="radio" name="scheme_for" value="0" checked="checked" />
            </div>
            <div class="col-md-6">
                Scheme For Handset
                <input type="radio" name="scheme_for" value="1" />
            </div>
            <input type="hidden" name="branches" id="branches" />
            <div class="clearfix"></div><hr>
            <div class="col-md-12">
                <div class="pull-right">
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url() ?>index.php/master/gift_scheme/add" class="btn btn-success waves-effect waves-red" style="margin: 0"><i class="fav fa fa-plus"></i> Submit</button>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div><br>
        </div>
    <?php echo form_close(); ?>
</div>
<script>
    $(".chosen-select").chosen({
        no_results_text: "Oops, nothing found!"
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-account-network fa-lg"></span> Vendor Has Branches</h3></center></div><div class="clearfix"></div><hr>
<script>
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
                            $('#state').val(post.Circle);
                            $('#district').val(post.District);
                            $('#city').val(post.Block);
                        }else{
                            alert ('Invalid Pincode');
                        }
                    });
                }
            });
        });
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div id="purchase" style=" padding: 20px 10px; margin: 0">
    <form id="pay" class="collapse">
        <div class="col-md-8 col-md-offset-2">
            <article role="login" class="" style="padding: 15px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Vendor</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Vendor</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="vendor" placeholder="Enter Vendor Name" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Contact</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="contact" placeholder="Enter Contact Number" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Email</label>
                <div class="col-md-7">
                    <input type="email" class="form-control" name="email" placeholder="Enter Email" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">GST</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="gst" id="gst" placeholder="Enter GST" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Delivery Days</label>
                <div class="col-md-7">
                    <input type="number" class="form-control" name="delivery_days" placeholder="Enter Delivery Days" />
                    <input type="hidden" class="form-control" name="status" value="1" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Address</label>
                <div class="col-md-7">
                    <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required="" ></textarea>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Pincode</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">State</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="state" readonly="" name="state" placeholder="State" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">District</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="district" readonly="" name="district" placeholder="District" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">City</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="city" readonly="" name="city" placeholder="City" />
                </div><div class="clearfix"></div><br>
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_vendor') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                <div class="clearfix"></div>
            </article><div class="clearfix"></div>
        </div>
    </form>
    <div class="col-md-5">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-4">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('vendor_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div>
    <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
    <div class="clearfix"></div>
    <table id="vendor_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        <thead>
            <th>Sr</th>
            <th>Vendor</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Address</th>
            <th>Pincode</th>
            <th>State</th>
            <th>District</th>
            <th>City</th>
            <th>GST</th>
            <th>Delivery Days</th>
            <th>Status</th>
            <th>Edit</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($vendor_data as $vendor){ ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $vendor->vendor_name; ?></td>
                <td><?php echo $vendor->vendor_contact; ?></td>
                <td><?php echo $vendor->vendor_email; ?></td>
                <td><?php echo $vendor->vendor_address; ?></td>
                <td><?php echo $vendor->pincode; ?></td>
                <td><?php echo $vendor->state; ?></td>
                <td><?php echo $vendor->district; ?></td>
                <td><?php echo $vendor->city ; ?></td>
                <td><?php echo $vendor->vendor_gst ; ?></td>
                <td><?php echo $vendor->delivery_days; ?></td>
                <td><?php if($vendor->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                <td>
                    <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                        <span class="mdi mdi-pen text-danger fa-lg"></span>
                    </a>
                     <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form>    
                                <div class="modal-body">
                                    <div class="thumbnail">
                                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Vendor</h4></center><hr>
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
                                    <a href="#edit<?php echo $i ?>" class="btn btn-warning waves-effect simple-tooltip"  data-target="modal">Cancel</a>
                                    <button type="submit" formmethod="POST" value="<?php echo $vendor->id_vendor  ?>" name="id" formaction="<?php echo base_url('Master/edit_vendor') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                                    <div class="clearfix"></div>    
                                    </div>

                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="col-md-3">
    </div><div class="clearfix"></div>
</div>
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