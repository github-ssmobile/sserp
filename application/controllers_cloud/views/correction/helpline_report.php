<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnsubmit').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var idstatus = $('#idstatus').val();
            if(from !='' && to !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Correction/ajax_get_helpline_report",
                    method:"POST",
                    data:{from: from, to: to, idbranch: idbranch, idstatus: idstatus, branches: branches},
                    success:function(data)
                    {
                        $('#helpline_data').html(data);
                    }
                });
            }else{
                alert("Select Date Range");
                return false;
            }
        });
       
   });
   
</script>
<style>
  .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    

  }
  .fixleft1{
    position: sticky;
    left:80px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft2{
    position: sticky;
    left:140px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft3{
    position: sticky;
    left:180px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft4{
    position: sticky;
    left:255px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
</style>

<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-file-excel fa-lg"></span> Helpline Report</h3></center></div>
<div class="col-md-1">
    <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
</div><div class="clearfix"></div><hr>
<div class="clearfix"></div><br>
<div class="" style="padding: 0; margin: 0;">
    <div class="col-md-2"><b>From</b><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required></div>
    <div class="col-md-2"><b>To</b><input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required></div>
    <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
    <?php } else { ?>
        <div class="col-md-2"><b>Branch</b>
            <select class="form-control chosen-select" name="idbranch" id="idbranch">
                <option value="0">All Branch</option>
                <?php foreach($branch_data as $bdata){ ?>
                    <option value="<?php echo $bdata->id_branch?>"><?php echo $bdata->branch_name?></option>
                <?php $branches[] = $bdata->id_branch; } ?>
            </select>
        </div>
        <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
    <?php } ?>
    <div class="col-md-2"><b>Status</b>
        <select class="form-control chosen-select" name="idstatus" id="idstatus">
            <option value="">All Status</option>
            <option value="0">Pending</option>
            <option value="1">On Hold</option>
            <option value="2">Closed</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btnsubmit" style="margin-top: 10px;">Search</button>
    </div>
    <div class="clearfix"></div><br>
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
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
        <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('helpline_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div> 
    <div class="clearfix"></div><br>
    <div id="helpline_data" style="overflow-x: auto;height: 700px;"></div>
</div>

<?php include __DIR__.'../../footer.php'; ?>