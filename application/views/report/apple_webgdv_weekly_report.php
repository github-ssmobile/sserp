<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('.btnrep').click(function (){
          var fromdate = $('#fromdate').val();
            if(fromdate == ''){
               
                alert("Please Select date");
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
<div align="center" class="text-darken-4 col-md-9 col-md-offset-1 text-center">
    <span class="mdi mdi-file-document fa-2x"> Apple WEB GDV Weekly Report</span>
</div>

<div class="clearfix"></div><hr>
<!--<h4 style="color: #ff3333"><center>File Allowed To Download Once In Day</center></h4>-->
 <?php if( $save = $this->session->flashdata('alert_dms_data')): ?>
        <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
            <?= $save ?>
        </div>
<?php endif; ?>
<form>
    <div class="col-md-1">From</div> 
    <div class="col-md-2">
        <input type="date" name="fromdate" id="fromdate" class="form-control input-sm" placeholder="From Date" required="">
    </div>
    <div class="col-md-1">
        <button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/ajax_apple_webgdv_weekly_report" id="btnrep" class="btn btn-info" style="margin-top: 0">Search</button>
    </div>
</form>
<div class="clearfix"></div><br>
<?php include __DIR__.'../../footer.php'; ?>