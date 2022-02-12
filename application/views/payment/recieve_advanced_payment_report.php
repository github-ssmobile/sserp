<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
          $('#advanced_payment_data').DataTable();
        $(document).on("click", "#filter_btn", function (event) {
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var idstatus = $('#idstatus').val();
            $.ajax({
                url: "<?php echo base_url() ?>Payment/ajax_get_advance_payment_received_report",
                method: "POST",
                data:{datefrom:datefrom, dateto:dateto, idbranch: idbranch,branches:branches,idstatus:idstatus},
                success: function (data)
                {
                    $('#advanced_payment_data').html(data);
                }
            });
        });
    }); 
</script>
<style>
    /*.recon0_0{ recon pending/pending*/
        /*background-color: #ffebee;*/
    /*}*/
/*    .recon01{ recon pending/sale
        background-color: #005bc0;
    }*/
/*    .recon02{ recon pending/refund
        background-color: #ffccff;
    }*/
    .recon1_0{ /*recon done/claim pending*/
        background-color: #fcfdef;
    }
    .recon1_1{ /*recon done/sale*/
        background-color: #eaedff;
    }
    .recon1_2{ /*recon done/claim refund*/
        background-color: #ffebee;
    }
</style>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-image-filter-tilt-shift fa-lg"></span> Advanced Payment Received Report</h3></center></div>
<div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-1">Status</div>
<div class="col-md-2">
    <select class="form-control" id="idstatus">
        <option value="all">All</option>
        <option value="0">Pending</option>
        <option value="1">Done</option>
        <option value="2">Refund</option>
    </select>
</div>
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Branch</div>
<div class="col-md-3">
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php $branches[] = $branch->id_branch; } ?>
    </select>
</div>
<input type="hidden" name="branches" id="branches" value="<?php echo implode(',',$branches) ?>">
<?php } ?>
<div class="col-md-2"><button class="btn btn-primary btn-sm waves-effect" id="filter_btn"><i class="fa fa-filter"></i> Filter</button></div>
<div class="col-md-2">
        <button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('advanced_payment_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div>
<!--<div class="col-md-3">
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Payment Status</option>
        <option value="">All</option>
        
    </select>
</div>-->

<div class="clearfix"></div><br>
<table class="table table-condensed table-bordered" id="advanced_payment_data">
    <thead style="background: #49c5bf;">
        <th>Sr</th>
        <th>Date</th>
        <th>Product</th>
        <th>Sales Promoter</th>
         <th>Customer</th>
        <th>Contact</th>
        <th>Payment head</th>
        <th>Payment type</th>
        <th>Amount</th>
        <th>Branch Name</th>
        <th>Entry Time</th>
        <th>Days Diff</th>
        <th>Remark</th>
        <th>Reconciliation</th>
        <th>Inv No</th>
        <th>Inv Date</th>
        <th>Status</th>
        <th>Refund</th>
        <th>Print</th>
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; $total_amt=0; foreach($cash_payment_data as $cash_payment){ ?>
        <tr class="recon<?php echo $cash_payment->payment_receive.'_'.$cash_payment->claim ?>">
            <td><?php echo $i; ?></td>
            <td><?php echo date('d-m-Y', strtotime($cash_payment->date)) ?></td>
            <td><?php echo $cash_payment->full_name ?></td>
            <td><?php echo $cash_payment->sales_person ?></td>
            <td><?php echo $cash_payment->cust_fname.' '.$cash_payment->cust_lname ?></td>
            <td><?php echo $cash_payment->cust_contact ?></td>
            <td><?php echo $cash_payment->payment_head; ?></td>
            <td><?php echo $cash_payment->payment_mode; ?></td>
            <td>
                <?php echo $cash_payment->amount ?>
                <input type="hidden" class="ref_amount" value="<?php echo $cash_payment->amount ?>" />
            </td>
            <td><?php echo $cash_payment->branch_name ?></td>
            <td><?php echo $cash_payment->entry_time ?></td>
            <td><?php $now = time(); // or your date as well
                $your_date = strtotime($cash_payment->entry_time);
                $datediff = $now - $your_date;
                echo round($datediff / (60 * 60 * 24)); ?></td>
            <td><?php echo $cash_payment->remark ?></td>
            <td><?php if($cash_payment->payment_receive){ echo 'Done'; }else{ echo 'Pending'; }?></td>
            <?php if($cash_payment->claim == 1){ ?>
            <td><a href="<?php echo base_url('Sale/sale_details/'.$cash_payment->idsale) ?>" class="waves-effect" style="color: #005bc0"><?php echo $cash_payment->inv_no ?></a></td>
            <td><?php echo date('d-m-Y', strtotime($cash_payment->inv_date)) ?></td>
            <td>Sale</td>
            <td>-</td>
            <?php }elseif($cash_payment->claim == 0 && $cash_payment->payment_receive){ ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <?php }elseif($cash_payment->claim == 2){ ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>Refund<br>Remark: <?php echo $cash_payment->refund_remark ?></td>
            <?php }else{ ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <?php } ?>
            <td><a href="<?php echo base_url()?>Payment/advance_booking_received_receipt/<?php echo $cash_payment->id_advance_payment_receive ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
        <?php $i++; } ?>
    </tbody>
</table>
<?php require_once 'customer_master.php'; ?>
<?php include __DIR__ . '../../footer.php'; ?>