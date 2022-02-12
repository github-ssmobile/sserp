<?php include __DIR__.'../../header.php'; ?>
<!--<style>
    .chosen-container-single .chosen-single {
    position: relative;
    display: block;
    overflow: hidden;
    padding: 4px 0 4px 15px;
    height: auto;
    border: 1px solid #aaa;
    border-radius: 5px;
    background-color: #fff;
    background: -webkit-gradient(linear, 50% 0, 50% 100%, color-stop(20%, #fff), color-stop(50%, #f6f6f6), color-stop(52%, #eee), color-stop(100%, #f4f4f4));
    background: -webkit-linear-gradient(#fff 20%, #f6f6f6 50%, #eee 52%, #f4f4f4 100%);
    background: -moz-linear-gradient(#fff 20%, #f6f6f6 50%, #eee 52%, #f4f4f4 100%);
    background: -o-linear-gradient(#fff 20%, #f6f6f6 50%, #eee 52%, #f4f4f4 100%);
    background: linear-gradient(#fff 20%, #f6f6f6 50%, #eee 52%, #f4f4f4 100%);
        background-clip: border-box;
    background-clip: padding-box;
    box-shadow: 0 0 3px #fff inset, 0 1px 1px rgba(0, 0, 0, .1);
    color: #444;
    text-decoration: none;
    white-space: nowrap;
    line-height: 24px;
    width: 300px;
    
}
.chosen-container .chosen-results {
    color: #444;
    position: relative;
    overflow-x: hidden;
    overflow-y: auto;
    margin: 0 4px 4px 0;
    padding: 0 0 0 4px;
    max-height: 240px;
    -webkit-overflow-scrolling: touch;
    width: 300px;
    background-color: white;
}
.chosen-container-single .chosen-search input[type="text"] {
    margin: 1px 0;
    padding: 4px 20px 4px 5px;
    width: 300px;
    height: auto;
    outline: 0;
    border: 1px solid #aaa;
    background: #fff url(chosen-sprite.png) no-repeat 100% -20px;
    background: url(chosen-sprite.png) no-repeat 100% -20px;
    font-size: 1em;
    font-family: sans-serif;
    line-height: normal;
    border-radius: 0;
}
</style>-->
<script>
    $(document).ready(function (){
       $('.btnsubmit').click(function (){
            var idoldmodel = $(this).closest('td').find('#idoldmodel').val();
            var newmodel = $(this).closest('td').parent('tr').find('#newmodel').val();
            if(newmodel != ''){
                $.ajax({
                url:"<?php echo base_url() ?>Catalogue/update_old_model_data",
                method:"POST",
                data:{idoldmodel : idoldmodel, newmodel: newmodel},
                success:function(data)
                {
                    alert("Model Updated");
                }
            });
           }else{
               alert('select Model');
               return false;
           }
           
       }); 
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-chemical-weapon"></span> Old Model Mapping</h3></center></div>
    <div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
             <?php echo form_open('Catalogue/update_old_model_data', array('id' => 'pay','class' => 'collapse' )) ?>
            <div  class="col-md-6 thumbnail col-md-offset-3">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Attribute Type</h4></center><hr>
                <div class="col-md-12">
                    <label class="col-md-3 col-md-offset-1">Old Model</label>
                    <div class="col-md-7">
                        <select class="select form-control" id="idoldmodel" name="idoldmodel">
                            <option>Select Model </option>
                            <?php foreach ($old_model_data as $old){ ?>
                                <option value="<?php echo $old->id_old_model_data;?>"><?php echo $old->old_model_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1"> ERP Model </label>
                    <div class="col-md-7">
                        <select class=" select form-control" id="newmodel" name="newmodel">
                            <option>Select Model </option>
                            <?php foreach ($model_data as $new){ ?>
                                <option value="<?php echo $new->id_variant;?>"><?php echo $new->full_name; ?></option>
                            <?php } ?>
                        </select>
                    </div><div class="clearfix"></div>
                    <div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" class="pull-right btn btn-info waves-effect ">Save</button>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div><div class="clearfix"></div><hr>
            <?php echo form_close(); ?>
           
            <!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>-->
            <div class="clearfix"></div><br>
            
            <table id="attribute_type" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Product Id</th>
                    <th>Old Model Name</th>
                    <th>Idvariant</th>
                    <th>Model_name</th>
                    <th>Edit</th>
                </thead>
                <tbody >
                    <?php $i=1; foreach ($old_model_data as $old_model){ ?>
                    <tr>
                        <?php // echo form_open('Catalogue/update_old_model_data') ?>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $old_model->id_oldmodel; ?></td>
                        <td><?php echo $old_model->old_model_name; ?></td>
                        <td><?php echo $old_model->id_variant; ?></td>
                        <td><?php //echo $old_model->full_name; ?>
                            <select class="chosen-select form-control" style="width:100% " id="newmodel" name="newmodel" style="width: 100%">
                                <?php if($old_model->id_variant){?>
                                    <option value="<?php echo $old_model->id_variant;?>"><?php echo $old_model->full_name; ?></option>
                                <?php }else{?>
                                    <option value="">Select Model </option>
                                <?php } ?>
                                <?php foreach ($model_data as $new){ ?>
                                    <option value="<?php echo $new->id_variant;?>"><?php echo $new->full_name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="idoldmodel" id="idoldmodel" value="<?php echo $old_model->id_old_model_data ?>">
                            <a  class="btn btn-info btn-sm pull-right waves-effect btnsubmit"value="<?php echo $old_model->id_old_model_data ?><span class=""></span> Save</a><div class="clearfix"></div>
                            
                            
<!--                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $old_model->id_old_model_data ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $old_model->id_old_model_data ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                       <?php echo form_open('Catalogue/update_old_model_data') ?>
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Attribute Type</h4></center><hr>
                                                <div class="col-md-12">
                                                    <label class="col-md-3 col-md-offset-1">Product ID</label>
                                                    <div class="col-md-7"><?php echo $old_model->id_oldmodel; ?></div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Old Model</label>
                                                    <div class="col-md-7"><?php echo $old_model->old_model_name; ?></div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">New Model</label>
                                                    <div class="col-md-7" >
                                                        <select class="chosen-select form-control" style="width:100% " id="newmodel" name="newmodel" style="width: 100%">
                                                            <?php if($old_model->id_variant){?>
                                                                <option value="<?php echo $old_model->id_variant;?>"><?php echo $old_model->full_name; ?></option>
                                                            <?php }else{?>
                                                            <option>Select Model </option>
                                                            <?php } ?>
                                                            <?php foreach ($model_data as $new){ ?>
                                                                <option value="<?php echo $new->id_variant;?>"><?php echo $new->full_name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <input type="hidden" name="idoldmodel" value="<?php echo $old_model->id_old_model_data ?>">
                                            <a href="#edit<?php echo $old_model->id_old_model_data ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                            <button type="submit" value="<?php echo $old_model->id_old_model_data ?>" name="id" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                        </div>-->
                                        <?php //echo form_close();?>
<!--                                    </div>
                                </div>
                            </div>-->
                        </td>
                    </tr>
                    <?php  } ?>
                </tbody>
            </table>
<!--            <div class="col-md-3">
            </div>-->
            <div class="clearfix"></div> <hr>
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_mapping_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <div class="clearfix"></div><br>
              <table id="model_mapping_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Product Id</th>
                    <th>Old Model Name</th>
                    <th>Idvariant</th>
                    <th>Model_name</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($old_data as $odata){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $odata->id_oldmodel; ?></td>
                        <td><?php echo $odata->old_model_name; ?></td>
                        <td><?php echo $odata->id_variant; ?></td>
                        <td><?php echo $odata->full_name; ?></td>
                    </tr>
                    <?php  } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>