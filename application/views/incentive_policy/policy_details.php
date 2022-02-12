<?php include __DIR__ . '../../header.php'; ?>

<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Incentive Policy Details</h3></center>
</div>
<div class="clearfix"></div><hr>
<div class="thumbnail" >
    <div class="" id="pay" style="padding: 10px;">
        <div class="col-md-4 col-sm-4 col-xs-4 ">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('policy_details');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <?php if($inc_policy){ ?>
        <table class="table table-bordered table-condensed " id="policy_details">
            <thead style="background-color: #99ccff">
            <th style="text-align: center">Month</th>
                <th>Policy Name</th>
                <th>Product Category</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Policy Slabs</th>
            </thead>
            <tbody>
                <?php foreach ($inc_policy as $incp){
                    if($policy_details){
                        foreach ($policy_details as $pdata){

                            $category = $pdata->category_name;
                            $brand = $pdata->brand_name;
                        }
                    }else{
                        $category = '';
                        $brand = '';
                    }
                    ?>
                <tr>
                    <td><?php echo $incp->month_year; ?></td>
                    <td><?php echo $incp->policy_name; ?></td>
                    <td><?php echo $incp->product_category_name; ?></td>
                    <td><?php echo $category; ?></td>
                    <td><?php if($brand != ''){ echo $brand; }else{ echo 'All Brands'; }?></td>
                    <td>
                        <?php $ss = 1; foreach ($policy_details as $pdata){
                          if($pdata->full_name != ''){ 
                              echo $ss++.') '.$pdata->full_name.'<br>';
                          }else{
                              echo 'All Models'.'<br>';
                          }
                        } ?>
                    </td>
                    <td>
                        <table class="table table-bordered">
                            <thead>
                                <th>Slab Name</th>
                                <th>Min Slab</th>
                                <th>Max Slab</th>
                                <th>Inc per</th>
                            </thead>
                            <tbody>
                                <?php foreach ($slab_details as $sdata){?>
                                <tr>
                                    <td><?php echo $sdata->slab_name ?></td>
                                    <td><?php echo $sdata->min_slab ?></td>
                                    <td><?php echo $sdata->max_slab ?></td>
                                    <td><?php if($incp->cal_type == 0){ echo $sdata->slab_per.'%'; }else{ echo 'Rs '.$sdata->slab_per;} ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
            
        <?php }?>
        

    </div>
    <div class="clearfix"></div><br>
</div>
<div class="clearfix"></div><br>


<div id="policydata"></div>
<?php include __DIR__ . '../../footer.php'; ?>