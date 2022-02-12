<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Branch </h3></center><div class="clearfix"></div><hr>
<script>
    $(document).ajaxStart(function () {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function () {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    $(document).ready(function () {
        $(document).on("change", "#pincode", function (event) {
            var par = $(this).parent().parent();
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
                            $(par).find('#state').val(post.State);
                            $(par).find('#district').val(post.District);
                            $(par).find('#city').val(post.Block);
                        }else{
                            alert ('Invalid Pincode');
                        }
                    });
                }
            });
        });
        
        $(document).on("click", ".edit-btn", function (event) {           
            var id = $(this).attr('val');
            $(".edit").show();
                $.ajax({
                        url: "<?php echo base_url() ?>Master/ajax_branch_details/"+id,
                        method: "POST",                        
                        success: function (data)
                        {
                            $('.edit').html(data);
                            $('#form-edit').collapse('show');
                            $('#pay').collapse('hide');
                            jQuery(".chosen-select").chosen({ search_contains: true });
                             $("html, body").animate({scrollTop: 0}, 100);
                        }
                    });
        });
         $(document).on("click", ".close-edit", function (event) {
            $('#form-edit').collapse('hide');
            $('.edit').html("");
             $("html, body").animate({scrollTop: 0}, 100);
        });
        
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
    <div id="purchase" style="padding: 20px 10px; margin: 0">
        <div class="edit" style="display:none"></div>
        <form id="pay" class="collapse">
            <div class="col-md-8 thumbnail col-md-offset-2">
                <div class="col-md-10 col-md-offset-1">                
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Branch</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Branch Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Branch Name" name="branch" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3 col-md-offset-1">Branch Code</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Branch Code" name="branch_code" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Branch GSTIN</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Branch GSTIN" name="branch_gstno" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Address</label>
                    <div class="col-md-7">
                        <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required=""></textarea>
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
                    <label class="col-md-3 col-md-offset-1">Branch Email</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="email" placeholder="Enter branch email" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Contact Person</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person Name" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Contact</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="contact" placeholder="Enter Contact" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Latitude </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="latitude" placeholder="Enter map latitude " required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Longitude</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="longitude" placeholder="Enter map longitude" required="" />
                    </div><div class="clearfix"></div><br>                                        
                    <label class="col-md-3 col-md-offset-1">Apple Store Id</label>
                    <div class="col-md-7">
                       <input type="text" class="form-control" name="apple_id" placeholder="Enter apple Store Id" required="" />
                    </div><div class="clearfix"></div><br>                                        
                    <label class="col-md-3 col-md-offset-1">Bfl Id</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="bfl_id" placeholder="Enter bfl Id" required="" />
                    </div><div class="clearfix"></div><br>      
                     <label class="col-md-3 col-md-offset-1">Partner Type</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="partnertype" id="partnertype" required="">
                            <option value="">Select Partner Type</option>
                            <?php foreach ($partner_type_data as $ptype) { ?>
                                <option value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Company</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="company" id="company" required="">
                            <option value="">Select Company</option>
                            <?php foreach ($comapny_data as $comapny) { ?>
                                <option value="<?php echo $comapny->company_id; ?>"><?php echo $comapny->company_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                     <label class="col-md-3 col-md-offset-1">Print Head </label>
                    <div class="col-md-7">
                        <select class=" form-control" name="print_head" id="print_head" required="">
                            <option value="">Select Print Head</option>
                            <?php foreach ($print_head_data as $printhead) { ?>
                                <option value="<?php echo $printhead->id_print_head; ?>"><?php echo $printhead->company_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Warehouse</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="warehouse" id="warehouse" required="">
                            <option value="">Select Warehouse</option>
                            <?php foreach ($warehouse_data as $warehouse) { ?>
                                <option value="<?php echo $warehouse->id_branch; ?>"><?php echo $warehouse->branch_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Zone</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="zone" id="zone" required="">
                            <option value="">Select Zone</option>
                            <?php foreach ($zone_data as $zone) { ?>
                                <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Route</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="route" id="route" required="">
                            <option value="">Select Route</option>
                            <?php foreach ($route_data as $route) { ?>
                                <option value="<?php echo $route->id_route; ?>"><?php echo $route->route_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Branch Category</label>
                    <div class="col-md-7">
                        <select class=" form-control" name="branch_category" id="branch_category" required="">
                            <option value="">Select Category</option>
                            <?php foreach ($branch_category_data as $branch_category) { ?>
                                <option value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Purchase Direct Inward</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="direct_inward">
                            <option value="1">Allow</option>
                            <option value="0">Dis-Allow</option>
                        </select>
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Accessories ID</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" name="acc_branch_id" placeholder="Enter Accessories Branch Id" />
                    </div><div class="clearfix"></div><br>  
                    <label class="col-md-3 col-md-offset-1">Hrms ID</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" name="hrms_branch_id" placeholder="Enter HRMS Branch Id" />
                    </div><div class="clearfix"></div><br> 
                    <div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" name="id" formmethod="POST" formaction="<?php echo base_url('Master/save_branch_details') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div><hr>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="branch_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
            <th>Branch ID</th>
            <th>Branch</th>
            <th>Code</th>
            <th>Address</th>
            <th>Pincode</th>
            <th>Manager</th>
            <th>Contact</th>
            <th>Zone</th>
            <th>Branch Category</th>
            <th>Partner Type</th>
            <th>Status</th>
            <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($branch_data as $branch) { ?>
                    <tr>
                        <td><?php echo $branch->id_branch; ?></td>
                        <td><?php echo $branch->branch_name; ?></td>
                        <td><?php echo $branch->branch_code; ?></td>
                        <td><?php echo $branch->branch_address; ?></td>
                        <td><?php echo $branch->branch_pincode; ?></td>
                        <td><?php echo $branch->branch_contact_person; ?></td>
                        <td><?php echo $branch->branch_contact; ?></td>      
                        <td><?php echo $branch->zone_name; ?></td>                        
                        <td><?php echo $branch->branch_category_name; ?></td>                        
                        <td><?php echo $branch->partner_type; ?></td>  
                        <td><?php if ($branch->active == 1) {
                        echo 'Active';
                    } else {
                        echo 'In Active';
                    } ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect edit-btn" val="<?php echo $branch->id_branch; ?>"   style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__ . '../../footer.php'; ?>