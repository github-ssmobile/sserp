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
            var amount = parentdiv.find(".editamount").val();
            var status = parentdiv.find(".status").val();
            var idpeticash = parentdiv.find(".idpetticash").val();
            
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/update_user_petticash_data'); ?>",
                data: {amount: amount, status: status, idpeticash: idpeticash},
                success: function(data){
                    alert("Petti Cash Updated Successfully");
                    parentdiv.find('.editamount').prop('readonly',true);
                    parentdiv.find('.status').prop('disabled',true);
                    parentdiv.find('.btnedit').show();
                    parentdiv.find('.saveedit').hide();
                  
                    var total =0;
                    
                     $('tr').each(function (){
                        $(this).find('.editamount').each(function (){
                            var tamt = $(this).val();
                            if(!isNaN(tamt) && tamt.length !== 0){
                                 total += parseFloat(tamt);
                            }
                        });
                    });
                     $('.total').html(total);            
                }
            });
       })
       
       $('#btnsubmit').click(function (){
            var iduser = $('#iduser').val();
            var amount = $('#amount').val();
            if(iduser == ''){
                alert("Select User");
                return false;
            }
            if(amount == '' || amount <=0){
                alert("Amount Should Be Greater Than 0");
                return false;
            }
            
       })
       
    });
</script>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-bank fa-lg"></span> User Petty Cash</h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <?php echo form_open_multipart('Expense/save_petty_cash', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add User Petty Cash </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/petty_cash.png" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <div class="col-md-2"><b>Date</b></div>
                <div class="col-md-10"><input type="text" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" readonly=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>User</b></div>
                <div class="col-md-10">
                    <select class="form-control" id="iduser" name="iduser">
                        <option value="">Select User</option>
                        <?php foreach ($user_data as $user){ ?>
                        <option value="<?php echo $user->id_users?>"><?php echo $user->user_name;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>Amount</b></div>
                <div class="col-md-10"><input type="number" class="form-control" id="amount" name="amount"></div>
                <div class="clearfix"></div><br><hr>
            </div>
            <div class="clearfix"></div><hr>
            
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right" id="btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_user_petty_cash">Submit</button>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
        <?php echo form_close(); ?>
        
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('user_petty_cash');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail"  style="padding: 0; overflow: auto;">
             <table id="user_petty_cash" class="table table-bordered table-condensed" >
                <thead style="background-color: #99ccff;border: #6699ff">
                    <th><b>Sr.</b></th>
                    <th><b>Date</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Amount</b></th>
                    <th><b>Status</b></th>
                    <th><b>Action</b></th>
                </thead>
                <tbody class="data_1">
                    <?php  $i=1; $total=0; foreach ($user_petty_cash_data as $petty){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('Y-m-d h:i:sA', strtotime($petty->entry_time)); ?></td>
                        <td><?php echo $petty->user_name; ?></td>
                        <td><div style="display: none"><?php echo $petty->amount; ?></div>
                            <input type="text" name="editamount"  class="form-control editamount" value="<?php echo $petty->amount; ?>" readonly>
                        </td>
                        <td><div style="display: none"><?php if($petty->status == 0){ echo 'Active'; }else{ echo 'InActive'; } ?></div>
                            <select class="form-control status" disabled>
                                <option><?php if($petty->status == 0){ echo 'Active'; }else{ echo 'InActive'; } ?></option>
                                <option value="0">Active</option>
                                <option value="1">InActive</option>
                            </select>
                        </td>
                        <td>
                            <?php $total = $total + $petty->amount;?>
                            <input type="hidden" class="form-control idpetticash" value="<?php echo $petty->id_user_petti_cash; ?>" >
                            <a class="btn btn-warning btn-sm btnedit "><center><i class="fa fa-pencil"></i></center></a>
                            <a class="btn btn-primary btn-sm saveedit" style="display: none">Submit</a>
                        </td>
                    </tr>
                    <?php  } ?>
                    <tr>
                        <td ></td>
                        <td ></td>
                        <td><b>Total</b></td>
                        <td><div class="total"><b><?php echo $total; ?></b></div></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>