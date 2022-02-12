<?php include __DIR__ . '../../header.php';?>
<script>
    $(document).ready(function (){
       $('#isl_file').click(function (){
          var fromdate = $('#fromdate').val();
            if(fromdate == ''){
                alert("Please Select date");
                return false;
            }
       });
       $('#osl_file').click(function (){
          var fromdate = $('#fromdate').val();
            if(fromdate == ''){
                alert("Please Select date");
                return false;
            }
       });
       $('#ret_file').click(function (){
          var fromdate = $('#fromdate').val();
            if(fromdate == ''){
                alert("Please Select date");
                return false;
            }
       });
    });
</script>
<div align="center" class="text-darken-4 col-md-8 col-md-offset-1 text-center">
    <span class="mdi mdi-file-document fa-2x"> Apple DMS Report</span>
</div>
<div class="col-md-1 pull-right">
  
</div><div class="clearfix"></div><hr>
<!--<h4 style="color: #ff3333"><center>File Allowed To Download Once In Day</center></h4>-->
 <?php if( $save = $this->session->flashdata('alert_dms_data')): ?>
        <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
            <?= $save ?>
        </div>
<?php endif; ?>
<form>
    <div class="col-md-1">Date</div> 
    <div class="col-md-2">
        <input type="text" name="fromdate" id="fromdate" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" required="">
    </div>
    <div class="col-md-2 col-sm-4" style="padding-right: 0" >
        <button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/isl_file" id="isl_file" class="box waves-effect waves-teal waves-block btn btn-primary" style="padding: 10px;margin-top: 0;width: 100%;font-size: 17px;  background-image: linear-gradient(to right top, #101c6b, #194390, #2c6bb3, #4a94d4, #70bef2);"> <b>Apple ISL File</b></button>
    </div>
    <div class="col-md-2 col-sm-4" style="padding-right: 0" >
        <!--<button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/osl_file" id="osl_file" class="box waves-effect waves-teal waves-block btn btn-info" style="padding: 10px;margin-top: 0;width: 100%; font-size: 17px; background-image: linear-gradient(to left bottom, #012d92, #0062be, #0091cd, #00bcc8, #51e3bd);"><b>Apple OSL File</b></button>-->
        <button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/osl_file" id="osl_file" class="box waves-effect waves-teal waves-block btn btn-info" style="padding: 10px;margin-top: 0;width: 100%; font-size: 17px; background-image: linear-gradient(to right top, #162f76, #0061a7, #0093c5, #00c3d1, #5bf2d2);"><b>Apple OSL File</b></button>
    </div>
    <div class="col-md-2 col-sm-4" style="padding-right: 0" >
        <button type="submit" formmethod="POST" formaction="<?php echo base_url()?>Master/sale_return_file" id="ret_file" class="box waves-effect waves-teal waves-block btn btn-warning " style="padding: 10px;margin-top: 0;width: 100%; font-size: 17px;  background-image: linear-gradient(to right top, #101c6b, #194390, #2c6bb3, #4a94d4, #70bef2);"> <b>Apple RET File</b></button>

    </div><div class="clearfix"></div>
</form>
<?php include __DIR__ . '../../footer.php'; ?>