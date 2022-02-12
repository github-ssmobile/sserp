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
            var role = $('#role').val();
            if(from != '' && to != '' && idbranch != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Audit/ajax_accountant_audit_report",
                    method:"POST",
                    data:{from: from, to: to, idbranch: idbranch, role: role, idcat: idcat, idbrand: idbrand},
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
        z-index: 9;
    }
   
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> <?php echo $page_name; ?> </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
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
            <select class="form-control chosen-select" name="idcat" id="idcat">
                <option value="0">All</option>
                <?php foreach($category_data as $cat){ ?>
                <option value="<?php echo $cat->id_product_category ?>"><?php echo $cat->product_category_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <b>Brand</b>
            <select class="form-control chosen-select" name="idbrand" id="idbrand">
                <option value="0">All</option>
                <?php foreach($brand_data as $brand){ ?>
                <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2"><b>From</b><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" ></div>
        <div class="col-md-2"><b>To</b><input type="text" class="form-control" data-provide="datepicker" id="to" name="to"></div>
        <input type="hidden" name="role" id="role" value="<?php echo $role;?>">
        <div class="col-md-1"><button class="btn btn-primary" id="btnreport">Submit</button></div>
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