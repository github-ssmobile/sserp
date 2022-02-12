<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idcat = $('#idcat').val();
            var idbrand = $('#idbrand').val();
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
                alert("Select Date Range");
                return false;
            }
        });
    });
</script>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> Audit Report </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-3">
            <b>Product Category</b>
            <select class="form-control chosen-select" name="idcat" id="idcat">
                <option value="0">All</option>
                <?php foreach($category_data as $cat){ ?>
                <option value="<?php echo $cat->id_product_category ?>"><?php echo $cat->product_category_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <b>Brand</b>
            <select class="form-control chosen-select" name="idbrand" id="idbrand">
                <option value="0">All</option>
                <?php foreach($brand_data as $brand){ ?>
                <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <b>From</b>
            <!--<input type="text" class="form-control datepick" id="from" name="from" >-->
             <input type="text" class="form-control" data-provide="datepicker" name="from" id="from">
        </div>
        <div class="col-md-2">
            <b>To</b>
            <input type="text" class="form-control" data-provide="datepicker" name="to" id="to">
            <!--<input type="text" class="form-control datepick" id="to" name="to">-->
        </div>
        <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <input type="text" name="role" id="role" value="<?php echo $role; ?>">
        <div class="col-md-1"><button class="btn btn-primary" id="btnreport">Submit</button></div>
        <div class="clearfix"></div><br>
        <div id="report_data">
            
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>