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

        $('#idcostheader').change(function (){
            var idtype = $('#idcostheader option:selected').attr('idtype');
            $('#type_value').val('0');
            if(idtype == 0){
                $('#idtypeval').css("display", "none");
            }else{
                $('#idtypeval').css("display", "block");
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
                url:"<?php echo base_url() ?>Costing/ajax_get_branch_costing_data_for_dowenloadxl",
                method:"POST",
                data:{idcostheader: idcostheader, monthyear: monthyear, acc_data: acc_data, type_value: type_value, idtype: idtype},
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
            // var upload_file = $('#upload_file').val();
            // var upload_file = $('#upload_file')[0].files[0];
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Add Branch Costing Data </h3></center></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>
<div  style="padding: 20px 10px; margin: 0">
    <input type="hidden" name="infield" id="infield">
    <div class="col-md-2"><b>Month</b>
        <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear">
    </div>
    <div class="col-md-3"><b>Cost Header</b>
        <select name="idcostheader" class="form-control" id="idcostheader">
            <option value="">Select Cost Header</option>
            <?php foreach ($costing_headers as $cheader){  ?>
                <option value="<?php echo $cheader->id_cost_header; ?>" idtype="<?php echo $cheader->idtype?>"><?php echo $cheader->cost_header_name;?></option>
            <?php  } ?>
        </select>
    </div>
    <div class="col-md-2" id="idtypeval" style="display: none"><b>Value</b>
        <input type="text" class="form-control" name="type_value" id="type_value" value="0">
    </div>
    <div class="col-md-2"><br>
        <button class="btn btn-primary" id="btnreport">Filter</button>
    </div>
    <div class="col-md-2"><br>
        <button class="btn btn-primary" id="btnreportt" onclick="javascript:xport.toCSV('branch_costing_data');">Download Format</button>
    </div>
    <div class="col-md-2"><br>
        <button class="btn btn-primary" id="btnreporttt" data-toggle="modal" data-target="#edit2">Upload Expense Sheet</button>
    </div>
    
    <div class="clearfix"></div><hr>

    <div class="clearfix"></div><br>
    <div id="report_data">
    </div>
</div>
<div class="clearfix"></div>

<div class="modal fade" id="edit2" style="z-index: 999999; display: none; padding-right: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="excel_upload_form">    
                <div class="modal-body">
                    <div class="thumbnail">
                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span>Upload File</h4></center><hr>

                        <label class="col-md-3 col-md-offset-1">Select File</label>
                        <div class="col-md-7">
                            <input type="file" class="form-control" name="upload_file" id='upload_file' required="">
                        </div><div class="clearfix"></div><br>

                        <a data-dismiss="modal" class="btn btn-warning waves-effect simple-tooltip">Cancel</a>
                        <button type="button" name="upload-data" id="upload-data" class="pull-right btn btn-info waves-effect">Upload</button>
                        <div class="clearfix"></div>    
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>