<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-checkbox-multiple-marked fa-lg"></span> Dispatch Type </h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Create Vendor"></a></div><div class="clearfix"></div><hr>
<script>
    $(document).ajaxStart(function() {
        $('.img').show(); // show the gif image when ajax starts
    }).ajaxStop(function() {
        $('.img').hide(); // hide the gif image when ajax completes
    });
  
</script>
<div id="purchase">
    <form id="pay" class="">
        <div class="col-md-8 col-md-offset-2" >
            <article role="login" class="" style="padding: 15px; border-radius: 10px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Dispatch Type</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Dispatch Type</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="type" placeholder="Enter Dispatch Type Name" required="" />
                </div><div class="clearfix"></div><hr>
              
                <a class="btn btn-warning waves-effect gradient1" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_dispatch_type') ?>" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
                <div class="clearfix"></div>
            </article><div class="clearfix"></div><br>
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
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('vendor_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div><div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto">
        <table id="vendor_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Dispatch_type</th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($dispatch_data as $dispatch){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $dispatch->dispatch_type; ?></td>
                    <td><?php if($dispatch->status == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="btn btn-sm waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                         <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Dispatch Type</h4></center><hr>
                                            <label class="col-md-3 col-md-offset-1">Dispatch Type</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" name="type" value="<?php echo $dispatch->dispatch_type; ?>" placeholder="Enter Dispatch Type Name" required="" />
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">Status</label>
                                            <div class="col-md-7">
                                                <select class="select form-control" name="status">
                                                    <option value="<?php echo $dispatch->status ?>"><?php if($dispatch->status == 1){ echo 'Active'; } elseif($dispatch->status == 0){ echo 'In Active'; } ?></option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>

                                            </div><div class="clearfix"></div><br>
                                            <a href="#edit<?php echo $i ?>" class="btn btn-warning waves-effect simple-tooltip"  data-target="modal">Cancel</a>
                                            <button type="submit" formmethod="POST" value="<?php echo $dispatch->id_dispatch_type  ?>" name="id" formaction="<?php echo base_url('Master/edit_dispatch_type') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                                            <div class="clearfix"></div>    
                                            </div>
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
    </div><div class="clearfix"></div>
</div>
<div class="clearfix"></div>
   
<?php include __DIR__.'../../footer.php'; ?>