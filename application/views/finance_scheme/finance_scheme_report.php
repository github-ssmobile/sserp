<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        
         $(document).on('change', '#schemetypes', function() {          
            var idhead = +$('#schemetypes').val();    
            
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_get_payment_mode_byidhead_edit",
                method:"POST",
                data:{idhead : idhead},
                success:function(data)
                {
                    $("#paymentmode").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
        $(document).on('change', '#brandid', function() {          
            var brand = +$('#brandid').val();    
            
            var product_category=0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_modellist_by_brand",
                method:"POST",
                data:{brand : brand,product_category: product_category},
                success:function(data)
                {
                    $("#modellist").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
        $('#btnsubmit').click(function (){
             var from = $('#from_date').val();
//            var to = $('#to_date').val();
            var type = $('#schemetypes').val();    
            var idmode = $('.idpmodel').val();   
            var brand = $('#brandid').val();    
            var idvariant = $('#idvariant').val();    
            if(from != '' && brand != '' && idvariant != '' && type != '' && idmode != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Finance_scheme/ajax_get_finance_scheme_byfilter",
                    method:"POST",
                    data:{from : from, brand: brand, idvariant: idvariant, type: type, idmode: idmode},
                    success:function(data)
                    {
                        $('#financeschemedata').html(data);
                    }
                });
            }else{
                alert("Select Filter Data Properly");
                return false;
            }
        });
       
    });
    
</script>
<style>
.fixheader {
    background-color: #fbf7c0;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 9;
}
.fixheader1 {
    background-color: #fbf7c0;
    position: sticky;
    top: 28px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 9;
}
/*.fixonleft {
    position: fixed;
    bottom: 120px;
    right: 80px; 
}
.fixonright {
    position: fixed;
    bottom: 120px;
    left: 250px; 
}*/

.chosen-container {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    font-size: 13px;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    width: 183px !important;
}
</style>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span>Finance Scheme Report</h3>
    </center>
</div>
<div class="clearfix"></div><hr>
<div class="" >
   <!--<div class="col-md-2" style="padding: 5px"><b>From</b>-->
       <input type="hidden" class="form-control" data-provide="datepicker" id="from_date" value="<?php echo date('Y-m-d'); ?>" name="from_date" required">
    <!--</div>-->
<!--    <div class="col-md-2" style="padding: 5px"><b>To</b>
        <input type="text" class="form-control" data-provide="datepicker" id="to_date" name="to_date" required">
    </div>-->
    <div class="col-md-2" style="padding: 5px"><b>Brand</b>
        <select class="chosen-select form-control input-sm "  id="brandid" required="">
            <option value="">Select Brand</option>            
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2 " id="modellist" style="padding: 5px"><b>Model</b>
        <select class="chosen-select form-control input-sm " name="idvariant" id="idvariant">
            <option value="">Select Model</option>
        </select>
    </div>
    <div class="col-md-2 " style="padding: 5px"><b>Scheme Type</b>
         <select class="chosen-select form-control input-sm" name="schemetypes" id="schemetypes">
            <option value="0">All scheme type</option>
            <option value="4">Finance Scheme</option>
            <option value="3">Swipe Scheme</option>
        </select>
    </div>
    <div class="col-md-2 " style="padding: 5px"><b>Payment Mode</b>
        <div id="paymentmode">
            <select class="chosen-select form-control input-sm idpmodel" name="idpmodel" id="idpmodel">
                <option value="0">All Finance Provider</option>
            </select>
        </div>
    </div>
    <div class="col-md-2 ">
        <br>
        <button class="btn btn-primary" id="btnsubmit">Filter</button>
    </div>
</div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="overflow: auto">
    <div class="clearfix"></div><br>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-6">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-1 col-sm-2">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('finance_scheme_data');" style="margin-top: 6px;line-height: unset; "><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="clearfix"></div><br>
    <div style="overflow-x: auto;height: 600px;">
        <div id="financeschemedata"></div>

    </div>
    </div>
    
</div>
<?php } include __DIR__.'../../footer.php'; ?>