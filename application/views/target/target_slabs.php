<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<script>
    $(document).ready(function (){
       $('#tar_per').change(function (){
           var monthyear = $('#monthyear').val();
           var per = +$('#tar_per').val();
           if(per != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Target/ajax_get_target_slab_data_bymonth",
                    method:"POST",
                    data:{monthyear: monthyear},
                    success:function(data)
                    {
                        var slab_per = +data;
                        var remain = 0;
                        var total = slab_per + per;
                        if(total > 100){
                            remain = 100 - slab_per;
                            alert(" Allow Max Input per(%) is "+remain);
                            $('#tar_per').val('');
                        }
                    }
                });
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-database-plus fa-lg"></span> Target Slabs Setup</h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Create Vendor"></a></div><div class="clearfix"></div><hr>
<div id="purchase">
    <form id="pay" class="collapse">
        <div class="col-md-8 col-md-offset-2" >
            <article role="login" class="" style="padding: 15px; border-radius: 10px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Target Slabs</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Month</label>
                <div class="col-md-7">
                    <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>" required>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Target Slab Name</label>
                <div class="col-md-7">
                    <input type="text" class="form-control input-sm" placeholder="Enter Slab Name" id="slab_name" name="slab_name">
                </div><div class="clearfix"></div><br>
                 <label class="col-md-3 col-md-offset-1">Target(%) </label>
                <div class="col-md-7">
                    <input type="text" name="tar_per" id="tar_per" class="form-control input-sm " placeholder="Enter Target Percentage" required>
                </div><div class="clearfix"></div><br>
               
                <label class="col-md-3 col-md-offset-1">From </label>
                <div class="col-md-7">
                    <input type="text" name="from" id="from" data-provide="datepicker" class="form-control input-sm " placeholder="From Date" required>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">To </label>
                <div class="col-md-7">
                    <input type="text" name="to" id="to" data-provide="datepicker" class="form-control input-sm " placeholder="To Date" required>
                </div><div class="clearfix"></div><br>
               
               
                <a class="btn btn-warning waves-effect gradient1" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Target/save_target_slabs_per') ?>" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
                <div class="clearfix"></div>
            </article><div class="clearfix"></div><br>
        </div>
    </form>
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
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('Target_Slab_Data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div><div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto">
        <table id="Target_Slab_Data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead style="background-color: #7cb6f4">
                <th>Sr</th>
                <th>Month</th>
                <th>Target Slab Name</th>
                <th>From</th>
                <th>To</th>
                <th>Target (%)</th>
                <th>Action</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($slab_data as $tdata){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $tdata->month_year; ?></td>
                    <td><?php echo $tdata->slab_name; ?></td>
                    <td><?php echo $tdata->from_date; ?></td>
                    <td><?php echo $tdata->to_date; ?></td>
                    <td><?php echo $tdata->target_per.'%'; ?></td>
                    <td>
                        <a class="btn btn-sm waves-effect" target="_blank" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Vendor</h4></center><hr>
                                            
                                           <label class="col-md-3 col-md-offset-1">Month</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control monthpick" placeholder="Select Month"  name="monthyear1"  value="<?php echo $tdata->month_year?>" required>
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">Slab Name</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-sm" placeholder="Enter Slab Name"  name="slab_name1" value="<?php echo $tdata->slab_name?>">
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">From </label>
                                            <div class="col-md-7">
                                                <input type="text" name="from1"  data-provide="datepicker" class="form-control input-sm " placeholder="From Date" value="<?php echo $tdata->from_date?>" required>
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">To </label>
                                            <div class="col-md-7">
                                                <input type="text" name="to1"  data-provide="datepicker" class="form-control input-sm " placeholder="To Date" value="<?php echo $tdata->to_date?>" required>
                                            </div><div class="clearfix"></div><br>

                                            <label class="col-md-3 col-md-offset-1">Target (%) </label>
                                            <div class="col-md-7">
                                                <input type="text" name="tar_per1" class="form-control input-sm " placeholder="Enter Target Percentage" value="<?php echo $tdata->target_per?>" required>
                                            </div><div class="clearfix"></div><br>
                                            
                                            <a data-dismiss="modal" class="btn btn-warning waves-effect simple-tooltip">Cancel</a>
                                            <button type="submit" formmethod="POST" value="<?php echo $tdata->id_target_slab  ?>" name="id" formaction="<?php echo base_url('Target/edit_target_slab_per') ?>" class="pull-right btn btn-info waves-effect">Submit</button>
                                            <div class="clearfix"></div>    
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div><div class="clearfix"></div>
</div>
<div class="clearfix"></div>
   
<?php include __DIR__.'../../footer.php'; ?>