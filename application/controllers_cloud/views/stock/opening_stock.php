<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.submit_btn').click(function (){
//            var idgodown = $('#idgodown') .val();
            var idbranch = $('#idbranch') .val();
            if( idbranch != ''){
                if(!confirm('Do You Want To Upload File')) {
                    return false;
                }
            }else{
                alert("Select Godown And Branch");
            }
        });
        $('#btnreport').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var branchid = $('#branch').val();
            var branches = $('#branches').val();
            if(from !='' && to !='' && branchid !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Stock/ajax_opening_stock_data",
                    method:"POST",
                    data:{from: from, to: to, branchid: branchid, branches: branches},
                    success:function(data)
                    {
                        $('#report_data').html(data);
                    }
                });
           }else{
               alert("Select Date Range");
               return false;
           }
        });
          $('#btnqtystock').click(function (){
            $('#qtydata').show();
        });
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
    <div class="col-md-1">
        <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
    </div><div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
               
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="mdi mdi-file-excel" style="font-size: 28px"></span> Upload CSV File </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/opening_stock.jpg" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <?php echo form_open_multipart('Stock/upload_opening_stock_excel', array('id' => 'pay')) ?>     
                <center><h4>Imei Excel Upload</h4></center>
                    <div class="col-md-8"><a style="color: #003eff;cursor: pointer" onclick="javascript:xport.toCSV('Opening_stock_excel_format');">Click To Download CSV Upload Format</a></div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-3"><b>Upload Excel </b></div>
                     <div class="col-md-9">
                         <input type="file" name="uploadfile" id="uploadfile" required="">
                    </div>
                    <div class="clearfix"></div><br>
                    <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                        <div class="col-md-2">
                            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
                        </div>
                    <?php }else { ?>
                    <div class="col-md-3"><b>Branch</b></div>
                    <div class="col-md-9">
                        <select class="form-control chosen-select" name="idbranch" id="idbranch" required="">
                            <option value="">Select Branch</option>
                            <?php foreach ($branch_data as $branch){ ?>
                                <option value="<?php echo $branch->id_branch?>"><?php echo $branch->branch_name;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <?php  } ?>
                    <div class="clearfix"></div><br>
    <!--                <div class="col-md-3"><b>Godown</b></div>
                    <div class="col-md-9">
                        <select class="form-control" name="idgodown" id="idgodown" >
                            <option value="">Select Godown</option>
                            <?php foreach ($godown_data as $godown){ ?>
                            <option value="<?php echo $godown->id_godown?>"><?php echo $godown->godown_name;?></option>
                            <?php } ?>
                        </select>
                    </div>-->
               
                <div class="clearfix"></div><hr>
                <!--<a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>-->
                 <a class="btn btn-warning" id="btnqtystock">Click Qty Stock Upload </a>
                <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Stock/upload_opening_stock_excel">Submit</button>
                <div class="clearfix"></div><br>
                <?php echo form_close(); ?>
                <div class="clearfix"></div>
                <div id="qtydata" style="display: none">
                    <div class="col-md-8"><a style="color: #003eff;cursor: pointer" onclick="javascript:xport.toCSV('Qty_Opening_stock_excel_format');">Click To Download Qty CSV Upload Format</a></div>
                    <div class="clearfix"></div><br>
                    <!--<center><h4>Qty Excel Upload</h4></center>-->
                    <form enctype="multipart/form-data">
                    <div class="col-md-4"><b>Upload Qty Excel </b></div>
                    <div class="col-md-8">
                         <input type="file" name="qtyfile" id="qtyfile" required="">
                    </div>
                    <div class="clearfix"></div><br>
                    <button type="submit" class="btn btn-info pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Stock/upload_qty_stock_excel">Qty Upload</button>
                    </form>
                </div>
                <div class="clearfix"></div><br>
                <div style="display: none">
                    <table id="Opening_stock_excel_format">
                        <thead>
                            <th>Model Name</th>
                            <th>Godown Name</th>
                            <th>Imei 1</th>
                            <th>Imei 2</th>
                            <th>Serial No</th>
                        </thead>
                    </table> 
                    <table id="Qty_Opening_stock_excel_format">
                        <thead>
                            <th>Idbranch</th>
                            <th>Model Name</th>
                            <th>Qty</th>
                        </thead>
                    </table> 
                </div>
            </div>
        </div>
       
        <div class="clearfix"></div><br>
<!--        <div class="col-md-2">
            <input type="text" placeholder="Select From date" class="form-control" name="from" id="from" data-provide="datepicker">
        </div>
        <div class="col-md-2">
            <input type="text" placeholder="Select To date" class="form-control" name="to" id="to" data-provide="datepicker">
        </div>
        <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="branch" name="branch" value="<?php echo $_SESSION['idbranch']?>">
        <?php } else { ?>
            <div class="col-md-2">
                <select class="form-control" name="branch" id="branch">
                    <option value="0">All Branch</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch?>"><?php echo $branch->branch_name;?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
        <?php } ?>
        <div class="col-md-1"><button type="submit" id="btnreport" class="btn btn-info">Submit</button></div>
         <div class="col-md-3 col-sm-3 col-xs-3 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-primary btn-sm pull-right " onclick="javascript:xport.toCSV('opening_stock_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div>
        <div class="clearfix"></div><br>
        
        <div id="report_data"></div>-->
    </div>
<?php include __DIR__.'../../footer.php'; ?>