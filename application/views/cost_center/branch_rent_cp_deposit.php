<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>

<style>
    .fixheader {
        /*background-color: #fbf7c0;*/
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixheader1 {
        position: sticky;
        top: 30px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixleft{
        position: sticky;
        left:0px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        background-color: #c6e6f5;

    }
    .fixleft1{
        position: sticky;
        left:45px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        background-color: #c6e6f5;

    }
    .fixleft2{
        position: sticky;
        left:150px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        background-color: #c6e6f5;

    }
    .textcenter{
      text-align: center;
  }
  
  .table{
      border-collapse: separate;
      border-spacing: 0;
  }
  .borderleft{
      border-left: 1px solid #999999;
  }
</style>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Deposit Amount Details </h3></center></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>
<div  style="padding: 20px 10px; margin: 0">
    <div class="col-md-2"><b>Cost Header</b></div>
    <div class="col-md-3">
        <select name="idcostheader" class="form-control" id="idcostheader">
            <option value="">Select Cost Header</option>
            <option value="1">Rent Deposit</option>
            <option value="2">Channel Partner</option>
        </select>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary" id="btnreport">Filter</button>
    </div>
    <div class="clearfix"></div><hr>
    <div class="col-md-4 col-sm-4 col-xs-4 ">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
        <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_costing_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div> 
    <div class="clearfix"></div><br>
    <div id="report_data">
    </div>
</div>
<div class="clearfix"></div>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idcostheader = $('#idcostheader') .val();

            if(idcostheader != ''){
             $.ajax({
                url:"<?php echo base_url() ?>get_branch_rent_cp_deposit_data",
                method:"POST",
                data:{idcostheader: idcostheader},
                success:function(data)
                {
                    $('#report_data').html(data);
                }
            });
         }else{
            alert("Select Branch Costing Header ! ");
            return false;
        }
    });

        $(document).on("click", "#save_data", function (event) {
            var formData = new FormData($('#form_cp_rent_data')[0]);
            $('#save_data').attr('disabled', true).html('Loading');
            $.ajax({
                url:base_url + 'save_branch_rent_cp_details',
                type: "POST",
                data: formData,
                async: true,
                dataType:"JSON",
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                {
                    if(response.status)
                    {
                        alert(response.message);
                        setTimeout(function(){
                            window.location.href = base_url + 'branch_rent_cp_deposit';
                        },2000);
                    }else{
                        alert(response.message);
                    }
                    $('#save_data').attr('disabled', false).html('Save');
                }
            });
            return false;
            
        });
         $(document).on("click", ".approve_branch", function (event) {
            var approve_branch_id=$(this).attr('data-id');
            var formData = new FormData();
                formData.append('approve_branch_id',approve_branch_id);
            $.ajax({
                url:base_url + 'approve_cofo_branch',
                type: "POST",
                data: formData,
                async: true,
                dataType:"JSON",
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                {
                    if(response.status)
                    {
                        alert(response.message);
                        setTimeout(function(){
                            window.location.href = base_url + 'branch_rent_cp_deposit';
                        },2000);
                    }else{
                        alert(response.message);
                    }
                    $('#save_data').attr('disabled', false).html('Save');
                }
            });
         });
          $(document).on("click", ".receive_deposit", function (event) {
            var approve_branch_id=$(this).attr('data-id');
             var remark=$(this).parent().parent().find('.remark').val();
            var formData = new FormData();
                formData.append('approve_branch_id',approve_branch_id);
                
                  formData.append('remark',remark);
            $.ajax({
                url:base_url + 'receive_cofo_branch_deposit',
                type: "POST",
                data: formData,
                async: true,
                dataType:"JSON",
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                {
                    if(response.status)
                    {
                        alert(response.message);
                        setTimeout(function(){
                            window.location.href = base_url + 'branch_rent_cp_deposit';
                        },2000);
                    }else{
                        alert(response.message);
                    }
                    $('#save_data').attr('disabled', false).html('Save');
                }
            });
         });
         $(document).on("click", ".pay_deposit", function (event) {
            var approve_branch_id=$(this).attr('data-id');
            var formData = new FormData();
            var deposit_paid_amt=$(this).parent().parent().find('.deposit_paid_amt').val();
            var deposit_paid_date=$(this).parent().parent().find('.deposit_paid_date').val();
            var trans_id=$(this).parent().parent().find('.trans_id').val();
            var remark=$(this).parent().parent().find('.remark').val();
                formData.append('approve_branch_id',approve_branch_id);
                formData.append('deposit_paid_amt',deposit_paid_amt);
                formData.append('deposit_paid_date',deposit_paid_date);
                formData.append('trans_id',trans_id);
                formData.append('remark',remark);
            $.ajax({
                url:base_url + 'pay_branch_deposit',
                type: "POST",
                data: formData,
                async: true,
                dataType:"JSON",
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                {
                    if(response.status)
                    {
                        alert(response.message);
                        setTimeout(function(){
                            window.location.href = base_url + 'branch_rent_cp_deposit';
                        },2000);
                    }else{
                        alert(response.message);
                    }
                    $('#save_data').attr('disabled', false).html('Save');
                }
            });
         });
    });
     
    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    });
</script>
<?php include __DIR__.'../../footer.php'; ?>