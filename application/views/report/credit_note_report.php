<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('.btnsubmit').click(function(){
            var idcompany = $('#idcompany').val();
            var from = $('#from').val();
            var to = $("#to").val();
            if(from != '' && to != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Report/ajax_get_credit_note_report",
                    method:"POST",
                    data:{idcompany : idcompany, from: from, to: to},
                    success:function(data)
                    {
                        $("#credit_note").html(data);
                    }
                });
            }else{
                alert("Select date Range");
                return false;
            }
        });
    });
</script>
<style>
 .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
        font-size: 15px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        z-index: 9;
    }
</style>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-file-document fa-lg"></span>Credit Note Report</h3></center></div><div class="clearfix"></div><br>
<div>
    <div class="col-md-2">
        <select class="form-control chosen-select" name="idcompany" id="idcompany">
            <option value="0">All Company</option>
            <?php foreach($comapny_data as $company){ ?>
                <option value="<?php echo $company->company_id; ?>"><?php echo $company->company_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="from" name="from" >
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="to" name="to" >
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-info btnsubmit ">Submit</button>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>     
    <div class="col-md-1 col-sm-1 col-xs-1 pull-right"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('credit_note_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div>
    <div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <div id="credit_note" style="overflow-x: auto;height: 700px">
        
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>