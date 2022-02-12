<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var role = $('#role') .val();
            var from = $('#from') .val();
            var to = $('#to') .val();
            var idcategory = $('#idcategory') .val();
            if(from != '' && to != '' && role != '' && idcategory != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Audit/ajax_analysis_report",
                    method:"POST",
                    data:{from: from, to: to, role: role, idcategory: idcategory},
                    success:function(data)
                    {
                        $('#report_data').html(data);
                    }
                });
            }else{
                alert("Select Data");
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
        z-index: 999;
    }
    .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #fbf7c0;

  }
  .fixleft1{
    position: sticky;
    left:80px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> <?php echo $page_name;?> </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-1"><b>Category</b></div>
        <div class="col-md-2">
            <select name="idcategory" class="form-control" id="idcategory">
                <option value="0">All Category</option>
                <?php foreach ($category_data as $cat){ ?>
                <option value="<?php echo $cat->id_product_category; ?>"><?php echo $cat->product_category_name;?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="col-md-1"><b>From</b></div>
        <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" ></div>
        <div class="col-md-1"><b>To</b></div>
        <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="to" name="to"></div>
        <div class="col-md-1">
            <input type="hidden" class="form-control" id="role" name="role" value="<?php echo $role; ?>">
            <button class="btn btn-primary" id="btnreport">Submit</button>
        </div>
       <div class="clearfix"></div><br>
         <div class="col-md-4 col-sm-4 col-xs-4 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('accountant_analysis_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="report_data" style="overflow-x: auto;height: 700px;">
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>