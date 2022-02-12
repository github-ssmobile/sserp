<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<center><h3><span class="mdi mdi-xaml fa-lg"></span> Vendors SKUs</h3></center>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px; background-color: #fff">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="">
            <div class="col-md-5">
                <div class="thumbnail" style="border: none"><br>
                    <img src="<?php echo base_url() ?>assets/images/sku.jpg" style="width: 80%" />
                </div>
            </div>
            <div class="col-md-6"><br>
                <div class="thumbnail">
                    <center><h4><span class="mdi mdi-flattr"></span> Add Vendors SKUs</h4></center><hr>
                    
                    <label class="col-md-3 col-md-offset-1">SKU Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="sku_name" placeholder="Enter SKU Name" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">SKU Column Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="sku_col_name" pattern="[a-z_]*" placeholder="Enter SKU Column Name" title="This value will auto create column into model_variant (Only small letters) " required=""/>
                    </div>
                    <div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_vendors_sku') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
        
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="payment_mode_data" class="table table-condensed table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>SKU Name</th>
                <th>Column Name</th>
                <th>Status</th>                
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($sku_data as $skus){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $skus->vendor_name; ?></td>
                    <td><?php echo $skus->column_name; ?></td>                    
                    <td><?php if($skus->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>                    
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>