<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
     $('#btnsubmit').click(function (){
         var from = $('#from').val();
         var to = $('#to').val();
         var idcompany = $('#idcompany').val();
         $('#sale_data').html('');
         if(from != '' && to != ''){
             $.ajax({
                url:"<?php echo base_url() ?>generate-e-invoice-bulk-data",
                method:"POST",
                data:{from: from, to: to, idcompany: idcompany},
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
     $(document).on("click", ".generate-inv", function (event) {
         var id_sale = $(this).attr('data-id');
         var data = { 'id_sale[]' : []};

         data['id_sale[]'].push($(this).attr("data-id"));

         if(id_sale != ''){
             $.ajax({
                url:"<?php echo base_url() ?>generate-e-invoice-b2b-sale",
                method:"POST",
                dataType:"JSON",
                data:data,
                success:function(data)
                {
                    alert(data.message);
                }
            });
         }else{
             alert("Sales Id Not empty");
             return false;
         }
     }); 

     $(document).on("click", ".generate-bulk", function (event) {

         var data = [];
         $("input:checked").each(function() {
          data.push($(this).attr("data-id"));
      });

         console.log(data);
         if(data != ''){
           $.ajax({
            url:"<?php echo base_url() ?>generate-e-invoice-b2b-sale",
            method:"POST",
            dataType:"JSON",
            data:{'id_sale':data},
            success:function(data)
            {
                alert(data.message);
                $('#btnsubmit').click();
            }
        });
       }else{
           alert("Sales Id Not empty");
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
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="fa fa-file-text-o  fa-lg"></span> Generate E-Invoice</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-2">
    <b>From</b>
    <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required="" placeholder="Date From">
</div>
<div class="col-md-2">
    <b>To</b>
    <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required="" placeholder="Date To">
</div>
<div class="col-md-2">
    <b>Company</b>
    <select class="form-control chosen-select" name="idcompany" id="idcompany">
        <option value="0">All Company</option>
        <?php foreach($company_data as $company){ ?>
            <option value="<?php echo $company->company_id; ?>"><?php echo $company->company_name; ?></option>
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
    <button type="button" class="btn btn-primary btn-sm generate-bulk"><span class="fa fa-file-excel-o"></span> Bulk Generate</button>
</div>
<div class="clearfix"></div><br>
<div id="sale_data" style="overflow-x: auto;height: 700px">

</div>
<?php include __DIR__.'../../footer.php'; ?>