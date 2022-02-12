<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span>&nbsp;Add Column </h3></center><div class="clearfix"></div><hr>
<script>
    $(document).ajaxStart(function () {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function () {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    $(document).ready(function () {
        
        
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
                            $('#customer_data_form').collapse('hide');
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
        <form id="customer_data_form" class="collapse">
            <div class="col-md-8 thumbnail col-md-offset-2">
                <div class="col-md-10 col-md-offset-1">                
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> &nbsp;Add Column For Customer Form</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Field Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Field Name" name="field_name" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3 col-md-offset-1">Column Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" placeholder="Enter Column Name" name="column_name" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Data Type</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="data_type">
                            <option value="-1">Select Data Type</option>
                            <option value="int">Int</option>
                            <option value="varchar">Varchar</option>
                            <option value="date">Date</option>
                            <option value="text">Text</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="-1">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Sequence</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" id="sequence"  class="sequence"  name="sequence" placeholder="Enter Sequence" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Field Required</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="filed_required">
                            <option value="-1">Select Required</option>
                            <option value="1">Required</option>
                            <option value="0">Not Required</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#customer_data_form">Cancel</a>
                    <button type="submit"  formmethod="POST" formaction="<?php echo base_url('Customer_loyalty/save_cutomer_form_data') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#customer_data_form" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="bank_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Field Name</th>            
                <th>Column Name</th>
                <th>Data Type</th>
                <th>Status</th>
                <th>Sequence</th>
                <th>Required</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($customer_form_data as $data){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $data->field_name; ?></td>
                        <td><?php echo $data->column_name; ?></td>
                        <td><?php echo $data->data_type; ?></td>
                        <td>
                            <?php if ($data->status == 1){
                                echo 'Active';
                            } else {
                                echo 'In Active';
                            } ?>
                        </td>
                        <td><?php echo $data->sequence; ?></td>
                        <td>
                            <?php if ($data->filed_required == 1){
                                echo 'Required';
                            } else {
                                echo 'Not Required';
                            } ?>
                        </td>
                        <td>
                        <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $data->id; ?>" style="margin: 0" >
                        <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $data->id; ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <?php echo form_open_multipart('Customer_loyalty/edit_customer_form_data') ?>    
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Column</h4></center><hr>
                                            <label class="col-md-4">Sequence</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="sequence" value="<?php echo $data->sequence; ?>" required="" />
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-4">Status</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="status">
                                                    <option value="1" <?php if(isset($data) && !(empty($data))) { if($data->status=="1") echo "selected"; } ?>>Active</option>
                                                    <option value="0" <?php if(isset($data) && !(empty($data))) { if($data->status=="0") echo "selected"; } ?>>In Active</option>
                                                </select>
                                            </div><div class="clearfix"></div><br>
                                             <label class="col-md-4">Field Required</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="filed_required">
                                                    <option value="1" <?php if(isset($data) && !(empty($data))) { if($data->filed_required=="1") echo "selected"; } ?>>Required</option>
                                                    <option value="0" <?php if(isset($data) && !(empty($data))) { if($data->filed_required=="0") echo "selected"; } ?>>Not Required</option>
                                                </select>
                                            </div><div class="clearfix"></div>
                                            <hr>
                                        </div>
                                        <a href="#edit<?php echo $data->id; ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                        <button type="submit" value="<?php echo $data->id; ?>" name="id"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                    </div></div>
                                </form>
                            </div>
                        </div>
                        </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table><div class="clearfix"></div>
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__ . '../../footer.php'; ?>