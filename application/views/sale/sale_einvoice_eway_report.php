<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
     $('#btnsubmit').click(function (){

         var billtype = $('#billtype').val();
         $('#sale_data').html('');

         $.ajax({
            url:"<?php echo base_url() ?>e-invoice-eway-report-data",
            method:"POST",
            data:{billtype: billtype},
            success:function(data)
            {
                $('#sale_data').html(data);
            }
        });

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
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="fa fa-file-text-o  fa-lg"></span> EWAY and E-Invoice Report</h3></center></div><div class="clearfix"></div><hr><br>
<!-- <div class="col-md-2">
    <b>From</b>
    <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required="" placeholder="Date From">
</div>
<div class="col-md-2">
    <b>To</b>
    <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required="" placeholder="Date To">
</div> -->

<div class="col-md-2">
    <b>Bill Type</b>
    <select class="form-control chosen-select" name="billtype" id="billtype">
        <option value="0">EWAY</option>
        <option value="1">E Invoice with Eway</option>
        <option value="3">B2B E Invoice</option>
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

<div class="clearfix"></div><br>
<div id="sale_data" style="overflow-x: auto;height: 700px">

</div>
<?php include __DIR__.'../../footer.php'; ?>