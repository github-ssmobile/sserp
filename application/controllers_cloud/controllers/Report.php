<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Sale_model');
        $this->load->model('General_model');
        $this->load->model('Report_model');
        $this->load->model('Reconciliation_model');
    }
    public function sale_payment_report() {
        $q['tab_active'] = 'Report';
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $q['payment_head'] = $this->General_model->get_payment_head_data();
        $q['payment_mode'] = $this->General_model->get_payment_mode_data();
        $this->load->view('report/sale_payment_report', $q);
    }
    public function daybook_report() {
        $q['tab_active'] = 'Reports';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $this->load->view('report/daybook_report', $q);
    }
    public function cash_deposit_report() {
//        level 1 = admin, 2 = idbranch, 3 = user_has_branch
        $q['tab_active'] = 'Reports';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/cash_deposit_report', $q);
    }
    public function cash_reconciled_report() {
        $q['tab_active'] = 'Reports';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $this->load->view('report/cash_reconciled_report', $q);
    }
    public function cheque_reconciled_report() {
        $q['tab_active'] = 'Reports';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $this->load->view('report/cheque_reconciled_report', $q);
    }
    public function cash_closure_report() {
        $q['tab_active'] = 'Reports';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/cash_closure_report', $q);
    }
    public function cash_ledger_report() {
        $q['tab_active'] = 'Reports';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/cash_ledger_report', $q);
    }
    public function cheque_bounce_report() {
        $q['tab_active'] = 'Reports';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/cheque_bounce_report', $q);
    }
    public function bank_reconciled_report() {
        $q['tab_active'] = 'Reports';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['payment_mode'] = $this->General_model->get_payment_modes_for_reconciliation();
        $q['active_bank'] = $this->General_model->get_active_bank();
        $this->load->view('report/bank_reconciled_report', $q);
    }
    public function bank_reconciliation_report(){
       $q['tab_active'] = 'Report';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        if($_SESSION['idrole'] == 23){
            $q['payment_mode'] = $this->General_model->get_payment_modes_by_user($iduser);
        }else{
            $q['payment_mode'] = $this->General_model->get_payment_modes_for_reconciliation();
        }
        $this->load->view('reconciliation/bank_reconciliation_report', $q);
    }
    public function ajax_cash_reconciled_report() {
//        die(print_r($_POST));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $idbranches = $this->input->post('branches');
        $cash_deposit= $this->Reconciliation_model->ajax_get_cash_reconciled_report($idbranch,$idbranches,$datefrom,$dateto);
        if(count($cash_deposit) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>';
        }else{ ?>
        <thead class="fixedelement">
            <th>Date</th>
            <th>Branch</th>
            <th>Deposit Bank</th>
            <th>Deposit Cash</th>
            <th>Reconciled Cash</th>
            <th>Short Receive</th>
            <th>Received Date</th>
            <th>UTR</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($cash_deposit as $deposit) { ?>
            <form class="ajax_recon_form">
            <tr>
                <td><?php echo date('d-m-Y', strtotime($deposit->date)) ?></td>
                <td><?php echo $deposit->branch_name ?></td>
                <td><?php echo $deposit->bank_name ?></td>
                <td><?php echo $deposit->deposit_cash ?></td>
                <td><?php echo $deposit->received_amount ?></td>
                <td><?php echo $deposit->short_receive ?></td>
                <td><?php echo $deposit->received_date ?></td>
                <td><?php echo $deposit->received_utr ?></td>
            </tr>
            </form>
            <?php } ?>
        </tbody>
    <?php }}
    
    public function ajax_cheque_reconciled_report() {
//        die(print_r($_POST));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $idbranches = $this->input->post('branches');
        $cash_deposit= $this->Reconciliation_model->ajax_get_cheque_reconciled_report($idbranch,$idbranches,$datefrom,$dateto,2);
        if(count($cash_deposit) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }else{ ?>
        <thead class="fixedelement">
            <th>Invoice Date</th>
            <th>Branch</th>
            <th>Inv No</th>
            <th>Customer Bank</th>
            <th>Cheque No</th>
            <th>Expencted Amount</th>
            <th>Deposit Bank</th>
            <th>Reconciled Amount</th>
            <th>Short Receive</th>
            <th>UTR No</th>
            <th>Received Date</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($cash_deposit as $deposit) { ?>
            <form class="ajax_recon_form">
            <tr>
                <td><?php echo date('d-m-Y', strtotime($deposit->date)) ?></td>
                <td><?php echo $deposit->branch_name ?></td>
                <td><?php echo $deposit->inv_no ?></td>
                <td><?php echo $deposit->customer_bank_name ?></td>
                <td><?php echo $deposit->transaction_id ?></td>
                <td><?php echo $deposit->amount ?></td>
                <td><?php echo $deposit->bank_name ?></td>
                <td><?php echo $deposit->received_amount ?></td>
                <td><?php echo $deposit->pending_amt ?></td>
                <td><?php echo $deposit->utr_no ?></td>
                <td><?php echo $deposit->transfer_date ?></td>
            </tr>
            </form>
            <?php } ?>
        </tbody>
    <?php }
    }
    public function ajax_get_sale_payment() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $head_name = $this->input->post('head_name');
//        $credit_report = $this->Sale_model->ajax_get_sale_receivables($idpayment_head,$idpayment_mode,$idbranch); 
        $credit_report = $this->Sale_model->ajax_get_sale_receivables_without_receive($idpayment_head,$idpayment_mode,$idbranch); ?>
        <thead class="bg-info">
            <th>Sr</th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Date_time</th>
            <th>Branch</th>
            <th>Payment Head</th>
            <th>Payment Mode</th>
            <th>Custody Product</th>
            <th>Total Amount</th>
            <th>Txn No</th>
            <th>Approved By</th>
            <th>Received Amount</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($credit_report as $credit){ $credit_amt = $credit->amount - $credit->received_amount;  ?>
            <tr>
                <td><?php echo $i++; ?> </td>
                <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                <td>'<?php echo date('d-m-Y h:i a', strtotime($credit->entry_time)) ?></td>
                <td><?php echo $credit->branch_name ?></td>
                <td><?php echo $head_name ?></td>
                <td><?php echo $credit->payment_mode ?></td>
                <td><?php echo $credit->product_model_name ?></td>
                <td><?php echo $credit->amount ?></td>
                <td><?php echo $credit->transaction_id ?></td>
                <td><?php echo $credit->approved_by ?></td>
                <td><?php echo $credit->received_amount; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    <?php 
    }
    
    public function ajax_cash_closure_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $closure_report = $this->Report_model->ajax_cash_closure_report($datefrom,$dateto,$idbranch); 
        if($closure_report) {
        ?>
        <table id="cash_closure_report<?php echo date('d-m-Y') ?>" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        <thead>
            <th>Sr</th>
            <th>Entry Date</th>
            <th>Branch</th>
            <th>Reference Id</th>
            <th>Closure Amount</th>
            <th>Remark</th>
            <th>Actual Entry time</th>
<!--            <th>Status</th>
            <th>Deposit Date</th>-->
            <th>Info</th>
        </thead>
        <tbody id="myTable">
            <?php $i=1; foreach($closure_report as $cash_closure){ ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $cash_closure->entry_time ?></td>
                <td><?php echo $cash_closure->branch_name ?></td>
                <td><?php echo $cash_closure->idcombine ?></td>
                <td><?php echo $cash_closure->closure_cash ?></td>
                <td><?php echo $cash_closure->remark ?></td>
                <td><?php echo $cash_closure->actual_entry_time ?></td>
<!--                <td><?php // if($cash_closure->status == 0){ echo "Pending"; }else{ echo "Deposited"; } ?></td>
                <td><?php echo $cash_closure->deposit_date ?></td>-->
                <td><a target="_blank" class="btn btn-primary btn-floating" href="<?php echo base_url()?>Report/cash_closer_details/<?php echo $cash_closure->id_cash_closure?>"><span class="fa fa-info"></span></a></td>
            </tr>
            <?php $i++; } ?>
        </tbody>
        </table>
        <?php }else{?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
    }
    
    public function cash_closer_details($id){
         $q['tab_active'] = 'Reports';
        $q['closer_data'] = $this->Report_model->closer_details_byid($id);
        $this->load->view('report/closure_denomination_details', $q);
    }

    public function ajax_cash_deposit_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $deposit_report = $this->Report_model->ajax_cash_deposit_report($datefrom,$dateto,$idbranch);
        ?>
        <thead>
            <th>Sr</th>
            <th>Date</th>
            <th>Branch</th>
            <th>Deposit Amount</th>
            <th>Bank</th>
            <th>Remark</th>
            <th>Status</th>
        </thead>
        <tbody id="myTable">
            <?php $i=1; $total_amt=0; foreach($deposit_report as $cash_deposit){ ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $cash_deposit->entry_time ?></td>
                <td><?php echo $cash_deposit->branch_name ?></td>
                <td><?php $total_amt += $cash_deposit->deposit_cash; echo $cash_deposit->deposit_cash ?></td>
                <td><?php echo $cash_deposit->bank_name ?></td>
                <td><?php echo $cash_deposit->remark ?></td>
                <td><?php if($cash_deposit->reconciliation_status == 0){ echo "Deposited"; }else{ echo "Reconciled"; } ?></td>
            </tr>
            <?php $i++; } ?>
        </tbody>
        <thead>
            <th></th>
            <th></th>
            <th>Total</th>
            <th><?php echo $total_amt; ?></th>
            <th></th>
            <th></th>
            <th></th>
        </thead>
        <?php
    }
    
    public function ajax_cash_ledger_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $cash_ledger_report = $this->Report_model->ajax_cash_ledger_report($datefrom,$dateto,$idbranch); ?>
        <thead class="fixedelement">
            <th>Sr</th>
            <th>Date</th>
            <th>Branch</th>
            <th>Entry Type</th>
            <th>Invoice No</th>
            <th>Cash Amount</th>
<!--            <th>Customer</th>
            <th>Customer GSTIN</th>-->
        </thead>
        <tbody id="myTable">
            <?php $i=1; $total_amt=0; foreach($cash_ledger_report as $cash_ledger){ ?>
            <tr style="background-color: <?php echo $cash_ledger->color_code ?>">
                <td><?php echo $i; ?></td>
                <td><?php echo $cash_ledger->entry_time ?></td>
                <td><?php echo $cash_ledger->branch_name ?></td>
                <td><?php echo $cash_ledger->entry_type_name ?></td>
                <td><?php echo $cash_ledger->inv_no ?></td>
                <td><?php $total_amt += $cash_ledger->amount; echo $cash_ledger->amount ?></td>
                <!--<td><?php // echo $cash_ledger->customer_fname.' '.$cash_ledger->customer_lname ?></td>-->
                <!--<td><?php // echo $cash_ledger->customer_gst ?></td>-->
            </tr>
            <?php $i++; } ?>
        </tbody>
        <thead class="fixedelement_bottom">
            <th></th>
            <th>Date Range</th>
            <th><?php echo $datefrom ?></th>
            <th><?php echo $dateto ?></th>
            <th>Total</th>
            <th><?php echo $total_amt; ?></th>
            <!--<th></th>-->
            <!--<th></th>-->
        </thead>
        <?php
    }
    
    public function ajax_cheque_bounce_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $cheque_bounce_report = $this->Report_model->ajax_cheque_bounce_report($datefrom,$dateto,$idbranch, $branches); ?>
        <thead class="fixedelement">
            <th>Sr</th>
            <th>Invoice Date</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <th>Bounce Date</th>
            <th>Cheque Amount</th>
            <th>Bounce Amount</th>
            <th>Total Amount</th>
            <th>UTR No</th>
            <th>Reconciliation By</th>
        </thead>
        <tbody id="data_1">
            <?php $i=1; $total_amt=0;$bounce_charges=0;$total_cheque_amount=0; foreach($cheque_bounce_report as $cheque_bounce){ ?>
            <tr style="background-color: <?php echo $cheque_bounce->color_code ?>">
                <td><?php echo $i; ?></td>
                <td><?php echo $cheque_bounce->date ?></td>
                <td><?php echo $cheque_bounce->branch_name ?></td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Sale/sale_details/'.$cheque_bounce->idsale) ?>"><?php echo $cheque_bounce->inv_no ?></a></td>
                <td><?php echo $cheque_bounce->bounce_date ?></td>
                <td><?php $total_amt += $cheque_bounce->amount; echo $cheque_bounce->amount ?></td>
                <td><?php $bounce_charges += $cheque_bounce->bounce_charges; echo $cheque_bounce->bounce_charges ?></td>
                <td><?php $total_cheque_amount += $cheque_bounce->total_cheque_amount; echo $cheque_bounce->total_cheque_amount ?></td>
                <td><?php echo $cheque_bounce->bounce_utr ?></td>
                <td><?php echo $cheque_bounce->user_name ?></td>
                <!--<td><?php // echo $cheque_bounce->customer_fname.' '.$cheque_bounce->customer_lname ?></td>-->
                <!--<td><?php // echo $cheque_bounce->customer_gst ?></td>-->
            </tr>
            <?php $i++; } ?>
        </tbody>
        <thead class="fixedelement_bottom">
            <th></th>
            <th></th>
            <th><?php echo $datefrom ?> To</th>
            <th><?php echo $dateto ?></th>
            <th>Total</th>
            <th><?php echo $total_amt; ?></th>
            <th><?php echo $bounce_charges; ?></th>
            <th><?php echo $total_cheque_amount; ?></th>
            <th></th>
            <th></th>
        </thead>
        <?php
    }
    
    public function ajax_new_daybook_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $payment_mode_data = $this->General_model->get_active_payment_mode_data();
        $payment_mode_count = count($payment_mode_data);
        // sale report
        $daybook_report = $this->Report_model->ajax_get_daybook_sale_report($datefrom,$dateto,$idbranch,$branches);
        // opening cash till date
        $opening_cash_bydate = $this->Report_model->ajax_get_opening_cash_bydate($datefrom,$idbranch,$branches);
        // ajax_get_cash_payment_receive_report
        $cash_payment_receive_report = $this->Report_model->ajax_get_cash_payment_receive_report($datefrom,$dateto,$idbranch,$branches);
        // ajax_get_advanced_payment_receive_report
        $adv_payment_receive_report = $this->Report_model->ajax_get_adv_payment_receive_report($datefrom,$dateto,$idbranch,$branches);
        // ajax_get_advanced_payment_refund_report
        $adv_payment_refund_report = $this->Report_model->ajax_get_adv_payment_refund_report($datefrom,$dateto,$idbranch,$branches);
//        die(print_r($adv_payment_refund_report));
        // opening sales return cash
        $daybook_salesreturn_report = $this->Report_model->ajax_get_daybook_sales_return_report($datefrom,$dateto,$idbranch,$branches);
        // expense cash
        $daybook_expense_report = $this->Report_model->ajax_get_daybook_expense_report($datefrom,$dateto,$idbranch,$branches);
        // bank deposit cash
        $daybook_deposite_to_bank_report = $this->Report_model->ajax_get_cash_deposite_to_bank_report($datefrom,$dateto,$idbranch,$branches);
        // Credit buyback receive
        $daybook_credit_buyback_recieve_report = $this->Report_model->ajax_get_daybook_credit_buyback_recieve_report($datefrom,$dateto,$idbranch,$branches);
        // die(print_r($daybook_expense_report));
        $amt=array();$ssramt=array();$amt[0]=0;$ssramt[0]=0;$sramt=0;$expamt=0;$depositamt=0;$cppramt=0;$adv_bookamt[0]=0;$advref=0;$totaladv_book[0]=0; ?>
        <thead class="fixedelement">
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice</th>
            <?php foreach ($payment_mode_data as $payment_mode) { ?>
            <th><?php echo $payment_mode->payment_head.' '.$payment_mode->payment_mode ?> </th>
            <?php $mode_name[] = $payment_mode->payment_mode; } ?>
            <th>Total</th>
        </thead>
        <tbody>
            <tr>
                <td colspan="3"><h4 style="margin: 0;color: #1d4cc2;font-family: Kurale"><i class="mdi mdi-plus-circle-outline"></i> Opening Cash Balance</h4></td>
                <td><?php echo $opening_cash_bydate->daybook_cash ?></td>
                <!--<td colspan="<?php // echo $payment_mode_count - 1; ?>"></td>-->
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th><?php echo $opening_cash_bydate->daybook_cash ?></th>
            </tr>
            <?php
// Sale Daybook
            if(count($daybook_report) > 0){ ?>
            <tr class="bg-green">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h4 style="margin: 0;color: #1d4cc2; font-family: Kurale"><i class="mdi mdi-plus-circle-outline"></i> Sale Collection</h4></td>
            </tr>
                <?php $j=0; foreach ($daybook_report as $daybook) { $totalamt[$j] = 0; ?>
            <tr>
                <td><?php echo $daybook->date ?> </td>
                <td><?php echo $daybook->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Sale/sale_details/'.$daybook->idsale) ?>"><?php echo $daybook->inv_no ?></a></td>
                <?php for($i=0; $i < count($mode_name); $i++){ ?>
                <td><?php $mdn = $mode_name[$i]; echo $daybook->$mdn; ?> </td>
                <!--<td><?php // $mdn = $mode_name[$i]; $sum_amount[] += $daybook->$mdn; echo $daybook->$mdn; ?> </td>-->
                <?php $amt[$i] += $daybook->$mdn; $totalamt[$j] += $daybook->$mdn; } ?>
                <th><?php echo $totalamt[$j] ?></th>
            </tr>
            <?php $j++; } ?>
            <tr>
                <th></th><th></th>
                <th>Total Sale</th>
                <?php for($i=0; $i < count($mode_name); $i++){ $mdn = $mode_name[$i]; ?>
                <th><?php echo $amt[$i]; ?></th>
                <?php } ?>
                <th><?php echo array_sum($amt) ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Sale Daybook Record Not Found...</h5></td>
            </tr>-->
            <?php } 
// Credit, buyback received Daybook
            if(count($daybook_credit_buyback_recieve_report) > 0){ ?>
            <tr class="bg-green">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h4 style="margin: 0;color: #1d4cc2;font-family: Kurale"><i class="mdi mdi-plus-circle-outline"></i> Credit/ Buyback Received</h4></td>
            </tr>
            <?php $j=0; $totalssramt[0]=0; foreach ($daybook_credit_buyback_recieve_report as $srrdaybook) { ?>
            <tr>
                <td><?php echo $srrdaybook->date ?> </td>
                <td><?php echo $srrdaybook->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Sale/sale_details/'.$srrdaybook->idsale) ?>"><?php echo $srrdaybook->inv_no ?></a></td>
                <?php for($i=0; $i < count($mode_name); $i++){ ?>
                <td><?php $mdn = $mode_name[$i]; echo $srrdaybook->$mdn; ?> </td>
                <?php $ssramt[$i] = $srrdaybook->$mdn + $ssramt[$i]; $totalssramt[$j] = $srrdaybook->$mdn + $totalssramt[$j]; } ?>
                <th><?php echo $totalssramt[$j]; ?></th>
            </tr>
            <?php $j++; } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <?php for($i=0; $i < count($mode_name); $i++){ $mdn = $mode_name[$i]; ?>
                <th><?php echo $ssramt[$i]; ?></th>
                <?php } ?>
                <th><?php echo array_sum($ssramt); ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Credit/ Buyback Receive Record Not Found...</h5></td>
            </tr>-->
            <?php } 
// Advanced booking
            if(count($adv_payment_receive_report) > 0){ ?>
            <tr class="bg-green">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h4 style="margin: 0;color: #1d4cc2;font-family: Kurale"><i class="mdi mdi-plus-circle-outline"></i> Advanced Booking Received</h4></td>
            </tr>
            <?php $j=0; $totaladv_book[0]=0; foreach ($adv_payment_receive_report as $adv_book) { ?>
            <tr>
                <td><?php echo $adv_book->date ?> </td>
                <td><?php echo $adv_book->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Payment/advance_booking_received_receipt/'.$adv_book->idsale) ?>">AdvPay/<?php echo $adv_book->branch_code.'/'.$adv_book->idsale ?></a></td>
                <?php for($i=0; $i < count($mode_name); $i++){ ?>
                <td><?php $mdn = $mode_name[$i]; echo $adv_book->$mdn; ?> </td>
                <?php $adv_bookamt[$i] = $adv_book->$mdn + $adv_bookamt[$i]; $totaladv_book[$j] = $adv_book->$mdn + $totaladv_book[$j]; } ?>
                <th><?php echo $totaladv_book[$j]; ?></th>
            </tr>
            <?php $j++; } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <?php for($i=0; $i < count($mode_name); $i++){ $mdn = $mode_name[$i]; ?>
                <th><?php echo $adv_bookamt[$i]; ?></th>
                <?php } ?>
                <th><?php echo array_sum($adv_bookamt); ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Credit/ Buyback Receive Record Not Found...</h5></td>
            </tr>-->
            <?php }
// cash_payment_receive_report
            if(count($cash_payment_receive_report) > 0){ ?>
            <tr class="bg-green">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h4 style="margin: 0;color: #1d4cc2;font-family: Kurale"><i class="mdi mdi-plus-circle-outline"></i> Cash Payment Received</h4></td>
            </tr>
            <?php foreach ($cash_payment_receive_report as $cprrdaybook) { ?>
            <tr>
                <td><?php echo $cprrdaybook->date ?> </td>
                <td><?php echo $cprrdaybook->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Payment/cash_payment_received_receipt/'.$cprrdaybook->id_cash_payment_receive) ?>">CashRec/<?php echo $cprrdaybook->id_cash_payment_receive ?></a></td>
                <td><?php echo $cprrdaybook->amount; $cppramt = $cppramt + $cprrdaybook->amount; ?> </td>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th><?php echo $cprrdaybook->amount; ?></th>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <th><?php echo $cppramt; ?></th>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th><?php echo $cppramt; ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Cash Payment Received Record Not Found...</h5></td>
            </tr>-->
            <?php }
// Sales return Daybook
            if(count($daybook_salesreturn_report) > 0){ ?>
            <tr class="bg-danger" style="color: #AC3F34">
                <td colspan="3"><h4 style="margin: 0;font-family: Kurale"><i class="mdi mdi-minus-circle-outline"></i> Sales Return</h4></td>
                <td>Cash</td>
                <td colspan="<?php echo $payment_mode_count; ?>"></td>
            </tr>
            <?php foreach ($daybook_salesreturn_report as $srdaybook) { ?>
            <tr>
                <td><?php echo $srdaybook->date; ?> </td>
                <td><?php echo $srdaybook->branch_name ?> </td>
                <!--<td><?php // echo $srdaybook->inv_no.'<hr style="margin:2px">SR'.$srdaybook->inv_no; ?> </td>-->
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Sales_return/sales_return_details/'.$srdaybook->id_salesreturn) ?>"><?php echo $srdaybook->sales_return_invid; ?></a></td>
                <td><?php echo $srdaybook->final_total; $sramt = $sramt + $srdaybook->final_total; ?></td>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <td><?php echo $srdaybook->final_total;?></td>
            </tr>
            <?php } ?>
            <tr>
                <th></th><th></th>
                <th>Total Return Cash</th>
                <th>-<?php echo $sramt; ?></th>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th>-<?php echo $sramt; ?></th>
            </tr>
            <?php // } else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Sales Return Daybook Record Not Found...</h5></td>
            </tr>-->
            <?php }
// Expense daybook
            if(count($daybook_expense_report) > 0){ ?>
            <tr class="bg-danger" style="color: #AC3F34">
                <td colspan="2"><h4 style="margin: 0;font-family: Kurale"><i class="mdi mdi-minus-circle-outline"></i> Expense</h4></td>
                <td>Expense Type</td>
                <td>Cash</td>
                <td colspan="<?php echo $payment_mode_count; ?>"></td>
            </tr>
            <?php foreach ($daybook_expense_report as $expdaybook) { ?>
            <tr>
                <td><?php echo $expdaybook->entry_time ?> </td>
                <td><?php echo $expdaybook->branch_name ?> </td>
                <td><?php echo $expdaybook->expense_type; ?> </td>
                <td><?php echo $expdaybook->approve_expense_amount; $expamt = $expamt + $expdaybook->approve_expense_amount; ?> </td>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <td><?php echo $expdaybook->approve_expense_amount; ?> </td>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total Expense</th>
                <th>-<?php echo $expamt; ?></th>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th>-<?php echo $expamt; ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Expense Daybook Record Not Found...</h5></td>
            </tr>-->
             <?php } 
// refund advanced payment in cash
            if(count($adv_payment_refund_report) > 0){ ?>
            <tr class="bg-danger" style="color: #AC3F34">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h4 style="margin: 0;font-family: Kurale"><i class="mdi mdi-minus-circle-outline"></i> Refund Advanced Payment</h4></td>
            </tr>
            <?php foreach ($adv_payment_refund_report as $adv_refamt) { ?>
            <tr>
                <td><?php echo $adv_refamt->refund_date ?> </td>
                <td><?php echo $adv_refamt->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Payment/advance_booking_received_receipt/'.$adv_refamt->id_advance_payment_receive) ?>">AdvRef/<?php echo $adv_book->branch_code.'/'.$adv_refamt->id_advance_payment_receive ?></a></td>
                <td><?php echo $adv_refamt->amount; $advref = $advref + $adv_refamt->amount; ?> </td>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <td><?php echo $adv_refamt->amount; ?> </td>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <th>-<?php echo $advref; ?></th>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th>-<?php echo $advref; ?></th>
            </tr>
            <?php // }else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Cash Payment Received Record Not Found...</h5></td>
            </tr>-->
            <?php } 
// Deposite to bank daybook
            if(count($daybook_deposite_to_bank_report) > 0){ ?>
            <tr class="bg-danger" style=" color: #AC3F34">
                <td colspan="2"><h4 style="margin: 0;font-family: Kurale"><i class="mdi mdi-minus-circle-outline"></i> Deposit to Bank</h4></td>
                <td>Bank</td>
                <td>Cash</td>
                <td colspan="<?php echo $payment_mode_count; ?>"></td>
            </tr>
            <?php foreach ($daybook_deposite_to_bank_report as $depositdaybook) { ?>
            <tr>
                <td><?php echo $depositdaybook->entry_time ?> </td>
                <td><?php echo $depositdaybook->branch_name ?> </td>
                <td><?php echo $depositdaybook->bank_name.' '.$depositdaybook->bank_ifsc ?> </td>
                <td><?php echo $depositdaybook->deposit_cash; $depositamt = $depositamt + $depositdaybook->deposit_cash; ?></td>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <td><?php echo $depositdaybook->deposit_cash;?></td>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total Deposit</th>
                <th>-<?php echo $depositamt; ?></th>
                <?php for($i=1; $i < count($mode_name); $i++){ echo '<td></td>'; } ?>
                <th>-<?php echo $depositamt; ?></th>
            </tr>
            <?php // } else{ ?>
<!--            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Cash Deposit to Bank Record Not Found...</h5></td>
            </tr>-->
            <?php } ?>
            <tfoot class="fixedelement_bottom">
                <th></th><th></th>
                <th>Available Cash</th>
                <th><?php 
                        $total_cash = $opening_cash_bydate->daybook_cash  + $amt[0] + $ssramt[0] + $cppramt - $sramt - $expamt - $depositamt + $adv_bookamt[0] - $advref;
                        echo $total_cash; ?>
                </th>
                <?php $sale_amount = 0;$creditr_amount = 0;$totaladv_bookamt=0;
                    for($i=1; $i < count($mode_name); $i++){ 
                        if (array_key_exists($i, $amt)){
                            $sale_amount = $amt[$i];
                        }if (array_key_exists($i, $ssramt)){
                            $creditr_amount = $ssramt[$i];
                        }if (array_key_exists($i, $adv_bookamt)){
                            $totaladv_bookamt = $adv_bookamt[$i];
                        } ?>
                <th><?php echo $sale_amount + $creditr_amount + $totaladv_bookamt; ?></th>
                <?php } ?>
                <th><?php echo array_sum($amt) + array_sum($ssramt) + array_sum($totaladv_book) + $opening_cash_bydate->daybook_cash; ?></th>
            </tfoot>
        </tbody>
    <?php 
    }
    
    public function ajax_get_daybook_report() {
//        die(print_r($_POST));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $payment_mode_data = $this->General_model->get_payment_mode_data();
        $daybook_report = $this->Report_model->ajax_get_daybook_sale_report($datefrom,$dateto,$idbranch,$branches);
//        die($daybook_report);
        
        $previous_available_cash = 0;
        
//        $daybook_credit_buyback_recieve_report = $this->Report_model->ajax_get_daybook_credit_buyback_recieve_report($datefrom,$dateto,$idbranch);
//        $daybook_salesreturn_report = $this->Report_model->ajax_get_daybook_sales_return_report($datefrom,$dateto,$idbranch); 
//        $daybook_expense_report = $this->Report_model->ajax_get_daybook_expense_report($datefrom,$dateto,$idbranch); 
//        $daybook_deposite_to_bank_report = $this->Report_model->ajax_get_daybook_deposite_to_bank_report($datefrom,$dateto,$idbranch);
//        
        $last_cash_deposite_entry = $this->Report_model->ajax_get_last_cash_deposite_entry($datefrom,$idbranch); 
        if($last_cash_deposite_entry != ''){
            $datefrom_deposit = $last_cash_deposite_entry->entry_time;
            $sum_sale_available_cash = $this->Report_model->ajax_get_sum_sale_opening_fromdate_cash($datefrom_deposit,$datefrom,$idbranch);
            $sum_sales_return_cash = $this->Report_model->get_sum_opening_sales_return_fromdate_cash($datefrom_deposit,$datefrom,$idbranch);
            $sum_expense_cash = $this->Report_model->get_sum_opening_expense_fromdate_cash($datefrom_deposit,$datefrom,$idbranch);
            $previous_available_cash = $sum_sale_available_cash->available_cash - $sum_sales_return_cash->sales_return_cash - $sum_expense_cash->expense_cash;
        }else{
            $sum_sale_available_cash = $this->Report_model->ajax_get_sum_sale_opening_cash($datefrom,$idbranch);
            $sum_sales_return_cash = $this->Report_model->get_sum_opening_sales_return_cash($datefrom,$idbranch);
            $sum_expense_cash = $this->Report_model->get_sum_opening_expense_cash($datefrom,$idbranch);
            $previous_available_cash = $sum_sale_available_cash->available_cash - $sum_sales_return_cash->sales_return_cash - $sum_expense_cash->expense_cash;
        }if($previous_available_cash == NULL){
            $previous_available_cash = 0;
        }
        
        
        $payment_mode_count = count($payment_mode_data);
        $amt=array();$ssramt=array();$amt[0]=0;$ssramt[0]=0;$sramt=0;$expamt=0;$depositamt=0; ?>
        <thead>
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice</th>
            <?php foreach ($payment_mode_data as $payment_mode) { ?>
            <th><?php echo $payment_mode->payment_head.' '.$payment_mode->payment_mode ?> </th>
            <?php $mode_name[] = $payment_mode->payment_mode; } ?>
            <th>Total</th>
        </thead>
        <tbody>
            <tr>
                <td colspan="3"><h5 style="margin: 0;color: #2A7E31"><i class="mdi mdi-plus-circle-outline"></i> Opening Cash Balance</h5></td>
                <td><?php echo $previous_available_cash ?></td>
                <td colspan="<?php echo $payment_mode_count - 1; ?>"></td>
            </tr>
            <?php
            // Sale Daybook
            if(count($daybook_report) > 0){ ?>
            <tr class="bg-success">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 style="margin: 0;color: #2A7E31"><i class="mdi mdi-plus-circle-outline"></i> Sale Collection</h5></td>
            </tr>
                <?php $j=0; foreach ($daybook_report as $daybook) { $totalamt[$j] = 0; ?>
            <tr>
                <td><?php echo $daybook->date ?> </td>
                <td><?php echo $daybook->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Reports/sale_details/'.$daybook->idsale) ?>"><?php echo $daybook->inv_no ?></a></td>
                <?php for($i=0; $i < count($mode_name); $i++){ ?>
                <td><?php $mdn = $mode_name[$i]; echo $daybook->$mdn; ?> </td>
                <!--<td><?php // $mdn = $mode_name[$i]; $sum_amount[] += $daybook->$mdn; echo $daybook->$mdn; ?> </td>-->
                <?php $amt[$i] += $daybook->$mdn; $totalamt[$j] += $daybook->$mdn; } ?>
                <th><?php echo $totalamt[$j] ?></th>
            </tr>
            <?php $j++; } ?>
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <?php for($i=0; $i < count($mode_name); $i++){ $mdn = $mode_name[$i]; ?>
                <th><?php echo $amt[$i]; ?></th>
                <?php } ?>
                <th><?php echo array_sum($amt) ?></th>
            </tr>
            <?php }else{ ?>
            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Sale Daybook Record Not Found...</h5></td>
            </tr>
            <?php } 
            // Credit, buyback received Daybook
            if(count($daybook_credit_buyback_recieve_report) > 0){ ?>
            <tr class="bg-success">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 style="margin: 0;color: #2A7E31"><i class="mdi mdi-plus-circle-outline"></i> Credit/ Buyback Received</h5></td>
            </tr>
            <?php $j=0; $totalssramt[$j]=0; foreach ($daybook_credit_buyback_recieve_report as $srrdaybook) { ?>
            <tr>
                <td><?php echo $srrdaybook->date ?> </td>
                <td><?php echo $srrdaybook->branch_name ?> </td>
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Reports/sale_details/'.$srrdaybook->idsale) ?>"><?php echo $srrdaybook->inv_no ?></a></td>
                <?php for($i=0; $i < count($mode_name); $i++){ ?>
                <td><?php $mdn = $mode_name[$i]; echo $srrdaybook->$mdn; ?> </td>
                <?php $ssramt[$i] = $srrdaybook->$mdn + $ssramt[$i]; $totalssramt[$j] = $srrdaybook->$mdn + $totalssramt[$j]; } ?>
                <th><?php echo $totalssramt[$j]; ?></th>
            </tr>
            <?php $j++; } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <?php for($i=0; $i < count($mode_name); $i++){ $mdn = $mode_name[$i]; ?>
                <th><?php echo $ssramt[$i]; ?></th>
                <?php } ?>
                <th><?php echo array_sum($ssramt); ?></th>
            </tr>
            <?php }else{ ?>
            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Credit/ Buyback Receive Record Not Found...</h5></td>
            </tr>
            <?php }
            // Sales return Daybook
            if(count($daybook_salesreturn_report) > 0){ ?>
            <tr class="bg-danger">
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 style="margin: 0;color: #AC3F34"><i class="mdi mdi-minus-circle-outline"></i> Sales Return</h5></td>
            </tr>
            <?php foreach ($daybook_salesreturn_report as $srdaybook) { ?>
            <tr>
                <td><?php echo $srdaybook->date; ?> </td>
                <td><?php echo $srdaybook->branch_name ?> </td>
                <!--<td><?php // echo $srdaybook->inv_no.'<hr style="margin:2px">SR'.$srdaybook->inv_no; ?> </td>-->
                <td><a style="color: #3333ff" target="_blank" href="<?php echo base_url('Reports/sales_return_details/'.$srdaybook->id_salesreturn) ?>"><?php echo $srdaybook->sales_return_invid; ?></a></td>
                <td>-<?php echo $srdaybook->final_total; $sramt = $sramt + $srdaybook->final_total; ?> </td>
            </tr>
            <?php } ?>
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <th>-<?php echo $sramt; ?></th>
            </tr>
            <?php } else{ ?>
            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Sales Return Daybook Record Not Found...</h5></td>
            </tr>
            <?php }
            // Expense daybook
            if(count($daybook_expense_report) > 0){ ?>
            <tr class="bg-danger" style=" color: #AC3F34">
                <td colspan="2"><h5 style="margin: 0;"><i class="mdi mdi-minus-circle-outline"></i> Expense</h5></td>
                <td>Expense Type</td>
                <td colspan="<?php echo $payment_mode_count + 1; ?>"></td>
            </tr>
            <?php foreach ($daybook_expense_report as $expdaybook) { ?>
            <tr>
                <td><?php echo $expdaybook->entry_time ?> </td>
                <td><?php echo $expdaybook->branch_name ?> </td>
                <td><?php echo $expdaybook->expense_type; ?> </td>
                <td>-<?php echo $expdaybook->expense_amount; $expamt = $expamt + $expdaybook->expense_amount; ?> </td>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <th>-<?php echo $expamt; ?></th>
            </tr>
            <?php } else{ ?>
            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Expense Daybook Record Not Found...</h5></td>
            </tr>
            <?php } 
            // Deposite to bank daybook
            if(count($daybook_deposite_to_bank_report) > 0){ ?>
            <tr class="bg-danger" style=" color: #AC3F34">
                <td colspan="2"><h5 style="margin: 0;"><i class="mdi mdi-minus-circle-outline"></i> Cash Deposite to Bank</h5></td>
                <td>Bank</td>
                <td colspan="<?php echo $payment_mode_count + 1; ?>"></td>
            </tr>
            <?php foreach ($daybook_deposite_to_bank_report as $depositdaybook) { ?>
            <tr>
                <td><?php echo $depositdaybook->entry_time ?> </td>
                <td><?php echo $depositdaybook->branch_name ?> </td>
                <td><?php echo $depositdaybook->bank_name.' '.$depositdaybook->bank_ifsc ?> </td>
                <td>-<?php echo $depositdaybook->deposit_cash; $depositamt = $depositamt + $depositdaybook->deposit_cash; ?> </td>
            </tr>
            <?php } ?> 
            <tr>
                <th></th><th></th>
                <th>Total</th>
                <th>-<?php echo $depositamt; ?></th>
            </tr>
            <?php } else{ ?>
            <tr>
                <td colspan="<?php echo $payment_mode_count + 4; ?>"><h5 class="red-text"><i class="mdi mdi-alert"></i> Cash Deposit to Bank Record Not Found...</h5></td>
            </tr>
            <?php } ?>
            <tfoot>
                <th></th><th></th>
                <th>Available Cash</th>
                <th><?php $total_cash = $previous_available_cash + $amt[0] + $ssramt[0] - $sramt - $expamt - $depositamt; echo $total_cash; ?></th>
            </tfoot>
        </tbody>
        <?php 
    }
    
    public function inter_state_sale()
    {   $user_id=$this->session->userdata('id_users');        
        $q['tab_active'] = '';  
        $q['title'] = 'Inter State Sale';  
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
            $q['comapny_data'] = $this->General_model->get_active_comapny();    
            $q['inter_state_data'] = $this->Report_model->get_inter_state_data_by_date('','','');
            $this->load->view('report/inter_state_sale', $q);
        }else{
            redirect('Report/404');
        }
    }
    public function ajax_inter_state_sale() {        
        $idcompany = $this->input->post('idcompany');
        $dateto = $this->input->post('dateto');
        $datefrom = $this->input->post('datefrom');
        $inter_state_data = $this->Report_model->get_inter_state_data_by_date($datefrom,$dateto,$idcompany);
        ?>
         <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Mandate </th> 
                <th>Date</th>
                <th>Invoice No</th>
                <th>Seller Company</th>
                <th>Buyer Company</th>
                <th>Buyer GST No</th>
                <th>Godown</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Model</th>
                <th>HSN</th>
                <th>IMEI</th>
                <th>Base Price</th>
                <th>Qty</th>
                <th>Taxable</th>
                <th>IGST Rate (%)</th>	                
                <th>IGST</th>	                
                <th>Total Amount</th>
                <th>Branch From</th>
                <th>Branch  To</th>
            </thead>
            <tbody>
                <?php foreach ($inter_state_data as $data){ ?>
                <tr>
                    <?php if($data->transaction_type=='Transfer'){ ?>                        
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $data->idoutward_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'T'.$data->idoutward_transfer ?></b></a></td>
                    <?php }else{ ?>
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/<?php echo $data->idoutward_transfer ?>/0" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'O'.$data->idoutward_transfer ?></b></a></td>                        
                    <?php } ?>
                    <td><?php echo $data->date ?></td>     
                    <td><?php echo $data->sales_invoice?></td>
                    <td><?php echo $data->company_from ?></td>
                    <td><?php echo $data->company_to ?></td>
                    <td><?php echo $data->gst_no_to ?></td>
                    <td><?php echo $data->godown_name ?></td>
                    <td><?php echo $data->product_category_name ?></td>
                    <td><?php echo $data->brand_name ?></td>
                    <td><?php echo $data->full_name ?></td>
                    <td><?php echo $data->hsn ?></td>
                    <td><?php echo $data->imei_no ?></td>
                    <?php
                        $total_amount = $data->price*($data->qty);
                        $cal = ($data->igst_per + 100) / 100;
                        $taxable = $total_amount / $cal;
                        $igstamt = $total_amount - $taxable;
                        $rate = $taxable / $data->qty;
                    ?>
                    <td><?php echo round($rate,2) ?></td>
                    <td><?php echo $data->qty ?></td>
                    <td><?php echo round($taxable, 2) ?></td>
                    <td><?php echo $data->igst_per ?></td>
                    <td><?php echo round($igstamt, 2) ?></td>
                    <td><?php echo $total_amount ?></td>
                    <td><?php echo $data->branch_from?></td>
                    <td><?php echo $data->branch_to ?></td>
                                   
                </tr>
                <?php } ?>
            </tbody> 
        <?php
    }

    public function inter_state_purchase()
    {   $user_id=$this->session->userdata('id_users');        
        $q['tab_active'] = '';  
        $q['title'] = 'Inter State Purchase';  
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
            $q['comapny_data'] = $this->General_model->get_active_comapny();    
            $q['inter_state_data'] = $this->Report_model->get_inter_state_purchase_by_date('','','');
            $this->load->view('report/inter_state_purchase', $q);
        }else{
            redirect('Report/404');
        }
    }
    
    public function ajax_inter_state_purchase() {        
        $idcompany = $this->input->post('idcompany');
        $dateto = $this->input->post('dateto');
        $datefrom = $this->input->post('datefrom');
        $inter_state_data = $this->Report_model->get_inter_state_purchase_by_date($datefrom,$dateto,$idcompany);
        ?>
         <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Mandate </th> 
                <th>Date</th>
                <th>Vendor Invoice No</th>
                <th>Invoice No</th>
                <th>Seller Company</th>
                <th>Seller GST No</th>
                <th>Buyer Company</th>
                <th>Godown</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Model</th>
                <th>HSN</th>
                <th>IMEI</th>
                <th>Base Price</th>
                <th>Qty</th>
                <th>Taxable</th>
                <th>IGST Rate (%)</th>	                
                <th>IGST</th>	                
                <th>Total Amount</th>
                <th>Branch From</th>
                <th>Branch  To</th>
            </thead>
            <tbody>
                <?php foreach ($inter_state_data as $data){ ?>
                <tr>
                    <?php if($data->transaction_type=='Transfer'){ ?>                        
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $data->idoutward_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'T'.$data->idoutward_transfer ?></b></a></td>
                    <?php }else{ ?>
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/<?php echo $data->idoutward_transfer ?>/0" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'O'.$data->idoutward_transfer ?></b></a></td>                        
                    <?php } ?>
                    <td><?php echo $data->date ?></td>     
                    <td><?php echo $data->sales_invoice?></td>
                    <td><?php echo $data->purchase_invoice?></td>
                    <td><?php echo $data->company_from ?></td>
                    <td><?php echo $data->gst_no_from ?></td>
                    <td><?php echo $data->company_to ?></td>
                    <td><?php echo $data->godown_name ?></td>
                    <td><?php echo $data->product_category_name ?></td>
                    <td><?php echo $data->brand_name ?></td>
                    <td><?php echo $data->full_name ?></td>
                    <td><?php echo $data->hsn ?></td>
                    <td><?php echo $data->imei_no ?></td>
                    <?php
                        $total_amount = $data->price*($data->qty);
                        $cal = ($data->igst_per + 100) / 100;
                        $taxable = $total_amount / $cal;
                        $igstamt = $total_amount - $taxable;
                        $rate = $taxable / $data->qty;
                    ?>
                    <td><?php echo round($rate,2) ?></td>
                    <td><?php echo $data->qty ?></td>
                    <td><?php echo round($taxable, 2) ?></td>
                    <td><?php echo $data->igst_per ?></td>
                    <td><?php echo round($igstamt, 2) ?></td>
                    <td><?php echo $total_amount ?></td>
                    <td><?php echo $data->branch_from?></td>
                    <td><?php echo $data->branch_to ?></td>
                                   
                </tr>
                <?php } ?>
            </tbody> 
        <?php
    }
    public function stock_in_out_report()
    {   $user_id=$this->session->userdata('id_users');        
        $q['tab_active'] = '';  
        $q['title'] = 'Stock In-Out Report';  
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
            $q['comapny_data'] = $this->General_model->get_active_comapny();    
            $q['in_out_data'] = $this->Report_model->get_in_out_by_date('','','');
            $this->load->view('report/inter_state_purchase', $q);
        }else{
            redirect('Report/404');
        }
    }
    
     public function cash_summary_report(){
        $q['tab_active'] = 'Report';
         if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/cash_summary_report', $q);
    }
    
    public function ajax_cash_sumamry_report() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $idbranches = $this->input->post('branches');
        $cash_entry_type = $this->Report_model->get_cash_entry_type(); 
        $cash_ledger_report = $this->Report_model->ajax_cash_sumamry_report($datefrom,$dateto,$idbranch,$idbranches); 
        $cash_closure_data = $this->Report_model->ajax_max_cash_closure_report($datefrom,$dateto,$idbranch,$idbranches); 
        
//        die('<pre>'.print_r($cash_ledger_report,1).'</pre>');
        
        $amt=array();$amt[0]=0;
        ?>
        <thead class="fixedelement">
            <th>Sr</th>
            <th>Date</th>
            <th>Branch</th>
            <?php foreach ($cash_entry_type as $entry_type) { if($entry_type->id_cash_entry_type != 3 ){ ?>
                <th><?php echo $entry_type->entry_type_name ?></th>
            <?php $mode_name[] = $entry_type->type_name;  } } ?>
            <th>Total</th>
            <th>Cash Closure</th>
        </thead>
        <tbody id="myTable">
            <?php $k=1; $j=0;$tcashclosure=0; $total_amt=0; foreach($cash_ledger_report as $cash_ledger){ $totalamt[$j] = 0; ?>
            <tr>
                <td><?php echo $k; ?></td>
                <td><?php echo $cash_ledger->date ?></td>
                <td><?php echo $cash_ledger->branch_name ?></td>
                <?php for($i=0; $i < count($mode_name); $i++){ $cashsum=0; ?>
                    <td><?php $mdn = $mode_name[$i]; 
                    if($i == 1){
                        $cashsum =  $cash_ledger->sale_return_cash + $cash_ledger->sale_return_replace_upgrad;
//                        echo $mdn;
                    }else{
                        $cashsum =  $cash_ledger->$mdn;
                    }
                    echo $cashsum;
                    ?> </td>
                <?php $amt[$i] += $cashsum; $totalamt[$j] += $cashsum; } ?>
                <td><?php echo $totalamt[$j]; ?></td>
                <td><?php  foreach($cash_closure_data as $cashcloser){
                        if($cashcloser->date == $cash_ledger->date && $cashcloser->cidbranch == $cash_ledger->idbranch){
                            echo $cashcloser->closure_cash;
                            $tcashclosure = $tcashclosure + $cashcloser->closure_cash; 
                        }
                    } ?>
                </td>
            </tr>
            <?php $k++; $j++; } ?>
        </tbody>
        <thead class="fixedelement_bottom">
            <th></th>
            <th></th>
            <th><?php echo $datefrom ?> To <?php echo $dateto ?></th>
            <?php for($i=0; $i < count($mode_name); $i++){ $sale_amount = 0;
                if (array_key_exists($i, $amt)){
                    $sale_amount = $amt[$i];
                } ?>
                <th><?php echo $sale_amount ?></th>
            <?php } ?>
            <th><?php echo array_sum($amt); ?></th>
            <th><?php  echo $tcashclosure; ?></th>
        </thead>
        <?php
    }
    
    public function credit_custudy_receipt_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/credit_custudy_receipt_report', $q);
    }
    public function ajax_get_credit_custudy_receipt_report(){
        $idbranch = $this->input->post('idbranch');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $credit_cust_data = $this->Report_model->ajax_get_credit_cust_receipt($from, $to, $idbranch);
//        die(print_r($credit_cust_data));
        if(count($credit_cust_data) > 0){ ?>
            <table class="table table-bordered table-condensed" id="credit_custudy_receipt">
                <thead style="background-color: #80baeb">
                    <th>Sr No.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>State</th>
                    <th>Cash</th>
                    <th>Receipt Type</th>
                    <th>Ledger name</th>
                    <th>Voucher</th>
                    <th>Invoice No</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($credit_cust_data as $cdata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $cdata->date; ?></td>
                        <td><?php echo $cdata->branch_name; ?></td>
                        <td><?php echo $cdata->branch_state_name; ?></td>
                        <td><?php echo $cdata->amount; ?></td>
                        <td><?php echo $cdata->payment_head.' '.$cdata->payment_mode; ?></td>
                        <td></td>
                        <td><?php $idp = sprintf("%'.05d\n", $cdata->id_payment_reconciliation);  echo date('Y', strtotime($cdata->date)).''.$cdata->branch_code.'/V'.$idp; ?></td>
                        <td><?php echo $cdata->inv_no; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
     public function credit_note_report(){
        $q['tab_active'] = 'Tally Report';
        $q['comapny_data'] = $this->General_model->get_active_comapny();    
        $this->load->view('report/credit_note_report', $q);
    }
    public function ajax_get_credit_note_report(){
        $idcompany = $this->input->post('idcompany');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $credit_note = $this->Report_model->ajax_get_credit_note_report($from, $to, $idcompany);
        if(count($credit_note) > 0){ ?>
            <table class="table table-bordered table-condensed" id="credit_note_report">
                <thead style="background-color: #97ccef" class="fixedelement">
                    <th>Sr No.</th>
                    <th>Invoice No</th>
                    <th>Ledger Name</th>
                    <th>Month</th>
                    <th>Invoice Type</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Customer Mobile</th>
                    <th>Customer GST No</th>
                    <th>Type</th>
                    <th>Product</th>
                    <th>Hidden Discount Credit Note Gross Value</th>
                    <th>Hidden Discount Credit Note Basic Value</th>
                    <th>Hidden Discount SGST</th>
                    <th>Hidden Discount CGST</th>
                    <th>Hidden Discount IGST</th>
                    <th>Credit Note No</th>
                    <th>GST</th>
                    <th>CGST</th>
                    <th>State</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($credit_note as $cnote){ 
                        $dis_amt = 0; 
                        if($cnote->is_mop == 1){
                            if($cnote->total_amount > $cnote->mop){
                                $sale_amount = $cnote->total_amount;
                            }else{
                                $sale_amount = $cnote->mop;
                            }
                            $dis_amt = $sale_amount - $cnote->total_amount;
                        }else{
                            $sale_amount = $cnote->total_amount;  
                            $dis_amt = 0;
                        }

                        $igst = $cnote->igst_per;
                        $cgst = $cnote->cgst_per;
                        $sgst = $cnote->sgst_per;
                        $gstper = $cnote->cgst_per + $cnote->sgst_per + $cnote->igst_per;
                        $igst_amount = 0;

                        if($dis_amt > 0){

                            if($igst != 0){
                                $price = $dis_amt;
                                $cal = ($igst+100)/100;
                                $taxable = $price/$cal; 
                                $gstamt = $price - $taxable;
                                $igst_amount = $gstamt; 
                            }else{
                                $gst = $cgst + $sgst;
                                $price = $dis_amt;
                                $cal = ($gst+100)/100;
                                $taxable = $price/$cal; 
                                $gstamt = $price - $taxable;
                                $cgst = $gstamt/2;
                            }
                            ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><a href="<?php echo base_url()?>Sale/sale_details/<?php echo $cnote->idsale?>" style="color: #3333ff;cursor: pointer;" target="_blank"><?php echo $cnote->inv_no;?></a></td>
                                <td></td>
                                <td><?php echo date('M', strtotime($cnote->date));?></td>
                                <td><?php echo 'Sales'.' '.$gstper.'%';?></td>
                                <td><?php echo $cnote->date;?></td>
                                <td><?php echo $cnote->customer_fname.' '.$cnote->customer_lname;?></td>
                                <td><?php echo $cnote->customer_contact;?></td>
                                <td><?php echo $cnote->customer_gst;?></td>
                                <td><?php if($cnote->customer_gst != '' || $cnote->customer_gst != NULL){ echo 'Regular'; }else{ echo 'UNREGISTERED'; }?></td>
                                <td><?php echo $cnote->product_name;?></td>
                                <td><?php echo $dis_amt;?></td>
                                <td><?php echo number_format($taxable,2); ?></td>
                                <td><?php echo number_format($cgst,2); ?></td>
                                <td><?php echo number_format($cgst,2); ?></td>
                                <td><?php echo number_format($igst_amount,2); ?></td>
                                <td><?php echo sprintf('%07d', $cnote->idsale);?></td>
                                <td><?php echo $gstper;?></td>
                                <td><?php echo $cnote->cgst_per;?></td>
                                <td><?php echo $cnote->state_name;?></td>
                            </tr>
                    <?php } } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
    
    public function jio_router_sale(){
        $q['tab_active'] = 'Report';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/jio_router_report', $q);
    }
    public function ajax_get_jio_router_sale(){
        $idbranch = $this->input->post('idbranch');
        $allidbranch = $this->input->post('allidbranch');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $jio_sale = $this->Report_model->ajax_get_jio_router_sale_data($idbranch, $allidbranch, $from, $to);
        
        if(count($jio_sale) > 0){ ?>
            <table class="table table-bordered table-condensed" id="jio_sale_report"> 
                <thead style="background-color: #99ccff">
                    <th>Sr.</th>
                    <th>Invoice Date</th>
                    <th>DC No</th>
                    <th>Customer Name</th>
                    <th>Customer Contact</th>
                    <th>Branch Name</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($jio_sale as $jio){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $jio->date; ?></td>
                        <td><?php echo $jio->inv_no;?></td>
                        <td><?php echo $jio->customer_fname.' '.$jio->customer_lname;?></td>
                        <td><?php echo $jio->customer_contact;?></td>
                        <td><?php echo $jio->branch_name;?></td>
                        <td><?php echo $jio->product_name;?></td>
                        <td><?php echo $jio->qty;?></td>
                        <td><?php echo $jio->total_amount;?></td>
                        <td><a class="btn btn-floating btn-primary" target="_blank" href="<?php echo base_url()?>Sale/sale_details/<?php echo $jio->id_sale ?>"><span class="fa fa-pencil"></span></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php } 
    }
    
    
    public function price_category_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $this->General_model->get_branch_array_byid($_SESSION['idbranch']); 
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
//        die('<pre>'.print_r($q['branch_data'],1).'</pre>');
        $q['product_category'] = $this->General_model->get_product_category_data();
        
        $q['zone_data'] = $this->General_model->get_zone_data();
          
        $this->load->view('report/price_category_report', $q);
    }
    
     public function ajax_get_price_category_data_byzone(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $branch_data = $this->Report_model->ajax_get_branch_byidzone($idzone);
        $product_category = $this->Report_model->get_product_category_data_byid($idproductcat);
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->ajax_get_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($idzone == 'all'){ ?>
             <table class="table table-bordered table-condensed text-center" id="zone_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none;"><?php echo $price->lab_name ?></th>
                    <th style="border-right: none;border-left: none;"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev = 0;$trev=0;  foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0; $sumrev=0;
                            
                            ?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td ><?php echo $sdata->product_category_name?></td>
                           <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                 <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php } ?>
                       <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;  $all_land = 0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
       <?php }else{
        ?>
                        
            <table class="table table-bordered table-condensed text-center" id="zone_price_category_report">
                 <thead style="background-color: #99ccff;" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none;"><?php echo $price->lab_name ?></th>
                    <th style="border-right: none;border-left: none;"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev = 0;$trev=0;  foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0; $sumrev=0;
                          ?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->product_category_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; ?> 
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                 <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php } ?>
                       <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;  $all_land = 0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>

        <?php }
    }
    
    public function ajax_get_price_category_data_bybranch(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $branch_data = $this->Report_model->ajax_get_branch_byid($idbranch);
        $product_category = $this->Report_model->get_product_category_data_byid($idproductcat);
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->ajax_get_sale_product_data_bybranch($idbranch,$allbranches, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        ?>
         <table class="table table-bordered table-condensed" id="zone_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none;"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none;border-right: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev = 0;$trev=0;  foreach ($sale_data as $sdata){ 
                            $sumsaleqty =0; $sumvol=0; $sumasp=0; $sumrev=0;?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->product_category_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php } ?>
                       <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;  $all_land = 0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php 
    }
  
    
   /*   price category live old
     public function ajax_get_price_category_data_byzone(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $branch_data = $this->Report_model->ajax_get_branch_byidzone($idzone);
        $product_category = $this->Report_model->get_product_category_data_byid($idproductcat);
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->ajax_get_sale_product_data_byzone($idzone, $idproductcat, $allproductcat, $from, $to);
        
        $val=array();
        $vol=array();
        ?>
            
            <table class="table table-bordered table-condensed" id="price_category_report">
                 <thead  class="" style="background-color: #ffffcc">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th colspan="3" class="textalign" ><?php echo $price->lab_name ?></th>                    
                    <?php }?>
                    <th colspan="3" class="textalign" >Total</th>  
                </thead>
                <thead style="background-color: #ffffcc"  class="">
                    <th>Zone</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>ASP</th>
                    <?php $append_char[] = $price->pname; 
                        $val[$price->pname]=0;
                        $vol[$price->pname]=0;
                    
                    } 
                        $val['total']=0;
                        $vol['total']=0;
                    ?>
                    <th>Total Volume</th>
                    <th>Total Value</th>
                    <th> Total ASP</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=0; foreach($sale_data as $attrvalues){
                        $idtype = $attrvalues['id_branch']; ?>
                            <?php foreach ($attrvalues[$idtype] as $values){ ?>
                            <tr style="border-bottom: none">
                                <td><?php echo $attrvalues['zonename'] ?></td>
                                <td><?php echo $attrvalues['branch_name'] ?></td>
                                <td><?php echo $values->product_category_name ?></td>
                                <?php 
                                $val_t=0;$vol_t=0;
                                for($j=0; $j<count($append_char);$j++){ $chr = $append_char[$j];
                                $appchr = 'cnt_'.$append_char[$j]; 
                                $val_r=0;$vol_r=0;
                                if($values->$appchr){ $vol_r=$values->$appchr; $vol[$chr]=$vol[$chr]+$vol_r; }
                                if($values->$chr){ $val_r=$values->$chr; $val[$chr]=$val[$chr]+$val_r; }    
                                
                                $val_t=$val_t+$val_r;
                                $vol_t=$vol_t+$vol_r;
                                ?>
                                <td><?php  echo $vol_r; ?></td>
                                <td><?php  echo $val_r;  ?></td>
                                
                                <td><?php if($vol_r){ echo round(($val_r)/($vol_r)); } else{ echo 0; } ?></td>
                                <?php } 
                                
                                $vol['total']=$vol['total']+$vol_t;
                                $val['total']=$val['total']+$val_t;
                                ?>
                                <td><?php  echo $vol_t; ?></td>    
                                <td><?php  echo $val_t;  ?></td>
                                <td><?php  if($vol_t){ echo round(($val_t)/($vol_t)); } else{ echo 0; }  ?></td>
                                
                            </tr>
                            <?php } ?>                           
                    <?php } ?>
                             <tr>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <?php for($j=0; $j<count($append_char);$j++){ ?>
                                <th><?php echo $vol[$append_char[$j]] ; ?></th>
                                <th><?php echo $val[$append_char[$j]] ; ?></th>
                                <th><?php if($vol[$append_char[$j]]){ echo round((($val[$append_char[$j]]) / ($vol[$append_char[$j]]))); }else{ echo 0; } ?> </th>
                                <?php } ?>
                                 <th><?php echo $vol['total'] ; ?></th>
                                <th><?php echo $val['total'] ; ?></th>
                                <th><?php if($vol['total']){ echo round((($val['total']) / ($vol['total']))); }else{ echo 0; } ?> </th>
                            </tr>
                            
                </tbody>
            </table>

        <?php 
    }
    
    public function ajax_get_price_category_data_bybranch(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $branch_data = $this->Report_model->ajax_get_branch_byid($idbranch);
        $product_category = $this->Report_model->get_product_category_data_byid($idproductcat);
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->ajax_get_sale_product_data_bybranch($idbranch, $idproductcat, $allproductcat, $from, $to);
        ?>
            <table class="table table-bordered table-condensed" id="price_category_report">
                <thead style="background-color: #ffffcc" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th colspan="3" class="textalign" ><?php echo $price->lab_name ?></th>                    
                    <?php }?>
                    <th colspan="3" class="textalign" >Total</th>  
                </thead>
                <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th>Zone</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                  <th>ASP</th>
                    <?php $append_char[] = $price->pname; 
                        $val[$price->pname]=0;
                        $vol[$price->pname]=0;
                    
                    } 
                        $val['total']=0;
                        $vol['total']=0;
                    ?>
                    <th>Total Volume</th>
                    <th>Total Value</th>
                    <th> Total ASP</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach($sale_data as $attrvalues){
                        $idtype = $attrvalues['id_branch']; ?>
                            <?php foreach ($attrvalues[$idtype] as $values){ ?>
                            <tr style="border-bottom: none">
                                <td><?php echo $attrvalues['zonename'] ?></td>
                                <td><?php echo $attrvalues['branch_name'] ?></td>
                                <td><?php echo $values->product_category_name ?></td>
                                <?php 
                                $val_t=0;$vol_t=0;
                                for($j=0; $j<count($append_char);$j++){ $chr = $append_char[$j];
                                $appchr = 'cnt_'.$append_char[$j]; 
                                $val_r=0;$vol_r=0;
                                if($values->$appchr){ $vol_r=$values->$appchr; $vol[$chr]=$vol[$chr]+$vol_r; }
                                if($values->$chr){ $val_r=$values->$chr; $val[$chr]=$val[$chr]+$val_r; }    
                                
                                $val_t=$val_t+$val_r;
                                $vol_t=$vol_t+$vol_r;
                                ?>
                                <td><?php  echo $vol_r; ?></td>
                                <td><?php  echo $val_r;  ?></td>
                                
                                <td><?php if($vol_r){ echo round(($val_r)/($vol_r)); } else{ echo 0; } ?></td>
                                <?php } 
                                
                                $vol['total']=$vol['total']+$vol_t;
                                $val['total']=$val['total']+$val_t;
                                ?>
                                <td><?php  echo $vol_t; ?></td>    
                                <td><?php  echo $val_t;  ?></td>
                                <td><?php  if($vol_t){ echo round(($val_t)/($vol_t)); } else{ echo 0; }  ?></td>
                                
                            </tr>
                            <?php } ?>
                    <?php } ?>
                             <tr>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <?php for($j=0; $j<count($append_char);$j++){ ?>
                                <th><?php echo $vol[$append_char[$j]] ; ?></th>
                                <th><?php echo $val[$append_char[$j]] ; ?></th>
                                <th><?php if($vol[$append_char[$j]]){ echo round((($val[$append_char[$j]]) / ($vol[$append_char[$j]]))); }else{ echo 0; } ?> </th>
                                <?php } ?>
                                 <th><?php echo $vol['total'] ; ?></th>
                                <th><?php echo $val['total'] ; ?></th>
                                <th><?php if($vol['total']){ echo round((($val['total']) / ($vol['total']))); }else{ echo 0; } ?> </th>
                            </tr>
                </tbody>
            </table>
          
        <?php 
    }
  
    */
    
     public function sale_time_analysis_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['product_category'] = $this->General_model->get_product_category_data();
        
        $q['zone_data'] = $this->General_model->get_zone_data();
          
        $this->load->view('report/sale_time_analysis_report', $q);
    }
    
    public function ajax_get_sale_time_analysis_byidzone(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $timeslots = $this->General_model->get_time_slot_data();
        $sale_data = $this->Report_model->ajax_get_sale_time_analysis_data_byidzone($idzone,$allzones,$idproductcat, $allproductcat,$from,$to);
        $cluster_head = $this->General_model->get_cluster_head_data();
        
        if($sale_data){ ?>
            <!--<div  >-->
            <table class="table table-bordered table-condensed text-center" id="sale_time_analysis_report">
               <thead style="background-color: #ffffcc" class="fixheader ">
                    <th style="text-align: center"></th>
                    <th class=""></th>
                    <th  class="fixheaderleft"></th>
                    <th></th>
                    <th></th>
                    <th class="fixheaderleft1"></th>
                    <?php foreach($timeslots as $tslots){ ?>
                    <th></th>
                    <th style="border-right: none;text-align: center;"><?php echo $tslots->slot_name ?></th>
                    <th></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th style="text-align: center" class="">Zone</th>
                    <th style="text-align: center">Cluster Head</th>
                    <th style="text-align: center" class="fixheaderleft">Branch</th>
                    <th style="text-align: center">Partner Type</th>
                    <th style="text-align: center">Branch Category</th>
                    <th style="text-align: center" class="fixheaderleft1">Product Category</th>
                   <?php foreach($timeslots as $tslots){ ?>
                    <th style="text-align: center">Volume</th>
                    <th style="text-align: center">Value</th>
                    <th style="text-align: center">Asp</th>
                   <?php } ?>
                    <th>Volume Total</th>
                    <th>Value Total</th>
                    <th>Asp Total</th>
                </thead>
                <tbody class="data_1">
                    <?php $total = []; $tqty=0;$tval=0;$tasp=0;   foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0; ?>
                    <tr>
                        <td class=""><?php echo $sdata->zone_name?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sdata->id_branch){
                                        echo $clust->clust_name.',';
                                    }
                                } ?>
                        </td>
                        <td class="fixheaderleft"><?php echo $sdata->branch_name?></td>
                        <td><?php echo $sdata->partner_type?></td>
                        <td><?php echo $sdata->branch_category_name?></td>
                        <td class="fixheaderleft1"><?php echo $sdata->product_category_name?></td>
                        <?php foreach($timeslots as $tslots){ 
                            $sale_qty = 'saleqt'.$tslots->id_time_slab; 
                            $sale_amount = 'total'.$tslots->id_time_slab; ?>
                            <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                            <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                            <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp); $sumasp = $sumasp + $asp; ?></td>
                        <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                              $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                              $total['t_asp'.$sale_amount][] = $asp;
                        } ?>
                        <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                        <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?><b></td>
                        <td><b><?php if($sumsaleqty > 0){ echo round($sumvol/$sumsaleqty);}else{ echo '0';} $tasp = $tasp + $sumasp; ?></b></td>
                            
                    </tr>
                    <?php } ?>
                    <tr >
                        <td></td>
                        <td></td>
                        <td class="fixheaderleft"></td>
                        <td></td>
                        <td></td>
                        <td class="fixheaderleft1"><b>Total</b></td>
                        <?php foreach($timeslots as $tslots){ 
                             $arrqt=0;$arrval=0; $arrasp=0; ?>
                        <td><b><?php $arrqt = array_sum($total['tqtsaleqt'.$tslots->id_time_slab]); echo $arrqt;  ?></b></td>
                        <td><b><?php $arrval = array_sum($total['tvaltotal'.$tslots->id_time_slab]); echo $arrval; ?></b></td>
                        <td><b><?php if($arrqt > 0){ $arrasp = $arrval/$arrqt;}else{ $arrasp =0;} echo round($arrasp);  // echo round(array_sum($total['t_asptotal'.$tslots->id_time_slab])); ?></b></td>
                        <?php }?>
                        <td><b><?php echo round($tqty); ?></b></td>
                        <td><b><?php echo round($tval); ?></b></td>
                        <td><b><?php  if($tqty > 0){ echo round($tval/$tqty);}else{ echo '0'; } ?></b></td>
                    </tr>
                </tbody>
            </table>
            <!--</div>-->
        <?php }
    }
    public function ajax_get_sale_time_analysis_byidbranch(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $timeslots = $this->General_model->get_time_slot_data();
        $sale_data = $this->Report_model->ajax_get_sale_time_analysis_data_byidbranch($idbranch,$allbranches,$idproductcat, $allproductcat,$from,$to);
        $cluster_head = $this->General_model->get_cluster_head_data();
        
        if($sale_data){ ?>
            <!--<div  style="overflow-x: auto;height: 700px;">-->
                <table class="table table-bordered table-condensed text-center" id="sale_time_analysis_report">
                   <thead style="background-color: #ffffcc" class="fixheader">
                        <th></th>
                        <th></th>
                        <th class="fixheaderleft"></th>
                        <th></th>
                        <th></th>
                        <th  class="fixheaderleft1"></th>
                        <?php foreach($timeslots as $tslots){ ?>
                        <th ></th>
                        <th  style="border-right: none;text-align: center" ><?php echo $tslots->slot_name ?></th>
                        <th ></th>
                        <?php }?>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                    <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Cluster Head</th>
                        <th style="text-align: center" class="fixheaderleft">Branch</th>
                        <th style="text-align: center">Partner Type</th>
                        <th style="text-align: center">Branch Category</th>
                        <th style="text-align: center" class="fixheaderleft1">Product Category</th>
                       <?php foreach($timeslots as $tslots){ ?>
                        <th style="text-align: center"> Volume</th>
                        <th style="text-align: center">Value</th>
                        <th style="text-align: center">Asp</th>
                       <?php } ?>
                        <th>Volume Total</th>
                        <th>Value Total</th>
                        <th>Asp Total</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $total = []; $tqty=0;$tval=0;$tasp=0;  foreach ($sale_data as $sdata){ 
                            $sumsaleqty =0; $sumvol=0; $sumasp=0;?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php foreach ($cluster_head as $clust){ if($clust->clustbranch == $sdata->id_branch){echo $clust->clust_name.',';}} ?></td>
                            <td class="fixheaderleft"><?php echo $sdata->branch_name?></td>
                            <td><?php echo $sdata->partner_type?></td>
                            <td><?php echo $sdata->branch_category_name?></td>
                            <td class="fixheaderleft1"><?php echo $sdata->product_category_name?></td>
                            <?php foreach($timeslots as $tslots){
                                $sale_qty = 'saleqt'.$tslots->id_time_slab; 
                                $sale_amount = 'total'.$tslots->id_time_slab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp); $sumasp = $sumasp + $asp;  ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['t_asp'.$sale_amount][] = $asp;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php if($sumsaleqty > 0){ echo round($sumvol/$sumsaleqty);}else{ echo '0';} $tasp = $tasp + $sumasp; ?></b></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="fixheaderleft"></td>
                            <td></td>
                            <td></td>
                            <td class="fixheaderleft1"><b>Total</b></td>
                            <?php foreach($timeslots as $tslots){
                                 $arrqt=0;$arrval=0; $arrasp=0; ?>
                                <td><b><?php $arrqt = array_sum($total['tqtsaleqt'.$tslots->id_time_slab]); echo $arrqt;  ?></b></td>
                                <td><b><?php $arrval = array_sum($total['tvaltotal'.$tslots->id_time_slab]); echo $arrval; ?></b></td>
                                <td><b><?php if($arrqt > 0){ $arrasp = $arrval/$arrqt;}else{ $arrasp =0;} echo round($arrasp);  // echo round(array_sum($total['t_asptotal'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ echo round($tval/$tqty);}else{ echo '0'; } ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <!--</div>-->
        <?php }
    }
    
    
    public function ajax_get_sale_time_analysis_byidzone_old(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $timeslots = $this->General_model->get_time_slot_data();
        $sale_data = $this->Report_model->ajax_get_sale_time_analysis_data_byidzone($idzone,$allzones,$idproductcat, $allproductcat,$from,$to);
        $cluster_head = $this->General_model->get_cluster_head_data();
        
        if($sale_data){ ?>
            <!--<div  >-->
            <table class="table table-bordered table-condensed text-center" id="sale_time_analysis_report">
               <thead style="background-color: #ffffcc" class="fixheader ">
               <th style="text-align: center"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($timeslots as $tslots){ ?>
                    <th style="border-right: none;text-align: center;"><?php echo $tslots->slot_name ?></th>
                    <th style="border-left: none;text-align: center;"></th>
                    <?php }?>
                </thead>
                <thead style="background-color: #ffffcc"  class="fixheader1">
                <th style="text-align: center">Zone</th>
                    <th style="text-align: center">Cluster Head</th>
                    <th style="text-align: center">Branch</th>
                    <th style="text-align: center">Partner Type</th>
                    <th style="text-align: center">Branch Category</th>
                    <th style="text-align: center">Product Category</th>
                   <?php foreach($timeslots as $tslots){ ?>
                    <th style="text-align: center">Volume</th>
                    <th style="text-align: center">Value</th>
                   <?php } ?>
                </thead>
                <tbody class="data_1">
                    <?php $total = [];  foreach ($sale_data as $sdata){ ?>
                    <tr>
                        <td><?php echo $sdata->zone_name?></td>
                          <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sdata->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sdata->branch_name?></td>
                        <td><?php echo $sdata->partner_type?></td>
                        <td><?php echo $sdata->branch_category_name?></td>
                        <td><?php echo $sdata->product_category_name?></td>
                        <?php foreach($timeslots as $tslots){
                            $sale_qty = 'saleqt'.$tslots->id_time_slab; 
                            $sale_amount = 'total'.$tslots->id_time_slab; ?>
                            <td><?php echo $sdata->$sale_qty; ?></td>
                            <td><?php echo $sdata->$sale_amount;  ?></td>
                        <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                              $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                        } ?>
                    </tr>
                    <?php } ?>
                    <tr >
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <?php foreach($timeslots as $tslots){ ?>
                        <td><b><?php echo array_sum($total['tqtsaleqt'.$tslots->id_time_slab]); ?></b></td>
                        <td><b><?php echo array_sum($total['tvaltotal'.$tslots->id_time_slab]); ?></b></td>
                        <?php }?>
                    </tr>
                </tbody>
            </table>
            <!--</div>-->
        <?php }
    }
    public function ajax_get_sale_time_analysis_byidbranch_old(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $timeslots = $this->General_model->get_time_slot_data();
        $sale_data = $this->Report_model->ajax_get_sale_time_analysis_data_byidbranch($idbranch,$allbranches,$idproductcat, $allproductcat,$from,$to);
        $cluster_head = $this->General_model->get_cluster_head_data();
        
        if($sale_data){ ?>
            <!--<div  style="overflow-x: auto;height: 700px;">-->
                <table class="table table-bordered table-condensed text-center" id="sale_time_analysis_report">
                   <thead style="background-color: #ffffcc" class="fixheader">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <?php foreach($timeslots as $tslots){ ?>
                        <th style="border-right: none;text-align: center"><?php echo $tslots->slot_name ?></th>
                        <th style="border-left: none;text-align: center"></th>
                        <?php }?>
                    </thead>
                    <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Cluster Head</th>
                        <th style="text-align: center">Branch</th>
                        <th style="text-align: center">Partner Type</th>
                        <th style="text-align: center">Branch Category</th>
                        <th style="text-align: center">Product Category</th>
                       <?php foreach($timeslots as $tslots){ ?>
                        <th style="text-align: center"> Volume</th>
                        <th style="text-align: center">Value</th>
                       <?php } ?>
                    </thead>
                    <tbody class="data_1">
                        <?php $total = [];  foreach ($sale_data as $sdata){ ?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php foreach ($cluster_head as $clust){ if($clust->clustbranch == $sdata->id_branch){echo $clust->clust_name.', ';}} ?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td><?php echo $sdata->partner_type?></td>
                            <td><?php echo $sdata->branch_category_name?></td>
                            <td><?php echo $sdata->product_category_name?></td>
                            <?php foreach($timeslots as $tslots){
                                $sale_qty = 'saleqt'.$tslots->id_time_slab; 
                                $sale_amount = 'total'.$tslots->id_time_slab; ?>
                                <td><?php echo $sdata->$sale_qty; ?></td>
                                <td><?php echo $sdata->$sale_amount;  ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                            } ?>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($timeslots as $tslots){ ?>
                            <td><b><?php echo array_sum($total['tqtsaleqt'.$tslots->id_time_slab]); ?></b></td>
                            <td><b><?php echo array_sum($total['tvaltotal'.$tslots->id_time_slab]); ?></b></td>
                            <?php }?>
                        </tr>
                    </tbody>
                </table>
            <!--</div>-->
        <?php }
    }
    
     public function zone_price_category_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['product_category'] = $this->General_model->get_product_category_data();
        
        $q['zone_data'] = $this->General_model->get_zone_data();
          
        $this->load->view('report/zone_price_category_report', $q);
    }
    public function ajax_get_zone_price_category_data_byzone() {
    
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $branch_data = $this->Report_model->ajax_get_branch_byidzone($idzone);
        $product_category = $this->Report_model->get_product_category_data_byid($idproductcat);
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->ajax_get_zonesale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to);
//        $sale_data1 = $this->Report_model->ajax_get_sale_product_data_byzone1($idzone, $idproductcat, $allproductcat);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        ?>
            <div style="overflow-x: auto;height: 700px;">
            <table class="table table-bordered table-condensed" id="zone_price_category_report">
                 <thead style="background-color: #ffffcc" class="fixheader">
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th></th>
                    <th style="border-right: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th>Zone</th>
                    <th>Category</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0;  foreach ($sale_data as $sdata){ 
                            $sumsaleqty =0; $sumvol=0; $sumasp=0;?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td ><?php echo $sdata->product_category_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                        </tr>
                        <?php } ?>
                       <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                        </tr>
                </tbody>
            </table>
        </div>
        <?php 
        
    }
    
    //Brand & Promotor Wise price category report
     public function brand_price_category_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $this->General_model->get_branch_array_byid($_SESSION['idbranch']); 
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
//        die('<pre>'.print_r($q['branch_data'],1).'</pre>');
        $q['product_category'] = $this->General_model->get_product_category_data();
        
        $q['zone_data'] = $this->General_model->get_zone_data();
          
        $this->load->view('report/brand_price_category_report', $q);
    }
    
    public function ajax_get_brand_price_category_data_bybranch(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->get_brand_sale_product_data_bybranch($idbranch,$allbranches, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        ?>
         <table class="table table-bordered table-condensed" id="brand_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-right: none;border-left: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Brand</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0;   $rev = 0;$trev=0;
                   $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                    $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;
                    $old_name=$sale_data[0]->id_zone;
                    foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0;$sumrev=0;
                        
                        //Zone Wise Total
                        if($old_name == $sdata->id_zone){
                           foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab;
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }else{ ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <?php foreach($price_cat as $pslots){ ?>
                                <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;}  ?></b></td>
                                <td><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php  echo round($zone_rev); ?></b></td>
                            </tr>
                            <?php   
                            $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                            $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;
                    
                            foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                 $landing = 'landing'.$pslots->id_price_category_lab;
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }?>
                    
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->brand_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab;?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                 <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php  $old_name=$sdata->id_zone; } ?>
                         <!--//Zone Wise Total-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($price_cat as $pslots){ ?>
                            <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                            <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                            <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $b_rev = $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev; $zone_rev = $zone_rev + $b_rev; ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($zone_tqty); ?></b></td>
                            <td><b><?php echo round($zone_tval); ?></b></td>
                            <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                            <td><b><?php  echo round($zone_rev); ?></b></td>
                        </tr>
                       <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;$all_land=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                             <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php 
    }
  
      public function ajax_get_brand_price_category_data_byzone(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->get_brand_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($idzone == 'all'){ ?>
            <table class="table table-bordered table-condensed" id="brand_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none;border-right: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Brand</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev=0; $trev=0;
                    
                   foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0; $sumrev=0; ?>
                        
                       <tr>
                            <td ><?php echo $sdata->brand_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab;
                                $landing = 'landing'.$pslots->id_price_category_lab;
                                ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php   } ?>
                       <tr>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;$all_rev=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                             <td><b><?php echo $trev; ?></b></td>
                            
                        </tr>
                </tbody>
            </table>
        <?php }
//        elseif($idzone == '0')
           else { ?>
            <table class="table table-bordered table-condensed" id="brand_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none;border-right: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                    <th>Brand</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0;  $rev = 0;$trev=0;
                    $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                    $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;
                   $old_name = $sale_data[0]->id_zone;
                    
                   foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0;$sumrev=0;
                        
                     //Zone Wise Total
                        if($old_name == $sdata->id_zone){
                           foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; 
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }else{ ?>
                            <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <?php foreach($price_cat as $pslots){ ?>
                                <td><b><?php $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                                <td><b><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></b></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php echo round($zone_rev); ?></b></td>
                            </tr>
                            <?php   
                             $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                            $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0;$zone_rev=0;
                            foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; 
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }?>
                       <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td ><?php echo $sdata->brand_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab;
                                $landing = 'landing'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php $old_name=$sdata->id_zone;    } ?>
                        <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <?php foreach($price_cat as $pslots){ ?>
                                <td><b><?php $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                                <td><b><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></b></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php echo round($zone_rev); ?></b></td>
                            </tr>
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0; $all_rev=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                        <!--//Zone Wise Total
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($price_cat as $pslots){ ?>
                            <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                            <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                            <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($zone_tqty); ?></b></td>
                            <td><b><?php echo round($zone_tval); ?></b></td>
                            <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                        </tr>
                       <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php }
//        else{
        ?>
<!--         <table class="table table-bordered table-condensed" id="brand_price_category_report">
                 <thead style="background-color: #ffffcc" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th></th>
                    <th style="border-right: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #ffffcc"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Brand</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; 
                    $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                    $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; 
                    $old_name=$sale_data[0]->id_zone;
                    foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0;
                             
                            //Zone Wise Total
                            if($old_name == $sdata->id_zone){
                               foreach($price_cat as $pslots){ 
                                   $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                    $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                    $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                    $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                               }
                            }else{ ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <?php foreach($price_cat as $pslots){ ?>
                                    <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                    <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                    <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                                    <?php }?>
                                    <td><b><?php echo round($zone_tqty); ?></b></td>
                                    <td><b><?php echo round($zone_tval); ?></b></td>
                                    <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                </tr>
                                <?php   
                                 $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0;
                                foreach($price_cat as $pslots){ 
                                   $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                    $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                    $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                    $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                    
                               }
                            }?>
                    
                    
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->brand_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                        </tr>
                        <?php  $old_name=$sdata->id_zone; } ?>
                        
                        //Zone Wise Total
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($price_cat as $pslots){ ?>
                            <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                            <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                            <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($zone_tqty); ?></b></td>
                            <td><b><?php echo round($zone_tval); ?></b></td>
                            <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                        </tr>
                        Overall Total
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                        </tr>
                </tbody>
            </table>-->
        <?php //}
    }
    
     public function promotor_price_category_report(){
        $q['tab_active'] = 'Report';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $this->General_model->get_branch_array_byid($_SESSION['idbranch']); 
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
//        die('<pre>'.print_r($q['branch_data'],1).'</pre>');
        $q['product_category'] = $this->General_model->get_product_category_data();
        
        $q['zone_data'] = $this->General_model->get_zone_data();
          
        $this->load->view('report/promotor_price_category_report', $q);
    }
    
     public function ajax_get_promotor_price_category_data_bybranch(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allbranches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->get_promotor_sale_product_data_bybranch($idbranch,$allbranches, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        ?>
         <table class="table table-bordered table-condensed" id="promotor_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-right: none;border-left: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Promotor</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev = 0;$trev=0;
                   $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                    $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;
                    $old_name=$sale_data[0]->id_zone;
                    foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0;$sumrev=0;
                        
                        //Zone Wise Total
                        if($old_name == $sdata->id_zone){
                           foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                 $landing = 'landing'.$pslots->id_price_category_lab;
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }else{ ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <?php foreach($price_cat as $pslots){ ?>
                                <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                                <td><b><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></b></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php  echo round($zone_rev); ?></b></td>
                            </tr>
                            <?php   
                            $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                            $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;$b_rev=0;
                    
                            foreach($price_cat as $pslots){ 
                               $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab;
                                $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                           }
                        }?>
                    
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->user_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab;?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php  $old_name=$sdata->id_zone; } ?>
                         <!--//Zone Wise Total-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($price_cat as $pslots){ ?>
                            <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                            <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                            <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                             <td><b><?php $b_rev = $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev; $zone_rev = $zone_rev + $b_rev; ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($zone_tqty); ?></b></td>
                            <td><b><?php echo round($zone_tval); ?></b></td>
                            <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                            <td><b><?php  echo round($zone_rev); ?></b></td>
                        </tr>
                       <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;$all_land=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php 
    }
    
     public function ajax_get_promotor_price_category_data_byzone(){
        $idzone = $this->input->post('idzone');
        $idproductcat = $this->input->post('idproductcat');
        $allproductcat = $this->input->post('allpcats');
        $allzones = $this->input->post('allzones');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        
        $price_cat = $this->Report_model->get_price_category_lab_data();
        
        $sale_data = $this->Report_model->get_promotor_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($idzone == 'all'){ ?>
            <table class="table table-bordered table-condensed" id="promotor_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none;border-right: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; $rev=0; $trev=0; foreach ($sale_data as $sdata){ 
                            $sumsaleqty =0; $sumvol=0; $sumasp=0; $sumrev=0;?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                 $landing = 'landing'.$pslots->id_price_category_lab;?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php } ?>
                       <tr>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;$all_land=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                           <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                             <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php }else{
        ?>
         <table class="table table-bordered table-condensed" id="promotor_price_category_report">
                 <thead style="background-color: #99ccff" class="fixheader">
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php foreach($price_cat as $price){ ?>
                    <th style="border-right: none"></th>
                    <th style="border-right: none;border-left: none"><?php echo $price->lab_name ?></th>
                    <th style="border-left: none;border-right: none"></th>
                    <th style="border-left: none"></th>
                    <?php }?>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead style="background-color: #99ccff"  class="fixheader1">
                    <th>Zone</th>
                     <th>Branch</th>
                    <th>Promotor</th>
                    <?php foreach($price_cat as $price){ ?>
                    <th>Volume</th>
                    <th>Value</th>
                    <th>Asp</th>
                    <th>Revenue</th>
                    <?php  } ?>
                    <th>Total Value</th>
                    <th>Total Volume</th>
                    <th>Total ASP</th>
                    <th>Total Revenue</th>
                </thead>
                <tbody class="data_1">
                   <?php $total = []; $tqty=0;$tval=0;$tasp=0; 
                    $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                    $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0; $zone_rev=0;$rev = 0;$trev=0;
                    $old_name=$sale_data[0]->id_zone;
                    foreach ($sale_data as $sdata){ 
                        $sumsaleqty =0; $sumvol=0; $sumasp=0;$sumrev=0;
                             
                            //Zone Wise Total
                            if($old_name == $sdata->id_zone){
                               foreach($price_cat as $pslots){ 
                                    $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                    $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                    $landing = 'landing'.$pslots->id_price_category_lab; 
                                    $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                    $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                    $zonetotal['tlanding'.$landing][] = $sdata->$landing;  
                               }
                            }else{ ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <?php foreach($price_cat as $pslots){ ?>
                                    <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                                    <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                                    <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                                    <td><b><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></b></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php echo round($zone_rev); ?></b></td>
                                </tr>
                                <?php   
                                 $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                $zonetotal = []; $zone_tqty=0;$zone_tval=0;$zone_tasp=0;$zone_rev=0;
                                foreach($price_cat as $pslots){ 
                                   $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                    $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                    $landing = 'landing'.$pslots->id_price_category_lab; 
                                    $zonetotal['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                    $zonetotal['tval'.$sale_amount][] = $sdata->$sale_amount;  
                                    $zonetotal['tlanding'.$landing][] = $sdata->$landing;
                               }
                            }?>
                        <tr>
                            <td><?php echo $sdata->zone_name?></td>
                            <td><?php echo $sdata->branch_name?></td>
                            <td ><?php echo $sdata->user_name?></td>
                            <?php foreach($price_cat as $pslots){
                                $sale_qty = 'saleqt'.$pslots->id_price_category_lab; 
                                $sale_amount = 'total'.$pslots->id_price_category_lab; 
                                $landing = 'landing'.$pslots->id_price_category_lab; ?>
                                <td><?php if($sdata->$sale_qty){ echo $sdata->$sale_qty;}else{ echo 0; }  $sumsaleqty = $sumsaleqty + $sdata->$sale_qty; ?></td>
                                <td><?php if($sdata->$sale_amount){ echo $sdata->$sale_amount; } else{ echo 0;} $sumvol = $sumvol + $sdata->$sale_amount; ?></td>
                                <td><?php if($sdata->$sale_qty > 0){ $asp = $sdata->$sale_amount/$sdata->$sale_qty; } else{ $asp = 0;} echo round($asp);   ?></td>
                                <td><?php $rev = $sdata->$sale_amount - $sdata->$landing; echo $rev; $sumrev = $sumrev + $rev; ?></td>
                            <?php $total['tqt'.$sale_qty][] = $sdata->$sale_qty;
                                  $total['tval'.$sale_amount][] = $sdata->$sale_amount;
                                  $total['tlanding'.$landing][] = $sdata->$landing;
                            } ?>
                            <td><b><?php echo round($sumsaleqty); $tqty = $tqty + $sumsaleqty; ?></b></td>
                            <td><b><?php echo round($sumvol); $tval = $tval + $sumvol; ?></b></td>
                            <td><b><?php  if($sumsaleqty > 0){ $sumasp = $sumvol/$sumsaleqty;} else{ $sumasp = 0;}echo round($sumasp); ?></b></td>
                            <td><b><?php  echo $sumrev; $trev = $trev + $sumrev; ?></b></td>
                        </tr>
                        <?php  $old_name=$sdata->id_zone; } ?>
                        
                        <!--//Zone Wise Total-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php foreach($price_cat as $pslots){ ?>
                            <td><b><?php    $b_volum = array_sum($zonetotal['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($b_volum); $zone_tqty = $zone_tqty + $b_volum; ?></b></td>
                            <td><b><?php $b_value = array_sum($zonetotal['tvaltotal'.$pslots->id_price_category_lab]);  echo round($b_value);  $zone_tval =  $zone_tval + $b_value; ?></b></td>
                            <td><b><?php if($b_volum > 0){ echo round($b_value/$b_volum); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            <td><b><?php $b_rev =  $b_value - array_sum($zonetotal['tlandinglanding'.$pslots->id_price_category_lab]); echo $b_rev;  $zone_rev = $zone_rev + $b_rev; ?></b></td>
                                <?php }?>
                                <td><b><?php echo round($zone_tqty); ?></b></td>
                                <td><b><?php echo round($zone_tval); ?></b></td>
                                <td><b><?php if($zone_tqty > 0){ $zone_tasp = $zone_tval / $zone_tqty;}else{$zone_tasp = 0;} echo round($zone_tasp); ?></b></td>
                                <td><b><?php echo round($zone_rev); ?></b></td>
                        </tr>
                        <!--Overall Total-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <?php $all_val= 0; $all_volume=0;$all_rev=0; foreach($price_cat as $pslots){ ?>
                            <td><b><?php $all_val = array_sum($total['tqtsaleqt'.$pslots->id_price_category_lab]); echo round($all_val); ?></b></td>
                            <td><b><?php $all_volume = array_sum($total['tvaltotal'.$pslots->id_price_category_lab]);  echo round($all_volume); ?></b></td>
                            <td><b><?php if($all_val > 0){ echo round($all_volume/$all_val); }else{ echo 0;} //echo round(array_sum($total['t_asptotal'.$pslots->id_price_category_lab])); // round(array_sum($total['tasp'.$tslots->id_time_slab])); ?></b></td>
                            
                             <td><b><?php $all_land = array_sum($total['tlandinglanding'.$pslots->id_price_category_lab]); echo $all_volume - $all_land;  ?></b></td>
                            <?php }?>
                            <td><b><?php echo round($tqty); ?></b></td>
                            <td><b><?php echo round($tval); ?></b></td>
                            <td><b><?php if($tqty > 0){ $tasp = $tval / $tqty;}else{$tasp = 0;} echo round($tasp); ?></b></td>
                            <td><b><?php echo $trev; ?></b></td>
                        </tr>
                </tbody>
            </table>
        <?php }
    }
    
     public function tally_jio_router_sale(){
        $q['tab_active'] = 'Report';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('report/tally_jio_router_report', $q);
    }
    public function ajax_get_tally_jio_router_sale(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        $dc = 1;
        
        $sale_data = $this->Sale_model->get_tally_sale_report($from, $to, $idcompany, $dc);
        $payment_mode_data = $this->General_model->get_active_payment_mode_data();
        if(count($sale_data) >0){
        ?>
        <table id="tally_jio_sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelementtop">
                <th>Sr</th>
                <th>Invoice No</th>
                <!--<th>Sale Type</th>-->
                <th>HSN</th>
                <th>Invoice Date</th>
                <th>Customer Name</th>
                <th>Customer Mobile</th>
                <!--<th>Customer GST No</th>-->
                <!--<th>Type</th>-->
                <th>State</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <!--<th>Base Price</th>-->
                <!--<th>CGST</th>-->
                <!--<th>SGST</th>-->
                <!--<th>IGST</th>-->
                <!--<th>Round Up</th>-->
                <!--<th>Total Amount</th>-->
                <!--<th>GST %</th>-->
                <!--<th>CGST</th>-->
                <!--<th>Discount</th>-->
                <th>Total Settelment</th>
                <?php foreach($payment_mode_data as $pmode){ ?>
                    <th><?php echo $pmode->payment_mode ?></th>
                    <?php if($pmode->tranxid_type != ''){ echo '<th>'.$pmode->tranxid_type.'</th>'; } ?>
                <?php }?>
               <th>Details</th>
                <th>Print</th>
                
            </thead>
            <tbody class="data_1">
                <?php  $sr=1; $totalround = 0; $total =0; $total_base=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$cmobine=0; $saleid=0;
                $old_inv=null;
                foreach ($sale_data as $sale) { 
                 
                        if($sale->is_mop == 1){
                            if($sale->total_amount > $sale->mop){
                                $sale_amount = $sale->total_amount;
                            }else{
                                $sale_amount = $sale->mop;
                            }
                        }else{
                            $sale_amount = $sale->total_amount;   
                        }
                    
                    $igst = $sale->igst_per;
                    $cgst = $sale->cgst_per;
                    $sgst = $sale->sgst_per;
                   
                    $igst_amount = 0;
                    if($igst != 0){
                        $price = $sale_amount;
                        $cal = ($igst+100)/100;
                        $taxable = $price/$cal; 
                        $gstamt = $price - $taxable;
                        $igst_amount = $gstamt; 
                        
                    }else{
                        $gst = $cgst + $sgst;
                        $price = $sale_amount;
                        $cal = ($gst+100)/100;
                        $taxable = $price/$cal; 
                        $gstamt = $price - $taxable;
                        $cgst = $gstamt/2;
                    }
                    ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $sale->inv_no;  ?></td>
                    <!--<td><?php echo 'Sales'; echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per.'%' ?></td>-->
                    <td><?php echo $sale->hsn?></td>
                    <td><?php echo date('d/m/Y', strtotime($sale->entry_time)) ?></td>
                    <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                    <td><?php echo $sale->customer_contact ?></td>
                    <!--<td><?php echo $sale->customer_gst ?></td>-->
                    <!--<td><?php if($sale->customer_gst == ''){echo 'UNREGISTERED'; }else{ echo 'Regular'; }?></td>-->
                    <td><?php echo $sale->customer_state; ?></td>
                    <td><?php echo $sale->brand_name; ?></td>
                    <td><?php echo $sale->product_name;  ?></td>
                    <td><?php echo $sale->qty; ?></td>
                    <!--<td><?php $total_base = $total_base + $taxable; echo number_format($taxable,3) ?></td>-->
                    <!--<td><?php $total_cgst = $total_cgst + $cgst; echo number_format($cgst,3);  ?></td>-->
                    <!--<td><?php echo number_format($cgst,3);  ?></td>-->
                    <!--<td><?php $total_igst = $total_cgst + $igst_amount; echo number_format($igst_amount,3); ?></td>-->
                    <!--<td><?php $tobase = $cgst + $cgst + $igst_amount + $taxable;   $trount = number_format(($sale_amount - $tobase),2);  echo $trount; // $totalround = $totalround + $trount;?></td>-->
                    <!--<td><?php $total = $total + $sale_amount; echo $sale_amount; ?></td>-->
                    <!--<td><?php echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per ?></td>-->
                    <!--<td><?php echo $sale->cgst_per ?></td>-->                    
                    <!--<td><?php echo ($sale_amount-$sale->total_amount) ?></td>-->    
                    <?php if($old_inv==null){ ?>
                        <td><?php echo $sale->total_settlement ?></td>
                     <?php        for($i=0; $i < count($payment_mode_data); $i++){
                                ?>
                                <td><?php if($cmobine == 0){  $mdn = $payment_mode_data[$i]->payment_mode; $mdntrans = $payment_mode_data[$i]->payment_mode."_transaction_id"; 
                                echo $sale->$mdn; } ?>
                                </td>
                                <?php if($payment_mode_data[$i]->tranxid_type != NULL){ ?>
                                    <td><?php  echo $sale->$mdntrans; ?></td>
                                <?php }  ?>
                            <?php }  ?>
                             <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                             <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                   <?php
                    }elseif($old_inv==$sale->inv_no){ ?>
                        <td></td>
                       <?php  for($i=0; $i < count($payment_mode_data); $i++){ ?>                                
                                <td></td>       
                                <?php if($payment_mode_data[$i]->tranxid_type != NULL){ ?>
                                    <td></td>
                                 <?php }  ?>                                
                        <?php }
                    }else{         ?>               
                       <td><?php echo $sale->total_settlement ?></td>
                       <?php 
                            for($i=0; $i < count($payment_mode_data); $i++){
                                ?>
                                <td><?php if($cmobine == 0){  $mdn = $payment_mode_data[$i]->payment_mode; $mdntrans = $payment_mode_data[$i]->payment_mode."_transaction_id";  
                                echo $sale->$mdn; } ?>
                                </td>
                                <?php if($payment_mode_data[$i]->tranxid_type != NULL){ ?>
                                    <td><?php  echo $sale->$mdntrans; ?></td>
                                <?php }  ?>
                            <?php }  
                            ?>
                             <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                             <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                   <?php
                } ?>
                    
                   
                   
               </tr>
                <?php $old_inv=$sale->inv_no; } ?>
<!--                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo number_format($total_base,2); ?></b></td>
                    <td><b><?php echo number_format($total_cgst,2); ?></b></td>
                    <td><b><?php echo number_format($total_cgst,2); ?></b></td>
                    <td><b><?php echo number_format($total_igst,2); ?></b></td>
                    <td><b><?php echo number_format($totalround,2); ?></b></td>
                    <td><b><?php echo number_format($total,2); ?></b></td>
                    <td></td>
                </tr>-->
            </tbody>
        </table>
        <?php $cmobine =0;
        }else{ ?>
           <script>
               $(document).ready(function (){
                  alert("Data Not Found"); 
               });
           </script> 
        <?php }
        
    }
    
       public function tally_sales_return_report(){
        $q['tab_active'] = 'Tally Report';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['company_data'] = $this->General_model->get_company_data();

        $this->load->view('report/tally_sales_return_report', $q);  
    }
    
    public function ajax_get_tally_sale_return_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
//        die(print_r($_POST));
        $sales_return = $this->Report_model->ajax_get_tally_sales_return_product_data($from, $to, $idcompany, $idpcat, $idbrand);
//        die('<pre>'.print_r($sales_return,1).'</pre>');
        
        if($sales_return){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;" id="tally_sale_return_report">
                <thead class="fixedelementtop">
                    <th>Sr</th>
                    <th>Sales Return Date</th>
                    <th>Sales Return No</th>
                    <th>Original Invoice No</th>
                    <th>Original Invoice Date</th>
                    <th>Invoice Type</th>
                    <th>Type</th>
                    <th>Customer GST No</th>
                    <th>Customer Name</th>
                    <th>Customer Mobile</th>
                    <th>Address</th>
                    <th>Place</th>
                    <th>Pincode</th>
                    <th>State</th>
                    <th>County</th>
                    <th>Qty</th>
                    <th>Product Name</th>
                    <th>Category name</th>
                    <th>HSN code</th>
                    <th>Base Price</th>
                    <th>IGST</th>
                    <th>SGST</th>
                    <th>CGST</th>
                    <th>IGST(%)</th>
                    <th>CGST(%)</th>
                    <th>SGST(%)</th>
                    <th>Roundoff</th>
                    <th>Total Amount</th>
                    <th>Settlement Type</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($sales_return as $return){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo date('d/m/Y', strtotime($return->date)); ?></td>
                        <td><?php echo $return->sales_return_invid ?></td>
                        <td><a href="<?php echo base_url('Sale/sale_details/'.$return->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $return->inv_no ?></a></td>
                        <td><?php echo $return->inv_date ?></td>
                        <td><?php $per = $return->cgst_per + $return->sgst_per + $return->igst_per; echo 'Sales '.$per.'%' ?></td>
                        <td><?php  if($return->customer_gst  == ''){ echo 'Unregistered'; }else{ echo 'Regular'; } ?></td>
                        <td><?php echo $return->customer_gst ?></td>
                        <td><?php echo $return->customer_fname.' '.$return->customer_lname ?></td>
                        <td><?php echo $return->customer_contact ?></td>
                        <td><?php echo $return->customer_address ?></td>
                        <td><?php echo $return->customer_district ?></td>
                        <td><?php echo $return->customer_pincode ?></td>
                        <td><?php echo $return->customer_state ?></td>
                        <td><?php echo 'India'; ?></td>
                        <td><?php echo $return->qty ?></td>
                        <td><?php echo $return->product_name ?></td>
                        <td><?php echo $return->product_category_name ?></td>
                        <td><?php echo $return->hsn ?></td>
                        <td><?php echo number_format($return->taxable_amt,2) ?></td>
                        <td><?php echo number_format($return->igst_amt,2) ?></td>
                        <td><?php echo number_format($return->sgst_amt,2) ?></td>
                        <td><?php echo number_format($return->cgst_amt,2) ?></td>
                        <td><?php echo 'Output IGST '. $return->igst_per.'%'?></td>
                        <td><?php echo 'Output SGST '. $return->sgst_per.'%'?></td>
                        <td><?php echo 'Output CGST '. $return->cgst_per.'%'?></td>
                        <td></td>
                        <td><?php echo number_format($return->total_amount,2) ?></td>
                        <td><?php echo 'Cash of Handset'; ?></td>
                        <!--<td><?php // echo $return->sales_return_approved_by ?></td>-->
                        <td><a href="<?php echo base_url('Sales_return/sales_return_details/'.$return->idsales_return) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
            
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
    }
    
    public function tally_sale_report(){
        $q['tab_active'] = 'Tally Report';

        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('report/tally_sale_report', $q);  
    }
    
     public function ajax_get_tally_sale_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        $dc = 0;//$this->input->post('dc');
        $sale_data = $this->Sale_model->get_tally_sale_report($from, $to, $idcompany, $dc);
        $payment_mode_data = $this->General_model->get_active_payment_mode_data();
        if(count($sale_data) >0){
        ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelementtop">
                <th>Sr</th>
                <th>Invoice No</th>
                <th>Sale Type</th>
                <th>HSN</th>
                <th>Invoice Date</th>
                <th>Customer Name</th>
                <th>Customer Mobile</th>
                <th>Customer GST No</th>
                <th>Type</th>
                <th>State</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Base Price</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Round Up</th>
                <th>Total Amount</th>
                <th>GST %</th>
                <th>CGST</th>
                <th>% against</th>
                <th>Discount</th>
                <th>Total Settelment</th>
                <?php foreach($payment_mode_data as $pmode){ ?>
                    <th><?php echo $pmode->payment_mode ?></th>
                    <?php if($pmode->tranxid_type != ''){ echo '<th>'.$pmode->tranxid_type.'</th>'; } ?>
                <?php }?>
               <th>Details</th>
                <th>Print</th>
                
            </thead>
            <tbody class="data_1">
                <?php  $sr=1; $totalround = 0; $total =0; $total_base=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$cmobine=0; $saleid=0;
                $old_inv=null;
                $total_sett_amt =0;
                $per=0; $dis=0;$dis_amt=0;$pmode=0;
                foreach ($sale_data as $sale) { 
                    $total_sett_amt =0;
                    if($sale->is_mop == 1){
                        if($sale->total_amount > $sale->mop){
                            $sale_amount = $sale->total_amount;
                        }else{
                            $sale_amount = $sale->mop;
                        }
                    }else{
                        $sale_amount = $sale->total_amount;   
                    }
                    foreach ($sale_data as $ss) { 
                        if($ss->is_mop == 1){
                            if($ss->total_amount > $ss->mop){
                                $ss_amount = $ss->total_amount;
                            }else{
                                $ss_amount = $ss->mop;
                            }
                        }else{
                            $ss_amount = $ss->total_amount;   
                        }
                    
                        if($sale->inv_no == $ss->inv_no){
                           $total_sett_amt =  $total_sett_amt + $ss_amount;
                        }
                    }
                    $igst = $sale->igst_per;
                    $cgst = $sale->cgst_per;
                    $sgst = $sale->sgst_per;
                   
                    $igst_amount = 0;
                    if($igst != 0){
                        $price = $sale_amount;
                        $cal = ($igst+100)/100;
                        $taxable = $price/$cal; 
                        $gstamt = $price - $taxable;
                        $igst_amount = $gstamt; 
                        
                    }else{
                        $gst = $cgst + $sgst;
                        $price = $sale_amount;
                        $cal = ($gst+100)/100;
                        $taxable = $price/$cal; 
                        $gstamt = $price - $taxable;
                        $cgst = $gstamt/2;
                    }
                    ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $sale->inv_no;  ?></td>
                    <td><?php echo 'Sales'; echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per.'%' ?></td>
                    <td><?php echo $sale->hsn?></td>
                    <td><?php echo date('d/m/Y', strtotime($sale->entry_time)) ?></td>
                    <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                    <td><?php echo $sale->customer_contact ?></td>
                    <td><?php echo $sale->customer_gst ?></td>
                    <td><?php if($sale->customer_gst == ''){echo 'UNREGISTERED'; }else{ echo 'Regular'; }?></td>
                    <td><?php echo $sale->customer_state; ?></td>
                    <td><?php echo $sale->brand_name; ?></td>
                    <td><?php echo $sale->product_name;  ?></td>
                    <td><?php echo $sale->qty; ?></td>
                    <td><?php $total_base = $total_base + $taxable; echo number_format($taxable,3) ?></td>
                    <td><?php $total_cgst = $total_cgst + $cgst; echo number_format($cgst,3);  ?></td>
                    <td><?php echo number_format($cgst,3);  ?></td>
                    <td><?php $total_igst = $total_cgst + $igst_amount; echo number_format($igst_amount,3); ?></td>
                    <td><?php $tobase = $cgst + $cgst + $igst_amount + $taxable;   $trount = number_format(($sale_amount - $tobase),2);  echo $trount; // $totalround = $totalround + $trount;?></td>
                    <td><?php echo $sale_amount; $total = $total + $sale_amount;  ?></td>
                    <td><?php echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per ?></td>
                    <td><?php echo $sale->cgst_per ?></td>    
                     <?php 
                       $total_sett_amt =  $sale->total_settlement;
                        $per = ($sale_amount/$total_sett_amt)*100;
                        $dis = $sale_amount-$sale->total_amount;
                        if($dis > 0){
                            $dis_amt = ($per/100)*$dis;
                        }else{
                            $per = 0;
                            $dis_amt = 0;
                        }
                    ?>
                    <td><?php echo round($per,2).'%'; // echo '('.$sale_amount.'/'.$total_sett_amt.')*100';  // echo round($per,2); ?></td>
                    <td><?php echo round($dis,2); ?></td>   
                    <td><?php echo $sale_amount - $dis; ?></td>   
                    <?php for($i=0; $i < count($payment_mode_data); $i++){   ?>
                        <td><?php 
                            $mdn = $payment_mode_data[$i]->payment_mode; $mdntrans = $payment_mode_data[$i]->payment_mode."_transaction_id";  
                            $pmode = ($per/100)*$sale->$mdn;
                            echo round($pmode);?>
                        </td>
                        <?php if($payment_mode_data[$i]->tranxid_type != NULL){ ?>
                            <td><?php  echo $sale->$mdntrans; ?></td>
                        <?php }  ?>
                    <?php }  ?>
                     <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                     <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                </tr>
                <?php $old_inv=$sale->inv_no; } ?>
            </tbody>
        </table>
        <?php $cmobine =0;
        }else{ ?>
           <script>
               $(document).ready(function (){
                  alert("Data Not Found"); 
               });
           </script> 
        <?php }
    }
    
    public function warehouse_branch_shipment_report(){
        $q['tab_active'] = 'Shipment Report';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/warehouse_branch_shipment_report', $q);
    }  
    
    public function ajax_get_wh_to_branch_shipment_data(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idstatus = $this->input->post('idstatus');
        $allbranch = $this->input->post('allbranch');
        $wh_data = $this->Report_model->get_wharehouse_to_branch_shipment_data($from,$to,$idbranch,$idstatus,$allbranch);
//        die('<pre>'.print_r($wh_data,1).'</pre>');
        if($wh_data){ ?>
           <table class="table table-bordered table-condensed" id="warehouse_to_branch_shipment_report">
               <thead style="background-color: #99ccff" class="fixedelementtop">
                    <th>DC</th>
                    <th>Date</th>
                    <th>Branch From</th>
                    <th>Branch To</th>
                    <th>Qty</th>
                    <th>Allocation Type</th>
                    <!--<th>Remark</th>-->
                    <th>Action</th>
               </thead>
               <tbody>
                   <?php foreach($wh_data as $wh){ ?>
                   <tr>
                       <td><?php echo $wh->idstock_allocation ?></td>
                       <td><?php echo $wh->date ?></td>
                       <td><?php echo $wh->branch_from?></td>
                       <td><?php echo $wh->branch_name?></td>
                       <td><?php echo $wh->total_product?></td>
                       <td><?php echo $wh->dispatch_type?></td>
                       <!--<td><?php echo $wh->shipment_remark?></td>-->
                       <td><a class="btn btn-primary btn-sm" target="_blank" href="<?php echo base_url();?>Report/wh_to_bran_shipment_details/<?php echo $wh->id_outward ?>">View</a></td>
                   </tr>
                   <?php } ?>
               </tbody>
           </table>
        <?php }
    }
    
    public function wh_to_bran_shipment_details($id){
        $q['tab_active'] = 'Shipment Report';
        $q['wh_detals'] = $this->Report_model->get_wh_to_branch_shipment_details($id);
        $q['outward_data'] = $this->Report_model->get_outward_data_byid($id);
//        die('<pre>'.print_r( $q['outward_data'],1).'</pre>');
        $this->load->view('report/warehouse_branch_shipment_details', $q);
    }
    
    public function branch_to_branch_shipment_report(){
        $q['tab_active'] = 'Shipment Report';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('report/branch_to_branch_shipment_report', $q);
    }  
    
     public function ajax_get_b_to_b_shipment_data(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idstatus = $this->input->post('idstatus');
        $allbranch = $this->input->post('allbranch');
        $wh_data = $this->Report_model->get_b_to_b_shipment_data($from,$to,$idbranch,$idstatus,$allbranch);
        if($wh_data){ ?>
           <table class="table table-bordered table-condensed" id="b_to_b_shipment">
               <thead style="background-color: #99ccff" class="fixedelementtop">
                    <th>DC</th>
                    <th>Date</th>
                    <th>Branch From</th>
                    <th>Branch To</th>
                    <th>Product</th>
                    <th>Total Qty</th>
                    <th>Allocation Type</th>
                    <th>Remark</th>
                    <th>Action</th>
               </thead>
               <tbody class="data_1">
                   <?php foreach($wh_data as $wh){ ?>
                   <tr>
                       <td><?php echo $wh->id_transfer ?></td>
                       <td><?php echo $wh->date ?></td>
                       <td><?php echo $wh->branch_from?></td>
                       <td><?php echo $wh->branch_name?></td>
                       <td><?php echo $wh->total_product?></td>
                       <td><?php  echo $wh->no_of_boxes?></td>
                       <td><?php echo $wh->dispatch_type?></td>
                       <td><?php echo $wh->shipment_remark?></td>
                       <td><a class="btn btn-primary btn-sm" target="_blank" href="<?php echo base_url();?>Report/b_to_b_shipment_details/<?php echo $wh->id_transfer ?>">View</a></td>
                   </tr>
                   <?php } ?>
               </tbody>
           </table>
        <?php }
    }
    
    public function b_to_b_shipment_details($id) {
         $q['tab_active'] = 'Shipment Report';
        $q['transfer_product_data'] = $this->Report_model->get_transfer_product_shipment_details($id);
        $q['transfer_data'] = $this->Report_model->get_transfer_data_byid($id);
//        die('<pre>'.print_r( $q['outward_data'],1).'</pre>');
        $this->load->view('report/branch_to_branch_shipment_details', $q);
    }
}