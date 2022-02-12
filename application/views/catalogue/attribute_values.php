<?php include __DIR__.'../../header.php'; ?>
<center><h3><span class="mdi mdi-steam fa-lg"></span> <?php  echo $attribute->attribute_name; ?></h3></center>
  <script>            
    $(document).ready(function(){
        $('.delete-val').click(function(){
            var id=$(this).attr("val");
            ele=$(this).closest("td").parent('tr');
            $.ajax({
                url:"<?php echo base_url() ?>Catalogue/detele_attribute_value",
                method:"POST",
                data:{id : id},
                success:function(data)
                {
                    if(data==0){
                        alert("Fail to delete attribute value!");
                    }else{
                        alert("Attribute value deleted!");
                        $(ele).fadeOut();
                    }
                }
            });
        });
       
    });
</script>
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
            <?php echo form_open('Catalogue/save_attribute_value', array('id' => 'pay','class' => 'collapse' )) ?>            
            <div class="col-md-6 thumbnail col-md-offset-3">                    
                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Attribute Value</h4></center><hr>
                        <div class="col-md-12">
                        
                        <label class="col-md-3 col-md-offset-1">Attribute Value</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" placeholder="Enter Attribute Value" name="attribute_value" required="" />
                            <input type="hidden"  name="idattribute" value="<?php  echo $attribute->id_attribute; ?>" required="" />                            
                        </div><div class="clearfix"></div><br>
                        
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('attribute_value');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category" style="margin-bottom: 2px"></a>
            <div class="clearfix"></div>
            <table id="attribute_value" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>                    
                    <th>Attribute</th>
                    <th>Value</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    if(count($attributes_data)>0){
                    $i=1; foreach ($attributes_data as $attribute){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>                        
                        <td><?php echo $attribute->attribute_name; ?></td>
                        <td><?php echo $attribute->attribute_value; ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-primary fa-lg"></span>
                            </a>
                        </td>
                        <td>
                            <a class="thumbnail btn-link waves-effect delete-val" val="<?php  echo $attribute->id_attribute_value  ?>" style="margin: 0" >
                                <span class="mdi mdi-delete text-danger fa-lg"></span>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <?php echo form_open('Catalogue/edit_attribute_value') ?>    
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Attribute Value</h4></center><hr>
                                        <div class="col-md-12">
                                        <label class="col-md-4 col-md-offset-1">Attribute Value</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="<?php echo $attribute->attribute_value; ?>" placeholder="Enter attribute value" name="attribute_value" />                                                                                                                                    
                                            <input type="hidden"  name="id" value="<?php  echo $attribute->id_attribute_value  ?>" required="" />
                                            <input type="hidden"  name="idattribute" value="<?php  echo $attribute->id_attribute; ?>" required="" />                            
                                        </div><div class="clearfix"></div><br>                                        
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