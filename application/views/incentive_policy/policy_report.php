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
                    url:"<?php echo base_url() ?>Incentive_policy/ajax_get_policy_report_data",
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
    <center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Policy Report</h3></center>
</div>
<!--<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div>-->
<div class="clearfix"></div><hr>
    
<div class="clearfix"></div><br>
    <div class="col-md-1"><b>Month</b></div>
    <div class="col-md-2">
        <input type="text" class="form-control monthpick"  placeholder="Select Month" id="month" name="month"  value="<?php echo date('Y-m');?>">
    </div>
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