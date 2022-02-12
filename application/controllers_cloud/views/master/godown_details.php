<?php include __DIR__ . '../../header.php'; ?>
<center><h3><span class="mdi mdi-checkbox-marked-circle-outline fa-lg"></span> Godown</h3></center>

<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">
            <div class="col-md-3">
                
            </div>
            <div class="col-md-5 col-md-offset-1"><br><br><br>
                <div class="thumbnail">
                    <center><h4><span class="mdi mdi-account-edit" style="font-size: 28px"></span> Add Godown</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Godown Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="name" placeholder="Enter User Name" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_godown') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div><hr>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('godown_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="godown_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Name</th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($godown_data as $godown){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $godown->godown_name; ?></td>
                    <td><?php if($godown->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                         <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form> 
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                 <center><h4><span class="mdi mdi-account-edit" style="font-size: 28px"></span> Add Godown</h4></center><hr>
                                                <label class="col-md-3 col-md-offset-1">Godown Name</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" name="name" value="<?php echo $godown->godown_name; ?>" placeholder="Enter User Name" required=""/>
                                                </div><div class="clearfix"></div><br>
                                                <label class="col-md-3 col-md-offset-1">Status</label>
                                                <div class="col-md-7">
                                                    <select class="select form-control" name="status">
                                                            <option value="<?php echo $godown->active ?>"><?php if($godown->active == 1){ echo 'Active'; } elseif($godown->active == 0){ echo 'In Active'; } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                </div><div class="clearfix"></div><hr>
                                                <a href="#edit<?php echo $i ?>" class="btn btn-warning waves-effect simple-tooltip" data-target="model">Cancel</a>
                                                <button type="submit" formmethod="POST" value="<?php echo $godown->id_godown ?>" name="id"    formaction="<?php echo base_url('Master/edit_godown') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>