<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-bank fa-lg"></span> Bank </h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">        
            <div class="col-md-6" style="background: border-box"><br><br>
                <img src="<?php echo base_url('assets/images/bank.png') ?>" style="width: 100%" />
            </div>
        <div class="col-md-6 thumbnail">
            <div class="">
                <center><h4><span class="mdi mdi-bank" style="font-size: 28px"></span> Add Bank</h4></center><hr>
                <label class="col-md-4">Bank Name</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="bank_name" placeholder="Enter Bank name" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Branch Name</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="bank_branch" placeholder="Enter branch name" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Account No.</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="account_no" placeholder="Enter Account Number"/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Bank IFSC</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="bank_ifsc" placeholder="Enter Bank IFSC"/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Cheque Return charges</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="chq_return_charges" placeholder="Enter Cheque Return charges"/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Account Type</label>
                <div class="col-md-8">
                    <select class="select form-control" name="account_type">
                        <option value="">Select Account Type</option>
                        <option>CC</option>
                        <option>CA</option>
                        <option>SB</option>
                    </select>
                </div><div class="clearfix"></div><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" name="id" formmethod="POST" formaction="<?php echo base_url('Master/save_bank_details') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('bank_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="bank_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Bank</th>            
                <th>Branch</th>
                <th>Account</th>
                <th>IFSC</th>
                <th>Cheque Return charges</th>
                <th>Account Type</th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($bank_data as $bank){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $bank->bank_name; ?></td>
                        <td><?php echo $bank->bank_branch; ?></td>
                        <td><?php echo $bank->account_no; ?></td>
                        <td><?php echo $bank->bank_ifsc; ?></td>
                        <td><?php echo $bank->chq_return_charges; ?></td>
                        <td><?php echo $bank->account_type; ?></td>
                        <td>
                            <?php if ($bank->active == 1){
                                echo 'Active';
                            } else {
                                echo 'In Active';
                            } ?>
                        </td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <?php echo form_open_multipart('Master/edit_bank') ?>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Zone</h4></center><hr>
                                                    <label class="col-md-4">Bank Name</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="bank_name" value="<?php echo $bank->bank_name ?>" required="" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Branch Name</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="bank_branch" value="<?php echo $bank->bank_branch ?>" placeholder="Enter branch name" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Account No</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="account_no" value="<?php echo $bank->account_no ?>" placeholder="Enter Account No"/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Bank IFSC</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="bank_ifsc" value="<?php echo $bank->bank_ifsc ?>" placeholder="Enter Bank IFSC" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Cheque Return charges</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="chq_return_charges" value="<?php echo $bank->chq_return_charges ?>" placeholder="Enter Cheque Return charges"/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Account Type</label>
                                                    <div class="col-md-8">
                                                        <select class="form-control" name="account_type">
                                                            <option value="">Select Account Type</option>
                                                            <option selected=""><?php echo $bank->account_type ?></option>
                                                            <option>CC</option>
                                                            <option>CA</option>
                                                            <option>SB</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><br>
                                                    
                                                    <label class="col-md-4">Status</label>
                                                    <div class="col-md-8">
                                                        <select class="form-control" name="status">
                                                            <option value="<?php echo $bank->active ?>"><?php if ($bank->active == 1) {
                                                                echo 'Active';
                                                            } elseif ($bank->active == 0) {
                                                                echo 'In Active';
                                                            } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><hr>
                                                </div>
                                                <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $bank->id_bank ?>" name="id"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>