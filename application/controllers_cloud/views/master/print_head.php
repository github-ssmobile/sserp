<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-checkerboard fa-lg"></span> Print Head </h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form enctype="multipart/form-data" id="pay" class="collapse">        
            <div class="col-md-5" style="background: border-box"><br><br>
                <img src="<?php echo base_url('assets/images/Company-Vectors.webp') ?>" style="width: 100%" />
            </div>
        <div class="col-md-7 thumbnail">
            <div class="">
                <center><h4><span class="mdi mdi-bank" style="font-size: 28px"></span> Add Print Head</h4></center><hr>
                <label class="col-md-4">Comapny Logo</label>
                <div class="col-md-8">
                    <div class="thumbnail" id="image-preview" style="min-height: 200px">
                        <label for="image-upload" id="image-label">Upload Image</label>
                          <input type="file" name="userfile" id="file" onchange="loadFilee(event)" >
                        <img  id="userfileimage" style="width: 100%; height: 200px;"/>
                    </div>
                    <script>
                        var loadFilee = function (event) {
                            var visitoutput = document.getElementById('userfileimage');
                            visitoutput.src = URL.createObjectURL(event.target.files[0]);
                        };
                    </script>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Comapny Name</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="company_name" placeholder="Enter Bank name" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4">Company Address</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="company_address" placeholder="Enter branch name" required=""/>
                </div><div class="clearfix"></div><br>
                
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" name="id" formmethod="POST" formaction="<?php echo base_url('Master/save_print_head') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
                <th>Company Logo</th>            
                <th>Company Name</th>            
                <th>Company Address</th>
                <th>Action</th>
            </thead>
            <tbody class="data_1">
                <?php $i = 1;
                foreach ($print_head_data as $head_data){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><img src="<?php echo base_url()?><?php echo $head_data->company_logo?>" height="50"></td>
                        <td><?php echo $head_data->company_name; ?></td>
                        <td><?php echo $head_data->company_address; ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >    
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <form enctype="multipart/form-data"> 
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Zone</h4></center><hr>
                                                <label class="col-md-4">Comapny Logo</label>
                                                <div class="col-md-8">
                                                    <img src="<?php echo base_url()?><?php echo $head_data->company_logo?>" height="40px">
                                                </div>
                                                <div class="clearfix"></div><br>
                                                <label class="col-md-4">New Logo</label>
                                                <div class="col-md-8">
                                                    <div class="thumbnail" id="image-preview" style="min-height: 150px">
                                                        <label for="image-upload" id="image-label">Upload Image</label>
                                                        <input type="file" name="userfile" id="file" onchange="loadFilees(event)" >
                                                        <img  id="userfileimage1" style="width: 100%; height: 150px;"/>
                                                    </div>
                                                    <script>
                                                        var loadFilees = function (event) {
                                                            var visitoutput = document.getElementById('userfileimage1');
                                                            visitoutput.src = URL.createObjectURL(event.target.files[0]);
                                                        };
                                                    </script>
                                                </div><div class="clearfix"></div><br>
                                                <label class="col-md-4">Comapny Name</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="company_name" placeholder="Enter Bank name" value="<?php echo $head_data->company_name ?>" />
                                                </div><div class="clearfix"></div><br>
                                                <label class="col-md-4">Company Address</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="company_address" placeholder="Enter branch name" value="<?php echo $head_data->company_address ?>" />
                                                </div><div class="clearfix"></div><br>
                                                <input type="hidden" name="oldlogo" value="<?php echo $head_data->company_logo ?>">
                                                <input type="hidden" name="idprinthead" value="<?php echo $head_data->id_print_head ?>">
                                                <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/edit_print_head" class="btn btn-info pull-right waves-effect"> Save</button>
                                                <div class="clearfix"></div>
                                            </div></div>
                                        </form>
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