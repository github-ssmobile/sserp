<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Warehouse </h3></center><div class="clearfix"></div>
<script>
    $(document).ajaxStart(function () {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function () {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    $(document).ready(function () {

        $(document).on("change", "#pincode", function (event) {
            par = $(this).parent().parent();
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
        
        $(document).on("click", ".edit-btn", function (event) {           
            var id = $(this).attr('val');
            $(".edit").show();
                $.ajax({
                        url: "<?php echo base_url() ?>Master/ajax_warehouse_details/"+id,
                        method: "POST",                        
                        success: function (data)
                        {
                            $('.edit').html(data);
                            $('#form-edit').collapse('show');
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
                <div class="">
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Warehouse</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Warehouse Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Warehouse Name" name="branch" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3 col-md-offset-1">Warehouse Code</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Warehouse Code" name="branch_code" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3 col-md-offset-1">Address</label>
                    <div class="col-md-7">
                        <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required="" ></textarea>
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
                    <label class="col-md-3 col-md-offset-1">PO Approval</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="po_approval">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" name="id" formmethod="POST" formaction="<?php echo base_url('Master/save_warehouse_details') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>

                </div><div class="clearfix"></div>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('warehouse_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="warehouse_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
            <th>Sr</th>
            <th>Branch</th>
            <th>Address</th>
            <th>Pincode</th>
            <th>Manager</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($branch_data as $branch) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $branch->branch_name; ?></td>
                        <td><?php echo $branch->branch_address; ?></td>
                        <td><?php echo $branch->branch_pincode; ?></td>
                        <td><?php echo $branch->branch_contact_person; ?></td>
                        <td><?php echo $branch->branch_contact; ?></td>                        
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