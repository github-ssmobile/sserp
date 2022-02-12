<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<!--<script>
    $(document).ready(function(){
        $('#role').change(function(){
            var role = $(this).val();
            var level_value = $("#role option:selected").attr('level');
            if(role != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Master/ajax_get_role_mappings",
                    method:"POST",
                    data:{role : role},
                    success:function(data){
                        $('#mapping').html(data);
                        $(".chosen-select").chosen({ search_contains: true });
                    }
                });
                $('#level').val(level_value);
            }
        });
    });
</script>-->
<center><h3><span class="mdi mdi-account-circle fa-lg"></span> User Details</h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="padding: 20px 10px; margin: 0">
        <form >
            <?php foreach ($user_data as $user){ ?>
            <div class="col-md-5">
                <div class="thumbnail" >
                    <!--<img src="<?php echo base_url() ?>assets/images/emp.png" style="width: 100%" />-->
                     <center><h4><span class="mdi mdi-account-edit" style="font-size: 28px"></span> Edit User</h4></center><hr>
                    <label class="col-md-4">User Role</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" disabled="" value="<?php echo $user->role ?>" />
                    </div><div class="clearfix"></div><br>
                    <div id="mapping"></div>
                    <label class="col-md-4">User Name</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="full_name" value="<?php echo $user->user_name ?>" placeholder="Enter User Name" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">User Id</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="name" value="<?php echo $user->userid ?>"  placeholder="Enter User Id" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Password</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="password" value="<?php echo $user->user_password ?>" required="" placeholder="Enter Password" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Contact</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="contact" value="<?php echo $user->user_contact ?>"  placeholder="Enter Contact" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Email Id</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="email" value="<?php echo $user->user_email ?>" placeholder="Enter Email Id" />
                        <input type="hidden" name="level" id="level" value="<?php echo $user->level?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Status</label>
                    <div class="col-md-8">
                         <select class="select form-control" name="status">
                            <option value="<?php echo $user->active ?>"><?php if($user->active == 1){ echo ' Active'; } else{ echo ' In Active'; } ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="clearfix"></div><br>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thumbnail">
                    <!---------------------Warehouse ------------------------>
                    <?php if($user->has_warehouse){ ?>
                        <label class="col-md-4">Warehouse</label>
                        <div class="col-md-8">
                            <?php if($user->role_type == 1 ){?>
                                <select data-placeholder="Select Warehouse"  name="idbranch" class="chosen-select" style="min-width: 100%">
                                     <option value="<?php echo $user->idbranch ?>"><?php echo $user->branch_name ?></option>
                                    <?php foreach ($warehouse_data as $warehouse){ ?>
                                    <option value="<?php echo $warehouse->id_branch ?>"><?php echo $warehouse->branch_name ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else{  ?>
                                <select data-placeholder="Select Multiple Warehouse" multiple id="warehouse" class="chosen-select" style="min-width: 100%">
                                    <option value="all">All Warehouse</option>
                                    <?php foreach ($warehouse_data as $warehouse){ ?>
                                    <option value="<?php echo $warehouse->id_branch ?>"><?php echo $warehouse->branch_name ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?> 
                        </div>
                        <div class="clearfix"></div><br>
                       <?php  if($user_warehouse){ ?>
                        <div class="col-md-5"><?php echo $user_warehouse->branch_name ?></div>
                        <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_warehouse/<?php echo $user->id_users ?>/<?php echo $user_warehouse->id_user_has_branch; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <div class="clearfix"></div><hr>
                        <?php }
                    }
                    //---------------------- Branch ----------------------
                    if($user->has_branch){ ?>
                        <label class="col-md-4">Branch</label>
                        <div class="col-md-8">
                        <?php if($user->role_type == 2 ){
                            if($user->level == 2 ){?>
                            <select data-placeholder="Select Branch" class="chosen-select" name="idbranch"  style="min-width: 100%">
                                <option value="<?php echo $user->idbranch ?>"><?php echo $user->branch_name ?></option>
                                <?php foreach ($branch_data as $branch){  ?>
                                    <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <select data-placeholder="Select Multiple Branches" multiple id="branch" class="chosen-select"  style="min-width: 100%">
                                <option value="all">All Branches</option>
                                <?php foreach ($branch_data as $branch){ ?>
                                <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                                <?php } ?>
                            </select>
                        <?php }
                        }else{ ?>
                            <select data-placeholder="Select Multiple Branches" multiple id="branch" class="chosen-select"  style="min-width: 100%">
                                <option value="all">All Branches</option>
                                <?php foreach ($branch_data as $branch){ ?>
                                <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                        </div>
                        <div class="clearfix"></div><br>
                        <?php foreach($user_branch as $userbranch){ ?>
                            <div class="col-md-5"><?php echo $userbranch->branch_name ?></div>
                            <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_warehouse/<?php echo $user->id_users ?>/<?php echo $userbranch->id_user_has_branch; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><br>
                    <?php }
                    
                    //------------------------- Product Category -------------------------
                    
                    if($user->has_product_category){ ?>
                        <label class="col-md-4">Product Category</label>
                        <div class="col-md-8">
                            <?php // if($user->has_warehouse){ ?>
                              <select data-placeholder="Select Multiple Category" multiple id="product_cat" class="chosen-select"  style="min-width: 100%">
                                  <option value="all">All Product Category</option>
                                <?php foreach ($product_category as $category){ ?>
                                    <option value="<?php echo $category->id_product_category ?>"><?php echo $category->product_category_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        
                        <?php foreach($user_product_cat as $upc){ ?>
                            <div class="col-md-5"><?php echo $upc->product_category_name ?></div>
                            <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_product_category/<?php echo $user->id_users ?>/<?php echo $upc->id_user_category; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><hr>
                    <?php } 
                    //--------------------------Brand-----------------------------
                    if($user->has_brand){ ?>
                        <label class="col-md-4">Brand</label>
                        <div class="col-md-8">
                            <select data-placeholder="Select Multiple Brands" multiple id="brand" class="chosen-select"  style="min-width: 100%">
                                <option value="all">All Brands</option>
                                <?php foreach($brand_data as $brand){ ?>
                                <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <?php foreach($user_brand as $ub){ ?>
                            <div class="col-md-5"><?php echo $ub->brand_name ?></div>
                            <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_brand/<?php echo $user->id_users ?>/<?php echo $ub->id_user_has_brand; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><br>
                    <?php } 
                    //-----------------------Payment Mode----------------------
                    if($user->has_paymentmode){ ?>
                        <label class="col-md-4">Payment Mode</label>
                        <div class="col-md-8">
                            <select data-placeholder="Select Multiple Payment Mode" multiple id="mode" class="chosen-select"  style="min-width: 100%">
                                 <option value="all">All Payment Mode</option>
                                <?php foreach($payment_mode as $payment){ ?>
                                <option value="<?php echo $payment->id_paymentmode ?>"><?php echo $payment->payment_mode.' '.$payment->payment_head ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <?php foreach($user_payment_mode as $user_payment){ ?>
                        <div class="col-md-4"><?php echo $user_payment->payment_mode ?></div>
                        <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_payment_mode/<?php echo $user->id_users ?>/<?php echo $user_payment->id_user_has_paymentmode; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><br>
                    <?php } if($user->has_expense_wallet){ ?>
                        <label class="col-md-4">Wallet Type</label>
                        <div class="col-md-8">
                            <select data-placeholder="Select Multiple Wallet Type" multiple id="wallet" class="chosen-select" required="" style="min-width: 100%">
                                <option value="">Select Wallet Type</option>
                                <?php foreach($wallet_data as $wallet){ ?>
                                <option value="<?php echo $wallet->id_wallet_type ?>"><?php echo $wallet->wallet_type ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <?php foreach($user_wallet_type as $wdata){ ?>
                        <div class="col-md-4"><?php echo $wdata->wallet_type ?></div>
                        <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_wallet_type/<?php echo $wdata->idusers ?>/<?php echo $wdata->id_user_has_wallet_type; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><br>
                    <?php } ?>
                        <!--//------------------------Costing Headers------------------> 
                   <?php  if($user->has_costing_header){ ?>
                        <label class="col-md-4">Branch Costing Headersdd</label>
                        <div class="col-md-8">
                            <select data-placeholder="Select Multiple Costing Header" multiple id="costing" class="chosen-select" required="" style="min-width: 100%">
                                <option value="all">All Costing Headers</option>
                                <?php foreach($costing_data as $cdata){ ?>
                                <option value="<?php echo $cdata->id_cost_header ?>"><?php echo $cdata->cost_header_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <?php foreach($user_has_costing_header as $udata){ ?>
                        <div class="col-md-4"><?php echo $udata->cost_header_name ?></div>
                        <div class="col-md-1"><a href="<?php echo base_url()?>Master/delete_user_has_costing_header/<?php echo $udata->iduser ?>/<?php echo $udata->id_user_has_costing_headers; ?>" style="color: red"><span class="fa fa-trash"></span></a></div>
                        <?php } ?>
                        <div class="clearfix"></div><br>
                    <?php } 
                   ?>
                    <input type="hidden" id="warehouses" name="warehouses" />
                    <input type="hidden" id="branches" name="branches" />
                    <input type="hidden" id="brands" name="brands" />
                    <input type="hidden" id="product_cats" name="product_cats" />
                    <input type="hidden" id="payment_modes" name="payment_modes" />
                      <input type="hidden" id="wallet_types" name="wallet_types" />
                      <input type="hidden" id="costing_headers" name="costing_headers" />
                    <script>
                        $(document).ready(function(){
                            $('#warehouse').change(function(){
                                $('#warehouses').val($(this).val());
                            });
                            $('#branch').change(function(){
                                $('#branches').val($(this).val());
                            });
                            $('#product_cat').change(function(){
                                $('#product_cats').val($(this).val());
                            });
                            $('#brand').change(function(){
                                $('#brands').val($(this).val());
                            });
                            $('#mode').change(function(){
                                $('#payment_modes').val($(this).val());
                            });
                            $('#wallet').change(function(){
                                $('#wallet_types').val($(this).val());
                            });
                             $('#costing').change(function(){
                                $('#costing_headers').val($(this).val());
                            });
                        });
                    </script>
                </div>
            </div>
            <div class="clearfix"></div><hr>
            <?php } ?>
            <a class="btn btn-warning waves-effect gradient1" href="<?php echo base_url()?>Master/user_details">Cancel</a>
            <input type="hidden" name="iduser" value="<?php echo $user->id_users?>">
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_edit_user') ?>" class="pull-right btn btn-info waves-effect gradient2">Save</button>
            <div class="clearfix"></div>
        </form>
      
        <div class="col-md-4">
            <div id="count_1" class="text-info"></div>
        </div>
        
        <div class="clearfix"></div>
        
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>