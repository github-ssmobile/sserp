<?php include __DIR__ . '../../header.php'; ?>

<script>
    $(document).ajaxStart(function () {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function () {
        $('.img').hide(); // hide the gif image when ajax completes
    });
    
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>

<center><h3 style="margin-top: 0"><span class="mdi mdi-repeat fa-lg"></span> Branch Category </h3></center>    
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">        
        <div class="col-md-8 thumbnail col-md-offset-2">
            <div class="col-md-10 col-md-offset-1">  
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Branch Category</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Branch Category Name</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="category_name" placeholder="Enter branch_category name" required=""/>
                </div>
                <div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Status</label>
                <div class="col-md-7">
                    <select class="select form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="clearfix"></div><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" name="id" formmethod="POST" formaction="<?php echo base_url('Master/save_branch_category_details') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                <div class="clearfix"></div>
                
            </div><div class="clearfix"></div>  
        </div>
        <div class="clearfix"></div><hr>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_category_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="branch_category_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
            <th>Sr</th>
            <th>Name</th>            
            <th>Status</th>
            <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($branch_category_data as $branch_category) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $branch_category->branch_category_name; ?></td>
                        <td><?php if ($branch_category->active == 1) {
                        echo 'Active';
                    } else {
                        echo 'In Active';
                    } ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <?php echo form_open_multipart('Master/edit_branch_category') ?>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Branch Category</h4></center><hr>
                                                    <label class="col-md-3 col-md-offset-1">Branch Category Name</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="category_name" value="<?php echo $branch_category->branch_category_name ?>" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Status</label>
                                                    <div class="col-md-7">
                                                        <select class="form-control" name="status">
                                                            <option value="<?php echo $branch_category->active ?>"><?php if ($branch_category->active == 1) {
                                                                echo 'Active';
                                                            } elseif ($branch_category->active == 0) {
                                                                echo 'In Active';
                                                            } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><hr>

                                                    <div class="clearfix"></div>


                                                    <div class="clearfix"></div>
                                                </div>
                                                <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $branch_category->id_branch_category ?>" name="id"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                            </div></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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