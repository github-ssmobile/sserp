<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
       $('.btnedit').click(function (){
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            parentdiv.find('.editamount').prop('readonly',false);
            parentdiv.find('.status').prop('disabled',false);
            parentdiv.find('.btnedit').hide();
            parentdiv.find('.saveedit').show();
           
       }) ;
       
       $('.saveedit').click(function (){
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            var status = parentdiv.find(".status").val();
            var idbranch = parentdiv.find(".idbranch").val();
            
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/update_branch_expenseallowed_data'); ?>",
                data: {status: status, idbranch: idbranch},
                success: function(data){
                    alert("Branch Updated Successfully");
                    parentdiv.find('.status').prop('disabled',true);
                    parentdiv.find('.btnedit').show();
                    parentdiv.find('.saveedit').hide();
                }
            });
       })
     
    });
</script>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-bank fa-lg"></span>Branch Petty Cash</h3></center></div>
<!--<div class="col-md-1">
    <a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div>-->
<div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        
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
      
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('expense_allowed_branch_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail" style="padding: 0; overflow: auto;">
             <table class="table table-bordered table-condensed" id="expense_allowed_branch_data">
                <thead style="background-color: #99ccff;border: #6699ff">
                    <th><b>Sr.</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Expense Allowed</b></th>
                    <th><b>Action</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total=0; foreach ($branch_data as $bdata){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $bdata->branch_name; ?></td>
                        <td><div style="display: none"><?php if($bdata->expense_allowed == 1){ echo 'Allowed'; }else{ echo 'Not Allowed'; } ?></div>
                            <select class="form-control status" disabled>
                                 <option value="<?php echo $bdata->expense_allowed;?>"><?php if($bdata->expense_allowed == 1){ echo 'Allowed'; }else{ echo 'Not Allowed'; } ?></option>
                                <option value="1">Allowed</option>
                                <option value="0">Not Allowed</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" class="form-control idbranch" value="<?php echo $bdata->id_branch; ?>" >
                            <a class="btn btn-warning btn-sm btnedit "><center><i class="fa fa-pencil"></i></center></a>
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