<?php include __DIR__.'../../header.php'; ?>
<style>
    .greytext{
        color: #dddfeb;
    }
    .greytext:hover{
        transform: scale(1.3);
    }
    .box{
        box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
        border: 1px solid #e3e6f0;
        border-left-color: rgb(227, 230, 240);
        border-left-style: solid;
        border-left-width: 1px;
        border-radius: 8px;
        background: #fff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    <div class="col-md-4"><br>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url($_SESSION['dashboard']) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('Master/role_details') ?>">User Role</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Role Has Menu</li>
        </ol>
    </div>
    <div class="col-md-4">
        <center><h3><span class="mdi mdi-account-edit fa-lg"></span> User Role Has Menu</h3></center>
    </div><div class="clearfix"></div>
    
    <div class="" style="padding: 0; margin: 0;">
        <div class="thumbnail" style=" min-height: 550px;">
            <?php foreach ($user_role as $user) { ?>
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Menu to <b><?php echo $user->role ?></b> Role</h4></center><hr>
            <?php break; } ?>
            
            <div class="">
                <form>
                <?php if(count($menu_data) > 0) { ?>
                <div class="thumbnail"><br>
                    <div class="col-md-5"><input type="text" class="filter_1 form-control" id="filter" placeholder="Search anything from table"/></div>
                    <div class="col-md-5"><span class="green-text" id="count_1" ></span></div>                    
                    <div class="col-md-2"><button type="submit" class="btn btn-sm btn-primary" formmethod="POST" formaction="<?php echo base_url('Master/save_userrole_menu') ?>">Submit</button></div>
                    <input type="hidden" name="idrole" value="<?php echo $idrole ?>" />
                    <div class="clearfix"></div><hr>
                    <table class="table table-hover table-responsive">
                        <thead>
                            <th>Menu</th>                            
                            <th>On/Off</th>
                            <th>Menu sequence</th>                            
                            <th>Submenu</th>
                            <th>On/Off</th>    
                            <th>SubMenu sequence</th> 
                            <th>Access</th>
                            <!--<th>Create</th>-->
                            <!--<th>Update</th>-->
                            <!--<th>Delete</th>-->
                            <!--<th>Remove</th>-->
                        </thead>
                        <tbody class="data_1">
                            <?php $count=1; 
                                $old_menu_id=null;
                                foreach ($menu_data as $mb) { 
                                $array_ids= multi_array_search($userrole_has_menu,array('idmenu' => $mb->id_menu,'idsubmenu' => $mb->id_submenu));
                                $barr[] = $mb->id_menu; ?>
                                <tr>
                                    <td>
                                        <i class="<?php echo $mb->font; ?> fa-lg "></i> <label > <?php echo $mb->menu; ?></label>
                                        <input name="model_id[]" value="<?php echo $mb->id_menu; ?>" type="hidden"  > 
                                    </td>
                                    <td>
                                        <?php if($mb->submenu==null) { ?>
                                            <div class="material-switch col-md-2">
                                                <?php $checked="";
                                                if(count($array_ids)>0){
                                                    $checked="checked";
                                                } ?>                                                
                                                <input name="model<?php echo $count ?>" id="mn<?php echo $mb->id_menu; ?>"  type="checkbox" <?php echo $checked; ?>  > 
                                                <label for="mn<?php echo $mb->id_menu; ?>" class="label-primary"></label> 
                                            </div>
                                        <?php }else{ ?>
                                        <input name="model<?php echo $count ?>" type="hidden" value="0"> 
                                        <?php } ?>
                                    </td> 
                                    <td>
                                        <?php 
                                        $menu_sequence="";
                                            if(count($array_ids)>0){
                                                $menu_sequence=$userrole_has_menu[$array_ids[0]]->menu_sequence; 
                                            }
                                        if($old_menu_id==null) { ?>
                                        <input type="text" name="menu_sequence[]" value="<?php echo $menu_sequence; ?>" />
                                        <?php }else if($old_menu_id==$mb->id_menu){
                                            ?>
                                            <input type="text" readonly="" name="menu_sequence[]" value="0" />
                                        <?php }else{ ?>
                                            <input type="text" name="menu_sequence[]" value="<?php echo $menu_sequence; ?>" />
                                        <?php } $old_menu_id=$mb->id_menu;?>
                                    </td> 
                                    <td> 
                                        <?php if($mb->submenu!=null) { ?>
                                        <i class="<?php echo $mb->subfont; ?> fa-lg "></i> <label > <?php echo $mb->submenu; ?></label>     
                                        <input name="submodel_id[]" value="<?php echo $mb->id_submenu; ?>" type="hidden"  > 
                                        <?php }else{ ?>
                                        <input name="submodel_id[]" value="0" type="hidden"  > 
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($mb->submenu!=null) { ?>
                                        <div class="material-switch col-md-2">
                                            <?php $checked="";
                                                if(count($array_ids)>0){
                                                    $checked="checked";
                                                } ?> 
                                            <input name="submodel<?php echo $count ?>" id="sub<?php echo $mb->id_submenu; ?>"  type="checkbox" <?php echo $checked; ?>> 
                                            <label for="sub<?php echo $mb->id_submenu; ?>" class="label-primary"></label> 
                                        </div>
                                        <?php }else{ ?>
                                        <input name="submodel<?php echo $count ?>" type="hidden" value="0"> 
                                        <?php } ?>
                                    </td>
                                    <?php 
                                    $sequence="";$rd=0;$cr=0;$up=0;$dl=0;
                                    if(count($array_ids)>0){
                                        $sequence=$userrole_has_menu[$array_ids[0]]->sequence; 
                                        $rd=$userrole_has_menu[$array_ids[0]]->read_permission;    
                                        $cr=$userrole_has_menu[$array_ids[0]]->create_permission;    
                                        $up=$userrole_has_menu[$array_ids[0]]->update_permission;    
                                        $dl=$userrole_has_menu[$array_ids[0]]->delete_permission; 
                                    } 
                                    if($mb->submenu!=null) { ?>
                                    <td>
                                        <input type="text"  name="submenu_sequence[]" value="<?php echo $sequence; ?>" />
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($rd==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input  id="rd<?php echo $mb->id_submenu ?>" name="read<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="rd<?php echo $mb->id_submenu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($cr==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input id="cr<?php echo $mb->id_submenu ?>" name="create<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="cr<?php echo $mb->id_submenu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($up==1){
                                                    $checked="checked";
                                                } ?>   
                                            
                                            <input id="up<?php echo $mb->id_submenu ?>" name="update<?php echo $count ?>"  value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="up<?php echo $mb->id_submenu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($dl==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input id="dl<?php echo $mb->id_submenu ?>" name="delete<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="dl<?php echo $mb->id_submenu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <?php }else{  ?>                                               
                                    
                                    <td>
                                        <input type="text"  readonly="" name="submenu_sequence[]" value="<?php echo $sequence ?>" />
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($rd==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input id="mrd<?php echo $mb->id_menu ?>" name="read<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="mrd<?php echo $mb->id_menu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($cr==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input id="mcr<?php echo $mb->id_menu ?>" name="create<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="mcr<?php echo $mb->id_menu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($up==1){
                                                    $checked="checked";
                                                } ?> 
                                            
                                            <input id="mup<?php echo $mb->id_menu ?>" name="update<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="mup<?php echo $mb->id_menu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="material-switch">
                                            <?php $checked="";
                                                if($dl==1){
                                                    $checked="checked";
                                                } ?> 
                                            <input id="mdl<?php echo $mb->id_menu ?>" name="delete<?php echo $count ?>" value="1" type="checkbox" <?php echo $checked; ?> /> 
                                            <label for="mdl<?php echo $mb->id_menu ?>" class="label-primary"></label> 
                                        </div>
                                    </td>
                                    <?php 
                                    } ?>
                                    <td>
                                    </td>
                                </tr>
                                <script>
                                    $(document).ready(function(){
                                        $('#sub<?php echo $mb->id_submenu ?>').change(function(){
                                            if($(this).is(":checked")){
                                                $('#rd<?php echo $mb->id_submenu ?>').attr('Checked','Checked'); 
                                            }else{
                                                $('#rd<?php echo $mb->id_submenu ?>').val('0');                                                
                                                $('#rd<?php echo $mb->id_submenu ?>').removeAttr('Checked');
                                                $('#cr<?php echo $mb->id_submenu ?>').removeAttr('Checked');
                                                $('#up<?php echo $mb->id_submenu ?>').removeAttr('Checked');
                                                $('#dl<?php echo $mb->id_submenu ?>').removeAttr('Checked');
                                            }
                                        });
                                        $('#mn<?php echo $mb->id_menu ?>').change(function(){
                                            if($(this).is(":checked")){
                                                $('#mrd<?php echo $mb->id_menu ?>').attr('Checked','Checked'); 
                                            }else{
                                                $('#mrd<?php echo $mb->id_menu ?>').val('0'); 
                                                $('#mrd<?php echo $mb->id_menu ?>').removeAttr('Checked');
                                                $('#mcr<?php echo $mb->id_menu ?>').removeAttr('Checked');
                                                $('#mup<?php echo $mb->id_menu ?>').removeAttr('Checked');
                                                $('#mdl<?php echo $mb->id_menu ?>').removeAttr('Checked');
                                            }
                                        });
                                    });
                                </script>
                            <?php $count++; } ?>
                        </tbody>
                    </table>
                </div>
                <input type="text" name="count" value="" />
                <?php }?>
               </form>
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>