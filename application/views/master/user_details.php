<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<script>
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
        $('#idrole').change(function (){
            var idrole = $('#idrole').val();
            if(idrole != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Master/ajax_get_user_data_byidrole",
                    method:"POST",
                    data:{idrole : idrole},
                    success:function(data){
                        $('#userdata').html(data);
                    }
                });
            }else{
               alert("Select User Role");
               return false;
           }
       });
    });
</script>
<center><h3><span class="mdi mdi-account-circle fa-lg"></span> User Details</h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">
            <div class="col-md-5">
                <div class="thumbnail" style="border: none">
                    <img src="<?php echo base_url() ?>assets/images/emp.png" style="width: 100%" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="thumbnail">
                    
                    <center><h4><span class="mdi mdi-account-edit" style="font-size: 28px"></span> Add User</h4></center><hr>
             
                    <label class="col-md-4">User Role</label>
                    <div class="col-md-8">
                        <select class="select form-control" name="role" id="role" required="">
                            <?php if($this->session->userdata('level') == 2){ ?> 
                                <option value="">Select Role</option>
                                <?php foreach($user_role as $role){ if($role->id_userrole == 17){ ?>
                                    <option value="<?php echo $role->id_userrole ?>" level="<?php echo $role->level ?>"><?php echo $role->role ?></option>
                                <?php }} ?>
                            <?php }else{ ?>
                                <option value="">Select Role</option>
                                <?php foreach($user_role as $role){ if($role->id_userrole != 9){ ?>
                                    <option value="<?php echo $role->id_userrole ?>" level="<?php echo $role->level ?>"><?php echo $role->role ?></option>
                                <?php }} ?>
                            <?php } ?>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <div id="mapping"></div>
                    <label class="col-md-4">User Name</label>
                    <div class="col-md-8">
                        <input type="hidden" name="id_users" value="<?php echo $_SESSION['id_users'] ?>" />
                        <input type="text" class="form-control" name="full_name" placeholder="Enter User Name" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">User Id</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="name" placeholder="Enter User Id" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Password</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="password" placeholder="Enter Password" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Contact</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="contact" placeholder="Enter Contact" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Email Id</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="email" placeholder="Enter Email Id" />
                        <input type="hidden" name="level" id="level" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Status</label>
                    <div class="col-md-8">
                        <select class="select form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect gradient1" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_user') ?>" class="pull-right btn btn-info waves-effect gradient2">Save</button>
                    <div class="clearfix"></div>
                </div>
            </div><div class="clearfix"></div><hr>
        </form>
        <div class="col-md-3">
            <select class="form-control input-sm" id="idrole" name="idrole">
                <option value="">Select Role</option>

                <?php 
                if($this->session->userdata('idrole') == 30){ 
                    foreach($user_role as $urole){
                        if($urole->id_userrole == 17){
                         ?>
                         <option value="<?php echo $urole->id_userrole ?>"><?php echo $urole->role?></option>
                     <?php }
                 }
             }else{  
                foreach($user_role as $urole){ ?>
                    <option value="<?php echo $urole->id_userrole ?>"><?php echo $urole->role?></option> 
                <?php  }
            }
            ?>
        </select>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-3">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('user_data');"><span class="fa fa-file-excel-o"></span> Excel</button>
    </div>
    <?php if($this->session->userdata('idrole') != 30){ ?> <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
<?php } ?>
    <div class="clearfix"></div>
    <div id="userdata"></div>
<!--        <table id="user_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>User Name</th>
                <th>User Id</th>
                <th>Branch</th>
                <th>User Role</th>
                <th>User Contact</th>
                <th>Password</th>
                <th>Level<br><small>1=admin, 2=idbranch, 3=multiple Branches</small></th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($user_data as $user){  
                    if($this->session->userdata('level') == 2){ 
                        if($user->iduserrole == 17 && $user->idbranch == $_SESSION['idbranch']) { ?>
                            <tr>
                                <td><?php echo $i++;?></td>
                                <td><?php echo $user->user_name; ?></td>
                                <td><?php echo $user->userid; ?></td>
                                <td><?php if($user->level==1){ echo 'All Branches'; }elseif($user->level==3){ echo 'Multi Branches'; }else{ echo $user->branch_name; } ?></td>
                                <td><?php echo $user->role; ?></td>
                                <td><?php echo $user->user_contact; ?></td>
                                <td><?php echo $user->user_password; ?></td>
                                <td><?php echo $user->level; ?></td>
                                <td><?php  if($user->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                                <td>
                                    <a class="thumbnail btn-link waves-effect" href="<?php echo base_url()?>Master/edit_user_details/<?php echo $user->id_users;?>"  style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
                                    <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
                                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form>
                                                <div class="modal-body">
                                                    <div class="thumbnail">
                                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit User</h4></center><hr>
                                                        <label class="col-md-4">Branch</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" disabled="" value="<?php echo $user->branch_name ?>" />
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">User Role</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" disabled="" value="<?php echo $user->role ?>" />
                                                        </div><div class="clearfix"></div><br>
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
                                                        <label class="col-md-4">Status</label>
                                                        <div class="col-md-8">
                                                            <select class="select form-control" name="status">
                                                                <option value="<?php echo $user->active ?>"><?php if($user->active == 1){ echo 'Active'; } elseif($user->active == 0){ echo 'In Active'; } ?></option>
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                        </div><div class="clearfix"></div><hr>
                                                        <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                    <button type="submit" value="<?php echo $user->id_users  ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_user') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                                    </div>

                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } }else{ ?>
                            <tr>
                                <td><?php echo $i++;?></td>
                                <td><?php echo $user->user_name; ?></td>
                                <td><?php echo $user->userid; ?></td>
                                <td><?php if($user->level==1){ echo 'All Branches'; }elseif($user->level==3){ echo 'Multi Branches'; }else{ echo $user->branch_name; } ?></td>
                                <td><?php echo $user->role; ?></td>
                                <td><?php echo $user->user_contact; ?></td>
                                <td><?php echo $user->user_password; ?></td>
                                <td><?php echo $user->level; ?></td>
                                <td><?php  if($user->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                                <td>
                                    <a class="thumbnail btn-link waves-effect" href="<?php echo base_url()?>Master/edit_user_details/<?php echo $user->id_users;?>"  style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
                                    <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
                                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form>
                                            <div class="modal-body">
                                                <div class="thumbnail">
                                                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit User</h4></center><hr>
                                                    <label class="col-md-4">Branch</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" disabled="" value="<?php echo $user->branch_name ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">User Role</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" disabled="" value="<?php echo $user->role ?>" />
                                                    </div><div class="clearfix"></div><br>
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
                                                    <label class="col-md-4">Status</label>
                                                    <div class="col-md-8">
                                                        <select class="select form-control" name="status">
                                                            <option value="<?php echo $user->active ?>"><?php if($user->active == 1){ echo 'Active'; } elseif($user->active == 0){ echo 'In Active'; } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><hr>
                                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $user->id_users  ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_user') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                                </div>

                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                </td>
                            </tr>
                <?php } } ?>
            </tbody>
        </table>-->
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>