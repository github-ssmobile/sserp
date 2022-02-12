<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       
       $('#wallet').click(function (){
           $('#wallet_type').show();
           $('#expense_header').hide();
           $('#expense_type').hide();
        });
       $('#ex_header').click(function (){
           $('#wallet_type').hide();
           $('#expense_header').show();
           $('#expense_type').hide();
        });
       $('#ex_type').click(function (){
           $('#wallet_type').hide();
            $('#expense_header').hide();
           $('#expense_type').show();
        });
   });
</script>
<style>
.blink_me {
  animation: blinker 2s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<div class="col-md-8"><center><h3 style="margin: 10px 0"><span class="fa fa-money fa-lg"></span> Expense Wallet </h3></center></div>
<div class="col-md-2">
</div>
<div class="col-md-1">
    <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
</div><div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <div class="col-md-2"></div>
        <button class="col-md-2" id="wallet" style="border: 1px solid #051937;border-radius: 10px; background-color: #ffffff;height: 60px;margin-right: 15px;padding: 10px;border-left: 6px solid #00649c;border-bottom: 6px solid #00649c">
            <center><b><h5>Add Wallet Type</b></h5></center>
        </button>
        <button class="col-md-2" id="ex_header" style="border: 1px solid #0090bc;border-radius: 10px; background-color: #ffffff;height: 60px;margin-right: 15px;padding: 10px;border-left: 6px solid #0090bc;border-bottom: 6px solid #0090bc">
            <center><b><h5>Add Expense Headers </b></h5></center>
        </button>
        <button class="col-md-2" id="ex_type" style="border: 1px solid #e2796f;border-radius: 10px; background-color: #ffffff;height: 60px;margin-right: 15px;padding: 10px;border-left: 6px solid #e2796f;border-bottom: 6px solid #e2796f">
            <center><b><h5>Add Expense Type</b></h5></center>
        </button>
        <div class="clearfix"></div><br>
        
        <!-----------Wallet Type---------------->
        
        <div  id="wallet_type" style="display: none">
            <div class="col-md-6 thumbnail  col-md-offset-2" style="border-radius: 8px">
                <?php echo form_open(); ?>
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Create Wallet Type</h4></center><hr>
                <div class="col-md-3"><b>Wallet Type</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="wallettype" required=""></div>
                <div class="clearfix"></div><br><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url()?>Expense_wallet/wallet_type"> Cancel</a>
                <button type="submit" class="btn btn-primary pull-right" id="btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Expense_wallet/save_expense_wallet">Submit</button>
                <div class="clearfix"></div>
                <?php echo form_close(); ?>
            </div>
            <div class="clearfix"></div><br>
        </div>
        
        
        <!-----------Expense Header---------------->
        
        <div id="expense_header" style="display: none">
            <div class="col-md-6 thumbnail  col-md-offset-2" style="border-radius: 8px">
                <?php echo form_open(); ?>
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Create Expense Header</h4></center><hr>
                <div class="col-md-3"><b>Expense Headers</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="exheaders" required=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-3"><b>Wallet Type</b></div>
                <div class="col-md-8">
                    <select class="form-control" required="" name="idwallet">
                        <option value="">Select Wallet Type</option>
                        <?php foreach ($wallet_type as $wtype){?>
                        <option value="<?php echo $wtype->id_wallet_type?>"><?php echo $wtype->wallet_type?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url()?>Expense_wallet/wallet_type"> Cancel</a>
                <button type="submit" class="btn btn-primary pull-right" id="btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Expense_wallet/save_expense_header">Submit</button>
                <div class="clearfix"></div>
                <?php echo form_close(); ?>
            </div>
             <div class="clearfix"></div><br>
        </div>
       
        
        <!-----------Expense Type---------------->
        
        <div id="expense_type" style="display: none">
            <div class="col-md-6 thumbnail  col-md-offset-2" style="border-radius: 8px">
                <?php echo form_open(); ?>
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Create Expense Type</h4></center><hr>
                <div class="col-md-3"><b>Expense Type</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="extype" required=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-3"><b>Expense Header</b></div>
                <div class="col-md-8">
                    <select class="form-control" required="" name="idheader">
                        <option value="">Select Expense Header</option>
                        <?php foreach ($expense_headers as $head){?>
                        <option value="<?php echo $head->id_expense_head ?>"><?php echo $head->expense_type?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url()?>Expense_wallet/wallet_type"> Cancel</a>
                <button type="submit" class="btn btn-primary pull-right" id="btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Expense_wallet/save_expense_subheader">Submit</button>
                <div class="clearfix"></div>
                <?php echo form_close(); ?>
            </div>
             <div class="clearfix"></div><br>
        </div>
        <div class="clearfix"></div><br>
    </div>
        <div class="col-md-2">
            <table class="table table-bordered table-condensed">
                <thead>
                <th colspan="2">Wallet Type</th>
                </thead>
                <thead>
                    <th>Sr.</th>   
                    <th>Wallet Type</th>  
                    <th>Edit</th>   
                </thead>
                <tbody>
                    <?php $sr=1; foreach ($wallet_type as $wtype){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $wtype->wallet_type?></td>
                        <td>
                            <a class="btn btn-floating btn-primary bnt-sm" data-toggle="modal" data-target="#edit<?php echo $sr ?>"><span class="fa fa-pencil"></span></a>
                            <div class="modal fade" id="edit<?php echo $sr ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" action="<?php echo base_url()?>Expense_wallet/edit_wallet_type">
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Wallet Type</h4></center><hr>

                                                <div class="col-md-10 col-md-offset-1">
                                                    <label class="col-md-3 col-md-offset-1">Wallet Type</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="wallet" value="<?php echo $wtype->wallet_type; ?>" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                </div>
                                                <a href="#edit<?php echo $sr ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $wtype->id_wallet_type ?>" name="idw"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
            <div class="clearfix"></div>
        </div>
    
        <!--Expense Header-->
         <div class="col-md-4">
            <table class="table table-bordered table-condensed">
                <thead>
                <th colspan="3">Expense Headers</th>
                </thead>
                <thead>
                    <th>Sr.</th>   
                    <th>Expense Header</th>   
                    <th>Wallet Type</th>   
                     <th>Edit</th> 
                </thead>
                <tbody>
                    <?php $i=1; foreach ($expense_headers as $head){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $head->expense_type?></td>
                        <td><?php echo $head->wallet_type?></td>
                        <td>
                            <a class="btn btn-floating btn-primary bnt-sm" data-toggle="modal" data-target="#edithead<?php echo $head->id_expense_head ?>"><span class="fa fa-pencil"></span></a>
                            <div class="modal fade" id="edithead<?php echo $head->id_expense_head ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" action="<?php echo base_url()?>Expense_wallet/edit_expense_header">
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Expense Header</h4></center><hr>
                                                <div class="col-md-12">
                                                    <label class="col-md-4 ">Expense Header</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="head" value="<?php echo $head->expense_type; ?>" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4 ">Wallet type</label>
                                                    <div class="col-md-7">
                                                        <select class="form-control" required="" name="idwallet">
                                                            <option value="<?php echo $head->idwallet?>"><?php echo $head->wallet_type?></option>
                                                        <?php foreach ($wallet_type as $wtype){?>
                                                        <option value="<?php echo $wtype->id_wallet_type?>"><?php echo $wtype->wallet_type?></option>
                                                        <?php } ?>
                                                    </select>
                                                    </div><div class="clearfix"></div><br>
                                                </div>
                                                <div class="clearfix"></div><hr>
                                                <a href="#edithead<?php echo $head->id_expense_head ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $head->id_expense_head ?>" name="idh"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
            <div class="clearfix"></div>
        </div>
        
        <!--Expense Subheader-->
        
         <div class="col-md-6">
             <div class="thumbnail" style="padding: 0">
                 <table class="table table-condensed" style="margin-bottom: 0">
                <thead>
                <th colspan="4">Expense Type(subheader)</th>
                </thead>
                <thead>
                    <!--<th>Sr.</th>-->   
                    <th>Expense Type</th>   
                    <th>Expense Header</th>   
                    <th>Wallet Type</th>   
                    <th>Edit</th>   
                </thead>
                <tbody>
                    <?php $sr=1; foreach ($expense_subheaders as $sub){ ?>
                    <tr>
                        <!--<td><?php // echo $sr++; ?></td>-->
                        <td><?php echo $sub->expense_subheader?></td>
                        <td><?php echo $sub->expense_type?></td>
                        <td><?php echo $sub->wallet_type?></td>
                        <td>
                             <a class="btn btn-floating btn-primary bnt-sm" data-toggle="modal" data-target="#editsubhead<?php echo $sub->id_expense_subheader ?>"><span class="fa fa-pencil"></span></a>
                            <div class="modal fade" id="editsubhead<?php echo $sub->id_expense_subheader ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" action="<?php echo base_url()?>Expense_wallet/edit_expense_subheader">
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Expense Header</h4></center><hr>

                                                <div class="col-md-11 col-md-offset-1">
                                                    <label class="col-md-4 ">Expense SubHeader</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" name="subhead" value="<?php echo $sub->expense_subheader; ?>" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Expense Header</label>
                                                    <div class="col-md-7">
                                                        <select class="form-control" required="" name="idheader">
                                                            <option value="<?php echo $sub->id_header?>"><?php echo $sub->expense_type ?></option>
                                                            <?php foreach ($expense_headers as $head){?>
                                                             <option value="<?php echo $head->id_expense_head ?>"><?php echo $head->expense_type?></option>
                                                             <?php } ?>
                                                    </select>
                                                    </div><div class="clearfix"></div><br>
                                                </div>
                                                <a href="#editsubhead<?php echo $sub->id_expense_subheader?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $sub->id_expense_subheader ?>" name="idsub"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
                 </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        

<?php include __DIR__.'../../footer.php'; ?>