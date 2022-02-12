<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
           var from = $('#from').val();
           var to = $('#to').val();
           var idbranch = $('#idbranch').val();
           var idpcat = $('#idpcat').val();
           var idbrand = $('#idbrand').val();
           if(from != '' && to != '' && idbranch !='' && idpcat != '' && idbrand != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Stock/ajax_get_focus_model_sale_report",
                    method:"POST",
                    data:{from: from, to: to, idbranch: idbranch, idpcat: idpcat, idbrand: idbrand},
                    success:function(data)
                    {
                        $('#sale_data').html(data);
                    }
                });
           }else{
               alert("Please Select Date Range");
               return false;
           }
       }); 
    });
</script>
<style>

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-magnify fa-lg"></span>Focus Model Sale report</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-2">
    <b>From</b>
    <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required="" placeholder="Date From">
</div>
<div class="col-md-2">
    <b>To</b>
    <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required="" placeholder="Date To">
</div>
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else {
        if($this->session->userdata('role_type') == 1){ ?>
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php }else{ ?>
            <div class="col-md-2">
                <b>Branch</b>
                <select class="form-control chosen-select" name="idbranch" id="idbranch">
                    <option value="0">All Branch</option>
                    <?php foreach($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } 
    }?>
<div class="col-md-2">
    <b>Product Category</b>
    <select class="form-control chosen-select" name="idpcat" id="idpcat">
        <option value="0">All</option>
        <?php foreach($product_category as $pcat){ ?>
        <option value="<?php echo $pcat->id_product_category ?>"><?php echo $pcat->product_category_name ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-2">
    <b>brand</b>
    <select class="form-control chosen-select" name="idbrand" id="idbrand">
        <option value="0">All</option>
        <?php foreach($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-2">
    <br>
    <button class="btn btn-primary" id="btnsubmit">Filter</button>
</div>
<div class="clearfix"></div><hr>
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
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('sale_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div>
<div class="clearfix"></div><br>
<div id="sale_data" style="overflow-x: auto"> 
    
</div>
<?php include __DIR__.'../../footer.php'; ?>