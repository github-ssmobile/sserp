<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idcat = $('#idcat') .val();
            var idbrand = $('#idbrand') .val();
            var from = $('#from') .val();
            var to = $('#to') .val();
            var idbranch = $('#idbranch').val();
            var idgodown = $('#idgodown').val();
            if(from != '' || to != '' || idbranch != '' || idcat != '' || idbrand != ''){
                $.ajax({
                  url:"<?php echo base_url() ?>Audit/ajax_get_missing_stock_data",
                  method:"POST",
                  data:{idbranch: idbranch, idcat: idcat, idbrand: idbrand, from: from, to: to, idgodown: idgodown},
                  success:function(data)
                  {
                      $('#report_data').html(data);
                  }
              });
            }else{
                alert("Select Filter");
                return false();
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
   
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> <?php echo $page_name; ?> </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
            <div class="col-md-2"><b>From</b><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" ></div>
            <div class="col-md-2"><b>To</b><input type="text" class="form-control" data-provide="datepicker" id="to" name="to"></div>
            <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <div class="col-md-2">
                    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
                </div>
            <?php } else {
                if($this->session->userdata('role_type') == 1){ ?>
                    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
                <?php }else{ ?>
                <div class="col-md-2">
                    <b>Branch</b>
                    <select class="form-control chosen-select" name="idbranch" id="idbranch">
                        <option value="">Select Branch</option>
                        <?php foreach($branch_data as $branch){ ?>
                            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } 
            }?>
                <div class="col-md-2">
                <b>Product Category</b>
                <select class="form-control chosen-select" name="idcat" id="idcat">
                    <option value="">Select Category</option>
                    <?php foreach($category_data as $cat){ ?>
                    <option value="<?php echo $cat->id_product_category ?>"><?php echo $cat->product_category_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <b>Brand</b>
                <select class="form-control chosen-select" name="idbrand" id="idbrand">
                    <option value="">Select Brand</option>
                    <?php foreach($brand_data as $brand){ ?>
                    <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="hidden" name="idgodown" id="idgodown" value="5">
            <div class="col-md-1"><button class="btn btn-primary" id="btnreport" >Submit</button></div>
            <div class="clearfix"></div><br>
        <div class="col-md-4 col-sm-4 col-xs-4 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('accountant_audit_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="report_data">
            
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>