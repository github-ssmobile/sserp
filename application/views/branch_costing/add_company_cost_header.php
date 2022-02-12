<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Company Costing Headers </h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Create Vendor"></a></div><div class="clearfix"></div><hr>
<div id="purchase">
    <form id="pay" class="collapse">
        <div class="col-md-8 col-md-offset-2" >
            <article role="login" class="" style="padding: 15px; border-radius: 10px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Cost Header</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Cost Header Name</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="cost_name" placeholder="Enter Company Costing Header Name" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Status</label>
                <div class="col-md-7">
                    <select class="form-control" name="status">
                        <option value="0">Active</option>
                        <option value="1">InActive</option>
                    </select>
                </div><div class="clearfix"></div><br>
                <a class="btn btn-warning waves-effect gradient1" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Costing/save_company_cost_header') ?>" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
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
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_costing_header');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div><div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto">
        <table id="branch_costing_header" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead style="background-color: #7cb6f4">
                <th>Sr</th>
                <th>Costing Header </th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($costing_headers as $tdata){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $tdata->company_cost_name; ?></td>
                    <td><?php if($tdata->status == 0){ echo 'Active'; } else{ echo 'Inactive'; }?></td>
                    <td>
                        <a class="btn btn-sm waves-effect" target="_blank" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Vendor</h4></center><hr>
                                            
                                            <label class="col-md-3 col-md-offset-1">Branch Costing Name</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" name="cost_name1" placeholder="Enter Company Cost Headre Name" value="<?php echo $tdata->company_cost_name; ?>" required="" />
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">Status</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="status1">
                                                    <?php if($tdata->active == 0){ ?>
                                                        <option value=" <?php $tdata->status ?>"> <?php if($tdata->status == 0){ echo 'Active'; } else{ echo 'Inactive'; } ?></option>
                                                    <?php }  ?>
                                                   
                                                    <option value="0">Active</option>
                                                    <option value="1">InActive</option>
                                                </select>
                                            </div><div class="clearfix"></div><br>
                                            
                                            <a data-dismiss="modal" class="btn btn-warning waves-effect simple-tooltip">Cancel</a>
                                            <button type="submit" formmethod="POST" value="<?php echo $tdata->id_company_costing  ?>" name="id" formaction="<?php echo base_url('Costing/edit_company_cost_header') ?>" class="pull-right btn btn-info waves-effect">Update</button>
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