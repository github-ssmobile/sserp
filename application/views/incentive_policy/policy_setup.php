<?php include __DIR__.'../../header.php'; ?>

<script>
    $(document).ready(function (){
        $('#idpcat').change(function (){
            var idpcat = $('#idpcat') .val();
            if(idpcat != ''){
                //category
               $.ajax({
                    url:"<?php echo base_url() ?>Incentive_policy/ajax_get_category_bypidcat",
                    method:"POST",
                    data:{idpcat: idpcat},
                    success:function(data)
                    {
                        $('.catdata').html(data);
                    }
                });
                
                //brand
                $.ajax({
                    url:"<?php echo base_url() ?>Incentive_policy/ajax_get_brand_bypidcat",
                    method:"POST",
                    data:{ idpcat: idpcat},
                    success:function(data)
                    {
                        $('.branddata').html(data);
                        $(".chosen-select").chosen({ search_contains: true });
                    }
                });
                //model
                $.ajax({
                    url:"<?php echo base_url() ?>Incentive_policy/ajax_get_model_byidbrand",
                    method:"POST",
                    data:{ idpcat: idpcat},
                    success:function(data)
                    {
                        $('.modeldata').html(data);
                        $(".chosen-select").chosen({ search_contains: true });
                    }
                });
                
                
            }else{
                alert("Select Data Properly ! ");
                return false;
            }
        });
        
        //report show
        $('#btnreport').click(function (){
            var month = $('#month').val();
//            var idpolicy = $('#idpolicy').val();
            var pdprodcat = $('#pdprodcat').val();
            if(month != '' && pdprodcat != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Incentive_policy/ajax_get_policy_data",
                    method:"POST",
                    data:{ month: month, pdprodcat: pdprodcat},
                    success:function(data)
                    {
                        $('#policydata').html(data);
                    }
                });
            }else{
                alert("Select data Properly!..");
                return false;
            }
        });
        
        
    });

    //Add More Swipe block
     $(document).on('click', '.add_swipe_scheme', function() {
        var myparent = '<div> <div class="col-md-1">\n\
                        <a class="btn btn-floating btn-danger btn-sm pull-right waves-effect waves-light rem_swipe_scheme" ><i class="fa fa-minus" style="padding-right: 20px;margin-top: -5px;"></i></a>\n\
                    </div><div class="clearfix"></div> <br>' ;
        myparent += $(".append_slab").find('div').html();
        myparent += '</div>';

        $(".append_slab").append(myparent);
    });
    $(document).on('click', '.rem_swipe_scheme', function() {
        $(this).closest('div').parent('div').remove();
    });

    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });
    });
</script>
<style>
    .fixheader {
        /*background-color: #fbf7c0;*/
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixheader1 {
        position: sticky;
        top: 30px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  .fixleft1{
    position: sticky;
    left:45px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #c6e6f5;

  }
  .fixleft2{
    position: sticky;
    left:150px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  .textcenter{
      text-align: center;
  }
  
  .table{
      border-collapse: separate;
      border-spacing: 0;
  }
  .borderleft{
      border-left: 1px solid #999999;
  }
</style>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Incentive Policy Setup</h3></center>
</div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>
     <div class="collapse" id="pay" style="padding: 10px;">
<div class="thumbnail" >
        <form>
            <div class="col-md-1"><b>Month</b></div>
            <div class="col-md-3">
                <input type="text" class="form-control monthpick"  placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
            </div>
            <div class="col-md-1"><b>Policy</b></div>
            <div class="col-md-3">
                <input type="text" class="form-control"  placeholder="Policy Name" name="policy_name">
            </div>
             <div class="col-md-1"><b>Policy Type</b></div>
            <div class="col-md-3">
                <select name="idtype" id="idtype" class="form-control input-sm">
                    <option value="">Select Policy Type</option>
                    <option value="0">Volume Connect</option>
                    <option value="1">Value Connect</option>
                    <option value="2">Qty Connect</option>
                </select>
            </div>
              <div class="clearfix"></div><br>
            <div class="col-md-1"><b>Product Category</b></div>
            <div class="col-md-3">
                <select name="idpcat" id="idpcat" class="form-control input-sm">
                    <option value="">Select Product Category</option>
                    <?php foreach ($product_category_data as $pdata){ ?>
                    <option value="<?php echo $pdata->id_product_category ?>"><?php echo $pdata->product_category_name; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-1"><b>Category</b></div>
            <div class="col-md-3">
                <div class="catdata">
                    <select name="idcat" id="idcat" class="form-control input-sm idcat">
                        <option value="">Select Category</option>
                    </select>
                </div>
            </div>

            <div class="col-md-1"><b>Brand</b></div>
            <div class="col-md-3">
                <div class="branddata">
                    <select name="idbrand" id="idbrand" class="form-control input-sm idbrand" >
                        <option value="">Select brand</option>
                        <?php foreach ($brand_data as $bdata){ ?>
                        <option value="<?php echo $bdata->id_brand ?>"><?php echo $bdata->brand_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-1"><b>Model</b></div>
            <div class="col-md-3">
                <div class="modeldata">
                    <select name="idmodel[]" id="idmodel" class="form-control input-sm">
                        <option value="">Select Model</option>
                    </select>
                </div>
            </div>

            <!--<div class="clearfix"></div><br>-->
            <div class="col-md-1"><b>Incentive Type</b></div>
            <div class="col-md-3">
                <select name="idincnt" id="idincnt" class="form-control input-sm">
                    <option value="">Select Incentive Calculation Type</option>
                    <option value="0">Percentage</option>
                    <option value="1">Amount</option>
                </select>
            </div>
             <div class="clearfix"></div><br><hr>
            <center><h4>Add Incentive Policy Slabs</h4></center>
            <div class="clearfix"></div><br>
            <div class="col-md-3 col-md-offset-1 textcenter "><b>Slab Name</b></div>
            <div class="col-md-2 textcenter"><b>Min Slab</b></div>
            <div class="col-md-2 textcenter"><b>Max Connect</b></div>
            <div class="col-md-2 textcenter"><b>value Of Connect</b></div>
            <div class="col-md-2 "><b>Add More</b></div>
            <div class="clearfix"></div><br>
            <div class="append_slab" >
                <div>
                    <div class="col-md-3 col-md-offset-1" >
                        <input type="text" name="slab_name[]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="conn_min[]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="conn_max[]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="conn_per[]" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-1"> 
                <a class="btn btn-floating btn-primary btn-sm pull-right waves-effect waves-light add_swipe_scheme" ><i class="fa fa-plus" style="padding-right: 20px;margin-top: -5px;"></i></a>
            </div>
             <div class="clearfix"></div><br>
            <div class="clearfix"></div><hr>
            <div class="col-md-12">
                <button class="btn btn-primary pull-right" id="btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Incentive_policy/save_incentive_policy">Submit</button>
            </div>
            <div class="clearfix"></div><br>
        </form>
          <br>
    </div>
    <div class="clearfix"></div><br>
        <!--policy_data-->
</div>
<div class="clearfix"></div><br>
    <div class="col-md-1"><b>Month</b></div>
    <div class="col-md-2">
        <input type="text" class="form-control monthpick"  placeholder="Select Month" id="month" name="month"  value="<?php echo date('Y-m');?>">
    </div>
<!--    <div class="col-md-1"><b>Policy</b></div>
    <div class="col-md-3">
        <select class="form-control" name="idpolicy" id="idpolicy">
            <option value="">Select Policy</option>
            <?php foreach ($policy_data as $pdata){ ?>
            <option value="<?php echo $pdata->id_incentive_policy; ?>"><?php echo $pdata->policy_name; ?></option>
            <?php } ?>
        </select>
    </div>-->
    <div class="col-md-2"><b>Product Category</b></div>
    <div class="col-md-3">
        <select class="form-control" name="pdprodcat" id="pdprodcat">
            <option value="">Select Product Category</option>
            <option value="0">All Product Category</option>
            <?php foreach ($product_category_data as $pcdata){ ?>
            <option value="<?php echo $pcdata->id_product_category; ?>"><?php echo $pcdata->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary" id="btnreport" >Search</button>
    </div>
<div class="clearfix"></div><hr>
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
    <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('incentive_policy_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
</div> 
<div class="clearfix"></div><br>
<div id="policydata"></div>
<?php include __DIR__.'../../footer.php'; ?>