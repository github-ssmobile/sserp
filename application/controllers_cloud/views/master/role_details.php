<?php include __DIR__.'../../header.php'; ?>
    <div class="col-md-8 col-md-offset-1 col-sm-10"><center><h3><span class="mdi mdi-account-edit fa-lg"></span> User Role </h3></center></div>
    <div class="col-md-2 col-sm-2">
        <a class="arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category" style="margin-bottom: 2px"></a>
    </div><div class="clearfix"></div><hr>
    <form id="pay" class="collapse">
        <div class="col-md-5"><br><br><br>
            <div class="thumbnail" style="border: none">
                <img src="<?php echo base_url('assets/images/roles.webp') ?>" style="width: 100%">
            </div>
        </div>
        <div class="col-md-6 col-md-offset-1">
            <div class="thumbnail">
                <center><h4><span class="mdi mdi-account-edit fa-lg"></span> Create User Role</h4></center><hr>
                <label class="col-md-3">Role</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" placeholder="Enter role" name="role" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3">Role Type</label>
                <div class="col-md-9">
                    <select name="role_type" required="" class="form-control">
                        <option value="0">All</option>
                        <option value="1">Warehouse</option>
                        <option value="2"> branch</option>
                    </select>
                    <!--<input type="number" class="form-control" name="level" placeholder="Enter Level" required="" min="1" max="3" />-->
                </div>
                <div class="clearfix"></div><br>
                <label class="col-md-3">Level</label>
                <div class="col-md-9">
                    <select name="level" required="" class="form-control">
                        <option value="">Select Level</option>
                        <option value="1">Access of All Branches</option>
                        <option value="2">Single branch</option>
                        <option value="3">multiple mapped branches</option>
                    </select>
                    <!--<input type="number" class="form-control" name="level" placeholder="Enter Level" required="" min="1" max="3" />-->
                </div>
                <div class="clearfix"></div><br>
                <label class="col-md-3">Home URL</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="home" placeholder="Enter Home URL" required="" />
                </div><div class="clearfix"></div><br>
                <center><b>Mapping Config</b></center>
                <div class="">
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="haswarehouse" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Warehouse</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="haswarehouse" name="haswarehouse" type="checkbox"/> 
                                    <label for="haswarehouse" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="hasbranch" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Branch</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="hasbranch" name="hasbranch" type="checkbox"/> 
                                    <label for="hasbranch" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div><div class="clearfix"></div>
                    
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="haspcategory" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Category</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="haspcategory" name="haspcategory" type="checkbox"/> 
                                    <label for="haspcategory" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="hasbrand" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Brand</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="hasbrand" name="hasbrand" type="checkbox"/> 
                                    <label for="hasbrand" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="haswallet" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Wallet</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="haswallet" name="haswallet" type="checkbox"/> 
                                    <label for="haswallet" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="has_paymentmode" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Payment Mode</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="has_paymentmode" name="has_paymentmode" type="checkbox"/> 
                                    <label for="has_paymentmode" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                     <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="has_expense_wallet" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Expense Wallet</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="has_expense_wallet" name="has_expense_wallet" type="checkbox"/> 
                                    <label for="has_expense_wallet" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-6 col-sm-6" style="padding: 5px">
                        <label for="has_costing_header" class="thumbnail" style="font-weight: normal">
                            <div class="col-md-9">Has Costing Headers</div>
                            <div class="col-md-3">
                                <div class="material-switch" style="margin-top: -5px">
                                    <input id="has_costing_header" name="has_costing_header" type="checkbox"/> 
                                    <label for="has_costing_header" class="label-primary"></label> 
                                </div>
                            </div><div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="clearfix"></div><hr>
                    
<!--                    <div class="col-md-4 col-sm-6">
                        Has Warehouse
                        <div class="material-switch">                                            
                            <input id="haswarehouse" name="haswarehouse" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="haswarehouse" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">Has Branch
                        <div class="material-switch">                                            
                            <input id="hasbranch" name="hasbranch" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp;<label for="hasbranch" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">Has Category
                        <div class="material-switch">                                            
                            <input id="haspcategory" name="haspcategory" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="haspcategory" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">Has Brand
                        <div class="material-switch">                                            
                            <input id="hasbrand" name="hasbrand" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp;<label for="hasbrand" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">Has Wallet
                        <div class="material-switch">                                            
                            <input id="haswallet" name="haswallet" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp;<label for="haswallet" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">Has Payment Mode
                        <div class="material-switch">                                            
                            <input id="hasmode" name="hasmode" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp;<label for="hasmode" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div>
                </div><div class="clearfix"></div><hr>-->
                
                <div class="col-md-10">
                <button class="btn btn-warning waves-effect gradient1" data-toggle="collapse" data-target="#pay">Cancel</button>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_role') ?>" class="pull-right gradient2 btn btn-info waves-effect">Save</button>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div><hr>
    </form>
    <div class="thumbnail" style="overflow: auto;">
         <table id="user_data" class=" table table-condensed table-bordered table-hover">
            <thead>
                <th>Sr</th>
                <th>Role Profile</th>
                <th>Level</th>  
                <th>Role Type</th>  
                <th>Home Url</th>  
                <th class="col-md-2 text-center">Role Setup</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($user_role as $user){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $user->role; ?></td>
                    <td><?php if($user->level==1){ echo "Admin"; }elseif($user->level==2){ echo "Branch"; }elseif($user->level==3){ echo "MultiBranch"; } ?></td>
                    <td><?php if($user->role_type==0){ echo "All"; }elseif($user->role_type==1){ echo "Warehouse"; }elseif($user->role_type==2){ echo "Branch"; } ?></td>
                    <td><?php echo $user->home; ?></td>
                    <td class="text-center">
                        <a class="thumbnail btn-sm btn-link waves-effect" style="margin-bottom: 0px !important;" href="<?php echo base_url('Master/role_has_menu/'.$user->id_userrole) ?>" >
                            <i class="mdi mdi-account-settings-variant text-primary fa-lg"></i>
                        </a>
                    </td>            
                    <td>
                        <a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-primary fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form>
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Role</h4></center><hr>
                                        <label class="col-md-3 col-md-offset-1">Role</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $user->role; ?>" name="role" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Role Type</label>
                                        <div class="col-md-7">
                                            <select name="role_type" required="" class="form-control">
                                                <option value="<?php echo $user->role_type ?>"><?php if($user->role_type == 0){ echo 'All'; }elseif ($user->role_type == 1){ echo 'Warehouse'; } elseif ( $user->role_type == 2){ echo 'Branch'; }?></option>
                                                <option value="0">All</option>
                                                <option value="1">Warehouse</option>
                                                <option value="2"> branch</option>
                                            </select>
                                        </div>
                                        <div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Level</label>
                                        <div class="col-md-7">
                                             <select name="level" required="" class="form-control">
                                                <option value="<?php echo $user->level; ?>"><?php if($user->level == 1){ echo ' Access of All Branches'; }elseif($user->level == 2){ echo ' Single branch'; } elseif ($user->level == 3) { echo ' multiple mapped branches'; }?></option>
                                                <option value="1">Access of All Branches</option>
                                                <option value="2">Single branch</option>
                                                <option value="3">multiple mapped branches</option>
                                            </select>
                                            <!--<input type="text" class="form-control" value="<?php echo $user->level; ?>" name="level" />-->
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Url</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $user->home; ?>" name="homeurl" />
                                        </div><div class="clearfix"></div><hr>
                                        <center><h5><span class="pe pe-7s-news-paper" style="font-size: 18px"></span> Mapping Config</h5></center><br>
                                         <div class="col-md-10 col-md-offset-1">
                                            <label class="col-md-4">Has Warehouse</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">  
                                                    <?php $checked="";
                                                        if($user->has_warehouse==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>haswarehouse" name="haswarehouse" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>haswarehouse" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <label class="col-md-4">Has Branch</label>
                                            <div class="col-md-2">
                                                <div class="material-switch"> 
                                                    <?php $checked="";
                                                        if($user->has_branch==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>hasbranch" name="hasbranch" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>hasbranch" class="label-primary"></label> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div><br>
                                        <div class="col-md-10 col-md-offset-1">
                                            <label class="col-md-4">Has Category</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">  
                                                    <?php $checked="";
                                                        if($user->has_product_category==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>haspcategory" name="haspcategory" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>haspcategory" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <label class="col-md-4">Has Brand</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">   
                                                    <?php $checked="";
                                                        if($user->has_brand==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>hasbrand" name="hasbrand" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>hasbrand" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <div class="clearfix"></div><br>
                                            <label class="col-md-4">Has Wallet</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">   
                                                    <?php $checked="";
                                                        if($user->has_wallet==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>haswallet" name="haswallet" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>haswallet" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <label class="col-md-4">Has Payment Mode</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">   
                                                    <?php $checked="";
                                                        if($user->has_paymentmode==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>has_paymentmode" name="has_paymentmode" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>has_paymentmode" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <label class="col-md-4">Has Expense Wallet</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">   
                                                    <?php $checked="";
                                                        if($user->has_expense_wallet==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>has_expense_wallet" name="has_expense_wallet" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>has_expense_wallet" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <label class="col-md-4">Has Costing Headers</label>
                                            <div class="col-md-2">
                                                <div class="material-switch">   
                                                    <?php $checked="";
                                                        if($user->has_costing_header==1){
                                                            $checked="checked";
                                                        } ?> 
                                                    <input id="<?php echo $i; ?>has_costing_header" name="has_costing_header" type="checkbox" <?php echo $checked; ?> /> 
                                                    <label for="<?php echo $i; ?>has_costing_header" class="label-primary"></label> 
                                                </div>
                                            </div>
                                            <div class="clearfix"></div><br>
                                        </div>
                                        <div class="clearfix"></div><br>
                                        
                                    </div>
                                    <div class="clearfix"></div>
                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" value="<?php echo $user->id_userrole  ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_role') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
    </div>
<?php include __DIR__.'../../footer.php'; ?>