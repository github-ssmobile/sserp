<?php include __DIR__ . '../../header.php'; ?>

<script>
    $(document).ajaxStart(function () {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function () {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    $(document).ready(function () {
        $(document).on("change", "#pincode", function (event) {
            var par=$(this).parent().parent();
            var pincode = $(this).val();
            $.ajax({
                url: "https://api.postalpincode.in/pincode/" + pincode,
                success: function (data)
                {
                    $(data).each(function (index, item) {
                        var result = item.Status;
                        if (result == 'Success') {
                            var postoffice = item.PostOffice;
                            var post = postoffice[postoffice.length - 1];
                            $(par).find('#state').val(post.Circle);
                            $(par).find('#district').val(post.District);
                            $(par).find('#city').val(post.Block);
                        } else {
                            alert('Invalid Pincode');
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

<center><h3 style="margin-top: 0"><span class="mdi mdi-home-modern fa-lg"></span> Company</h3></center>    
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <?php echo form_open_multipart('Master/save_company_details', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-6 thumbnail col-md-offset-3">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Company</h4></center><hr>

            <div class="col-md-10 col-md-offset-1">
                <label class="col-md-3 col-md-offset-1">Company Name</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="company" placeholder="Enter company name" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Address</label>
                <div class="col-md-7">
                    <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required="" ></textarea>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">GST</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="gst" id="gst" placeholder="Enter GST" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Pincode</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="pincode"  class="pincode"  name="pincode" placeholder="Enter Pincode" />
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
                <label class="col-md-3 col-md-offset-1">Status</label>
                <div class="col-md-7">
                    <select class="select form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div><div class="clearfix"></div><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" class="pull-right btn btn-info waves-effect">Save</button>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('brand_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="brand_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
            <th>Sr</th>
            <th>Name</th>
            <th>Address</th>
            <th>State</th>
            <th>GSTIN</th>
            <th>Status</th>
            <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($company_data as $company) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $company->company_name; ?></td>
                        <td><?php echo $company->company_address; ?></td>
                        <td><?php echo $company->company_state_name; ?></td>
                        <td><?php echo $company->company_gstin; ?></td>

                        <td><?php if ($company->active == 1) {
                        echo 'Active';
                    } else {
                        echo 'In Active';
                    } ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <?php echo form_open_multipart('Master/edit_comapany') ?>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Company</h4></center><hr>

                                                <div class="col-md-10 col-md-offset-1">
                                                    <label class="col-md-3 col-md-offset-1">Company Name</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="company" value="<?php echo $company->company_name; ?>" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Address</label>
                                                    <div class="col-md-7">
                                                        <textarea type="text" class="form-control" name="address"  required="" ><?php echo $company->company_address; ?></textarea>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">GST</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="gst" id="gst" value="<?php echo $company->company_gstin; ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Pincode</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" id="pincode" class="pincode" name="pincode" value="<?php echo $company->company_pincode; ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">State</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" id="state" readonly="" name="state" value="<?php echo $company->company_state_name; ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">District</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" id="district" readonly="" name="district" value="<?php echo $company->company_district; ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">City</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" id="city" readonly="" name="city" value="<?php echo $company->company_city; ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Status</label>
                                                    <div class="col-md-7">
                                                        <select class="select form-control" name="status">
                                                            <option value="<?php echo $company->active ?>"><?php if ($company->active == 1) {
                                                                echo 'Active';
                                                            } elseif ($company->active == 0) {
                                                                echo 'In Active';
                                                            } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><hr>

                                                    <div class="clearfix"></div>


                                                    <div class="clearfix"></div>
                                                </div>
                                                <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $company->company_id ?>" name="id"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                            </div></div>
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
<?php include __DIR__ . '../../footer.php'; ?>