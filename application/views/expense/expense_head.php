<?php include __DIR__.'../../header.php'; ?>

<script>
    $(document).ready(function (){
    
        $('.btnedit').click(function (){
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            parentdiv.find('.type1').prop('readonly',false);
            parentdiv.find('.need_approval1').prop('disabled',false);
            parentdiv.find('.status1').prop('disabled',false);
            parentdiv.find('.btnedit').hide();
            parentdiv.find('.saveedit').show();

        }) ;
        
        $('.saveedit').click(function (){
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            var type = parentdiv.find(".type1").val();
            var approval = parentdiv.find(".need_approval1").val();
            var status = parentdiv.find(".status1").val();
            var idexpensetype = parentdiv.find(".idexpensetype").val();
            if(type != ''){
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Expense/update_expense_head'); ?>",
                    data: {type: type, approval: approval, status: status, idexpensetype: idexpensetype},
                    success: function(data){
                        alert("Expense Type Updated Successfully");
                        parentdiv.find('.type1').prop('readonly',true);
                        parentdiv.find('.need_approval1').prop('disabled',true);
                        parentdiv.find('.status1').prop('disabled',true);
                        parentdiv.find('.btnedit').show();
                        parentdiv.find('.saveedit').hide();
                    }
                });
            }else{
                alert("Please Enter Expense Type");
                return false;
            }
       })
       
        
    })
</script>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-chemical-weapon fa-lg"></span> Expense Type </h3></center></div>
<div class="col-md-1">
    <a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>
</div><div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
          <?php echo form_open_multipart('Expense/save_expense_head', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Expense </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/expense.png" style="height: auto;width: 150px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                
                <div class="col-md-3"><b>Expense Type</b></div>
                <div class="col-md-9"><input type="text" class="form-control" placeholder="Enter Expense Type" name="type" required=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-3"><b>Need Approval</b></div>
                <div class="col-md-9">
                    <select class="form-control" name="need_approval">
                        <option value="0">NO</option>
                        <option value="1">YES</option>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-3"><b>Status</b></div>
                <div class="col-md-9">
                    <select class="form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0">In Active</option>
                    </select>
                </div>
                <div class="clearfix"></div><br>
            </div>
            <div class="clearfix"></div><hr>
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_expense_head">Submit</button>
        </div><div class="clearfix"></div>
        <?php echo form_close(); ?>
        <div class="clearfix"></div>
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
        <div class="col-md-2 col-md-offset-6">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('expense_head');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail"  style="padding: 0; overflow: auto;">
             <table id="expense_head" class="table table-bordered table-condensed" >
                <thead style="background-color: #99ccff;border: #6699ff">
                    <th><b>Sr.</b></th>
                    <th><b>Expense Head</b></th>
                    <th><b>Need Approval</b></th>
                    <th><b>Status</b></th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total=0; foreach ($expense_head as $head){  ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><div style="display: none"><?php echo $head->expense_type; ?></div><input type="text" class="form-control input-sm type1" readonly="" name="type1" value="<?php echo $head->expense_type; ?>"></td>
                        <td><div style="display: none"><?php if($head->need_approval == 1){ echo 'YES'; }else{ echo 'NO'; } ?></div>
                            <select class="form-control input-sm need_approval1"  name="need_approval1" disabled="true">
                                 <option value="<?php echo $head->need_approval; ?>"><?php if($head->need_approval == 1){ echo 'YES'; }else{ echo 'NO'; } ?></option>
                                <option value="0">NO</option>
                                <option value="1">YES</option>
                            </select>
                         </td>
                         <td><div style="display: none"><?php if($head->active == 1){ echo 'Active'; }else{ echo 'InActive'; }?></div>
                            <select class="form-control input-sm status1" name="status1" disabled="true">
                                <option value="<?php echo $head->active ?>"><?php if($head->active == 1){ echo 'Active'; }else{ echo 'InActive'; }?></option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" class="form-control idexpensetype" value="<?php echo $head->id_expense_head; ?>" >
                            <a class="btn btn-warning btn-sm btnedit"><span class="fa fa-pencil"></span></a>
                            <a class="btn btn-primary btn-sm saveedit" style="display: none">Submit</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>