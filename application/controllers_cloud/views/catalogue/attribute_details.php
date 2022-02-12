<?php include __DIR__.'../../header.php'; ?>
    <center><h3><span class="mdi mdi-steam fa-lg"></span> Attributes</h3></center>
  
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
            <?php echo form_open('Catalogue/save_attribute', array('id' => 'pay','class' => 'collapse' )) ?>            
            <div class="col-md-6 thumbnail col-md-offset-3">                    
                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Attribute</h4></center><hr>
                        <div class="col-md-12">
                        <label class="col-md-3 col-md-offset-1">Product Category</label>
                        <div class="col-md-7">
                            <select class="select form-control" name="idattributetype">
                                <option value="">Select Attribute Type</option>
                                <?php foreach ($attribute_type as $type) { ?>
                                <option value="<?php echo $type->id_attribute_type ?>"><?php echo $type->attribute_type ?></option>
                                <?php } ?>
                            </select>
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Attribute Name</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" pattern="[a-zA-Z0-9\s]+" placeholder="Enter Attribute name" name="attribute_name" required="" />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Status</label>
                        <div class="col-md-7">
                            <select class="select form-control" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div><div class="clearfix"></div><br>  
                        <label class="col-md-3 col-md-offset-1">Has Predefined Values</label>
                        <div class="col-md-7">
                            <input type="checkbox"  name="has_predefined_values">
                        </div>
                        <hr>
                        <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                        <button type="submit" class="pull-right btn btn-info waves-effect">Save</button>
                        <div class="clearfix"></div>
                        </div>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div><hr>
            <?php echo form_close(); ?>
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('attribute');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category" style="margin-bottom: 2px"></a>
            <div class="clearfix"></div>
            <table id="attribute" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Attribute Type</th>
                    <th>Attribute</th>
                    <th>Status</th>
                    <th>Add Values</th>
                    <th>Edit</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    if(count($attributes)>0){
                    $i=1; foreach ($attributes as $attribute){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $attribute->attribute_type; ?></td>
                        <td><?php echo $attribute->attribute_name; ?></td>
                        <td><?php if($attribute->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                        <td class="text-center">
                            <?php if($attribute->has_predefined_values==1){ ?>
                        <a class="thumbnail btn-link waves-effect" style="margin-bottom: 0px !important;" href="<?php echo base_url('Catalogue/attribute_values/'.$attribute->id_attribute) ?>" >
                            <i class="mdi mdi-account-settings-variant text-primary fa-lg"></i>
                        </a>   
                            <?php }else{ echo "-"; } ?>
                        </td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <?php echo form_open('Catalogue/edit_attribute') ?>    
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Attribute</h4></center><hr>
                                        <div class="col-md-12">
                                        <label class="col-md-4 col-md-offset-1">Attribute</label>
                                        <div class="col-md-6">
                                            <input type="text" pattern="[a-zA-Z0-9\s]+" class="form-control" value="<?php echo $attribute->attribute_name; ?>" placeholder="Enter attribute name" name="attribute_name" />                                                                                        
                                            <input type="hidden" value="<?php echo $attribute->id_attribute; ?>" name="id" />
                                        </div><div class="clearfix"></div><br>                                        
                                        <label class="col-md-4 col-md-offset-1">Status</label>
                                        <div class="col-md-6">
                                            <select class="select form-control" name="status">
                                                <option value="<?php echo $attribute->active ?>"><?php if($attribute->active == 1){ echo 'Active'; } elseif($attribute->active == 0){ echo 'In Active'; } ?></option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-4 col-md-offset-1">Has Predefined Values</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" <?php if($attribute->has_predefined_values == 1){ echo 'checked'; } ?> name="has_predefined_values">
                                        </div>
                                        <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" formmethod="POST"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button>
                                    <div class="clearfix"></div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php }} ?>
                </tbody>
            </table>
            <div class="col-md-3">
            </div><div class="clearfix"></div>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>