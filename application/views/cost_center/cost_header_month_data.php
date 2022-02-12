<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<script>
    $(document).ready(function (){
        $('#monthyear').change(function (){
            var monthyear = $('#monthyear') .val();
            if(monthyear != '' ){
             $.ajax({
                url:"http://117.247.86.62:8088/ssweb/index.php/stock/sale/ajax_get_allbranch_qualified_sale_bymonth",
                       // url:"<?php echo base_url()?>Costing/get_branch_qualified_sale",
                       method:"POST",
                       datatype : "json",
                       data:{monthyear: monthyear},
                       success:function(data)
                       {
                           $('#infield').val(data);
                       }
                   });
         }
     });

           $('#btnreport').click(function (){
            var idcostheader = $('#idcostheader') .val();
            var monthyear = $('#monthyear') .val();
            var acc_data = $('#infield').val();
            var idtype = $('#idcostheader option:selected').attr('idtype');
            var type_value = $('#type_value').val();


            if(idcostheader != '' && monthyear != '' && type_value != ''){
             $.ajax({
                url:"<?php echo base_url() ?>ajax_get_costing_data_for_month",
                method:"POST",
                data:{idcostheader: idcostheader, monthyear: monthyear},
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

        $('#upload-data').click(function (){
            var idcostheader = $('#idcostheader').val();
            var monthyear = $('#monthyear').val();
            var acc_data = $('#infield').val();
            var idtype = $('#idcostheader option:selected').attr('idtype');
            var type_value = $('#type_value').val();

            var formData = new FormData($('#excel_upload_form')[0]);
            formData.append('idcostheader',idcostheader);
            formData.append('monthyear',monthyear);
            formData.append('acc_data',acc_data);
            formData.append('idtype',idtype);
            formData.append('type_value',type_value);

            if(idcostheader != '' && monthyear != '' && type_value != ''){

             $.ajax({
                url:"<?php echo base_url() ?>upload_expense_format",
                method:"POST",
                data:formData,
                contentType: false,
                processData: false,

                success:function(data)
                {
                    $('#report_data').html();
                    $('#edit2').modal('hide');
                    $('#report_data').html(data);
                }
            });
         }else{
            alert("Select Branch Costing Header ! ");
            return false;
        }
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Branch Costing Data Setup</h3></center></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>
<div  style="padding: 20px 10px; margin: 0">
    <input type="hidden" name="infield" id="infield">

    <div class="col-md-3"><b>Cost Header Type</b>
        <select name="idcostheader" class="form-control" id="idcostheader">
            <option value="">Select Cost Header Type</option>
            <option value="1">Percentage</option>
            <option value="2">Fix</option>
            <option value="3">Interest</option>

        </select>
    </div>
    <div class="col-md-2"><b>Month</b>
        <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear">
    </div>
    
    <div class="col-md-2"><br>
        <button class="btn btn-primary" id="btnreport">Filter</button>
    </div>
    
    <div class="clearfix"></div><hr>
    <div id="report_data">
    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).on("click", "#save_data", function (event) {

            var formData = new FormData($('#cost-header-month-data')[0]);
            formData.append('idcostheader',$('#idcostheader').val());
            formData.append('monthyear',$('#monthyear').val());
           console.log(formData);
            $('#save_data').attr('disabled', true).html('Loading');
            $.ajax({
                url:base_url + 'save_cost_header_month_data',
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
                            window.location.href = base_url + 'cost_header_month_data';
                        },1000);
                    }else{
                        alert(response.message);
                    }
                    $('#save_data').attr('disabled', false).html('Save');
                }
            });
            return false;
            
        });
</script>
<?php include __DIR__.'../../footer.php'; ?>