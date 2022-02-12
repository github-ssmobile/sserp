<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idbrand = $('#idbrand') .val();
            var from = $('#from') .val();
            var idbranch = $('#idbranch').val();
            var role = $('#role').val();
            var status = $('#status').val();
            if(from != '' && role != '' && idbranch != '' && idbrand != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Audit/ajax_get_brand_wise_audit_report",
                    method:"POST",
                    data:{from: from, idbranch: idbranch, role: role, idbrand: idbrand, status: status},
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
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> <?php echo $page_name; ?> </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-2"><b>From</b><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" ></div>
        <div class="col-md-2"><b>Role</b>
            <select class="form-control chosen-select" name="role" id="role">
                <option value="">Select Role</option>
                <option value="Branch Accountant">Branch Accountant Audit</option>
                <option value="Auditor">Auditor Audit</option>
            </select>
        </div>
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
            <b>Brand</b>
            <select class="form-control chosen-select" name="idbrand" id="idbrand">
                <option value="0">All</option>
                <?php foreach($brand_data as $brand){ ?>
                <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2"><b>Status</b>
            <select class="form-control chosen-select" name="status" id="status">
                <option value="0">All Status</option>
                <option value="matched">Matched</option>
                <option value="missing">Missing</option>
                <option value="unmatched">Unmatched</option>
                <option value="match in">In Transit</option>
                <option value="matched out">Transfer To Branch</option>
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-primary" id="btnreport">Submit</button></div>
        <div class="clearfix"></div><br>
        <div class="col-md-4 col-sm-4 col-xs-4 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('brand_wise_audit_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="report_data">
            
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>