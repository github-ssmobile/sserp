<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sale extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Sale_model');
        $this->load->model('Stock_model');
        $this->load->model('Audit_model');
        $this->load->model('Report_model');
        $this->load->model('General_model');
        $this->load->model('Purchase_model');
        $this->load->model('Transfer_model');
        $this->load->model('common_model');

        
        $this->load->model('Customerloyalty_model');
    }
    public function index() {
        $q['tab_active'] = 'Sale';
        $idbranch = $_SESSION['idbranch'];
        $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $is_web_billing=$q['invoice_no']->web_billing;
       /* if($idbranch==81 || $idbranch==89){
            $ids=array(1,2,3,4,5,8,10);
            $q['payment_head'] = $this->General_model->get_active_payment_head_by_headids($ids);    
        }else{
            $q['payment_head'] = $this->General_model->get_active_payment_head();    
        }      */  
        $q['payment_mode'] = $this->General_model->get_active_payment_mode();
        $q['payment_attribute'] = $this->General_model->get_payment_head_has_attributes();
        $q['state_data'] = $this->General_model->get_state_data();
        $q['customer_formdata'] = $this->Customerloyalty_model->get_customer_formdata();
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
//        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
//        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
//        $q['last_date_entry'] = $this->Sale_model->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
        $q['allow_web_billing']=1;        
        $q['var_closer'] = $this->verify_cash_closure();
        $date=date('Y-m-d',strtotime("-1 days"));
        $this->Transfer_model->wipe_out_pending_b2b_requests($date,$idbranch); // remove pending b2b request
        $idclaim = $this->input->get('idclaim');
        if($idtoken = $this->input->get('idtoken')){
            $q['sale_token'] = $this->Sale_model->get_sale_token_byid($idtoken);
            if($q['sale_token']->status != 0){
                $this->session->set_flashdata('reject_data', $idtoken.' Sale token already used or rejected...');
                return redirect('Sale/sale_token');
            }
            $q['sale_token_product'] = $this->Sale_model->get_sale_token_product_byid($idtoken);
            $q['sale_token_payment'] = $this->Sale_model->get_sale_token_payment_byid($idtoken);
        }elseif($idclaim){
            $q['payment_received_data'] = $this->Sale_model->get_advanced_booking_byid_for_sale($idclaim);
            if($q['payment_received_data']->claim > 0){ 
                $this->session->set_flashdata('reject_data', 'Already claimed or refund to customer...');
                return redirect('Payment/recieve_advanced_payment');
            }
        }elseif($q['invoice_no']->web_billing==0){
            $q['allow_web_billing']=0;
        }
        
        // ******branch wise payment head and credit/custody data *******
        
        $payment_head = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);    
        $credit_data = $this->General_model->get_branch_credit_data($idbranch);
        
        
        $credit_limit = $q['invoice_no']->credit_limit;
        $credit_days = $q['invoice_no']->credit_days;
        
        $last_date = date('Y-m-d', strtotime('-'.$credit_days.'days'));
        $overall_credit = $credit_data->credit_amount;
        $credit_date = $credit_data->credit_date;
        
        $credit_status = 1;
        if($overall_credit!=NULL && $credit_date!=NULL){
            if($credit_limit > $overall_credit && $last_date < $credit_date){
                $credit_status = 1;                
            }else{
                $credit_status = 0;                
            }
        }
        
        $head_status = 0;
        foreach($payment_head as $head){
            if($head->id_paymenthead == 6){
                $head_status = 1;  
            }
        }
            if($head_status == 1){ //Branch has credit/custudy payment head
                if($credit_status == 1){ //display credit/custudy payment head 
                    $q['payment_head'] = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);
                }else{
                    foreach($payment_head as $head) { //disable credit/custudy payment head 
                        if($head->id_paymenthead != 6){ 
                            $ids[] =  $head->id_paymenthead;
                        } 
                    }
                    $q['payment_head'] = $this->General_model->get_active_payment_head_by_headids($ids);    
                }
            }else{
                $q['payment_head'] = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);
            } 
            
            
            
            $this->load->view('sale/create_invoice',$q);
        }
        public function corporate_sale() {
            $q['tab_active'] = 'Sale';
            $idbranch = $_SESSION['idbranch'];
            $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($idbranch);
            $q['payment_head'] = $this->General_model->get_active_corporate_payment_head();
            $q['payment_mode'] = $this->General_model->get_active_payment_mode();
            $q['payment_attribute'] = $this->General_model->get_payment_head_has_attributes();
            $q['state_data'] = $this->General_model->get_state_data();
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole(17); // sales promoter
        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
//        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
//        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
//        $q['last_date_entry'] = $this->Sale_model->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
        $q['var_closer'] = $this->verify_cash_closure();
        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
        $this->load->view('sale/corporate_sale',$q);
    }
    public function verify_cash_closure() {
        $idbranch = $_SESSION['idbranch'];
        $sale_last_entry_byidbranch = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $cash_closure_last_entry = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
//        $daybook_cash_sum = $this->Sale_model->get_daybook_cash_sum_byid($idbranch); // cash closure data
        $var_closer = 1;
//        if($daybook_cash_sum[0]->sum_cash == 0){
//            $var_closer = 1;
//        }else
        
        if(count($sale_last_entry_byidbranch)){
            if($sale_last_entry_byidbranch[0]->sum_cash == 0){
                $var_closer = 1;
            }else{
                if(count($cash_closure_last_entry) == 0){
                    $var_closer = 0;
                }else{
                    if($sale_last_entry_byidbranch[0]->date > $cash_closure_last_entry[0]->date){
                        $var_closer = 0;
                    }elseif($sale_last_entry_byidbranch[0]->date == $cash_closure_last_entry[0]->date){
//                        echo $sale_last_entry_byidbranch[0]->sum_cash;
//                        echo $cash_closure_last_entry[0]->closure_cash;
                        if($sale_last_entry_byidbranch[0]->sum_cash <= $cash_closure_last_entry[0]->closure_cash){
                            $var_closer = 1;
                        }else{
                            $var_closer = 0;
//                            die('hi');
                        }
                    }
                }
            }
        }
        if($_SESSION['idbranch'] == 7){
            $var_closer = 1;
        }
        return $var_closer;
    }
    public function customer_edit_form($idcucstomer) {
        $q['tab_active'] = 'Sale';
        $q['state_data'] = $this->General_model->get_state_data();
        $q['customer_data'] = $this->Sale_model->get_customer_byid($idcucstomer);
        $this->load->view('sale/customer_edit_form',$q);
    }
    public function edit_customer_details() {
//        die(print_r($_POST));
        $idcustomer = $this->input->post('idcustomer');
        $customer_fname = $this->input->post('customer_fname');
        $customer_lname = $this->input->post('customer_lname');
        $customer_address = $this->input->post('customer_address');
        $customer_gst = $this->input->post('customer_gst');
        $pincode = $this->input->post('pincode');
        $idstate = $this->input->post('idstate');
        $edit_customer = array(
            'customer_fname' => $customer_fname,
            'customer_lname' => $customer_lname,
            'customer_address' => $customer_address,
            'customer_gst' => $customer_gst,
            'customer_pincode' => $pincode,
            'idstate' => $idstate,
            'customer_state' => $this->input->post('state_name'),
        );
        $this->General_model->edit_customer_byid($idcustomer, $edit_customer);
        $customer_history = array(
            'idcustomer' => $idcustomer,
            'customer_fname' => $customer_fname,
            'customer_lname' => $customer_lname,
            'customer_address' => $customer_address,
            'customer_gst' => $customer_gst,
            'customer_pincode' => $pincode,
            'customer_idstate' => $idstate,
            'idbranch' => $this->session->userdata('idbranch'),
            'edited_by' => $this->session->userdata('id_users'),
            'entry_time' => date('Y-m-d H:i:s'),
        );
        $this->General_model->save_customer_edit_history($customer_history);
        $this->General_model->update_customer_edit_count($idcustomer);
        return redirect('Sale/customer_edit_form/'.$idcustomer);
    }
    public function customer_contact_autocomplete() {
        $cust_mobile = $_GET['term'];
        $result = $this->Sale_model->get_customer_bycontact($cust_mobile);
        if (count($result) > 0) {
            foreach ($result as $row) {
                $res[] = $row->customer_contact;
            }
        } else {
            $res = array();
        }
        echo json_encode($res);
    }
    public function get_product_names_autocomplete() {
        $result = $this->Sale_model->get_product_names($_GET['term']);
        if (count($result) > 0) {
            foreach ($result as $row) {
                $res[] = $row->full_name;
            }
        } else {
            $res = array();
        }
        echo json_encode($res);
    }
    
    public function imei_tracker() {
        $q['tab_active'] = 'Sale';
        $this->load->view('sale/imei_tracker',$q);
    }
    public function customer_list() {
        $q['tab_active'] = '';
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['customer_list'] = $this->Sale_model->get_customer_list();
        $this->load->view('sale/customer_list',$q);
    }
    public function ajax_get_customer_list_byidbranch(){
        $idbranch = $this->input->post('idbranch');
//        die(print_r($_POST));
//        $branches = $this->input->post('branches');
        $customer_list = $this->Sale_model->get_customer_list_byidbranch($idbranch);
        if(count($customer_list) > 0){ ?>
           <table id="branch_data" class="table table-condensed table-bordered" style="margin-bottom: 0; font-size: 13px;">
            <?php if($this->session->userdata('idrole') == 9){ ?>
                <thead class="bg-info">
                    <th>Sr</th>
                    <th>Edit</th>
                    <th>Customer Name</th>
                    <th>Contact</th>
                    <th>Email Id</th>
                    <th>GSTIN</th>
                    <th>Created At</th>
                    <th>Address</th>
                    <th>Pincode</th>
                    <th>City</th>
                    <th>District</th>
                    <th>State</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($customer_list as $customer){ ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/customer_edit_form/'.$customer->id_customer) ?>" class="btn btn-sm btn-primary btn-outline"><i class="fa fa-edit"></i></a></td>
                            <td><?php echo $customer->customer_fname.' '.$customer->customer_lname ?></td>
                            <td><?php echo $customer->customer_contact ?></td>
                            <td><?php echo $customer->customer_email ?></td>
                            <td><?php echo $customer->customer_gst ?></td>
                            <td><?php echo $customer->branch_name ?></td>
                            <td><?php echo $customer->customer_address ?></td>
                            <td><?php echo $customer->customer_pincode ?></td>
                            <td><?php echo $customer->customer_city ?></td>
                            <td><?php echo $customer->customer_district ?></td>
                            <td><?php echo $customer->customer_state ?></td>
                            <!--<td><?php // echo date('d-m-Y h:i a', strtotime($customer->entry_time)) ?></td>-->
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                <?php }else{ ?>
                    <thead class="bg-info">
                        <th>Sr</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Email Id</th>
                        <th>GSTIN</th>
                        <th>Created At</th>
                        <th>Address</th>
                        <th>Pincode</th>
                        <th>City</th>
                        <th>District</th>
                        <th>State</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $i=1; foreach ($customer_list as $customer){ ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo $customer->customer_fname.' '.$customer->customer_lname ?></td>
                                <td><?php echo $customer->customer_contact ?></td>
                                <td><?php echo $customer->customer_email ?></td>
                                <td><?php echo $customer->customer_gst ?></td>
                                <td><?php echo $customer->branch_name ?></td>
                                <td><?php echo $customer->customer_address ?></td>
                                <td><?php echo $customer->customer_pincode ?></td>
                                <td><?php echo $customer->customer_city ?></td>
                                <td><?php echo $customer->customer_district ?></td>
                                <td><?php echo $customer->customer_state ?></td>
                            </tr>
                            <?php $i++; } ?>
                        </tbody>
                    <?php } ?>
                </table>
            <?php }
        }
        public function invoice_print($idsale) {
//        $q['tab_active'] = '';
//        $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
//        $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
//        $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
//        $q['financer_of_idsale'] = $this->Sale_model->get_financer_of_idsale($idsale);
//        $this->load->view('sale/invoice_print', $q);
            $q['tab_active'] = '';
            $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
            $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
            if($q['sale_data'][0]->dcprint){
                $this->load->view('sale/dc_print', $q);
            }else{
                $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
                $q['financer_of_idsale'] = $this->Sale_model->get_financer_of_idsale($idsale);
                $this->load->view('sale/invoice_print', $q);
            }
        }
        public function invoice_print_14april($idsale) {
            $q['tab_active'] = '';
            $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
            $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
            if($q['sale_data'][0]->dcprint){
                $this->load->view('sale/dc_print_14april', $q);
            }else{
                $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
                $q['financer_of_idsale'] = $this->Sale_model->get_financer_of_idsale($idsale);
                $this->load->view('sale/invoice_print_14april', $q);
            }
        }
        public function invoice_search() {
            $q['tab_active'] = '';
            $idbranch = $_SESSION['idbranch'];
            if($_SESSION['level'] == 1){
                $q['sale_data'] = $this->Sale_model->get_sale_data();
            }else{
                $q['sale_data'] = $this->Sale_model->get_sale_byidbranch($idbranch);
            }
            if($_SESSION['level'] == 1){
                $q['branch_data'] = $this->General_model->get_active_branch_data();
            }elseif($_SESSION['level'] == 3){
                $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
            }
            $this->load->view('sale/invoice_search', $q);
        }
        public function ajax_get_invoice_search(){
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $idbranch = $this->input->post('idbranch');
            $branches = $this->input->post('branches');
//        die(print_r($_POST));
            $sale_data = $this->Sale_model->ajax_get_sales_data_byidbranch($from, $to, $idbranch, $branches);
            if($sale_data){ ?>
                <table id="sale_data_report" class="table table-bordered table-striped table-condensed table-info salehide">
                    <thead class="fixedelementtop">
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Branch</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>GSTIN</th>
                        <th>Sale Promotor</th>
                        <th>Total Basic</th>
                        <th>Total Discount</th>
                        <th>Total Amount</th>
                        <th>Info</th>
                        <th>Print</th>
                    </thead>
                    <tbody class="data_1">
                        <?php foreach ($sale_data as $sale) { ?>
                            <tr>
                                <td><?php echo $sale->entry_time ?></td>
                                <td><?php echo $sale->inv_no ?></td>
                                <td><?php echo $sale->branch_name ?></td>
                                <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                                <td><?php echo $sale->customer_contact ?></td>
                                <td><?php echo $sale->customer_gst ?></td>
                                <td><?php echo $sale->user_name ?></td>
                                <td><?php echo $sale->basic_total ?></td>
                                <td><?php echo $sale->discount_total ?></td>
                                <td><?php echo $sale->final_total ?></td>
                                <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                                <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                            </tr> 
                        <?php } ?>
                    </tbody>
                </table>
            <?php }else{ ?>
                <script>
                    $(document).ready(function (){
                     alert("Sale Data Not Found");
                 });
             </script>
         <?php }
     }

     public function ajax_get_invoice_search_byimeino(){
        $imei = $this->input->post('imei');
        $sale_data = $this->Sale_model->ajax_get_sales_data_byimei($imei);
        if($sale_data){ ?>
            <table id="sale_data_report" class="table table-bordered table-striped table-condensed table-info salehide">
                <thead class="fixedelementtop">
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th>Branch</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>GSTIN</th>
                    <th>Sale Promotor</th>
                    <th>product Name</th>
                    <th>Imei</th>
                    <th>Basic</th>
                    <th>Discount</th>
                    <th>Total Amount</th>
                    <th>Info</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach ($sale_data as $sale) { ?>
                        <tr>
                            <td><?php echo $sale->entry_time ?></td>
                            <td><?php echo $sale->inv_no ?></td>
                            <td><?php echo $sale->branch_name ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->customer_gst ?></td>
                            <td><?php echo $sale->user_name ?></td>
                            <td><?php echo $sale->product_name ?></td>
                            <td><?php echo $sale->imei_no ?></td>
                            <td><?php echo $sale->basic_total ?></td>
                            <td><?php echo $sale->discount_total ?></td>
                            <td><?php echo $sale->final_total ?></td>
                            <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                            <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                        </tr> 
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                 alert("Sale Data Not Found");
             });
         </script>
     <?php }
 }

 public function ajax_get_invoice_search_bycontact(){
    $contact_no = $this->input->post('contact_no');
    $sale_data = $this->Sale_model->ajax_get_sales_data_bycontact($contact_no);
    if($sale_data){ ?>
       <table id="sale_data_report" class="table table-bordered table-striped table-condensed table-info salehide">
        <thead class="fixedelementtop">
            <th>Date</th>
            <th>Invoice No</th>
            <th>Branch</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>GSTIN</th>
            <th>Sale Promotor</th>
            <th>Total Basic</th>
            <th>Total Discount</th>
            <th>Total Amount</th>
            <th>Info</th>
            <th>Print</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($sale_data as $sale) { ?>
                <tr>
                    <td><?php echo $sale->entry_time ?></td>
                    <td><?php echo $sale->inv_no ?></td>
                    <td><?php echo $sale->branch_name ?></td>
                    <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                    <td><?php echo $sale->customer_contact ?></td>
                    <td><?php echo $sale->customer_gst ?></td>
                    <td><?php echo $sale->user_name ?></td>
                    <td><?php echo $sale->basic_total ?></td>
                    <td><?php echo $sale->discount_total ?></td>
                    <td><?php echo $sale->final_total ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                </tr> 
            <?php } ?>
        </tbody>
    </table>
<?php }else{ ?>
    <script>
        $(document).ready(function (){
         alert("Sale Data Not Found");
     });
 </script>
<?php }
}

public function ajax_get_invoice_search_byinvoiceno(){
    $invoice_no = $this->input->post('invoice_no');
    $sale_data = $this->Sale_model->ajax_get_sales_data_byinvoice($invoice_no);
    if($sale_data){ ?>
       <table id="sale_data_report" class="table table-bordered table-striped table-condensed table-info salehide">
        <thead class="fixedelementtop">
            <th>Date</th>
            <th>Invoice No</th>
            <th>Branch</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>GSTIN</th>
            <th>Sale Promotor</th>
            <th>Total Basic</th>
            <th>Total Discount</th>
            <th>Total Amount</th>
            <th>Info</th>
            <th>Print</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($sale_data as $sale) { ?>
                <tr>
                    <td><?php echo $sale->entry_time ?></td>
                    <td><?php echo $sale->inv_no ?></td>
                    <td><?php echo $sale->branch_name ?></td>
                    <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                    <td><?php echo $sale->customer_contact ?></td>
                    <td><?php echo $sale->customer_gst ?></td>
                    <td><?php echo $sale->user_name ?></td>
                    <td><?php echo $sale->basic_total ?></td>
                    <td><?php echo $sale->discount_total ?></td>
                    <td><?php echo $sale->final_total ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                </tr> 
            <?php } ?>
        </tbody>
    </table>
<?php }else{ ?>
    <script>
        $(document).ready(function (){
         alert("Sale Data Not Found");
     });
 </script>
<?php }
}


public function sale_payment_receivables() {
    $q['tab_active'] = '';
    $q['branch_data'] = $this->General_model->get_active_branch_data();
    $q['payment_head'] = $this->General_model->get_active_payment_head();
    $q['payment_mode'] = $this->General_model->get_payment_mode_data();
//        $idbranch = $_SESSION['idbranch'];
//        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
//        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
//        $q['last_date_entry'] = $this->Sale_model->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
    $q['var_closer'] = $this->verify_cash_closure();
    $this->load->view('sale/sale_payment_receivables', $q);
}
public function credit_receivable_report() {
    $q['tab_active'] = '';
    if($_SESSION['level'] == 1){
        $q['branch_data'] = $this->General_model->get_branchandwarehouse_data();
//            $q['branch_data'] = $this->General_model->get_active_branch_data();
    }elseif($_SESSION['level'] == 3){
        $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
    }
    $q['payment_head'] = $this->General_model->get_active_payment_head();
    $q['payment_mode'] = $this->General_model->get_payment_mode_data();
    $this->load->view('sale/credit_receivable_report', $q);
}
public function dc_print($idsale) {
    $q['tab_active'] = '';
    $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
    $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
//        $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
    $this->load->view('sale/dc_print', $q);
}
public function dc_print_14april($idsale) {
    $q['tab_active'] = '';
    $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
    $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
//        $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
    $this->load->view('sale/dc_print_14april', $q);
}
public function sale_details($idsale) {
    $q['tab_active'] = '';
    $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);        
    $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
    $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
    $q['sale_reconciliation'] = $this->Sale_model->get_sale_reconciliation_byid($idsale);
    $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();
    
    $this->load->view('sale/sale_details', $q);
}
public function invoice_edit() {
    $q['tab_active'] = 'Correction';
    $this->load->view('sale/invoice_edit', $q);
}

public function ajax_get_imei_details() {
    $imei = $this->input->post('imei');
    $idbranch = $this->input->post('idbranch'); 
    $skuvariant = $this->input->post('skuvariant'); 
    $idgodown = $this->input->post('idgodown'); 
    $is_dcprint = $this->input->post('is_dcprint'); 
    $sale_type = $this->input->post('sale_type');
        // Quantity
    if($skuvariant){
        $models = $this->Sale_model->ajax_get_variant_byid_branch_godown_saletype($skuvariant, $idbranch, $idgodown, $sale_type);
        if(count($models)){
            foreach($models as $model){
                if($sale_type != 2){
                    $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($ageing_data){
                        $ageing = 1;
                    }else{
                        $ageing =0;
                    }

                    $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($focus_data){
                        $focus_status = 1;
                        $focus_amount = $focus_data->incentive_amount;
                    }else{
                        $focus_status = 0;
                        $focus_amount = 0;
                    }
                }
                if($sale_type == 2){
                    $model->dcprint = 0;
                    $model->mop = 1;
                    $model->landing = 1;
                    $model->is_gst = 1;
                    $model->id_stock = 0;
                    $model->idvendor = 1;
                    $model->qty = 0;
                    $ageing =0;
                    $focus_amount = 0;
                    $focus_status = 0;
                }
                if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                    if($is_dcprint == 0){
                            echo '1'; // previous is dc product
                        }else{
                            echo '2'; // previous is invoice product
                        }
                    }else{ $amount_diff = $model->mop - $model->landing; ?>
                        <tr id="m<?php echo $model->id_stock ?>" class="skuqty_row">
                            <td>
                                <?php echo $model->full_name; ?>
                                <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                                <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                                <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                <?php if($sale_type == 0){ ?>
                                    <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" />
                                <?php }else{ ?>
                                    <input type="text" id="activation_code" name="activation_code[]" class="activation_code form-control input-sm" required="" placeholder="Activation/Reference Code" />
                                <?php } ?>
                            </td>
                            <td>
                                <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo NULL ?>" />
                                <?php if($sale_type == 0){ ?>
                                    <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" />
                                <?php }else{ ?>
                                    <input type="text" id="insurance_imei" name="insurance_imei[]" class="insurance_imei form-control input-sm" required="" placeholder="Insurance IMEI/SRNO" pattern="[a-zA-Z0-9\-]+" />
                                <?php } ?>
                            </td>
                            <td><?php echo $model->qty; ?></td>
                            <td><?php echo $model->mrp; ?></td>
                            <td><?php echo $model->mop; ?></td>
                            <td>
                                <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $sale_type ?>" />
                                <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />-->
                                <?php if($sale_type != 0){ ?>
                                    <input type="hidden" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                    1
                                <?php }else{ ?>
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($sale_type == 2){ ?>
                                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px"/>
                                <?php }else{ ?>
                                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px" max="<?php echo $model->qty; ?>"/>
                                <?php } ?>
                            </td>
                            <td>
                                <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                            </td>
                            <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px" <?php if($model->is_mop == 0){ ?> readonly="" <?php } ?> /></td>
                            <td>
                                <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                                <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                <?php echo $model->igst ?>%
                            </td>
                            <td>
                                <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                            </td>
                            <td>
                                <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                            </td>
                        </tr>
                    <?php }}}else{
                        echo '0';
                    }
        }else{ // IMEI/ SRNO
            $models = $this->Sale_model->ajax_stock_data_byimei_branch($imei, $idbranch);
            if(count($models)){
                foreach($models as $model){

                    $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($ageing_data){
                        $ageing = 1;
                    }else{
                        $ageing =0;
                    }
                    
                    $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($focus_data){
                        $focus_status = 1;
                        $focus_amount = $focus_data->incentive_amount;
                    }else{
                        $focus_status = 0;
                        $focus_amount = 0;
                    }
                    
                    if($model->idgodown != 1){
                        echo '3'; // Other that New Godown not accepted
                    }else{
                        if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                            if($is_dcprint == 0){ 
                                echo '1'; // previous is dc product
                            }else{ 
                                echo '2'; // previous is dc invoice
                            }
                        }else{ $amount_diff = $model->mop - $model->landing; ?>
                            <tr id="m<?php echo $model->id_stock ?>">
                                <td>
                                    <?php echo $model->product_name; ?>
                                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                    <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                    <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                    <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $model->idgodown ?>" />
                                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                    <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                    <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                    <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                    <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                                    <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo $imei ?>" />
                                    <?php echo $imei; ?>
                                    <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" />
                                    <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" />
                                </td>
                                <td>1</td>
                                <td><?php echo $model->mrp; ?></td>
                                <td><?php echo $model->mop; ?></td>
                                <td>
                                    <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                    <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                    <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                    <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                    <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                    <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                    <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                    <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />-->
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                    <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $model->sale_type ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>
                                    <span id="spqty" class="spqty">1</span>
                                </td>
                                <td>
                                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                    <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                    <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                                </td>
                                <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" max="<?php echo $amount_diff ?>" step="0.001" style="width: 90px" <?php if($model->is_mop == 0){ ?> readonly="" <?php } ?>/></td>
                                <td>
                                    <input type="hidden" id="isgst" name="isgst[]" class="isgst"/>
                                    <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                    <?php echo $model->igst ?>%
                                </td>
                                <td>
                                    <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                    <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                                </td>
                                <td>
                                    <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                    <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                                </td>
                            </tr>
                        <?php }}}}else{
                            echo '0';
                        }
                    }
                }
                
                public function ajax_get_corporate_imei_details() {
                    $imei = $this->input->post('imei');
                    $idbranch = $this->input->post('idbranch'); 
                    $skuvariant = $this->input->post('skuvariant'); 
                    $idgodown = $this->input->post('idgodown'); 
                    $is_dcprint = $this->input->post('is_dcprint'); 
        // Quantity
                    if($skuvariant){
                        $models = $this->Sale_model->ajax_get_variant_byid_branch_godown($skuvariant, $idbranch, $idgodown);
                        if(count($models)){
                            foreach($models as $model){
                                $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                                if($ageing_data){
                                    $ageing = 1;
                                }else{
                                    $ageing =0;
                                }
                                
                                $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                                if($focus_data){
                                    $focus_status = 1;
                                    $focus_amount = $focus_data->incentive_amount;
                                }else{
                                    $focus_status = 0;
                                    $focus_amount = 0;
                                }
                                
                                if(($model->dcprint != $is_dcprint) && $is_dcprint != ''){
                                    if($is_dcprint == 0){
                            echo '1'; // previous is dc product
                        }else{
                            echo '2'; // previous is dc invoice
                        }
                    }else{ ?>
                        <tr id="m<?php echo $model->id_stock ?>">
                            <td>
                                <?php echo $model->product_name; ?>
                                <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                                <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                                <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                            </td>
                            <td><input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo NULL ?>" /></td>
                            <td><?php echo $model->qty; ?></td>
                            <td><?php echo $model->mrp; ?></td>
                            <td><?php echo $model->mop; ?></td>
                            <td>
                                <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                <input type="hidden" id="corporate_sale_price" name="corporate_sale_price[]" class="corporate_sale_price" value="<?php echo $model->corporate_sale_price ?>" />
                                <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->corporate_sale_price ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />
                            </td>
                            <td>
                                <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px" max="<?php echo $model->qty; ?>"/>
                            </td>
                            <td>
                                <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                            </td>
                            <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px"/></td>
                            <td>
                                <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                                <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                <?php echo $model->igst ?>%
                            </td>
                            <td>
                                <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                            </td>
                            <td>
                                <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                            </td>
                        </tr>
                    <?php }}}else{
                        echo '0';
                    }
        }else{ // IMEI/ SRNO
            $models = $this->Sale_model->ajax_stock_data_byimei_branch($imei, $idbranch);
            if(count($models)){
                foreach($models as $model){

                    $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($ageing_data){
                        $ageing = 1;
                    }else{
                        $ageing =0;
                    }
                    
                    $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($focus_data){
                        $focus_status = 1;
                        $focus_amount = $focus_data->incentive_amount;
                    }else{
                        $focus_status = 0;
                        $focus_amount = 0;
                    }
                    
                    if($model->idgodown != 1){
                        echo '3'; // Other that New Godown not accepted
                    }else{
                        if(($model->dcprint != $is_dcprint) && $is_dcprint != ''){
                            if($is_dcprint == 0){ 
                                echo '1'; // previous is dc product
                            }else{ 
                                echo '2'; // previous is dc invoice
                            }
                        }else{ ?>
                            <tr id="m<?php echo $model->id_stock ?>">
                                <td>
                                    <?php echo $model->product_name; ?>
                                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                    <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                    <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                    <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $model->idgodown ?>" />
                                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                    <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                    <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                    <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                    <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                                    <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo $imei ?>" />
                                    <?php echo $imei; ?>
                                </td>
                                <td>1</td>
                                <td><?php echo $model->mrp; ?></td>
                                <td><?php echo $model->mop; ?></td>
                                <td>
                                    <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                    <input type="hidden" id="corporate_sale_price" name="corporate_sale_price[]" class="corporate_sale_price" value="<?php echo $model->corporate_sale_price ?>" />
                                    <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                    <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                    <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                    <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                    <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                    <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->corporate_sale_price ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>
                                    <span id="spqty" class="spqty">1</span>
                                </td>
                                <td>
                                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                    <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                </td>
                                <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px"/></td>
                                <td>
                                    <input type="hidden" id="isgst" name="isgst[]" class="isgst"/>
                                    <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                    <?php echo $model->igst ?>%
                                </td>
                                <td>
                                    <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                    <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                                </td>
                                <td>
                                    <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                    <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                                </td>
                            </tr>
                        <?php }}}}else{
                            die('0');
                        }
                    }
                }
                
                public function ajax_track_imei() {
                    $imei = $this->input->post('imei');
                    $imei_history = $this->Sale_model->get_imei_history($imei);
//        die(print_r($imei_history));
                    if(count($imei_history) > 0){ ?>
                        <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                            <header>
                                <div class="text-center">
                                 <h1><?php echo '<small>'.$imei_history[count($imei_history)-1]->idvariant.']</small> '.$imei_history[count($imei_history)-1]->full_name ?></h1>
                                 <!--<h1><?php // echo $imei_history[count($imei_history)-1]->idvariant.'] '.$imei_history[count($imei_history)-1]->full_name ?></h1>-->
                                 <p><?php echo $imei; ?></p>
                             </div>
                         </header>
                     </div><div class="clearfix"></div><br>
                     <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                        <section class="timeline">
                            <div class="">
                                <?php $i=1; foreach ($imei_history as $history){ ?>
                                    <div class="timeline-item">
                                        <div class="timeline-img"></div>
                                        <div class="timeline-content">
                                            <h3 style="margin-top: 10px"><?php echo $history->entry_type ?>
                                            <div class="date pull-right"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div>
                                        </h3>
                                        <hr>
                                        <p style="font-size: 16px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="text-muted" style="font-family: Kurale">'.$history->godown_name.'</small>' ?></p>
                                        <?php if($history->transfer_from!=NULL){ ?>
                                            <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                                        <?php } ?>
                                        <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android fa-lg"></i> <?php echo $history->full_name ?></p>
                                        <div class="clearfix"></div>
                                        <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                                        <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <?php if($history->url_link){ // Purchase,Purchase return
                                    if($history->idimei_details_link == 4){ // Purchase,Purchase return ?>
                                        <a class="bnt-more" style="right: 70px" target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$history->idlink) ?>">
                                            <i class="fa fa-print fa-lg"></i>
                                        </a>
                                    <?php } ?>
                                    <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                        <i class="fa fa-send-o fa-lg"></i>
                                    </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
            <!--<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>-->
<!--            <script
              src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCGIMNp5mXHPmrhjGyMqswPwcDmpF-YmIM&callback=initMap&libraries=&v=weekly"
              defer ></script>-->
              <script>
//                "use strict";
//                function initMap() {
//                    const map = new google.maps.Map(document.getElementById("map"), {
//                        zoom: 10,
//                        center: {
//                            lat: <?php // echo $imei_history[0]->imei_latitude ?>,
//                            lng: <?php // echo $imei_history[0]->imei_longitude ?>
//                        },
//                        mapTypeId: "roadmap"
//                    });
//                    const flightPlanCoordinates = [
//                    <?php // foreach ($imei_history as $history){ ?>
//                        {
//                          lat: <?php // echo $history->imei_latitude ?>,
//                          lng: <?php // echo $history->imei_longitude ?>
//                        },
//                    <?php // } ?>
//                    ];
//                    var iconsetngs = { path: google.maps.SymbolPath.FORWARD_OPEN_ARROW };
//                    const flightPath = new google.maps.Polyline({
//                        path: flightPlanCoordinates,
//                        geodesic: true,
//                        strokeColor: "#FF0000",
//                        strokeOpacity: 1.0,
//                        strokeWeight: 2,
//                        icons: [{
//                            icon: iconsetngs,
//                            repeat:'35px',
//                            offset: '100%'
//                        }]
//                    });
//                    flightPath.setMap(map);
//                }
</script>
<!--<div class="col-md-10 col-md-offset-1"><div id="map"></div></div>-->
<?php }else{
    echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
    . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
    . '</center>'; 
}
}

public function ajax_get_customer_bycontact() {
    $cust_contact = $this->input->post('cust_mobile');
    $q['contact_list'] = $this->Sale_model->ajax_get_customer_bycontact($cust_contact);
    if(count($q['contact_list'])){
        $q['result'] = 'Success';
        $q['msg'] = '';
    }else{
        $q['result'] = 'Failed';
        $q['msg'] = 'Customer contact not found';
    }
    echo json_encode($q);
}
public function save_bfl_customer() {
    $datetime = date('Y-m-d H:i:s');
    $state_name = $this->input->post('customer_state');
    $state_data = $this->Sale_model->get_state_bystate_name($state_name);
    $contact_list = $this->Sale_model->ajax_get_customer_bycontact($this->input->post('customer_contact'));
    if(count($contact_list)){
        $updata = array(
            'customer_fname' => $this->input->post('customer_fname'),
            'customer_lname' => $this->input->post('customer_lname'),
            'customer_gst' => $this->input->post('customer_gst'),
            'customer_pincode' => $this->input->post('customer_pincode'),
            'customer_city' => $this->input->post('customer_city'),
            'customer_district' => $this->input->post('customer_district'),
            'idstate' => $state_data->id_state,
            'customer_state' => $state_name,
            'customer_address' => $this->input->post('customer_address'),
            'idbranch' => $this->input->post('idbranch'),
            'created_by' => $this->input->post('iduser'),
            'customer_latitude' => $this->input->post('customer_latitude'),
            'customer_longitude' => $this->input->post('customer_longitude'),
            'entry_time' => $datetime,
        );
        $this->General_model->edit_customer_byid($contact_list[0]->id_customer, $updata);
        $q['customer_data'][0] = array(
            'id_customer' => $contact_list[0]->id_customer,
            'customer_fname' => $this->input->post('customer_fname'),
            'customer_lname' => $this->input->post('customer_lname'),
            'customer_contact' => $this->input->post('customer_contact'),
            'customer_gst' => $this->input->post('customer_gst'),
            'customer_pincode' => $this->input->post('customer_pincode'),
            'customer_city' => $this->input->post('customer_city'),
            'customer_district' => $this->input->post('customer_district'),
            'idstate' => $state_data->id_state,
            'customer_state' => $state_name,
            'customer_address' => $this->input->post('customer_address'),
            'idbranch' => $this->input->post('idbranch'),
            'created_by' => $this->input->post('iduser'),
            'entry_time' => $datetime,
        );
        if(count($q['customer_data'])){
            $q['result'] = 'Success';
            $q['msg'] = 'Customer Updated Successully';
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'Customer creation failed.';
        }
    }else{
        $data = array(
            'customer_fname' => $this->input->post('customer_fname'),
            'customer_lname' => $this->input->post('customer_lname'),
            'customer_contact' => $this->input->post('customer_contact'),
            'customer_gst' => $this->input->post('customer_gst'),
            'customer_pincode' => $this->input->post('customer_pincode'),
            'customer_city' => $this->input->post('customer_city'),
            'customer_district' => $this->input->post('customer_district'),
            'idstate' => $state_data->id_state,
            'customer_state' => $state_name,
            'customer_address' => $this->input->post('customer_address'),
            'idbranch' => $this->input->post('idbranch'),
            'created_by' => $this->input->post('iduser'),
            'entry_time' => $datetime,
        );
        $q['state_data'] = $state_data;
        $idcustomer = $this->Sale_model->save_customer($data);
        if($idcustomer){
            $q['customer_data'] = $this->Sale_model->get_customer_byid($idcustomer);
            if(count($q['customer_data'])){
                $q['result'] = 'Success';
                $q['msg'] = 'Customer Created Successully';
            }else{
                $q['result'] = 'Failed';
                $q['msg'] = 'Customer creation failed.';
            }
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'Customer creation failed.';
        }
    }
//        die('<pre>'.print_r($q['customer_data'],1).'<pre>');
    echo json_encode($q);
}
public function save_customer() {
        //print_r($_POST);die;
    $customer_data = $_POST;
    $datetime = date('Y-m-d H:i:s');
    $state_name = $this->input->post('customer_state');
    $customer_gst = $this->input->post('customer_gst');
    $state_data = $this->Sale_model->get_state_bystate_name($state_name);
    $branchid = $this->input->post('idbranch');
    $stateid = $state_data->id_state;
    $idcreated_by = $this->input->post('iduser');
//        $data = array(
//            'customer_fname' => $this->input->post('customer_fname'),
//            'customer_lname' => $this->input->post('customer_lname'),
//            'customer_contact' => $this->input->post('customer_contact'),
//            'customer_gst' => $this->input->post('customer_gst'),
//            'customer_pincode' => $this->input->post('customer_pincode'),
//            'customer_city' => $this->input->post('customer_city'),
//            'customer_district' => $this->input->post('customer_district'),
//            'idstate' => $state_data->id_state,
//            'customer_state' => $state_name,
//            'customer_address' => $this->input->post('customer_address'),
//            'idbranch' => $this->input->post('idbranch'),
//            'created_by' => $this->input->post('iduser'),
//            'customer_latitude' => $this->input->post('customer_latitude'),
//            'customer_longitude' => $this->input->post('customer_longitude'),
//            'entry_time' => $datetime,
//        );
    $branch_data = array('idbranch' => $branchid,'idstate'=>$stateid,'created_by'=>$idcreated_by);
    $data1 = array_slice($customer_data,2);
    $data = array_merge($branch_data, $data1);
    
    foreach($data as $key => $value) {
        if($key == 'email_id') {
            $data['customer_email'] = $value;
            unset($data[$key]);
        }
    }
    
    if((substr($customer_gst, 0, 2) == $state_data->gst_code && $customer_gst != '') || $customer_gst == ''){
//            die('hi');
        $q['state_data'] = $state_data;
        $idcustomer = $this->Sale_model->save_customer($data);
        if($idcustomer){
            $q['customer_data'] = $this->Sale_model->get_customer_byid($idcustomer);
            if(count($q['customer_data'])){
                $q['result'] = 'Success';
                $q['msg'] = 'Customer Created Successully';
            }else{
                $q['result'] = 'Failed';
                $q['msg'] = 'Customer creation failed.';
            }
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'Customer creation failed.';
        }
    }else{
        $q['result'] = 'Failed';
        $q['msg'] = 'Customer GSTIN unmatched with State GST Code!!';
    }
    echo json_encode($q);
}

public function ajax_get_payment_mode_data_byidhead() {
    $head = $this->input->post('paymenthead');
    $headname = $this->input->post('headname');
        //***** Find Credit Balance for credit restriction *******//                    
    $credit_balance=0;
    if($head==6){
        $idbranch = $_SESSION['idbranch'];
        $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        //***** credit custudy check START *******//            
        $credit_data = $this->General_model->get_branch_credit_data($idbranch);            
            //branch credit limt and credit days
        $credit_limit = $invoice_no->credit_limit;
            //branch tilldate credit amount sum
        $overall_credit = $credit_data->credit_amount;
        $credit_balance=($credit_limit-$overall_credit);            
    }
    $payment_head = $this->General_model->get_payment_head_byid($head); 
    $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); ?>
    <div id="modes_block<?php echo $head ?>" class="modes_block modes_blockc<?php echo $head ?> thumbnail" style="margin-bottom: 5px; padding: 5px;">
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            <span style="font-size: 15px; font-family: Kurale"><?php echo $headname ?></span>
            <select id="paymenttype<?php echo $head ?>" class="form-control input-sm payment_type" name="payment_type[]" required="">
                <?php if($head == 4){ ?>
                    <option value="">Select Finance</option>
                <?php } ?>
                <?php foreach ($payment_mode as $mode) { if($mode->id_paymentmode != 17 && $mode->id_paymentmode != 18){ ?>
                    <option value="<?php echo $mode->id_paymentmode ?>"><?php echo $mode->payment_mode ?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            Amount
            <?php if($head==6){ ?>
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" max="<?php echo $credit_balance ?>" placeholder="Amount" value="0" min="1" required="" />    
            <?php }else{ ?>
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" placeholder="Amount" value="0" min="1" required="" />    
            <?php } ?>                
            <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="<?php echo $head ?>" />
            <input type="hidden" class="headname" name="headname[]" value="<?php echo $headname ?>" />
            <input type="hidden" class="credit_type" name="credit_type[]" value="<?php echo $payment_head->credit_type ?>" />
        </div>
        <?php if($payment_head->tranxid_type == NULL){ ?>
            <div class="col-md-2 col-sm-3 hidden">
                <?php echo $payment_head->tranxid_type ?>
                <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $payment_head->tranxid_type ?>" value="<?php echo NULL; ?>" />
            </div>
        <?php }else{ ?>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <?php echo $payment_head->tranxid_type ?>
                <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $payment_head->tranxid_type ?>" required="" pattern="[a-zA-Z0-9\-]+" />
            </div>
        <?php } ?>
        <?php foreach ($payment_attribute as $attribute){ ?>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <?php echo $attribute->attribute_name ?>
                <input type="text" class="form-control input-sm headattr" id="<?php echo $attribute->column_name ?>" name="headattr[<?php echo $head ?>][<?php echo $attribute->id_payment_attribute ?>][]" placeholder="<?php echo $attribute->attribute_name ?>" required="" />
            </div>
        <?php } if($payment_head->multiple_rows){ ?>
            <div class="col-md-2 col-sm-3 pull-right" style="padding: 0;">
                <center>Add More<br>
                    <a class="btn btn-primary btn-floating waves-effect add_more_payment" id="add_more_payment"><i class="fa fa-plus"></i></a>
                </center>
            </div>
        <?php } ?>
        <div class="clearfix"></div>
    </div><div class="clearfix"></div> 
    <script>
        $(document).ready(function(){
            $('#product_model_name').autocomplete({
                source: '<?php echo base_url('Sale/get_product_names_autocomplete') ?>',
            });
        });
    </script>
    <?php
}
function isTokenValid($token){
    if (!empty($_SESSION['tokens'][$token])) {
        unset($_SESSION['tokens'][$token]);
        return true;
    }
    return false;
}
public function save_sale() {
    $postedToken = filter_input(INPUT_POST, 'token');
    if(!empty($postedToken)){
        if($this->isTokenValid($postedToken)){
//        die('<pre>'.print_r($_POST,1).'</pre>');
            $this->db->trans_begin();
            $idbranch = $this->input->post('idbranch');
            $dcprint = $this->input->post('dcprint');
            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
            $invid = $invoice_no->invoice_no + 1; 
            $y = date('y', mktime(0, 0, 0, 9 + date('m')));
            $y1 = $y - 1;
            if($dcprint[0] == 0){
                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
            }else{
                $inv_no = 'DC'.$y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%04d', $invid);
            }
        //        if($this->input->post('cash_closure') == 1){
        //            $date = date('Y-m-d', strtotime("+1 days"));
        //        }else{
        //        }
            $date = date('Y-m-d');
            $datetime = date('Y-m-d H:i:s');
            $idstate = $this->input->post('idstate');
            $idcustomer = $this->input->post('idcustomer');
            $cust_fname = $this->input->post('cust_fname');
            $cust_lname = $this->input->post('cust_lname');
            $cust_idstate = $this->input->post('cust_idstate');
            $cust_pincode = $this->input->post('cust_pincode');
            $idsale_token = $this->input->post('idsale_token');
            if($idsale_token == ''){
                $idsale_token = NULL;
            }
                $gst_type = 0; //cgst
                if($idstate != $cust_idstate){
                    $gst_type = 1; //igst
                }
                $remark = $this->input->post('remark');
                if($id_advance_payment_receive = $this->input->post('id_advance_payment_receive')){
                    if($remark){
                        $remark .= '<hr>';
                    }
                    $remark .= 'Advanced Booking: AdvPay/'. $invoice_no->branch_code.'/'.$id_advance_payment_receive.' ('.$this->input->post('booking_date').'), Amount: '.$this->input->post('booking_amount').'- '.$this->input->post('booking_payment_mode');
                }else{
                    $id_advance_payment_receive = NULL;
                }
                $data = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_idstate' => $cust_idstate,
                    'customer_pincode' => $cust_pincode,
                    'customer_contact' => $this->input->post('cust_mobile'),
                    'customer_address' => $this->input->post('address'),
                    'customer_gst' => $this->input->post('gst_no'),
                    'idsalesperson' => $this->input->post('idsalesperson'),
                    'basic_total' => $this->input->post('gross_total'),
                    'discount_total' => $this->input->post('final_discount'),
                    'final_total' => $this->input->post('final_total'),
                    'gst_type' => $gst_type,
                    'created_by' => $this->input->post('created_by'),
                    'remark' => $remark,
                    'entry_time' => $datetime,
                    'dcprint' => $dcprint[0],
                    'idadvance_payment_receive' => $id_advance_payment_receive,
                    'idsaletoken' => $idsale_token,
                );
                $idsale = $this->Sale_model->save_sale($data);
                // Payment
                $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
                $credittype = $this->input->post('credit_type');
                $amount = $this->input->post('amount');
                $payment_type = $this->input->post('payment_type');
                $tranxid = $this->input->post('tranxid');
                $headattr = $this->input->post('headattr');
                $vin=array();
                foreach ($headattr as $idpayment_head => $attributedata){
                    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
                    $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
                    $attr_value=array();
                    for($m=0;$m<$mode_count;$m++){
                        foreach ($payment_attribute as $attr){
                            $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
                        }
                    }
                    for($j=0; $j < count($idpaymenthead); $j++){
                        if($idpaymenthead[$j] == $idpayment_head){
                            $vin[$j]=$attr_value;
                        }
                    }
                }
                $parr=array();
        //        die('<pre>'.print_r($vin,1).'</pre>');
        //        die('<pre>'.print_r($headattr,1).'</pre>');
                for($j=0; $j < count($idpaymenthead); $j++){
                    $received_amount=0;$pending_amt=$amount[$j];$received_entry_time=NULL;$payment_receive=0;
                    if($idpaymenthead[$j] == 1){
                        $received_amount = $amount[$j];
                        $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                        $srpayment = array(
                            'date' => $date,
                            'inv_no' => $inv_no,
                            'entry_type' => 1,
                            'idbranch' => $idbranch,
                            'idtable' => $idsale,
                            'table_name' => 'sale',
                            'amount' => $received_amount,
                        );
                        $this->Sale_model->save_daybook_cash_payment($srpayment);
                    }
        //            $received_amount=0;
        //            if($payment_type[$j] == 1){
        //                $received_amount = $amount[$j];
        //            }
                    $payment = array(
                        'date' => $date,
                        'idsale' => $idsale,
                        'amount' => $amount[$j],
        //                'received_amount' => $amount[$j],
                        'idpayment_head' => $idpaymenthead[$j],
                        'idpayment_mode' => $payment_type[$j],
                        'transaction_id' => $tranxid[$j],
                        'inv_no' => $inv_no,
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'created_by' => $this->input->post('created_by'),
                        'entry_time' => $datetime,
                        'received_amount' => $received_amount,
                        'received_entry_time'=>$received_entry_time,
                        'payment_receive' => $payment_receive
                    );
                    if(isset($vin[$j])>0){
                        $payment = array_merge($payment, $vin[$j]); 
                    }
        //            die('<pre>'.print_r($payment,1).'</pre>');
                    $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
                    if($credittype[$j] == 0){
                        $npayment = array(
                            'idsale_payment' => $id_sale_payment,
                            'inv_no' => $inv_no,
                            'idsale' => $idsale,
                            'date' => $date,
                            'idcustomer' => $idcustomer,
                            'idbranch' => $idbranch,
                            'amount' => $amount[$j],
                            'idpayment_head' => $idpaymenthead[$j],
                            'idpayment_mode' => $payment_type[$j],
                            'transaction_id' => $tranxid[$j],
                            'created_by' => $this->input->post('created_by'),
                            'entry_time' => $datetime,
                            'received_amount' => $received_amount,
                            'pending_amt' => $pending_amt,
                            'received_entry_time'=>$received_entry_time,
                            'payment_receive' => $payment_receive
                        );
                        if(isset($vin[$j])>0){
                            $npayment = array_merge($npayment, $vin[$j]); 
                        }
                        $this->Sale_model->save_payment_reconciliation($npayment);
                    }
        //            $parr[] = $payment;
                    $qy = "SELECT payment_mode FROM payment_mode WHERE id_paymentmode = $payment_type[$j]";
                    $query = $this->db->query($qy);
                    $payment_mode = $query->result(); 
                    $pay_mode =  $payment_mode[0]->payment_mode;
                    $cuttomer_payment = array(
                        'idcustomer'=>$idcustomer,
                        'inv_no'=>$inv_no,
                        'inv_date'=>$date,
                        'payment_head'=>$this->input->post('headname['.$j.']'),
                        'payment_mode'=>$pay_mode,
                        'amount'=>$amount[$j],
                        'idtransaction'=>$tranxid[$j],
                    );
//                        echo '<pre>';
//                        print_r($cuttomer_payment);die;
                    $this->Customerloyalty_model->save_handset_payment_history($cuttomer_payment); 
                    
                }
        //        die('<pre>'.print_r($parr,1).'</pre>');
                //Sale_product
                $idtype = $this->input->post('idtype');
                $idcategory = $this->input->post('idcategory');
                $idbrand = $this->input->post('idbrand');
                $idmodel = $this->input->post('idmodel');
                $idvariant = $this->input->post('idvariant');
                $idgodown = $this->input->post('idgodown');
                $skutype = $this->input->post('skutype');
                $product_name = $this->input->post('product_name');
                $imei = $this->input->post('imei');
                $price = $this->input->post('price');
                $basic = $this->input->post('basic');
                $discount_amt = $this->input->post('discount_amt');
                $total_amt = $this->input->post('total_amt');
                $landing = $this->input->post('landing');
                $mrp = $this->input->post('mrp');
                $mop = $this->input->post('mop');
                $nlc_price = $this->input->post('nlc_price');
                $ageing = $this->input->post('ageing');
                $focusstatus = $this->input->post('focus_st');
                $focus_incentive = $this->input->post('focus_incentive');
                $salesman_price = $this->input->post('salesman_price');
                $qty = $this->input->post('qty');
                $rowid = $this->input->post('rowid');
                $is_gst = $this->input->post('is_gst');
                $idvendor = $this->input->post('idvendor');
                $hsn = $this->input->post('hsn'); 
                $is_mop = $this->input->post('is_mop'); // price on invoice
                $sale_type = $this->input->post('sale_type'); // 0=Normal,1=PurchaseFirst,2=SaleFirst
                $insurance_imei = $this->input->post('insurance_imei'); 
                $activation_code = $this->input->post('activation_code'); 
        //        $imei_history[nest] = array();
                $insurance_recon = array();
                for($i = 0; $i < count($idvariant); $i++){
                    $cgst = 0; $sgst = 0; $igst = 0;
                    if($gst_type == 1){
                        $igst = $this->input->post('igst['.$i.']');
                    }else{
                        $cgst = $this->input->post('cgst['.$i.']');
                        $sgst = $this->input->post('sgst['.$i.']');
                    }
                    $sale_product[$i] = array(
                        'date' => $date,
                        'idsale' => $idsale,
                        'idmodel' => $idmodel[$i],
                        'idvariant' => $idvariant[$i],
                        'imei_no' => $imei[$i],
                        'hsn' => $hsn[$i],
                        'idskutype' => $skutype[$i],
                        'idgodown' => $idgodown[$i],
                        'idproductcategory' => $idtype[$i],
                        'idcategory' => $idcategory[$i],
                        'idbrand' => $idbrand[$i],
                        'product_name' => $product_name[$i],
                        'price' => $price[$i],
                        'landing' => $landing[$i],
                        'mrp' => $mrp[$i],
                        'mop' => $mop[$i],
                        'nlc_price' => $nlc_price[$i],
                        'ageing' => $ageing[$i],
                        'focus' => $focusstatus[$i],
                        'focus_incentive' => $focus_incentive[$i],
                        'salesman_price' => $salesman_price[$i],
                        'inv_no' => $inv_no,
                        'qty' => $qty[$i],
                        'idbranch' => $idbranch,
                        'discount_amt' => $discount_amt[$i],
                        'is_gst' => $is_gst[$i],
                        'is_mop' => $is_mop[$i],
                        'basic' => $basic[$i],
                        'idvendor' => $idvendor[$i],
                        'cgst_per' => $cgst,
                        'sgst_per' => $sgst,
                        'igst_per' => $igst,
                        'total_amount' => $total_amt[$i],
                        'entry_time' => $datetime,
                        'ssale_type' => $sale_type[$i],
                        'insurance_imei_no' => $insurance_imei[$i],
                        'activation_code' => $activation_code[$i],
                    );
                    $creted_by = $this->input->post('created_by');
                    $idsaleproduct = $this->Sale_model->save_sale_product($sale_product[$i]);
                    $idsaleproduct_1 = $this->Customerloyalty_model->save_customer_purchase($sale_product[$i],$idcustomer,$creted_by,$i);
                    if($skutype[$i] == 4){ //qunatity
                        if($sale_type[$i] == 2){
                            $this->load->model('Inward_model');
                            $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($idvariant[$i],4,$idbranch,$idgodown[$i]);
            //                $hostock = $this->Inward_model->get_hostock_byidmodel_skutype($product_id[$i], 4, 1);
                            if(count($hostock) == 0){
                                $inward_stock_sku = array(
                                    'date' => $date,
                                    'idgodown' => $idgodown[$i],
                                    'product_name' => $product_name[$i],
                                    'idskutype' => $skutype[$i],
                                    'idproductcategory' => $idtype[$i],
                                    'idcategory' => $idcategory[$i],
                                    'is_gst' => 1,
                                    'idvariant' => $idvariant[$i],
                                    'idbranch' => $idbranch,
                                    'idmodel' => $idmodel[$i],
                                    'idbrand' => $idbrand[$i],
                                    'created_by' => $this->input->post('created_by'),
                                    'idvendor' => $idvendor[$i],
                                    'qty' => -$qty[$i],
                                );
                                $this->Inward_model->save_stock($inward_stock_sku);
                            }else{
                                foreach ($hostock as $hstock){
                                    $updated_qty = $hstock->qty - $qty[$i];
                                    $this->Inward_model->update_stock_byid($hstock->id_stock,$updated_qty);
                                }
                            }
                        }else{
                            $this->Sale_model->minus_stock_byidstock($rowid[$i], $qty[$i]);
                        }
                    }else{
                        $this->Purchase_model->delete_stock_byidstock($rowid[$i]);
                        // IMEI History
                        $imei_history[]=array(
                            'imei_no' => $imei[$i],
                            'entry_type' => 'Sale',
                            'entry_time' => $datetime,
                            'date' => $date,
                            'idbranch' => $idbranch,
                            'idgodown' => $idgodown[$i],
                            'idvariant' => $idvariant[$i],
        //                    'model_variant_full_name' => $product_name[$i],
                            'idimei_details_link' => 4, // Sale from imei_details_link table
                            'idlink' => $idsale,
                            'iduser' => $this->input->post('created_by'),
        //                    'imei_latitude' => $this->input->post('cust_latitude'),
        //                    'imei_longitude' => $this->input->post('cust_longitude'),
                        );
                    }
                    if($sale_type[$i] != 0){
                        $insurance_recon[] = array(
                            'date' => $date,
                            'idsale' => $idsale,
                            'idsale_product' => $idsaleproduct,
                            'idmodel' => $idmodel[$i],
                            'idvariant' => $idvariant[$i],
                            'idskutype' => $skutype[$i],
                            'idproductcategory' => $idtype[$i],
                            'idcategory' => $idcategory[$i],
                            'idbrand' => $idbrand[$i],
                            'product_name' => $product_name[$i],
                            'inv_no' => $inv_no,
                            'qty' => $qty[$i],
                            'idbranch' => $idbranch,
                            'idvendor' => $idvendor[$i],
                            'ssale_type' => $sale_type[$i],
                            'total_amount' => $total_amt[$i],
                            'entry_time' => $datetime,
                            'insurance_imei_no' => $insurance_imei[$i],
                            'activation_code' => $activation_code[$i],
                        );
                    }
                }
                if(count($insurance_recon) > 0){
                    $this->Sale_model->save_batch_insurance_recon($insurance_recon);
                }
                if(count($imei_history) > 0){
                    $this->General_model->save_batch_imei_history($imei_history);
                }

                // BFL integration
                if($this->input->post('bfl_do_id')){
                    $bfl_data = array(
                        'do_id' => $this->input->post('bfl_do_id'),
                        'idsale' => $idsale,
                        'idsale_product' => $idsaleproduct,
                        'bfl_brand' => $this->input->post('bfl_brand'),
                        'bfl_model' => $this->input->post('bfl_model'),
                        'bfl_srno' => $this->input->post('bfl_srno'),
                        'idcustomer' => $idcustomer,
                        'mobile' => $this->input->post('bfl_mobile'),
                        'customer_name' => $this->input->post('bfl_customer'),
                        'customer_gst' => $this->input->post('gst_no'),
                        'scheme_code' => $this->input->post('scheme_code'),
                        'scheme' => $this->input->post('scheme'),
                        'mop' => $this->input->post('bfl_mop'),
                        'downpayment' => $this->input->post('bfl_downpayment'),
                        'netdisbursement' => $this->input->post('bfl_netdisbursement'),
                        'loan' => $this->input->post('bfl_loan'),
                        'emi_amount' => $this->input->post('bfl_emi_amount'),
                        'tenure' => $this->input->post('bfl_tenure'),
                        'bfl_remark' => $this->input->post('bfl_remark'),
                        'entry_time' => $datetime,
                    );
                    $this->Sale_model->save_bfl($bfl_data);			
                }
                $invoice_data = array( 'invoice_no' => $invid );
                $this->General_model->edit_db_branch($idbranch, $invoice_data);
                if($id_advance_payment_receive){
                    $this->load->model('Reconciliation_model');
                    $update_booking = array(
                        'claim' => 1,
                        'idsale' => $idsale,
                        'inv_no' => $inv_no,
                        'inv_date' => $date,
                    );
                    $this->Reconciliation_model->update_advanced_payment_byid($id_advance_payment_receive, $update_booking);
                    $adv_sale_pay_id=$this->common_model->getSingleRow('sale_payment', array('inv_no' => 'AdvPay/'. $invoice_no->branch_code.'/'.$id_advance_payment_receive));
                    $update_sale_payment = array(
                        'idsale' => $idsale,
                        'inv_no' => $inv_no,
                        // 'date' => $date,
                    );
                    
                    $ins= $this->common_model->updateRow('sale_payment', $update_sale_payment, array('inv_no'=>'AdvPay/'. $invoice_no->branch_code.'/'.$id_advance_payment_receive));


                    $update_sale_payment_rec = array(
                        'idsale' => $idsale,
                        'inv_no' => $inv_no,
                        'idsale_payment' => $adv_sale_pay_id['id_salepayment'],
                    );
                    
                    $upd_sale_pay= $this->common_model->updateRow('payment_reconciliation', $update_sale_payment_rec, array('inv_no'=>'AdvPay/'. $invoice_no->branch_code.'/'.$id_advance_payment_receive));
                }
                if($idsale_token){
                    $update_token = array(
                        'status' => 1,
                        'idsale' => $idsale,
                        'update_time' => $datetime,
                    );
                    $this->Sale_model->update_sale_token_byid($idsale_token, $update_token);
                }
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
                }else{
                    $this->db->trans_commit();
                    $this->session->set_flashdata('save_data', 'Invoice bill generated');
                }
        //        die('<pre>'.print_r($_POST,1).'</pre>');
                if($dcprint[0] == 0){
                    $this->session->set_userdata('idsale_url','invoice_print_14april/'.$idsale);
                    return redirect('Sale/invoice_print/'.$idsale);
                }else{
                    $this->session->set_userdata('idsale_url','dc_print_14april/'.$idsale);
                    return redirect('Sale/dc_print/'.$idsale);
                }
            }else{ ?>
                <script>
                    if (confirm('This message displayed, due to slow network. Entry already submitted... Go and check in invoice search menu. सबमिट बटण वर दोन वेळा क्लिक केले आहे, किंवा नेटवर्क समस्या मुळे हा संदेश प्रदर्शित झाला. चलन आधीच सबमिट झाले आहे... जर प्रिंट ओपन झाली नाही तर Invoice Search मेनूमध्ये तपासणी करा')){
                        window.location = "<?php echo $this->session->userdata('idsale_url') ?>";
                    }else{
                        window.location = "<?php echo $this->session->userdata('idsale_url') ?>";
                    }
                </script>
            <?php }
        }
    }
    
    public function save_corporate_sale() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $idbranch = $this->input->post('idbranch');
        $dcprint = $this->input->post('dcprint');
        $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $invid = $invoice_no->invoice_no + 1; 
        $y = date('y', mktime(0, 0, 0, 3 + date('m')));
        $y1 = $y + 1;
        if($dcprint[0] == 0){
            $inv_no = $y .'-'. $y1 . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
        }else{
            $inv_no = 'DC'.$y .'-'. $y1 . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
        }
//        if($this->input->post('cash_closure') == 1){
//            $date = date('Y-m-d', strtotime("+1 days"));
//        }else{
//        }
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $idstate = $this->input->post('idstate');
        $idcustomer = $this->input->post('idcustomer');
        $cust_fname = $this->input->post('cust_fname');
        $cust_lname = $this->input->post('cust_lname');
        $cust_idstate = $this->input->post('cust_idstate');
        $cust_pincode = $this->input->post('cust_pincode');
        $gst_type = 0; //cgst
        if($idstate != $cust_idstate){
            $gst_type = 1; //igst
        }
        $data = array(
            'date' => $date,
            'inv_no' => $inv_no,
            'idbranch' => $idbranch,
            'corporate_sale' => 1,
            'idcustomer' => $idcustomer,
            'customer_fname' => $cust_fname,
            'customer_lname' => $cust_lname,
            'customer_idstate' => $cust_idstate,
            'customer_pincode' => $cust_pincode,
            'customer_contact' => $this->input->post('cust_mobile'),
            'customer_address' => $this->input->post('address'),
            'customer_gst' => $this->input->post('gst_no'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'basic_total' => $this->input->post('gross_total'),
            'discount_total' => $this->input->post('final_discount'),
            'final_total' => $this->input->post('final_total'),
            'gst_type' => $gst_type,
            'created_by' => $this->input->post('created_by'),
            'remark' => $this->input->post('remark'),
            'entry_time' => $datetime,
            'dcprint' => $dcprint[0],
        );
        $idsale = $this->Sale_model->save_sale($data);
        // Payment
        $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
        $credittype = $this->input->post('credit_type');
        $amount = $this->input->post('amount');
        $payment_type = $this->input->post('payment_type');
        $tranxid = $this->input->post('tranxid');
        $headattr = $this->input->post('headattr');
        $vin=array();
        foreach ($headattr as $idpayment_head => $attributedata){
            $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
            $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
            $attr_value=array();
            for($m=0;$m<$mode_count;$m++){
                foreach ($payment_attribute as $attr){
                    $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
                }
            }
            for($j=0; $j < count($idpaymenthead); $j++){
                if($idpaymenthead[$j] == $idpayment_head){
                    $vin[$j]=$attr_value;
                }
            }
        }
        $parr=array();
//        die('<pre>'.print_r($vin,1).'</pre>');
//        die('<pre>'.print_r($headattr,1).'</pre>');
        for($j=0; $j < count($idpaymenthead); $j++){
            if($idpaymenthead[$j] == 1){
                $daybook_payment = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'entry_type' => 1,
                    'idbranch' => $idbranch,
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'amount' => $amount[$j],
                );
                $this->Sale_model->save_daybook_cash_payment($daybook_payment);
            }
            $payment = array(
                'date' => $date,
                'idsale' => $idsale,
                'amount' => $amount[$j],
                'corporate_sale' => 1,
                'idpayment_head' => $idpaymenthead[$j],
                'idpayment_mode' => $payment_type[$j],
                'transaction_id' => $tranxid[$j],
                'inv_no' => $inv_no,
                'idcustomer' => $idcustomer,
                'idbranch' => $idbranch,
                'created_by' => $this->input->post('created_by'),
                'entry_time' => $datetime,
            );
            if(isset($vin[$j])>0){
                $payment = array_merge($payment, $vin[$j]); 
            }
//            die('<pre>'.print_r($payment,1).'</pre>');
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$j] == 0){
                $npayment = array(
                    'idsale_payment' => $id_sale_payment,
                    'inv_no' => $inv_no,
                    'idsale' => $idsale,
                    'corporate_sale' => 1,
                    'date' => $date,
                    'idcustomer' => $idcustomer,
                    'idbranch' => $idbranch,
                    'amount' => $amount[$j],
                    'idpayment_head' => $idpaymenthead[$j],
                    'idpayment_mode' => $payment_type[$j],
                    'transaction_id' => $tranxid[$j],
                    'created_by' => $this->input->post('created_by'),
                    'entry_time' => $datetime,
                );
                if(isset($vin[$j])>0){
                    $npayment = array_merge($npayment, $vin[$j]); 
                }
                $this->Sale_model->save_payment_reconciliation($npayment);
            }
//            $parr[] = $payment;
        }
//        die('<pre>'.print_r($parr,1).'</pre>');
        //Sale_product
        $idtype = $this->input->post('idtype');
        $idcategory = $this->input->post('idcategory');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        $idvariant = $this->input->post('idvariant');
        $idgodown = $this->input->post('idgodown');
        $skutype = $this->input->post('skutype');
        $product_name = $this->input->post('product_name');
        $imei = $this->input->post('imei');
        $price = $this->input->post('price');
        $basic = $this->input->post('basic');
        $discount_amt = $this->input->post('discount_amt');
        $total_amt = $this->input->post('total_amt');
        $landing = $this->input->post('landing');
        $mrp = $this->input->post('mrp');
        $mop = $this->input->post('mop');
        $nlc_price = $this->input->post('nlc_price');
        $ageing = $this->input->post('ageing');
        $focusstatus = $this->input->post('focus_st');
        $focus_incentive = $this->input->post('focus_incentive');
        $salesman_price = $this->input->post('salesman_price');
        $qty = $this->input->post('qty');
        $rowid = $this->input->post('rowid');
        $is_gst = $this->input->post('is_gst');
        $idvendor = $this->input->post('idvendor');
        $hsn = $this->input->post('hsn'); 
        $is_mop = $this->input->post('is_mop'); // price on invoice
        $imei_history[] = array('nest'=>array());
        for($i = 0; $i < count($idvariant); $i++){
            $cgst = 0; $sgst = 0; $igst = 0;
            if($gst_type == 1){
                $igst = $this->input->post('igst['.$i.']');
            }else{
                $cgst = $this->input->post('cgst['.$i.']');
                $sgst = $this->input->post('sgst['.$i.']');
            }
            $sale_product[$i] = array(
                'date' => $date,
                'idsale' => $idsale,
                'idmodel' => $idmodel[$i],
                'idvariant' => $idvariant[$i],
                'imei_no' => $imei[$i],
                'hsn' => $hsn[$i],
                'idskutype' => $skutype[$i],
                'idgodown' => $idgodown[$i],
                'idproductcategory' => $idtype[$i],
                'idcategory' => $idcategory[$i],
                'idbrand' => $idbrand[$i],
                'product_name' => $product_name[$i],
                'price' => $price[$i],
                'landing' => $landing[$i],
                'mrp' => $mrp[$i],
                'mop' => $mop[$i],
                'nlc_price' => $nlc_price[$i],
                'ageing' => $ageing[$i],
                'focus' => $focusstatus[$i],
                'focus_incentive' => $focus_incentive[$i],
                'salesman_price' => $salesman_price[$i],
                'inv_no' => $inv_no,
                'qty' => $qty[$i],
                'idbranch' => $idbranch,
                'discount_amt' => $discount_amt[$i],
                'is_gst' => $is_gst[$i],
                'is_mop' => $is_mop[$i],
                'basic' => $basic[$i],
                'idvendor' => $idvendor[$i],
                'cgst_per' => $cgst,
                'sgst_per' => $sgst,
                'igst_per' => $igst,
                'total_amount' => $total_amt[$i],
                'entry_time' => $datetime,
            );
            $idsaleproduct = $this->Sale_model->save_sale_product($sale_product[$i]);
            if($skutype[$i] == 4){ //qunatity
                $this->Sale_model->minus_stock_byidstock($rowid[$i], $qty[$i]);
            }else{
                $this->Purchase_model->delete_stock_byidstock($rowid[$i]);
                // IMEI History
                $imei_history['nest'][]=array(
                    'imei_no' => $imei[$i],
                    'entry_type' => 'Sale',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $idgodown[$i],
                    'idvariant' => $idvariant[$i],
//                    'model_variant_full_name' => $product_name[$i],
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
                    'iduser' => $this->input->post('created_by'),
//                    'imei_latitude' => $this->input->post('cust_latitude'),
//                    'imei_longitude' => $this->input->post('cust_longitude'),
                );
            }
        }
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        // BFL integration
        if($this->input->post('bfl_do_id')){
            $bfl_data = array(
                'do_id' => $this->input->post('bfl_do_id'),
                'idsale' => $idsale,
                'idsale_product' => $idsaleproduct,
                'bfl_brand' => $this->input->post('bfl_brand'),
                'bfl_model' => $this->input->post('bfl_model'),
                'bfl_srno' => $this->input->post('bfl_srno'),
                'idcustomer' => $idcustomer,
                'mobile' => $this->input->post('mobile'),
                'customer_name' => $this->input->post('bfl_customer'),
                'customer_gst' => $this->input->post('gst_no'),
                'scheme_code' => $this->input->post('scheme_code'),
                'scheme' => $this->input->post('scheme'),
                'mop' => $this->input->post('bfl_mop'),
                'downpayment' => $this->input->post('bfl_downpayment'),
                'loan' => $this->input->post('bfl_loan'),
                'emi_amount' => $this->input->post('bfl_emi_amount'),
                'tenure' => $this->input->post('bfl_tenure'),
                'bfl_remark' => $this->input->post('bfl_remark'),
                'entry_time' => $datetime,
            );
            $this->Sale_model->save_bfl($bfl_data);			
        }
        $invoice_data = array( 'invoice_no' => $invid );
        $this->General_model->edit_db_branch($idbranch, $invoice_data);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Invoice bill generated');
        }
//        die('<pre>'.print_r($_POST,1).'</pre>');
        if($dcprint[0] == 0){
            return redirect('Sale/invoice_print/'.$idsale);
        }else{
            return redirect('Sale/dc_print/'.$idsale);
        }
    }
    
    public function ajax_get_payment_mode_byhead() {
        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($this->input->post('payment_head')); ?>
        <option value="">Select Mode</option>
        <?php foreach ($payment_mode as $mode){ ?>
            <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode; ?></option>
        <?php } 
    }
    
    public function ajax_get_sale_receivables() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $credit_report = $this->Sale_model->ajax_get_sale_receivables($idpayment_head,$idpayment_mode,$idbranch); 
        $payment_head = $this->General_model->get_active_payment_head_by_credit_receive_type();
//        $payment_mode = $this->General_model->get_active_payment_mode(); ?>
<thead class="bg-info">
    <th>Sr</th>
    <th>Invoice No</th>
    <th>Date_time</th>
    <th>Branch</th>
    <th>Customer Name</th>
    <th>Contact</th>
    <th>Corporate Sale</th>
    <th>Mode</th>
    <th>Buyback Product</th>
    <th>Total Amount</th>
    <th>Days</th>
    <th>Received Amt</th>
    <th>Pending Amt</th>
    <th>Payment Mode</th>
    <th>Approved By</th>
</thead>
<tbody class="data_1">
    <?php $i=1; foreach ($credit_report as $credit){ $credit_amt = $credit->amount - $credit->received_amount; //  if($credit_amt != 0){ ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
            <td><?php echo date('d/m/Y h:i a', strtotime($credit->entry_time)) ?></td>
            <td><?php echo $credit->branch_name; ?></td>
            <td><?php echo $credit->customer_fname.' '.$credit->customer_lname; ?></td>
            <td><?php echo $credit->customer_contact ?></td>
            <!--<td><?php // echo $credit->corporate_sale ?></td>-->
            <td><?php if($credit->corporate_sale==1){ echo "Corporate Sale"; } ?></td>
            <td><?php echo $credit->payment_mode ?></td>
            <td><?php echo $credit->product_model_name ?></td>
            <td><?php echo $credit->amount ?></td>
                <td><?php  $now = time(); // or your date as well
                $credit_date = strtotime($credit->entry_time);
                $datediff = $now - $credit_date;
                echo round($datediff / (86400)); ?> 
            </td>
            <td><?php echo $credit->received_amount; ?></td>
            <td><?php echo $credit_amt ?></td>
            <?php if($credit->valid_for_creadit_receive == 1){ ?>
               <!--Credit-->
               <td style="min-width: 400px">
                <form class="credit_payment_receive_form">
                    <?php if($idpayment_head == 7){ ?>
                        <div class="col-md-2" style="padding: 5px">
                            Cash
                            <input type="hidden" name="payment_head" value="1" />
                            <input type="hidden" name="payment_mode" value="1" />
                        </div>
                        <div class="col-md-6" style="padding: 5px">
                            <input type="number" name="amount" class="form-control input-sm amount<?php echo $credit->id_salepayment ?><?php echo $idpayment_head ?>" placeholder="Amount" min="<?php echo $credit->amount ?>" required="" />
                        </div>
                        <div class="hidden">
                            <input type="text" class="form-control input-sm tranxid" id="tranxid" name="tranxid" value="<?php echo NULL; ?>" />
                        </div>
                        <div class="col-md-3" style="padding: 5px">
                            <button type="submit" id="receive_submit" class="btn btn-sm btn-info waves-effect waves-ripple pull-right receive_submit" style="margin: 0; text-transform: capitalize"><i class="fa fa-sign-out"></i> Receive</button>
                        </div><div class="clearfix"></div>
                        <div class="col-md-2" style="padding: 5px">
                            Remark
                        </div>
                        <div class="col-md-10" style="padding: 5px">
                            <input type="text" class="form-control input-sm credit_receive_remark" id="credit_receive_remark" name="credit_receive_remark" placeholder="Enter Custody Receive Remark" />
                        </div>
                    <?php }else{ ?>
                        <div class="col-md-9" style="padding: 5px">
                            <select class="form-control input-sm" id="paymentmode<?php echo $credit->id_salepayment ?>" required="">
                                <option value="">Select Payment mode</option>
                                <?php foreach ($payment_head as $head) { ?>
                                    <option value="<?php echo $head->id_paymenthead ?>"><?php echo $head->payment_head ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3" style="padding: 5px">
                            <button type="submit" id="receive_submit" class="btn btn-sm btn-info waves-effect waves-ripple pull-right receive_submit" style="margin: 0; text-transform: capitalize"><i class="fa fa-sign-out"></i> Receive</button>
                        </div><div class="clearfix"></div>
                        <div class="modes"></div>
                    <?php } ?>
                    <input type="hidden" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>" />
                    <input type="hidden" name="inv_no" value="<?php echo $credit->inv_no ?>" />
                    <input type="hidden" name="idbranch" value="<?php echo $credit->idbranch ?>" />
                    <input type="hidden" name="idsale" value="<?php echo $credit->idsale ?>" />
                    <input type="hidden" name="id_salepayment" class="id_salepayment" value="<?php echo $credit->id_salepayment ?>" />
                    <input type="hidden" name="corporate_sale" value="<?php echo $credit->corporate_sale ?>" />
                    <input type="hidden" name="idcustomer" value="<?php echo $credit->idcustomer ?>" />
                    <input type="hidden" name="credit_amount" class="credit_amount" value="<?php echo $credit->amount ?>" />
                    <input type="hidden" name="pending_amount" class="pending_amount" value="<?php echo $credit_amt ?>" />
                    <input type="hidden" name="received_amount" class="received_amount" value="<?php echo $credit->received_amount ?>" />
                </form>
            </td>
        <?php }else{ ?>
            <td><?php echo $credit->payment_mode ?></td>
        <?php } ?>
        <td><?php echo $credit->approved_by ?></td>
    </tr>
    <input type="hidden" id="idpayment_head" value="<?php echo $idpayment_head ?>" />
    <script>
        $(document).ready(function(){
            $('#paymentmode<?php echo $credit->id_salepayment ?>').change(function(){
                var paymenthead = $(this).val();
                var credit_amount = $(this).closest('form').find('.pending_amount').val();
                var id_salepayment = $(this).closest('form').find('.id_salepayment').val();
                var modes = $(this).closest('form').find('.modes');
                var idpayment_head = $('#idpayment_head').val();
                if(paymenthead == ''){
                    modes.html('');
                }else{
                    $.ajax({
                        url: "<?php echo base_url() ?>Sale/ajax_get_payment_mode_data_byidhead_for_receivable",
                        method: "POST",
                        data:{paymenthead : paymenthead, credit_amount: credit_amount, id_salepayment: id_salepayment,idpayment_head:idpayment_head},
                        success: function (data)
                        {
                            modes.html(data);
                        }
                    });
                }
            });
        });
    </script>
<?php } ?>
</tbody>
<?php 
}

public function ajax_get_credit_receivable_report() {
    $idbranch = $this->input->post('idbranch');
    $idpayment_head = $this->input->post('idpayment_head');
    $idpayment_mode = $this->input->post('idpayment_mode');
    $idbranches = $this->input->post('branches');
    $from = $this->input->post('from');
    $to = $this->input->post('to');
    $credit_report = $this->Sale_model->ajax_get_credit_receivable_report($idpayment_head,$idpayment_mode,$idbranch,$idbranches,$from,$to);
    if(count($credit_report) == 0){
        echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
        '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
        . '</center>';
    }else{ ?>
        <thead class="bg-info">
            <th>Sr</th>
            <th>Invoice No</th>
            <th>Date_time</th>
            <th>Branch</th>
            <th>Corporate Sale</th>
            <th>Mode</th>
            <th>Total Amount</th>
            <th>Approved By</th>
            <th>Days</th>
            <th>Received Amt</th>
            <th>Pending Amt</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($credit_report as $credit){ 
                $credit_amt = $credit->amount - $credit->received_amount; //  if($credit_amt != 0){
                $now = time(); // or your date as well
                $credit_date = strtotime($credit->entry_time);
                $datediff = $now - $credit_date; ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                    <td><?php echo date('d/m/Y h:i a', strtotime($credit->entry_time)) ?></td>
                    <td><?php echo $credit->branch_name; ?></td>
                    <td><?php if($credit->corporate_sale==1){ echo 'Corporate Sale'; }else{ echo '-'; } ?></td>
                    <td><?php echo $credit->payment_mode ?></td>
                    <td><?php echo $credit->amount ?></td>
                    <td><?php echo $credit->approved_by ?></td>
                    <td><?php echo round($datediff / (86400)); ?></td>
                    <td><?php echo $credit->received_amount; ?></td>
                    <td><?php echo $credit_amt ?></td>
                </tr>
            <?php } ?>
        </tbody>
    <?php }
}

public function ajax_get_payment_mode_data_byidhead_for_receivable() {
    $head = $this->input->post('paymenthead');
    $credit_amt = $this->input->post('credit_amount');
    $id_salepayment = $this->input->post('id_salepayment');
    $idpayment_head = $this->input->post('idpayment_head');
    $payment_head = $this->General_model->get_payment_head_byid($head); 
    $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); ?>
    <div class="modes_block modes_blockc">
        <input type="hidden" name="payment_head" value="<?php echo $head ?>" />
        <div class="col-md-4" style="padding: 5px">
            <small>Type</small>
            <select class="form-control input-sm" name="payment_mode">
                <?php foreach ($payment_mode as $mode) { ?>
                    <option value="<?php echo $mode->id_paymentmode ?>"><?php echo $mode->payment_mode ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4" style="padding: 5px">
            <small>Amount</small>
            <?php // if($idpayment_head == 7){ ?>
                <!--<input type="number" name="amount" class="form-control input-sm amount<?php echo $id_salepayment ?><?php echo $head ?>" placeholder="Amount" min="<?php echo $credit_amt ?>" required="" />-->
                <?php // }else{ ?>
                    <input type="number" name="amount" class="form-control input-sm amount<?php echo $id_salepayment ?><?php echo $head ?>" placeholder="Amount" min="1" max="<?php echo $credit_amt ?>" required="" />
                    <?php // } ?>
                </div>
                <?php if($payment_head->tranxid_type == NULL){ ?>
                    <div class="col-md-4 hidden">
                        <small><?php echo $payment_head->tranxid_type ?></small>
                        <input type="text" class="form-control input-sm tranxid" id="tranxid" name="tranxid" placeholder="<?php echo $payment_head->tranxid_type ?>" value="<?php echo NULL; ?>" />
                    </div>
                <?php }else{ ?>
                    <div class="col-md-4" style="padding: 5px">
                        <small><?php echo $payment_head->tranxid_type ?></small>
                        <input type="text" class="form-control input-sm tranxid" id="tranxid" name="tranxid" placeholder="<?php echo $payment_head->tranxid_type ?>" required="" />
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
                <?php foreach ($payment_attribute as $attribute){ ?>
                    <div class="col-md-4" style="padding: 5px">
                        <small><?php echo $attribute->attribute_name ?></small>
                        <input type="text" class="form-control input-sm headattr" name="<?php echo $attribute->column_name ?>" placeholder="<?php echo $attribute->attribute_name ?>" required="" />
                    </div>
                <?php } ?>
                <div class="col-md-12" style="padding: 5px">
                    <input type="text" class="form-control input-sm credit_receive_remark" id="credit_receive_remark" name="credit_receive_remark" placeholder="Enter Credit Receive Remark" />
                </div>
                <div class="clearfix"></div>
                </div><div class="clearfix"></div> <?php
            }
            
            public function bfl_test() {
//        die('<pre>'.print_r($POST,1).'</pre>');
                $sfid = $this->input->post('sfid');
                $bfl_data = $this->Sale_model->bfl($sfid, 'testpartner');
                echo json_encode($bfl_data);
            }
            public function gstin_test() {
//        die('<pre>'.print_r($POST,1).'</pre>');
                $customer_gst = $this->input->post('customer_gst');
                $bfl_data = $this->Sale_model->get_gstin_details($customer_gst);
                echo json_encode($bfl_data);
//        echo '{"filing":[],"compliance":{"filingFrequency":null},"taxpayerInfo":{"dty":"Regular","adadr":[],"cxdt":"","pradr":{"ntr":"Retail Business, Wholesale Business","addr":{"bnm":"","bno":"644\/1\/3","dst":"Kolhapur","loc":"KOLHAPUR","pncd":"416001","st":"SHAHUPURI","flno":"2ND LANE","lt":"","stcd":"Maharashtra","city":"","lg":""}},"sts":"Active","gstin":"27GXCPK4908J1Z6","lgnm":"RUTUJA DADASO KADVEKAR","stjCd":"MHCG0029","stj":"LAXMIPURI_701","ctjCd":"UE0301","ctb":"Proprietorship","ctj":"RANGE-I","errorMsg":null,"frequencyType":null,"tradeNam":"D AND C CARE GENERAL","nba":["Retail Business","Wholesale Business"],"rgdt":"19\/10\/2020","panNo":"GXCPK4908J"}}';
            }
            
            public function bajaj_finance_integration() {
//        $payment_mode = $this->input->post('payment_mode');
                $sfid = $this->input->post('sfid');
                $bfl_store_id = $this->input->post('bfl_store_id');
                $bfl_data = $this->Sale_model->bfl_integration($sfid, $bfl_store_id);
//        die('<pre>'.print_r($bfl_data,1).'</pre>');
                echo json_encode($bfl_data);
            }
            
            public function sale_report(){
                $q['tab_active'] = 'Sale';
                
                if($this->session->userdata('level') == 1){
                    $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['zone_data'] = $this->General_model->get_active_zone();
        $this->load->view('sale/sale_report', $q);  
    }
    
    public function sale_revenue_report(){
        $q['tab_active'] = 'Sale';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['zone_data'] = $this->General_model->get_active_zone();
        $this->load->view('sale/sale_revenue_report', $q);  
    }
    
    public function ajax_get_sale_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idzone = $this->input->post('idzone');
        
        $idsaletype = $this->input->post('idsaletype');
        if($idsaletype == 'all'){
            $idsaletype = array(0,1);
        }
        
        $sale_data = $this->Sale_model->ajax_get_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone);
        $user_dara = $this->Sale_model->ajax_get_cluster_data($idbranch);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
                <thead class="fixedelementtop">
                    <th>Entry Time</th>
                    <th>Invoice No</th>
                    <th>Bill Type</th>
                    <th>Sale Type</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Partner type</th>
                    <th>Branch Category</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>GSTIN</th>
                    <th>Imei</th>
                    <th>Product Category</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>MOP</th>
                    <th>MRP</th>
                    <th>Amount</th>
                    <th>Sale Promotor Brand</th>
                    <th>Sale Promotor</th>
                    <th>Cluster Head</th>
                    <th>Token ID</th>
                    <th>Info</th>
                    <th>Print</th>
                    <th>Category</th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; foreach ($sale_data as $sale) { 
                        $userdata = $this->Sale_model->ajax_get_brand_name_byiduser($sale->id_users);?>
                        <tr>
                            <td><?php echo date('d-m-Y h:i a', strtotime($sale->entry_time)) ?></td>
                            <!--<td><?php // echo $sale->date ?></td>-->
                            <td><?php echo $sale->inv_no ?></td>
                            <td><?php if($sale->idsaletoken){ echo 'Token billing'; }elseif($sale->idsaletoken == '0'){ echo 'Direct Promoter Billing'; }else{ echo 'Normal Billing'; } ?></td>
                            <td><?php if($sale->corporate_sale == 1){ echo 'Online Sale'; }else { echo 'Offline Sale';} ?></td>
                            <td><?php echo $sale->branch_name ?></td>
                            <td><?php echo $sale->zone_name ?></td>
                            <td><?php echo $sale->partner_type ?></td>
                            <td><?php echo $sale->branch_category_name ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->customer_gst ?></td>
                            <td>'<?php echo $sale->imei_no ?></td>
                            <td><?php echo $sale->product_category_name ?></td>
                            <td><?php echo $sale->brand_name ?></td>
                            <td><?php echo $sale->full_name ?></td>
                            <td><?php echo 'Output CGST '. $sale->cgst_per.'%' ?></td>
                            <td><?php echo 'Output SGST '. $sale->sgst_per.'%' ?></td>
                            <td><?php echo 'Output IGST '. $sale->igst_per.'%' ?></td>
                            <td><?php echo $sale->mop ?></td>
                            <td><?php echo $sale->mrp ?></td>
                            <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; ?></td>
                            <td><?php if($userdata){echo $userdata->user_brand_name; }?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><?php foreach($user_dara as $clust){if($sale->idbranch == $clust->clust_branch){ echo $clust->clust_name.',' ; }}?></td>
                            <td><?php echo $sale->id_sale_token; ?></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                            <td><?php echo $sale->category_name ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <?php 
        }else{ ?>
         <script>
             $(document).ready(function (){
              alert("Data Not Found"); 
          });
      </script> 
  <?php }

}

public function ajax_get_sale_revenue_report(){
//        die(print_r($_POST));
    $from = $this->input->post('from');
    $to = $this->input->post('to');
    $idbranch = $this->input->post('idbranch');
    $idpcat = $this->input->post('idpcat');
    $idbrand = $this->input->post('idbrand');
    $idsaletype = $this->input->post('idsaletype');
    $idzone = $this->input->post('idzone');
    if($idsaletype == 'all'){
        $idsaletype = array(0,1);
    }
    
    $sale_data = $this->Sale_model->ajax_get_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone);
    $sale_return_data = $this->Sale_model->ajax_get_sale_return_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone);
    $user_dara = $this->Sale_model->ajax_get_cluster_data($idbranch);
//        die('<pre>'.print_r($sale_return_data,1).'</pre>');
    $price_cat = $this->Report_model->get_price_category_lab_data();
    
    if(count($sale_data) >0){
        ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelement">
                <th>Date</th>
                <th>Time</th>
                <th>Invoice No</th>
                <th>Branch</th>
                <th>Zone</th>
                <th>Partner Type</th>
                <th>Branch Category</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Sale Promotor Brand</th>
                <th>Sale Promotor</th>
                <th>Cluster Head</th>
                <th>Imei</th>
                <th>Product Category</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>MOP</th>
                <th>MRP</th>
                <th>Sale Amount</th>
                <th>Landing Amount</th>
                <th>Revenue Amount</th>
                <th>Revenue Percentage</th>
                <th>Price Catgory</th>
                <th>Sale Type</th>
                <th>Info</th>
                <th>Category</th>
                <!--<th>Print</th>-->
            </thead>
            <tbody class="data_1">
                <?php $trevenue = 0; $landing=0; $total=0; $sper=0; $tsper=0; $reper = 0; $treper = 0; foreach ($sale_data as $sale) { 
                    foreach($price_cat as $slab){
                        if($sale->total_amount >= $slab->min_lab && $sale->total_amount <= $slab->max_lab){
                            $price_slab = $slab->lab_name;
                        }
                    }
                    $userdata = $this->Sale_model->ajax_get_brand_name_byiduser($sale->id_users);?>
                    <tr>
                        <td><?php echo $sale->date ?></td>
                        <td><?php echo date('H:i:sa', strtotime($sale->entry_time)); ?></td>
                        <td><?php echo $sale->inv_no ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php echo $sale->zone_name ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                        <td><?php echo $sale->customer_contact ?></td>
                        <td><?php echo $sale->customer_gst ?></td>
                        <td><?php if($userdata){ echo $userdata->user_brand_name; }?></td>
                        <td><?php echo $sale->user_name ?></td>
                        <td><?php foreach($user_dara as $clust){if($sale->idbranch == $clust->clust_branch){ echo $clust->clust_name.','; }}?></td>
                        <td><?php echo $sale->imei_no ?></td>
                        <td><?php echo $sale->product_category_name ?></td>
                        <td><?php echo $sale->brand_name ?></td>
                        <td><?php echo $sale->full_name ?></td>
                        <td><?php echo $sale->mop; ?></td>
                        <td><?php echo $sale->mrp;?></td>
                        <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; ?></td>
                        <td><?php echo $sale->landing; $landing = $landing + $sale->landing; ?></td>
                        <td><?php $revenue = $sale->total_amount - $sale->landing; echo $revenue; $trevenue = $trevenue + $revenue;?></td>
                        <td><?php if($sale->landing != 0){ $sper =  round((($revenue * 100)/$sale->total_amount),2); }else{ $sper = 0;} echo $sper; $tsper = $tsper + $sper; ?></td>
                        <td><?php echo $price_slab; ?></td>
                        <td><?php if($sale->corporate_sale == 1){ echo 'Online Sale'; }else { echo 'Offline Sale';} ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                        <!--<td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                        <td><?php echo $sale->category_name ?></td>
                    </tr>
                <?php } ?>
                <tr>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo $total; ?></b></td>
                    <td><b><?php echo $landing; ?></b></td>
                    <td><b><?php echo $trevenue; ?></b></td>
                    <td><b><?php  if($landing != 0){ echo round(($trevenue *100 /$total),2);}else{ echo 0;} ; ?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="27" style="background-color: #9999ff;color: #FFFFFF"> <b> Sales Return </b> </td>
                    
                </tr>
                <?php $strevenue = 0; $tlanding=0; $stotal=0; foreach ($sale_return_data as $sale_return) { 
                 foreach($price_cat as $slab){
                    if($sale_return->total_amount >= $slab->min_lab && $sale_return->total_amount <= $slab->max_lab){
                        $sprice_slab = $slab->lab_name;
                    }
                }
                ?>
                <tr>
                    <td><?php echo $sale_return->date ?></td>
                    <td><?php echo date('H:i:sa', strtotime($sale_return->entry_time)); ?></td>
                    <td><?php echo $sale_return->sales_return_invid ?></td>                    
                    <td><?php echo $sale_return->branch_name ?></td>
                    <td><?php echo $sale_return->zone_name ?></td>
                    <td><?php echo $sale_return->partner_type ?></td>
                    <td><?php echo $sale->branch_category_name ?></td>
                    <td><?php echo $sale_return->customer_fname.' '.$sale_return->customer_lname ?></td>
                    <td><?php echo $sale_return->customer_contact ?></td>
                    <td><?php echo $sale_return->customer_gst ?></td>
                    <td><?php //echo $sale_return->customer_gst ?></td>
                    <td><?php echo $sale_return->user_name ?></td>
                    <td><?php foreach($user_dara as $clust){if($sale_return->idbranch == $clust->clust_branch){ echo $clust->clust_name.', '; }}?></td>
                    <td><?php echo $sale_return->imei_no ?></td>
                    <td><?php echo $sale_return->product_category_name ?></td>
                    <td><?php echo $sale_return->brand_name ?></td>
                    <td><?php echo $sale_return->full_name ?></td>
                    <?php if($sale_return->sales_return_type==3){ ?>
                     <td><?php echo $sale_return->total_amount; ?></td>
                     <td><?php echo $sale_return->total_amount;?></td>
                     <td><?php echo $sale_return->total_amount; $stotal = $stotal + $sale_return->total_amount; ?></td>
                     <td>   
                        <?php  $sale_landing=0; //$sale_landing=$sale_return->total_amount; $tlanding = $tlanding + $sale_return->total_amount;                                     
                        if($sale_return->landing==null || $sale_return->landing==1){ 
                            echo $sale_return->total_amount; $sale_landing=$sale_return->total_amount; $tlanding = $tlanding + $sale_return->total_amount;                                                                          
                        }else{ 
                            echo $sale_return->landing; $sale_landing=$sale_return->landing; $tlanding = $tlanding + $sale_return->landing;                                     
                        } 
                        ?>
                    </td>
                    <td><?php  $revenue = $sale_return->total_amount - $sale_landing; echo '-'.$revenue; $strevenue = $strevenue + $revenue;?></td>
                    <td><?php if($sale_landing != 0){ $sper =  round(($revenue * 100 /$sale_landing),2); }else{ $sper = 0;} echo '-'.$sper; $tsper = $tsper + $sper; ?></td> 
                <?php }else{ ?>
                 <td><?php echo $sale_return->mop; ?></td>
                 <td><?php echo $sale_return->mrp;?></td>
                 <td><?php echo $sale_return->total_amount; $stotal = $stotal + $sale_return->total_amount; ?></td>
                 <td><?php  $sale_landing=0; if($sale_return->landing!=null){ echo $sale_return->landing; $sale_landing=$sale_return->landing; $tlanding = $tlanding + $sale_return->landing; }else{ echo $sale_return->old_landing; $sale_landing=$sale_return->old_landing; $tlanding = $tlanding + $sale_return->old_landing;  } ?></td>
                 <td><?php  $revenue = $sale_return->total_amount - $sale_landing; echo '-'.$revenue; $strevenue = $strevenue + $revenue;?></td>
                 <td><?php if($sale_landing != 0){ $sper =  round(($revenue * 100 /$sale_landing),2); }else{ $sper = 0;} echo '-'.$sper; $tsper = $tsper + $sper; ?></td> 
             <?php } ?>
             <td><?php echo $sprice_slab; ?></td>
             <td><?php if($sale_return->corporate_sale == 1){ echo 'Online Sale'; }else { echo 'Offline Sale';} ?></td>
             <td><a target="_blank" href="<?php echo base_url('Sales_return/sales_return_details/'.$sale_return->id_salesreturn) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
             <!--<td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
             <td><?php echo $sale->category_name ?></td>
         </tr>
     <?php } ?>
     <tr>
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
        <td></td>
        <td></td>
        <td></td>
        <td></td>                    
        <td></td>
        <td></td>
        <td></td>
        <td><b>Total</b></td>
        <td><b><?php echo $stotal; ?></b></td>
        <td><b><?php echo $tlanding; ?></b></td>
        <td><b><?php echo '-'.$strevenue; ?></b></td>
        <td><b><?php if($tlanding !=0){ echo '-'.round(($strevenue * 100 /$stotal),2);}else{ echo 0;} ?></b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
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
        <td></td>
        <td></td>
        <td></td>
        <td></td>                          
        <td></td>
        <td></td>
        <td></td>
        <td><b>Todays Sale</b></td>
        <td><b><?php echo $total-$stotal; ?></b></td>
        <td><b><?php echo $landing-$tlanding; ?></b></td>
        <td><b><?php echo $trevenue-$strevenue; ?></b></td>
        <td><b><?php if(($landing-$tlanding ) != 0){ echo round((($trevenue-$strevenue)*100)/( $landing-$tlanding),2); } else { echo 0; } ?></b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</tbody>
</table>
<?php 
}else{ ?>
 <script>
     $(document).ready(function (){
      alert("Data Not Found"); 
  });
</script> 
<?php }
}

public function ajax_search_invoice_for_edit_branch() {
    $invno = $this->input->post('invno');
    $level = $this->input->post('level');
    $sale_data = $this->Sale_model->get_config_sale_byinvno_for_edit($invno);
    if(count($sale_data) > 0){
        foreach ($sale_data as $sale){
            if($sale_data[0]->idbranch != $this->session->userdata('idbranch')){
                echo '<center><h3><i class="mdi mdi-alert"></i> Your invoice is not created at your branch. </h3>' .
                '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                . '</center>';
            }else{
                $idsale = $sale->id_sale;
                $sale_product = $this->Sale_model->get_sale_product_byid($idsale);
                $sale_payment = $this->Sale_model->get_sale_payment_byid($idsale);
                $payment_head_has_attributes = $this->General_model->get_payment_head_has_attributes();
                $state_data = $this->General_model->get_state_data(); ?>
                <div style="font-family: K2D; font-size: 15px;">
                    <div class="panel panel-info">
                        <?php if($sale->corporate_sale == 1){ ?></center>
                        <div class="panel-heading">
                            <center><?php echo "Corporate Sale";?></center>
                        </div>
                    <?php } ?>
                    <div class="panel-body" style="min-height: 600px">
                        <div class="col-md-7">
                            Customer Details,<br>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">Contact</span>
                                <div class="col-md-10"><div id="spcust_contact"><?php echo $sale->customer_contact ?></div></div>
                            </div><div class="clearfix"></div>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">Customer</span>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <input type="text" class="form-control input-sm" id="customer_fname" placeholder="Customer First Name" value="<?php echo $sale->customer_fname ?>">
                                            <input type="hidden" id="idcustomer" value="<?php echo $sale->idcustomer ?>">
                                            <input type="hidden" id="idsale" value="<?php echo $sale->id_sale ?>">
                                        </div>
                                        <div class="input-group-btn">
                                            <input type="text" class="form-control input-sm" id="customer_lname" placeholder="Customer First Name" value="<?php echo $sale->customer_lname ?>">
                                        </div>
                                    </div>
                                </div>
                            </div><div class="clearfix"></div>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">Address</span>
                                <div class="col-md-10">
                                    <input type="text" class="form-control input-sm" id="customer_address" value="<?php echo $sale->customer_address ?>" />
                                </div>
                            </div><div class="clearfix"></div>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">Pincode</span>
                                <div class="col-md-10">
                                    <input type="text" class="form-control input-sm" id="pincode" value="<?php echo $sale->customer_pincode ?>" />
                                </div>
                            </div><div class="clearfix"></div>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">State</span>
                                <div class="col-md-10">
                                    <input type="hidden" id="config_edit" value="0">
                                    <input type="hidden" id="customer_idstate" value="<?php echo $sale->customer_idstate ?>">
                                    <input type="hidden" id="branch_idstate" value="<?php echo $sale->branch_idstate ?>">
                                    <input type="hidden" id="gst_type" value="<?php echo $sale->gst_type ?>">
                                    <select name="idstate" id="idstate" style="width: 100%" class="form-control input-sm">
                                        <option value="">Select State</option>
                                        <option value="<?php echo $sale->customer_idstate; ?>" selected="" ><?php echo $sale->customer_state; ?></option>
                                        <?php foreach ($state_data as $state) { ?>
                                            <option value="<?php echo $state->id_state; ?>" ><?php echo $state->state_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div><div class="clearfix"></div>
                            <div class="p-1">
                                <span class="col-md-2 text-muted">GSTIN</span>
                                <div class="col-md-10">
                                    <input type="text" class="form-control input-sm" id="customer_gst" value="<?php echo $sale->customer_gst ?>" placeholder="Enter GSTIN" />
                                </div><div class="clearfix"></div>
                                <div class="col-md-12 p-2">
                                    <?php if($sale->customer_gst == NULL){ ?>
                                        <div class="pull-right">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-outline" id="customer_edit_btn"><i class="mdi mdi-account-edit"></i> Edit Customer</a>
                                        </div>
                                    <?php }else{ ?>
                                        <marquee style="color: #ff0033">GSTIN already present, You can not edit customer details</marquee><br>
                                    <?php } ?>
                                </div>
                            </div><div class="clearfix"></div>
                        </div>
                        <div class="col-md-5"><br>
                            <div class="">
                                <span class="text-muted col-md-3">Sale Id:</span>
                                <div class="col-md-9"><?php echo $sale->id_sale ?></div>
                            </div><div class="clearfix"></div>
                            <div class="">
                                <span class="text-muted col-md-3">Invoice Date</span>
                                <div class="col-md-9"><?php echo date('d/m/Y', strtotime($sale->date)); ?></div>
                            </div><div class="clearfix"></div>
                            <div class="">
                                <div class="text-muted col-md-3">Invoice No</div>
                                <div class="col-md-9"><?php echo $sale->inv_no ?></div>
                            </div><div class="clearfix"></div>
                            <div class="">
                                <span class="text-muted col-md-3">Entry time</span>
                                <div class="col-md-9"><?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?></div>
                            </div><div class="clearfix"></div>
                            <div class="">
                                <span class="text-muted col-md-3">Remark</span>
                                <div class="col-md-9"><?php echo $sale->remark ?></div>
                            </div><div class="clearfix"></div>
                        </div><div class="clearfix"></div>
                        <?php // if($sale->date == date('Y-m-d')){ 
//                            echo '<center><h3><i class="mdi mdi-alert"></i> Not allowed to edit invoice after sale date...</h3>'.
//                                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
//                                . '</center>'; 
//                            }else{ ?>
    <div class="thumbnail" style="overflow: auto; padding: 2px;">
        <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
            <thead class="bg-info">
                <th class="col-md-4">Product</th>
                <th>IMEI/SRNO</th>
                <th>HSN</th>
                <th>Qty</th>
                <th>MOP</th>
                <th>Rate</th>
                <th>Basic</th>
                <th>Discount</th>
                <th>Amount</th>
                <th>Return</th>
            </thead>
            <tbody>
                <?php foreach ($sale_product as $product) { ?>
                    <?php // if($product->sales_return_type > 0 || $product->idsale_product_for_doa != NULL){ ?>
                        <tr class="product_row">
                            <td><?php echo $product->product_name; if($product->idsale_product_for_doa != NULL){ echo ' [DOA Replace]'; } ?></td>
                            <td><?php echo $product->imei_no ?></td>
                            <td><?php echo $product->hsn ?></td>
                            <td><?php echo $product->qty ?></td>
                            <td><?php echo $product->mop ?></td>
                            <td><?php echo $product->price ?></td>
                            <td><?php echo $product->basic ?></td>
                            <td><?php echo $product->discount_amt ?></td>
                            <td><?php echo $product->total_amount ?></td>
                            <td><?php if($product->sales_return_type==2){ echo 'Replace,Upgrade Return'; }elseif($product->sales_return_type==3){ echo 'DOA Return'; }else{ echo '-'; } ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="bg-info">
                        <td colspan="5"></td>
                        <td>Total</td>
                        <td>
                            <div id="spbasic_total"><?php echo $sale->basic_total ?></div>
                        </td>
                        <td>
                            <div id="spdiscount_total"><?php echo $sale->discount_total ?></div>
                        </td>
                        <td>
                            <div id="spfinal_total"><?php echo $sale->final_total ?></div>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Details</span>
        <div class="thumbnail" style="overflow: auto; padding: 2px;">
            <table class="table table-condensed table-bordered table-hover" style="font-size: 14px; margin-bottom: 0">
                <tbody>
                    <?php $sum = 0; foreach ($sale_payment as $payment){ ?>
                        <tr>
                            <td><span class="text-muted">Mode of Payment</span></td>
                            <td><?php echo $payment->payment_mode.' <small>'.$payment->payment_head.'</small>' ?></td>
                            <td><?php echo $payment->amount ?></td>
                            <?php if($payment->bounce_charges != 0){ ?>
                                <td><span class="text-muted">Bounce Charges</span></td>
                                <td><?php echo $payment->bounce_charges;?></td>
                            <?php } ?>
                            <?php if($payment->tranxid_type != NULL){ ?>
                                <td><span class="text-muted"><?php echo $payment->tranxid_type ?></span></td>
                                <td><?php echo $payment->transaction_id ?></td>
                            <?php } foreach ($payment_head_has_attributes as $has_attributes){
                                if($has_attributes->idpayment_head == $payment->idpayment_head){ ?>
                                    <td><span class="text-muted"><?php if($payment->idpayment_mode == 17){ echo 'Bank UTR'; }else{ echo $has_attributes->attribute_name; }?></span></td>
                                    <td><?php $clm = $has_attributes->column_name; echo $payment->$clm; ?></td>
                                <?php }} ?>
                            </tr>
                            <?php $sum += $payment->amount; } ?>
                            <tr style="font-weight: bold">
                                <td></td>
                                <td>Total</td>
                                <td><?php echo $sum ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php // } ?>
            </div>
        </div>
    </div>
<?php }}} else{
    echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice Number</h3>'.
    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
    . '</center>'; 
}
}

public function edit_sale_customer() {
//        die(print_r($_POST));
    $this->db->trans_begin();
    $idcustomer = $this->input->post('idcustomer');
    $customer_fname = $this->input->post('customer_fname');
    $customer_lname = $this->input->post('customer_lname');
    $customer_address = $this->input->post('customer_address');
    $customer_gst = $this->input->post('customer_gst');
    $idsale = $this->input->post('idsale');
    $pincode = $this->input->post('pincode');
    $old_gst_type = $this->input->post('gst_type');
//        $idstate = $this->input->post('idstate');
//        $customer_idstate = $this->input->post('customer_idstate');
    $branch_idstate = $this->input->post('branch_idstate');
    $idstate = $this->input->post('idstate');
        $gst_type = 0; //cgst
        if($idstate != $branch_idstate){
            $gst_type = 1; //igst
        }
//        if($this->input->post('config_edit') == 0){
//            $this->General_model->update_customer_edit_count($idcustomer);
//        }
        
        $edit_customer = array(
            'customer_fname' => $customer_fname,
            'customer_lname' => $customer_lname,
            'customer_address' => $customer_address,
            'customer_gst' => $customer_gst,
            'customer_pincode' => $pincode,
            'idstate' => $idstate,
            'customer_state' => $this->input->post('state_name'),
        );
        $this->General_model->edit_customer_byid($idcustomer, $edit_customer);
        
        $sale_customer = array(
            'customer_fname' => $customer_fname,
            'customer_lname' => $customer_lname,
            'customer_address' => $customer_address,
            'customer_gst' => $customer_gst,
            'gst_type' => $gst_type,
            'customer_idstate' => $idstate,
            'customer_pincode' => $pincode,
        );
        $this->Sale_model->update_sale_customer($idsale, $sale_customer);
        
//        if($old_gst_type != $gst_type){
//            $this->Sale_model->update_sale_product_byidsale_customer($idsale, $gst_type);
//        }
        $customer_history = array(
            'customer_fname' => $customer_fname,
            'customer_lname' => $customer_lname,
            'customer_address' => $customer_address,
            'idcustomer' => $idcustomer,
            'customer_gst' => $customer_gst,
            'customer_pincode' => $pincode,
            'customer_idstate' => $idstate,
//s            'idbranch' => $this->session->userdata('idbranch'),
            'edited_by' => $this->session->userdata('id_users'),
            'entry_time' => date('Y-m-d H:i:s'),
        );
        $this->General_model->save_customer_edit_history($customer_history);
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'success';
            $q['gst_type'] = $gst_type;
        }
        echo json_encode($q);
    }
    
    public function change_sale_customer() {
        $this->db->trans_begin();
        $idsale = $this->input->post('idsale');
        $sale_customer = array(
            'idcustomer' => $this->input->post('idcustomer'),
            'customer_contact' => $this->input->post('customer_contact'),
            'customer_fname' => $this->input->post('customer_fname'),
            'customer_lname' => $this->input->post('customer_lname'),
            'customer_address' => $this->input->post('customer_address'),
            'customer_gst' => $this->input->post('customer_gst'),
            'customer_pincode' => $this->input->post('cust_pincode'),
            'customer_idstate' => $this->input->post('cust_idstate'),
        );
        $this->Sale_model->update_sale_customer($idsale, $sale_customer);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'success';
        }
        echo json_encode($q);
    }
    
    public function ajax_get_payment_mode_attributes_byidhead() {
        $head = $this->input->post('paymenthead');
        $headname = $this->input->post('headname');
        $modename = $this->input->post('modename');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $credittype = $this->input->post('credit_type');
        $payment_head = $this->General_model->get_payment_head_byid($head); 
//        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
//        $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); 
        $head_has_attribute = $this->General_model->get_payment_head_has_attributes(); 
        $all_payment_attribute = $this->General_model->get_payment_attribute_data(); 
        ?>
        <div id="modes_block<?php echo $head ?>" class="modes_block modes_blockc<?php echo $head ?> hovereffect" style="margin-bottom: 7px; padding: 0px;">
            <div class="col-md-2 col-sm-3" style="padding: 7px 5px; background-image: linear-gradient(to right top, #24cfad, #0ed19f, #0dd28f, #21d37c, #37d366);">
                <center><span style="font-size: 17px; font-family: Kurale; color: #fff"><?php echo $modename.'<br><small>'.$headname.'</small>' ?></span></center>
                <!--<input type="hidden" class="idpayment_head" name="idpayment_head[]" value="<?php echo $head ?>" />-->
            </div>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                Amount
                <input type="hidden" class="idpayment_mode" name="idpaymentmode[]" value="<?php echo $idpayment_mode ?>" />
                <input type="hidden" class="credittype" name="credittype[]" value="<?php echo $credittype ?>" />
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" placeholder="Amount" value="0" min="1" required="" />
                <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="<?php echo $head ?>" />
                <input type="hidden" class="headname" name="headname[]" value="<?php echo $headname ?>" />
                <input type="hidden" class="credit_type" name="credit_type[]" value="<?php echo $payment_head->credit_type ?>" />
            </div>
            <?php if($payment_head->tranxid_type == NULL){ ?>
                <div class="col-md-2 col-sm-3 hidden">
                    <?php echo $payment_head->tranxid_type ?>
                    <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" value="<?php echo NULL; ?>" />
                </div>
            <?php }else{ ?>
                <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                    <?php echo $payment_head->tranxid_type ?>
                    <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $payment_head->tranxid_type ?>" required="" />
                </div>
            <?php } ?>
            <?php // foreach ($payment_attribute as $attribute){ ?>
<!--            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <?php // echo $attribute->attribute_name ?>
                <input type="text" class="form-control input-sm headattr" name="new_<?php echo $attribute->column_name ?>[]" placeholder="<?php echo $attribute->attribute_name ?>" required="" />
            </div>-->
            <?php // } ?>
            <?php foreach ($all_payment_attribute as $all_attribute){
                foreach ($head_has_attribute as $head_has){ if($head == $head_has->idpayment_head){ ?>
                    <?php if($head_has->idpayment_attribute != $all_attribute->id_payment_attribute){ ?>
                        <input type="hidden" class="form-control input-sm headattr" name="new_<?php echo $all_attribute->column_name ?>[]" />
                    <?php }else{ ?>
                        <div class="col-md-2 col-sm-3 <?php if($head_has->idpayment_attribute != $all_attribute->id_payment_attribute){ ?> hidden <?php } ?>" style="padding: 2px 5px">
                            <?php echo $all_attribute->attribute_name ?>
                            <input type="text" class="form-control input-sm headattr" name="new_<?php echo $all_attribute->column_name ?>[]" placeholder="<?php echo $all_attribute->attribute_name ?>" required="" />
                        </div>
                    <?php }}}} ?>
                    <div class="col-md-1 col-sm-3 pull-right" style="padding: 12px">
                        <center><a class="btn btn-danger btn-floating waves-effect remove_payment" id="remove_payment" style=""><i class="fa fa-minus"></i></a></center>
                    </div>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>
                <?php
            }
            
                    
            public function ajax_search_invoice_for_edit_config() {
                $invno = $this->input->post('invno');
                $level = $this->input->post('level');
                $sale_data= $this->common_model->getSingleRow('sale',array('inv_no'=>$invno));
                $einv_data= $this->common_model->getSingleRow('eway_einvoice_data',array('idoutword_no'=>$sale_data['id_sale']));
                if(empty($einv_data)){
                    if($level != 1 && $level != 3){
                        echo '<center><h3><i class="mdi mdi-alert"></i> You do not have authority to edit invoice. </h3>'.
                        '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                        . '</center>'; 
                    }else{
                        $sale_data = $this->Sale_model->get_config_sale_byinvno_for_edit($invno);
                        if(count($sale_data) > 0){
                            $mnt = date('Y-m-03', strtotime($sale_data[0]->date. "+1 month"));
                            $gstdate = date('Y-m-d', strtotime($sale_data[0]->date. "+2 days"));
                            $cdate = date('Y-m-d');
//                if(date('Y-m-d') < $mnt){  
                            foreach ($sale_data as $sale){
                                $idsale = $sale->id_sale;
                                $sale_product = $this->Sale_model->get_sale_product_byid($idsale);
                                $sale_payment = $this->Sale_model->get_sale_payment_byid_invoice_edit($idsale);
                                $sale_reconciliation = $this->Sale_model->get_sale_reconciliation_byid($idsale);
                                $payment_head_has_attributes = $this->General_model->get_payment_head_has_attributes();
                                $payment_mode = $this->General_model->get_active_payment_mode_head();
                                $state_data = $this->General_model->get_state_data(); ?>
                                <div style="font-family: K2D; font-size: 15px;">
                                    <div class="">
                                        <div class="thumbnail">
                                            <div class="col-md-7">
                                                To,<br>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">Contact</span>
                                                    <div class="col-md-10"><div id="spcust_contact"><?php echo $sale->customer_contact ?></div></div>
                                                </div><div class="clearfix"></div>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">Customer</span>
                                                    <div class="col-md-10">
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <input type="text" class="form-control input-sm" id="customer_fname" placeholder="Customer First Name" value="<?php echo $sale->customer_fname ?>">
                                                                <input type="hidden" id="idcustomer" value="<?php echo $sale->idcustomer ?>">
                                                                <input type="hidden" id="idsale" value="<?php echo $sale->id_sale ?>">
                                                            </div>
                                                            <div class="input-group-btn">
                                                                <input type="text" class="form-control input-sm" id="customer_lname" placeholder="Customer First Name" value="<?php echo $sale->customer_lname ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><div class="clearfix"></div>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">Address</span>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control input-sm" id="customer_address" value="<?php echo $sale->customer_address ?>" />
                                                    </div>
                                                </div><div class="clearfix"></div>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">Pincode</span>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control input-sm" id="pincode" value="<?php echo $sale->customer_pincode ?>" />
                                                    </div>
                                                </div><div class="clearfix"></div>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">State</span>
                                                    <div class="col-md-10">
                                                        <input type="hidden" id="customer_idstate" value="<?php echo $sale->customer_idstate ?>">
                                                        <input type="hidden" id="config_edit" value="1">
                                                        <input type="hidden" id="branch_idstate" value="<?php echo $sale->branch_idstate ?>">
                                                        <input type="hidden" id="gst_type" value="<?php echo $sale->gst_type ?>">
                                                        <select name="idstate" id="idstate" style="width: 100%" class="form-control input-sm">
                                                            <option value="">Select State</option>
                                                            <option value="<?php echo $sale->customer_idstate; ?>" selected="" ><?php echo $sale->customer_state; ?></option>
                                                            <?php foreach ($state_data as $state) { ?>
                                                                <option value="<?php echo $state->id_state; ?>" ><?php echo $state->state_name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div><div class="clearfix"></div>
                                                <div class="p-1">
                                                    <span class="col-md-2 text-muted">GSTIN</span>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control input-sm" id="customer_gst" value="<?php echo $sale->customer_gst ?>" placeholder="Enter GSTIN" />
                                                        <input type="hidden" class="form-control input-sm" id="oldgst" value="<?php echo $sale->customer_gst ?>"  />
                                                        <input type="hidden" class="form-control input-sm" id="gstdate" value="<?php echo $gstdate ?>"  />
                                                        <input type="hidden" class="form-control input-sm" id="cdate" value="<?php echo $cdate ?>"  />
                                                    </div><div class="clearfix"></div>
                                                    <div class="col-md-12 p-2">
                                                        <div class="pull-right">
                                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-outline" id="customer_edit_btn" ><i class="mdi mdi-account-edit"></i> Edit Customer</a>
                                                            <a href="javascript:void(0)" class="btn btn-sm btn-warning btn-outline" data-toggle="modal" data-target="#customer_selection_form"><i class="mdi mdi-account-plus"></i> Change Customer</a>
                                                        </div>
                                                    </div>
                                                </div><div class="clearfix"></div>
                                                <?php if($level != 1){?>
                                                    <script>
                                                        $(document).ready(function (){
                                                         $('#customer_gst').change(function (){
                                                          var cdate =  $('#cdate').val();
                                                          var gstdate =  $('#gstdate').val();
                                                          var oldgst =  $('#oldgst').val();
                                                          if(cdate > gstdate){
                                                              alert("Not Allowed to Edit GSTIN after " +gstdate);
                                                              $('#customer_gst').val(oldgst);
                                                              return false;
                                                          }
                                                      }); 
                                                     });
                                                 </script>
                                             <?php } ?>
                                         </div>
                                         <div class="col-md-5"><br>
                                            <div class="">
                                                <span class="text-muted col-md-3">Sale Id:</span>
                                                <div class="col-md-9"><?php echo $sale->id_sale ?></div>
                                            </div><div class="clearfix"></div>
                                            <div class="">
                                                <span class="text-muted col-md-3">Invoice Date</span>
                                                <div class="col-md-9"><?php echo date('d/m/Y', strtotime($sale->date)); ?></div>
                                            </div><div class="clearfix"></div>
                                            <div class="">
                                                <div class="text-muted col-md-3">Invoice No</div>
                                                <div class="col-md-9"><?php echo $sale->inv_no ?></div>
                                            </div><div class="clearfix"></div>
                                            <div class="">
                                                <span class="text-muted col-md-3">Entry time</span>
                                                <div class="col-md-9"><?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?></div>
                                            </div><div class="clearfix"></div>
                                            <div class="">
                                                <span class="text-muted col-md-3">Remark</span>
                                                <div class="col-md-9"><?php echo $sale->remark ?></div>
                                            </div><div class="clearfix"></div>
                                        </div><div class="clearfix"></div>
                                        <div class="modal fade" id="customer_selection_form" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <a href="" class="popup-close" data-dismiss="modal" aria-label="Close">x</a>
                                                        <h4 class="modal-title text-center"><span class="mdi mdi-account-switch" style="font-size: 28px"></span> Select Customer
                                                            <a href="<?php echo base_url('Sale/customer_list') ?>" target="_blank" class="btn btn-warning btn-floating waves-effect pull-right" style="line-height: 10px"><i class="mdi mdi-table fa-lg"></i></a>
                                                        </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="col-md-2">Mobile</div>
                                                        <div class="col-md-7">
                                                            <input list="text" maxlength="10" class="form-control input-sm" name="cust_mobile" id="cust_mobile" required="" placeholder="Customer Mobile No" pattern="[6789][0-9]{9}" />
                                                        </div>
                                                        <div class="col-md-3">
                                                            <a class="btn btn-sm btn-danger btn-outline" id="verify_get_customer"><i class="mdi mdi-account-plus"></i> Get Customer</a>
                                                        </div>
                                                        <div class="clearfix"></div><br>
                                                        <center id="empty_block"><h3 style="font-family: Kurale"><i class="mdi mdi-chevron-double-up"></i><br>Enter Mobile Number & click on Get Customer Button</h3><br></center>
                                                        <div id="customer_block" style="display: none">
                                                            <center id="empty_block"><h4 style="font-family: Kurale"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Selected Customer Details</h4></center>
                                                            <ul class="list-group">
                                                                <li class="list-group-item">
                                                                    <div class="col-md-3">Contact</div>
                                                                    <div class="col-md-9">
                                                                        <span id="spcustomer_contact"></span>
                                                                    </div><div class="clearfix"></div>
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <div class="col-md-3">Customer Name</div>
                                                                    <div class="col-md-9">
                                                                        <span id="spcust_fname"></span>
                                                                        <span id="spcust_lname"></span>
                                                                    </div><div class="clearfix"></div>
                                                                </li>
                                                                <li class="list-group-item">
                                                                   <div class="col-md-3">GST No</div>
                                                                   <div class="col-md-9">
                                                                    <div id="spgst_no"></div>
                                                                </div><div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="col-md-3">Pincode</div>
                                                                <div class="col-md-9">
                                                                    <div id="spcust_pincode"></div>
                                                                </div><div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="col-md-3">State</div>
                                                                <div class="col-md-9">
                                                                    <div id="spcustomer_state"></div>
                                                                </div><div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="col-md-3">Address</div>
                                                                <div class="col-md-9">
                                                                    <div id="spaddress"></div>
                                                                </div><div class="clearfix"></div>
                                                            </li>
                                                        </ul> 
                                                        <div class="col-md-12 p-2">
                                                            <div class="pull-right">
                                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-outline" data-dismiss="modal" >Cancel</a>
                                                                <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-outline" id="add_selected_customer" ><i class="mdi mdi-account-edit"></i> Add Selected Customer</a>
                                                            </div>
                                                        </div><div class="clearfix"></div>
                                                    </div>
                                                    <input type="hidden" id="nidcustomer" value=""/>
                                                    <input type="hidden" id="cust_fname" value=""/>
                                                    <input type="hidden" id="customer_contact" value=""/>
                                                    <input type="hidden" id="cust_lname" value=""/>
                                                    <input type="hidden" id="gst_no" value=""/>
                                                    <input type="hidden" id="cust_pincode" value=""/>
                                                    <input type="hidden" id="cust_idstate" value=""/>
                                                    <input type="hidden" id="address" value=""/>
                                                    <input type="hidden" id="customer_state" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php // if($sale->date == date('Y-m-d')){
            //                            echo '<center><h3><i class="mdi mdi-alert"></i> Not allowed to edit invoice after sale date...</h3>'.
            //                                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            //                                . '</center>'; 
            //                            }else{ ?>
            </div>
        </div>
            <!--                        <div class="col-md-4 warning-color" style="border-left: 10px solid #fff; padding: 5px">
                                        <a class="btn btn-block hovereffect waves-effect waves-orange" style="padding: 10px;">
                                            Edit Sale Product
                                        </a>
                                    </div>
                                    <div class="col-md-4 default-color" style="border-left: 10px solid #fff; padding: 5px">
                                        <a class="btn btn-block hovereffect waves-effect waves-teal" style="padding: 10px;">
                                            Edit Payment Amount, Trasaction id
                                        </a>
                                    </div>
                                    <div class="col-md-4 danger-color" style="border-left: 10px solid #fff; padding: 5px">
                                        <a class="btn btn-block hovereffect waves-effect waves-red" style="padding: 10px;">
                                            Add New Payment Mode
                                        </a>
                                    </div><div class="clearfix"></div>-->
                                    <form>
                                        <div class="thumbnail" style="overflow: auto; padding: 2px;">
                                            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
                                                <thead class="bg-info">
                                                    <th class="col-md-4">Product</th>
                                                    <th>IMEI/SRNO</th>
                                                    <th>HSN</th>
                                                    <th>Qty</th>
                                                    <th>MOP</th>
                                                    <th>Rate</th>
                                                    <th>Basic</th>
                                                    <th>Discount</th>
                                                    <th>Landing</th>
                                                    <th>Amount</th>
                                                    <th>Return</th>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($sale_product as $product) { ?>
                                                        <?php if($product->sales_return_type > 0 || $product->idsale_product_for_doa != NULL){ ?>
                                                            <tr class="product_row">
                                                                <td><?php echo $product->product_name; if($product->idsale_product_for_doa != NULL){ echo ' [DOA Replace]'; } ?>
                                                                <input type="hidden" id="freez_activation_code" class="freez_activation_code" value="<?php echo $product->activation_code ?>" />
                                                            </td>
                                                            <td><?php echo $product->imei_no; ?></td>
                                                            <td><?php echo $product->hsn ?></td>
                                                            <td>
                                                                <?php echo $product->qty ?>
                                                                <input type="hidden" id="freez_qty" class="freez_qty" value="<?php echo $product->qty ?>" />
                                                            </td>
                                                            <td><?php echo $product->mop ?></td>
                                                            <td>
                                                                <?php echo $product->price ?>
                                                                <input type="hidden" id="freez_price" class="freez_price" value="<?php echo $product->price ?>" />
                                                            </td>
                                                            <td>
                                                                <?php echo $product->basic ?>
                                                                <input type="hidden" id="freez_basic" class="freez_basic" value="<?php echo $product->basic ?>" />
                                                            </td>
                                                            <td>
                                                                <?php echo $product->discount_amt ?>
                                                                <input type="hidden" id="freez_discount_amt" class="freez_discount_amt" value="<?php echo $product->discount_amt ?>" />
                                                            </td>
                                                            <td>
                                                                <?php echo $product->landing ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $product->total_amount ?>
                                                                <input type="hidden" id="freez_total_amt" class="freez_total_amt" value="<?php echo $product->total_amount ?>" />
                                                            </td>
                                                            <td><?php if($product->sales_return_type==2){ echo 'Replace,Upgrade Return'; }elseif($product->sales_return_type==3){ echo 'DOA Return'; }else{ echo '-'; } ?></td>
                                                        </tr>
                                                    <?php }else{ $price_diff = $product->mop - $product->landing; ?>
                                                        <tr class="product_row">
                                                            <td>
                                                                <?php echo $product->product_name; ?>
                                                                <?php if($product->ssale_type != 0){ ?>
            <!--                                            <select class="form-control input-sm">
                                                            <option value="1929">Insurance OneAssist</option>
                                                            <option value="1930">Insurance SHIELD Shield</option>
                                                        </select>-->
                                                        <input type="text" id="activation_code" class="activation_code form-control input-sm" name="activation_code[]" value="<?php echo $product->activation_code ?>" />
                                                        <input type="hidden" class="old_activation_code" id="old_activation_code" name="old_activation_code[]" value="<?php echo $product->activation_code ?>" />
                                                    <?php }else{ ?>
                                                        <input type="hidden" id="activation_code" class="activation_code form-control input-sm" name="activation_code[]" value="<?php echo $product->activation_code ?>" />
                                                        <input type="hidden" class="old_activation_code" id="old_activation_code" name="old_activation_code[]" value="<?php echo $product->activation_code ?>" />
                                                    <?php } ?>
                                                </td>
                                                <td><?php // echo $product->imei_no ?>
                                                <?php if($product->ssale_type != 0){ ?>
                                                    <input type="text" id="insurance_imei_no" class="insurance_imei_no form-control input-sm" name="insurance_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" style="min-width: 150px" />
                                                    <input type="hidden" class="old_insurance_imei_no" id="old_insurance_imei_no" name="old_insurance_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" />
                                                    <input type="hidden" class="old_imei_no" id="old_imei_no" name="old_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" />
                                                    <input type="hidden" class="new_imei_no" id="new_imei_no" name="new_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" />
                                                <?php }else{ ?>
                                                    <input type="hidden" id="insurance_imei_no" class="insurance_imei_no form-control input-sm" name="insurance_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" style="min-width: 150px" />
                                                    <input type="hidden" class="old_insurance_imei_no" id="old_insurance_imei_no" name="old_insurance_imei_no[]" value="<?php echo $product->insurance_imei_no ?>" />
                                                    <input type="hidden" class="old_imei_no" id="old_imei_no" name="old_imei_no[]" value="<?php echo $product->imei_no ?>" />
                                                    <input type="text" class="new_imei_no form-control input-sm" id="new_imei_no" name="new_imei_no[]" value="<?php echo $product->imei_no ?>" style="min-width: 150px" />
                                                            <?php // echo $product->imei_no;
                                                        } ?>
                                                    </td>
                                                    <td><?php echo $product->hsn ?></td>
                                                    <td>
                                                        <?php if($product->idskutype != 4){ echo $product->qty; ?>
                                                            <input type="hidden" class="form-control input-sm qty" id="qty" name="qty[]" placeholder="Enter Qty" value="<?php echo $product->qty ?>" />
                                                            <input type="hidden" class="old_qty" id="old_qty" name="old_qty[]" value="<?php echo $product->qty ?>" />
                                                        <?php }else{ 
                                                            if($_SESSION['idrole'] == 25){ ?>
                                                                <input type="number" class="form-control input-sm qty" id="qty" name="qty[]" placeholder="Enter Qty" value="<?php echo $product->qty ?>" readonly="" style="min-width: 120px" />
                                                            <?php } else {?>
                                                                <input type="number" class="form-control input-sm qty" id="qty" name="qty[]" placeholder="Enter Qty" value="<?php echo $product->qty ?>" style="min-width: 120px" />
                                                            <?php } ?>
                                                            <input type="hidden" class="old_qty" id="old_qty" name="old_qty[]" value="<?php echo $product->qty ?>" />
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $product->mop ?></td>
                                                    <td><?php if($product->ssale_type != 0){ ?>
                                                        <input type="number" class="form-control input-sm price" id="price" name="price[]" placeholder="Enter Amount" value="<?php echo $product->price ?>" style="min-width: 120px" readonly="" />
                                                    <?php }else{
                                                        if($_SESSION['idrole'] == 25) { ?>
                                                            <input type="number" class="form-control input-sm price" id="price" name="price[]" placeholder="Enter Amount" value="<?php echo $product->price ?>" style="min-width: 120px" readonly="" />
                                                        <?php }else{ ?>
                                                            <input type="number" class="form-control input-sm price" id="price" name="price[]" placeholder="Enter Amount" value="<?php echo $product->price ?>" style="min-width: 120px" />
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <input type="hidden" class="old_price" id="old_price" name="old_price[]" value="<?php echo $product->price ?>" />
                                                    <input type="hidden" class="landing" id="landing" name="landing[]" value="<?php echo $product->landing ?>" />
                                                </td>
                                                <td>
                                                    <div class="spbasic"><?php echo $product->basic ?></div>
                                                    <input type="hidden" class="old_basic" id="old_basic" name="old_basic[]" value="<?php echo $product->basic ?>" />
                                                    <input type="hidden" class="basic" id="basic" name="basic[]" placeholder="Enter Amount" value="<?php echo $product->basic ?>" />
                                                    <input type="hidden" class="ssale_type" id="ssale_type" name="ssale_type[]"  value="<?php echo $product->ssale_type ?>" />
                                                </td>
                                                <td>
                                                    <?php if($product->ssale_type != 0){ ?>
                                                        <input type="number" class="form-control input-sm discount_amt" id="discount_amt" name="discount_amt[]" placeholder="Enter Amount" value="<?php echo $product->discount_amt ?>" style="min-width: 120px" readonly="" />
                                                    <?php }else{
                                                        if($_SESSION['idrole'] == 25){?>
                                                            <input type="number" class="form-control input-sm discount_amt" id="discount_amt" name="discount_amt[]" placeholder="Enter Amount" value="<?php echo $product->discount_amt ?>" style="min-width: 120px" readonly="" />
                                                        <?php } else{ ?>
                                                            <input type="number" class="form-control input-sm discount_amt" id="discount_amt" name="discount_amt[]" placeholder="Enter Amount" value="<?php echo $product->discount_amt ?>" style="min-width: 120px" <?php if(!$product->is_mop){ ?>readonly=""<?php } ?> />
                                                        <?php }?>
                                                    <?php } ?>
                                                    <input type="hidden" class="old_discount_amt" id="old_discount_amt" name="old_discount_amt[]" value="<?php echo $product->discount_amt ?>" />
                                                </td>
                                                <td>
                                                    <?php echo $product->landing ?>
                                                </td>
                                                <td>
                                                    <div class="sptotal_amt"><?php echo $product->total_amount ?></div>
                                                    <input type="hidden" id="total_amt" class="total_amt" name="total_amount[]" value="<?php echo $product->total_amount ?>" />
                                                    <input type="hidden" id="old_total_amt" class="old_total_amt" name="old_total_amount[]" value="<?php echo $product->total_amount ?>" />
                                                    <input type="hidden" id="price_diff" class="price_diff" name="price_diff[]" value="<?php echo $price_diff ?>" />
                                                    <input type="hidden" id="edited_idsaleproduct" class="edited_idsaleproduct" name="edited_idsaleproduct[]" value="0" />
                                                    <input type="hidden" id="idsaleproduct" class="idsaleproduct" name="idsaleproduct[]" value="<?php echo $product->id_saleproduct ?>" />
                                                    <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $product->product_name ?>" />
                                                    <input type="hidden" id="idvariant" class="idvariant" name="idvariant[]" value="<?php echo $product->idvariant ?>" />
                                                    <input type="hidden" id="imei_no" class="imei_no" name="imei_no[]" value="<?php echo $product->imei_no ?>" />
                                                    <input type="hidden" id="idgodown" class="idgodown" name="idgodown[]" value="<?php echo $product->idgodown ?>" />
                                                </td>
                                                <td><?php if($product->sales_return_type==2){ echo 'Replace,Upgrade Return'; }elseif($product->sales_return_type==3){ echo 'DOA Return ['.$product->doa_imei_no.']'; }else{ echo '-'; } ?></td>
                                            </tr>
                                        <?php }} ?>
                                        <tr class="bg-info">
                                            <td colspan="5"></td>
                                            <td>Total</td>
                                            <td>
                                                <?php // echo $sale->basic_total ?>
                                                <div id="spbasic_total"><?php echo $sale->basic_total ?></div>
                                                <input type="hidden" id="basic_total" class="basic_total" name="basic_total" value="<?php echo $sale->basic_total ?>" />
                                                <input type="hidden" id="old_basic_total" class="old_basic_total" name="old_basic_total" value="<?php echo $sale->basic_total ?>" />
                                            </td>
                                            <td>
                                                <div id="spdiscount_total"><?php echo $sale->discount_total ?></div>
                                                <input type="hidden" id="discount_total" class="discount_total" name="discount_total" value="<?php echo $sale->discount_total ?>" />
                                                <input type="hidden" id="old_discount_total" class="old_discount_total" name="old_discount_total" value="<?php echo $sale->discount_total ?>" />
                                            </td>
                                            <td></td>
                                            <td>
                                                <div id="spfinal_total"><?php echo $sale->final_total ?></div>
                                                <input type="hidden" id="final_total" class="final_total" name="final_total" value="<?php echo $sale->final_total ?>" />
                                                <input type="hidden" id="old_final_total" class="old_final_total" name="old_final_total" value="<?php echo $sale->final_total ?>" />
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Details</span>
                            <div class="thumbnail" style="overflow: auto; padding: 2px;">
                                <table class="table table-condensed table-bordered table-hover" style="font-size: 14px; margin-bottom: 0">
                                    <thead>
                                        <th>Action</th>
                                        <th>Payment Mode</th>
                                        <th>Amount</th>
                                        <th>Attributes</th>
                                        <th>Value</th>
                                        <th>Attributes</th>
                                        <th>Value</th>
                                        <th>Attributes</th>
                                        <th>Value</th>
                                    </thead>
                                    <tbody>
                                                <?php // die('<pre>'.print_r($sale_payment,1).'</pre>');
                                                $sum = 0; $cashr=0; foreach ($sale_payment as $payment){
                                                    if((($payment->reconciliation_status==1) || ($payment->credit_type == 1 && $payment->payment_receive == 1) || ($sale->date != date('Y-m-d') && $payment->idpayment_mode == 1)) && (($this->session->userdata('id_users') != 817) )) { // || ($this->session->userdata('id_users') != 817))
                                                    
                                                    //if(0) { ?>
                                                        <tr class="modes_block">
                                                            <td>
                                                                <?php if($_SESSION['idrole'] == 25){
                                                                    if($payment->payment_mode != 'Cash'  && $payment->reconciliation_status!=1){ ?>
                                                                        <a class="btn btn-sm btn-danger btn-outline" id="oldremove_payment" style="pointer-events: none"><i class="fa fa-trash-o"></i> Remove</a>
                                                                    <?php }else{

                                                                    }
                                                                }else{ ?>
                                                                    <a class="btn btn-sm btn-danger btn-outline" id="oldremove_payment" style="pointer-events: none"><i class="fa fa-trash-o"></i> Remove</a>
                                                                <?php } ?>
                                                                
                                                                <input type="hidden" class="idedited_sale_payments" name="idedited_sale_payments[]" value="0" disabled="true"  /> <!-- disabled="true" -->
                                                                <input type="hidden" class="idremoved_sale_payments" name="idremoved_sale_payments[]" value="0" disabled="true"  /> <!-- disabled="true" -->
                                                                <input type="hidden" class="idsale_payment" name="idsale_payment[]" value="<?php echo $payment->id_salepayment ?>" disabled="true"  /> <!-- disabled="true" -->
                                                                <input type="hidden" class="payment_mode" value="<?php echo $payment->payment_mode.' '.$payment->payment_head ?>" disabled="true" /> <!-- disabled="true" -->
                                                                <input type="hidden" class="payment_amount" value="<?php echo $payment->amount ?>" disabled="true" />
                                                                <input type="hidden" name="idpayment_mode[]" value="<?php echo $payment->idpayment_mode ?>" disabled="true" />
                                                                <input type="hidden" name="idpayment_head[]" value="<?php echo $payment->idpayment_head ?>" disabled="true" />
                                                            </td>
                                                            <td><?php echo $payment->payment_mode.' <small>'.$payment->payment_head.'</small>' ?></td>
                                                            <td>
                                                                <?php if($_SESSION['idrole'] == 25) {?>
                                                                    <input type="number" class="form-control input-sm edit_amount" id="edit_amount<?php echo $payment->idpayment_head ?>" name="edit_amount[]" placeholder="Enter Amount" value="<?php echo $payment->amount ?>" style="min-width: 120px" readonly="" disabled="true" />
                                                                <?php }else{ ?>
                                                                    <input type="number" class="form-control input-sm edit_amount" id="edit_amount<?php echo $payment->idpayment_head ?>" name="edit_amount[]" placeholder="Enter Amount" value="<?php echo $payment->amount ?>" style="min-width: 120px" disabled="true" />
                                                                <?php } ?>
                                                                <input type="hidden" class="form-control input-sm old_edit_amount" id="old_edit_amount<?php echo $payment->idpayment_head ?>" name="old_edit_amount[]" value="<?php echo $payment->amount ?>" disabled="true" />
                                                            </td>
                                                            <?php if($payment->bounce_charges != 0){ ?>
                                                                <td><span class="text-muted">Bounce Charges</span></td>
                                                                <td><?php echo $payment->bounce_charges;?></td>
                                                            <?php } ?>
                                                            <?php if($payment->tranxid_type != NULL){ ?>
                                                                <td><span class="text-muted"><?php echo $payment->tranxid_type ?></span></td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm transaction_id" name="transaction_id[<?php echo $payment->id_salepayment ?>]" placeholder="Enter Transaction Id" value="<?php echo $payment->transaction_id ?>" style="min-width: 150px" disabled="true"/>
                                                                    <input type="hidden" class="form-control input-sm old_transaction_id" name="old_transaction_id[<?php echo $payment->id_salepayment ?>]" value="<?php echo $payment->transaction_id ?>" disabled="true"/>
                                                                </td>
                                                            <?php } foreach ($payment_head_has_attributes as $has_attributes){ 
                                                                if($has_attributes->idpayment_head == $payment->idpayment_head){ ?>
                                                                    <td><span class="text-muted"><?php if($payment->idpayment_mode == 17){ echo 'Bank UTR'; }else{ echo $has_attributes->attribute_name; } ?></span></td>
                                                                    <td><?php $clm = $has_attributes->column_name; ?>
                                                                    <input type="text" class="form-control input-sm attr_value" name="<?php echo $has_attributes->column_name ?>[<?php echo $payment->id_salepayment ?>]" placeholder="Enter <?php echo $has_attributes->attribute_name ?>" value="<?php echo $payment->$clm ?>" required="" style="min-width: 120px" disabled="true" />
                                                                    <input type="hidden" name="old_<?php echo $has_attributes->column_name ?>[<?php echo $payment->id_salepayment ?>]" value="<?php echo $payment->$clm ?>" disabled="true"/>
                                                                </td>
                                                            <?php }} ?>
                                                        </tr>
                                                    <?php }else{ ?>
                                                        <tr class="modes_block">
                                                            <td>
                                                                <?php if($_SESSION['idrole'] == 25){
                                                                    if($payment->payment_mode != 'Cash'){ ?>
                                                                        <a class="btn btn-sm btn-danger btn-outline" id="oldremove_payment"><i class="fa fa-trash-o"></i> Removee</a>
                                                                    <?php }
                                                                } else{ 
                                                                    if($payment->payment_mode != 'Cash' && $payment->reconciliation_status!=1){ ?>

                                                                        <a class="btn btn-sm btn-danger btn-outline" id="oldremove_payment"><i class="fa fa-trash-o"></i> Remove</a>
                                                                    <?php }else{ ?>
                                                                     <a class="btn btn-sm btn-danger btn-outline" id="oldremove_payment"><i class="fa fa-trash-o"></i> Remove</a>
                                                                 <?php }

                                                             } ?>
                                                             <input type="hidden" class="idedited_sale_payments" name="idedited_sale_payments[]" value="0" />
                                                             <input type="hidden" class="idremoved_sale_payments" name="idremoved_sale_payments[]" value="0" />
                                                             <input type="hidden" class="idsale_payment" name="idsale_payment[]" value="<?php echo $payment->id_salepayment ?>" />
                                                             <input type="hidden" class="payment_mode" value="<?php echo $payment->payment_mode.' '.$payment->payment_head ?>" />
                                                             <input type="hidden" class="payment_amount" value="<?php echo $payment->amount ?>" />
                                                             <input type="hidden" name="idpayment_mode[]" value="<?php echo $payment->idpayment_mode ?>" />
                                                             <input type="hidden" name="idpayment_head[]" value="<?php echo $payment->idpayment_head ?>" />
                                                         </td>
                                                         <td><?php echo $payment->payment_mode.' <small>'.$payment->payment_head.'</small>' ?></td>
                                                         <td>
                                                            <?php if($_SESSION['idrole'] == 25) {?>
                                                                <input type="number" class="form-control input-sm edit_amount" id="edit_amount<?php echo $payment->idpayment_head ?>" name="edit_amount[]" placeholder="Enter Amount" value="<?php echo $payment->amount ?>" style="min-width: 120px" readonly />
                                                            <?php }else{  ?>
                                                                <input type="number" class="form-control input-sm edit_amount" id="edit_amount<?php echo $payment->idpayment_head ?>" name="edit_amount[]" placeholder="Enter Amount" value="<?php echo $payment->amount ?>" style="min-width: 120px" <?php if($payment->reconciliation_status==1 && $payment->idadvance_payment_receive!=''){ echo 'readonly';}?> />
                                                            <?php } ?>
                                                            <input type="hidden" class="form-control input-sm old_edit_amount" id="old_edit_amount<?php echo $payment->idpayment_head ?>" name="old_edit_amount[]" value="<?php echo $payment->amount ?>" />
                                                        </td>
                                                        <?php if($payment->bounce_charges != 0){ ?>
                                                            <td><span class="text-muted">Bounce Charges</span></td>
                                                            <td><?php echo $payment->bounce_charges;?></td>
                                                        <?php } ?>
                                                        <?php if($payment->tranxid_type != NULL){ ?>
                                                            <td><span class="text-muted"><?php echo $payment->tranxid_type ?></span></td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm transaction_id" name="transaction_id[<?php echo $payment->id_salepayment ?>]" placeholder="Enter Transaction Id" value="<?php echo $payment->transaction_id ?>" style="min-width: 150px" <?php if($payment->reconciliation_status==1){ echo 'readonly';}?> />
                                                                <input type="hidden" class="form-control input-sm old_transaction_id" name="old_transaction_id[<?php echo $payment->id_salepayment ?>]" value="<?php echo $payment->transaction_id ?>"/>
                                                            </td>
                                                        <?php } foreach ($payment_head_has_attributes as $has_attributes){ 
                                                            if($has_attributes->idpayment_head == $payment->idpayment_head){ ?>
                                                                <td><span class="text-muted"><?php if($payment->idpayment_mode == 17){ echo 'Bank UTR'; }else{ echo $has_attributes->attribute_name; } ?></span></td>
                                                                <td><?php $clm = $has_attributes->column_name; ?>
                                                                <input type="text" class="form-control input-sm attr_value" name="<?php echo $has_attributes->column_name ?>[<?php echo $payment->id_salepayment ?>]" placeholder="Enter <?php echo $has_attributes->attribute_name ?>" value="<?php echo $payment->$clm ?>" required="" style="min-width: 120px" <?php if($payment->reconciliation_status==1){ echo 'readonly';}?>/>
                                                                <input type="hidden" name="old_<?php echo $has_attributes->column_name ?>[<?php echo $payment->id_salepayment ?>]" value="<?php echo $payment->$clm ?>" />
                                                            </td>
                                                        <?php }} ?>
                                                    </tr>
                                                <?php } ?>
                                                <?php $sum += $payment->amount; if($payment->idpayment_mode == 1){ $cashr = 1; }} ?>
                                                <tr style="font-weight: bold">
                                                    <td></td>
                                                    <td>Total</td>
                                                    <td><div id="enfinal_total"><?php echo $sum ?></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br>
                                    <div style="font-size: 13px">
                                        <div class="col-md-3" >
                                            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="mdi mdi-plus-circle-outline"></i> Add Payment Mode</span>
                                        </div>
                                        <div class="col-md-3">
                                            <?php if($_SESSION['idrole'] == 25 ){ ?>
                                                <select name="select_payment_type[]" id="select_payment_type" class="chosen-select" style="width: 100%">
                                                    <option value="">Select Payment Mode</option>
                                                    <?php foreach ($payment_mode as $mode){ 
                                                        if($mode->id_paymentmode != 1){ ?>
                                                            <option value="<?php echo $mode->id_paymentmode; ?>" paymenthead="<?php echo $mode->idpaymenthead; ?>" headname="<?php echo $mode->payment_head ?>" modename="<?php echo $mode->payment_mode ?>" credit_type="<?php echo $mode->credit_type ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            <?php  }else{?>
                                                <select name="select_payment_type[]" id="select_payment_type" class="chosen-select" style="width: 100%">
                                                    <option value="">Select Payment Mode</option>
                                                    <?php foreach ($payment_mode as $mode){ 
            //                                            if($cashr != $mode->id_paymentmode){ ?>
                <option value="<?php echo $mode->id_paymentmode; ?>" paymenthead="<?php echo $mode->idpaymenthead; ?>" headname="<?php echo $mode->payment_head ?>" modename="<?php echo $mode->payment_mode ?>" credit_type="<?php echo $mode->credit_type ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
                                                <?php // }
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div><div class="clearfix"></div><br>
                                <div class="payment_block"></div>
                                <div class="clearfix"></div><hr>
                            </div>
                            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Reconciliation Details</span>
                            <div class="thumbnail" style="overflow: auto;padding: 2px">
                                <table class="table table-bordered table-condensed table-hover" style="font-size: 14px; margin-bottom: 0">
                                    <thead>
                                        <th>Mode</th>
                                        <th>Amount</th>
                                        <th>Txn No</th>
                                        <th>Reconciliation</th>
                                        <th>Pending</th>
                                        <th>Bank</th>
                                        <th>UTR</th>
                                        <th>Received Date</th>
                                    </thead>
                                    <tbody>
                                        <?php $total_remain=0; $total_received=0; $total_amt = 0;
                                        foreach ($sale_reconciliation as $recon){ $remain=0;
                                            $remain = $recon->amount - $recon->received_amount;
                                            $total_remain += $remain; 
                                            $total_amt += $recon->amount;
                                            $total_received += $recon->received_amount; ?>
                                            <tr <?php if(($recon->payment_receive) == 1){?> class="bg-danger" <?php } ?>>
                                                <td><?php echo $recon->payment_mode ?></td>
                                                <td><?php echo $recon->amount ?></td>
                                                <td><?php echo $recon->transaction_id ?></td>
                                                <td><?php echo $recon->received_amount ?></td>
                                                <td><?php echo $remain ?></td>
                                                <td><?php echo $recon->bank_name ?></td>
                                                <td><?php echo $recon->utr_no ?></td>
                                                <td><?php if($recon->received_amount > 0){ echo date('d/m/Y H:i:s', strtotime($recon->received_entry_time)); } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <thead>
                                        <th>Total</th>
                                        <th><?php echo $total_amt ?></th>
                                        <th></th>
                                        <th><?php echo $total_received ?></th>
                                        <th><?php echo $total_remain ?></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-md-9">
                                <div class="thumbnail" style="margin-bottom: 5px;padding: 0px;font-size: 14px;font-family: Kurale">
                                    <table class="table table-bordered" style="margin-bottom: 0">
                                        <tbody>
                                            <tr>
                                                <td class="col-md-2">Invoice Amount</td>
                                                <td class="col-md-2"><span id="invoice_amount"><?php echo $sale->final_total ?></span> <i class="mdi mdi-currency-inr fa-lg"></i></td>
                                                <td class="col-md-2">Entered Total</td>
                                                <td class="col-md-2"><span id="entered_amout"><?php echo $sale->final_total ?></span> <i class="mdi mdi-currency-inr fa-lg"></i></td>
                                                <td class="col-md-2">Remaining Amount</td>
                                                <td class="col-md-2"><span id="remaining_amount">0</span> <i class="mdi mdi-currency-inr fa-lg"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="inv_no" value="<?php echo $sale->inv_no ?>" />
                                <input type="hidden" name="invoice_entry_time" value="<?php echo $sale->entry_time ?>" />
                                <input type="hidden" name="invoice_date" value="<?php echo $sale->date ?>" />
                                <input type="hidden" name="idsale" value="<?php echo $sale->id_sale ?>" />
                                <input type="hidden" name="idcustomer" value="<?php echo $sale->idcustomer ?>">
                                <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $sale->id_branch ?>">
                                <button type="button" id="reverse_value" class="btn btn-warning waves-effect waves-light">Clear</button>
                                <button type="submit" formaction="<?php echo base_url('Sale/edit_invoice') ?>" formmethod="POST" id="correction_submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                            </div><div class="clearfix"></div>
                        </form>
                        <?php // } ?>
                    </div>
                <?php }
//                }else{
//                    echo '<center><h3><i class="mdi mdi-alert"></i> You have allowed for edit before '.$mnt.' </h3>'.
//                            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
//                        . '</center>'; 
//                }

            }
            else{
                echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice Number</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
            }
        }
    }else{
      echo '<center><h3><i class="mdi mdi-alert"></i> E Invoice Done for this Invoice No</h3>'.
      '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
      . '</center>'; 
  }
}

public function edit_invoice(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
    $this->db->trans_begin();
    $date = date('Y-m-d');
    $datetime = date('Y-m-d H:i:s');
    $invoice_entry_time = $this->input->post('invoice_entry_time');
    $invoice_date = $this->input->post('invoice_date');
    $inv_no = $this->input->post('inv_no');
    $idsale = $this->input->post('idsale');
    $idcustomer = $this->input->post('idcustomer');
    $idremoved_sale_payments = $this->input->post('idremoved_sale_payments');
    $idedited_sale_payments = $this->input->post('idedited_sale_payments');
    $edited_idsaleproduct = $this->input->post('edited_idsaleproduct');
    $edit_types = '';
    if(array_sum($edited_idsaleproduct) > 0){
        $edit_types .= 'Edit Product, ';
    }
    if(array_sum($idedited_sale_payments) > 0){
        $edit_types .= 'Edit Payment, ';
    }
    if(array_sum($idremoved_sale_payments) > 0){
        $edit_types .= 'Remove Payment';
    }
//        die($edit_types);
    $sale_data = array(
        'inv_no' => $inv_no,
        'invoice_date' => $invoice_date,
        'idsale' => $idsale,
        'date' => $date,
        'idcustomer' => $idcustomer,
        'basic_total' => $this->input->post('basic_total'),
        'old_basic_total' => $this->input->post('old_basic_total'),
        'discount_total' => $this->input->post('discount_total'),
        'old_discount_total' => $this->input->post('old_discount_total'),
        'final_total' => $this->input->post('final_total'),
        'old_final_total' => $this->input->post('old_final_total'),
        'idbranch' => $this->input->post('idbranch'),
        'entry_time' => $datetime,
        'created_by' => $this->session->userdata('id_users'),
        'edit_types' => $edit_types,
    );
    $sale_product = [];
    $sale_product_edit_history = [];
    $insurance_recon = [];
    $idsale_history = $this->Sale_model->save_sale_edit_history_data($sale_data);


        // sale product edit
    if(array_sum($edited_idsaleproduct) > 0){
        for($i=0;$i<count($edited_idsaleproduct);$i++){
            if($edited_idsaleproduct[$i] != 0){
                $qty = $this->input->post('qty');
                $old_qty = $this->input->post('old_qty');
                $price = $this->input->post('price');
                $old_price = $this->input->post('old_price');
                $discount_amt = $this->input->post('discount_amt');
                $old_discount_amt = $this->input->post('old_discount_amt');
                $insurance_imei_no = $this->input->post('insurance_imei_no');
                $old_insurance_imei_no = $this->input->post('old_insurance_imei_no');
                $activation_code = $this->input->post('activation_code');
                $old_activation_code = $this->input->post('old_activation_code');
                $old_imei_no = $this->input->post('old_imei_no');
                $new_imei_no = $this->input->post('new_imei_no');
                if($qty[$i] != $old_qty[$i] || $price[$i] != $old_price[$i] || $discount_amt[$i] != $old_discount_amt[$i] 
                    || $insurance_imei_no[$i] != $old_insurance_imei_no[$i] || $activation_code[$i] != $old_activation_code[$i] 
                    || $old_imei_no[$i] != $new_imei_no[$i]){
                    $sale_product_edit_history[] = array(
                        'idsale_edit_history' => $idsale_history,
                        'idsale_product' => $edited_idsaleproduct[$i],
                        'idvariant' => $this->input->post('idvariant['.$i.']'),
                        'product_name' => $this->input->post('product_name['.$i.']'),
                        'imei_no' => $new_imei_no[$i],
                        'qty' => $qty[$i],
                        'old_qty' => $old_qty[$i],
                        'price' => $price[$i],
                        'old_price' => $old_price[$i],
                        'discount_amt' => $discount_amt[$i],
                        'old_discount_amt' => $old_discount_amt[$i],
                        'basic' => $this->input->post('basic['.$i.']'),
                        'old_basic' => $this->input->post('old_basic['.$i.']'),
                        'total_amount' => $this->input->post('total_amount['.$i.']'),
                        'old_total_amount' => $this->input->post('old_total_amount['.$i.']'),
                    );
                    $sale_product[] = array(
                        'id_saleproduct' => $edited_idsaleproduct[$i],
                        'qty' => $qty[$i],
                        'price' => $price[$i],
                        'discount_amt' => $discount_amt[$i],
                        'imei_no' => $new_imei_no[$i],
                        'basic' => $this->input->post('basic['.$i.']'),
                        'total_amount' => $this->input->post('total_amount['.$i.']'),
                        'insurance_imei_no' => $this->input->post('insurance_imei_no['.$i.']'),
                        'activation_code' => $this->input->post('activation_code['.$i.']'),
                    );
                    $ssale_type = $this->input->post('ssale_type['.$i.']');
                    if($ssale_type != 0){
                        $insurance_recon[]=array(
                            'idsale_product' => $edited_idsaleproduct[$i],
                            'insurance_imei_no' => $this->input->post('insurance_imei_no['.$i.']'),
                            'activation_code' => $this->input->post('activation_code['.$i.']'),
                            'qty' => $this->input->post('qty['.$i.']'),
                            'total_amount' => $this->input->post('qty['.$i.']'),
                        );
                    }
//                        die($old_imei_no[$i].' '.$new_imei_no[$i]);s
                    if($old_imei_no[$i] != $new_imei_no[$i]){
                        $this->Sale_model->edit_batch_imei_history(4, $idsale,$old_imei_no[$i],$new_imei_no[$i]);
                        $this->Sale_model->update_stock_byimei($new_imei_no[$i], $old_imei_no[$i]);
                    }
                }
            }
        }
        if(count($sale_product_edit_history) > 0){
            if($this->Sale_model->save_sale_product_edit_history($sale_product_edit_history)){
                $this->Sale_model->edit_sale_product($sale_product);
            }
            if($ssale_type != 0){
                $this->Sale_model->edit_batch_insurance_recon($insurance_recon);
            }
            $edit_sale = array(
                'id_sale' => $idsale,
                'basic_total' => $this->input->post('basic_total'),
                'discount_total' => $this->input->post('discount_total'),
                'final_total' => $this->input->post('final_total'),
            );
            $this->Sale_model->edit_sale($idsale, $edit_sale);
        }
    }


//        $sale_payment_edit_history = [];
    $sale_payment_remove_history = array();
    for($i=0;$i<count($idremoved_sale_payments);$i++){
        if($idremoved_sale_payments[$i] != 0){
            $idrem = $idremoved_sale_payments[$i];
            $sale_payment_edit_history = array(
                'idsale_payment' => $idremoved_sale_payments[$i],
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $this->input->post('idpayment_head['.$i.']'),
                'idpayment_mode' => $this->input->post('idpayment_mode['.$i.']'),
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'old_amount' => $this->input->post('old_edit_amount['.$i.']'),
                'old_transaction_id' => $this->input->post('old_transaction_id['.$idrem.']'),
                'old_product_model_name' => $this->input->post('old_product_model_name['.$idrem.']'),
                'old_product_imei_no' => $this->input->post('old_product_imei_no['.$idrem.']'),
                'old_approved_by' => $this->input->post('old_approved_by['.$idrem.']'),
                'old_customer_bank_name' => $this->input->post('old_customer_bank_name['.$idrem.']'),
                'old_buyback_vendor_name' => $this->input->post('old_buyback_vendor_name['.$idrem.']'),
                'old_swipe_card_number' => $this->input->post('old_swipe_card_number['.$idrem.']'),
                'old_referral_name' => $this->input->post('old_referral_name['.$idrem.']'),
                'old_finance_promoter_name' => $this->input->post('old_finance_promoter_name['.$idrem.']'),
                'old_scheme_code' => $this->input->post('old_scheme_code['.$idrem.']'),
                'entry_type' => 'Removed',
                    'identry_type' => 3, // Removed
                );
            $this->Sale_model->save_sale_payment_edit_history($sale_payment_edit_history);
            if($this->input->post('idpayment_head['.$i.']') == 1){
                $this->Sale_model->remove_daybook_cash_amount($idsale, 1);
            }
        }
            // remved list need to enter
    }

        // Add new payment mode
    if($this->input->post('idpaymentmode')){
        $idpaymentmode = $this->input->post('idpaymentmode');
        $idpaymenthead = $this->input->post('idpaymenthead');
        $tranxid = $this->input->post('tranxid');
        $amount = $this->input->post('amount');
        $credittype = $this->input->post('credittype');
        for($i=0; $i<count($idpaymentmode);$i++){
            $payment_receive=0;$received_amount=0;$pending_amt=$amount[$i];$received_entry_time=NULL;
            if($idpaymentmode[$i] == 1){
                $received_amount = $amount[$i];
                $pending_amt=0;$received_entry_time=$invoice_entry_time;$payment_receive=1;
                $srpayment = array(
                    'date' => $invoice_date,
                    'inv_no' => $inv_no,
                    'entry_type' => 1,
                    'idbranch' => $this->input->post('idbranch'),
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'amount' => $amount[$i],
                    'entry_time' => $invoice_entry_time
                );
                $this->Sale_model->save_daybook_cash_payment($srpayment);
            }
            $payment = array(
                'date' => $invoice_date,
                'idsale' => $idsale,
                'amount' => $amount[$i],
                'idpayment_head' => $idpaymenthead[$i],
                'idpayment_mode' => $idpaymentmode[$i],
                'transaction_id' => $tranxid[$i],
                'inv_no' => $inv_no,
                'idcustomer' => $idcustomer,
                'idbranch' => $this->input->post('idbranch'),
                'created_by' => $this->input->post('created_by'),
                'entry_time' => $invoice_entry_time,
                'received_amount' => $received_amount,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
                'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
                'swipe_card_number' => $this->input->post('new_swipe_card_number['.$i.']'),
                'referral_name' => $this->input->post('new_referral_name['.$i.']'),
                'finance_promoter_name' => $this->input->post('new_finance_promoter_name['.$i.']'),
                'scheme_code' => $this->input->post('new_scheme_code['.$i.']'),
            );
//                die(print_r($payment));
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$i] == 0){
                $npayment = array(
                    'idsale_payment' => $id_sale_payment,
                    'inv_no' => $inv_no,
                    'idsale' => $idsale,
                    'date' => $invoice_date,
                    'idcustomer' => $idcustomer,
                    'idbranch' => $this->input->post('idbranch'),
                    'amount' => $amount[$i],
                    'idpayment_head' => $idpaymenthead[$i],
                    'idpayment_mode' => $idpaymentmode[$i],
                    'transaction_id' => $tranxid[$i],
                    'created_by' => $this->input->post('created_by'),
                    'entry_time' => $invoice_entry_time,
                    'received_amount' => $received_amount,
                    'pending_amt' => $pending_amt,
                    'received_entry_time'=>$received_entry_time,
                    'payment_receive' => $payment_receive,
                    'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                    'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                    'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                    'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                    'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
                    'swipe_card_number' => $this->input->post('new_swipe_card_number['.$i.']'),
                    'referral_name' => $this->input->post('new_referral_name['.$i.']'),
                    'finance_promoter_name' => $this->input->post('new_finance_promoter_name['.$i.']'),
                    'scheme_code' => $this->input->post('new_scheme_code['.$i.']'),
                    'correction_date' => $date,
                );
                $this->Sale_model->save_payment_reconciliation($npayment);
            }
            $sale_payment_edit_history = array(
                'idsale_payment' => $id_sale_payment,
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $idpaymenthead[$i],
                'idpayment_mode' => $idpaymentmode[$i],
                'transaction_id' => $tranxid[$i],
                'amount' => $amount[$i],
                'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
                'swipe_card_number' => $this->input->post('new_swipe_card_number['.$i.']'),
                'referral_name' => $this->input->post('new_referral_name['.$i.']'),
                'finance_promoter_name' => $this->input->post('new_finance_promoter_name['.$i.']'),
                'scheme_code' => $this->input->post('new_scheme_code['.$i.']'),
                'entry_type' => 'New Added',
                    'identry_type' => 1, // Add
                );
            $this->Sale_model->save_sale_payment_edit_history($sale_payment_edit_history);
        }
    }


//        die('<pre>'.print_r($sale_payment_edit_history,1).'</pre>');


    $sale_payment = [];
    $payment_recon_new = [];
    $edit_daybook_cash = [];
//        $edit_daybook_cash = [];
        // sale payment edit
    for($i=0;$i<count($idedited_sale_payments);$i++){
        if($idedited_sale_payments[$i] != 0){
            $idedi = $idedited_sale_payments[$i];
            $sale_payment_edit_history = array(
                'idsale_payment' => $idedited_sale_payments[$i],
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $this->input->post('idpayment_head['.$i.']'),
                'idpayment_mode' => $this->input->post('idpayment_mode['.$i.']'),
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'old_amount' => $this->input->post('old_edit_amount['.$i.']'),
                'transaction_id' => $this->input->post('transaction_id['.$idedi.']'),
                'old_transaction_id' => $this->input->post('old_transaction_id['.$idedi.']'),
                'product_model_name' => $this->input->post('product_model_name['.$idedi.']'),
                'product_imei_no' => $this->input->post('product_imei_no['.$idedi.']'),
                'approved_by' => $this->input->post('approved_by['.$idedi.']'),
                'customer_bank_name' => $this->input->post('customer_bank_name['.$idedi.']'),
                'buyback_vendor_name' => $this->input->post('buyback_vendor_name['.$idedi.']'),
                'swipe_card_number' => $this->input->post('swipe_card_number['.$idedi.']'),
                'referral_name' => $this->input->post('referral_name['.$idedi.']'),
                'finance_promoter_name' => $this->input->post('finance_promoter_name['.$idedi.']'),
                'scheme_code' => $this->input->post('scheme_code['.$idedi.']'),
                'old_product_model_name' => $this->input->post('old_product_model_name['.$idedi.']'),
                'old_product_imei_no' => $this->input->post('old_product_imei_no['.$idedi.']'),
                'old_approved_by' => $this->input->post('old_approved_by['.$idedi.']'),
                'old_customer_bank_name' => $this->input->post('old_customer_bank_name['.$idedi.']'),
                'old_buyback_vendor_name' => $this->input->post('old_buyback_vendor_name['.$idedi.']'),
                'old_swipe_card_number' => $this->input->post('old_swipe_card_number['.$idedi.']'),
                'old_referral_name' => $this->input->post('old_referral_name['.$idedi.']'),
                'old_finance_promoter_name' => $this->input->post('old_finance_promoter_name['.$idedi.']'),
                'old_scheme_code' => $this->input->post('old_scheme_code['.$idedi.']'),
                'entry_type' => 'Edited',
                    'identry_type' => 2, // Edited
                );
            $sale_payment[$i] = array(
                'id_salepayment' => $idedited_sale_payments[$i],
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'transaction_id' => $this->input->post('transaction_id['.$idedi.']'),
                'product_model_name' => $this->input->post('product_model_name['.$idedi.']'),
                'product_imei_no' => $this->input->post('product_imei_no['.$idedi.']'),
                'approved_by' => $this->input->post('approved_by['.$idedi.']'),
                'customer_bank_name' => $this->input->post('customer_bank_name['.$idedi.']'),
                'swipe_card_number' => $this->input->post('swipe_card_number['.$idedi.']'),
                'referral_name' => $this->input->post('referral_name['.$idedi.']'),
                'finance_promoter_name' => $this->input->post('finance_promoter_name['.$idedi.']'),
                'scheme_code' => $this->input->post('scheme_code['.$idedi.']'),
            );
            $payment_recon_new[$i] = array(
                'idsale_payment' => $idedited_sale_payments[$i],
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'transaction_id' => $this->input->post('transaction_id['.$idedi.']'),
                'product_model_name' => $this->input->post('product_model_name['.$idedi.']'),
                'product_imei_no' => $this->input->post('product_imei_no['.$idedi.']'),
                'approved_by' => $this->input->post('approved_by['.$idedi.']'),
                'customer_bank_name' => $this->input->post('customer_bank_name['.$idedi.']'),
                'swipe_card_number' => $this->input->post('swipe_card_number['.$idedi.']'),
                'referral_name' => $this->input->post('referral_name['.$idedi.']'),
                'finance_promoter_name' => $this->input->post('finance_promoter_name['.$idedi.']'),
                'scheme_code' => $this->input->post('scheme_code['.$idedi.']'),
                'correction_date' => $date,
            );
            if($this->input->post('idpayment_head['.$i.']') == 1){
                $cash_received = array(
                    'received_amount' => $this->input->post('edit_amount['.$i.']'),
                );
                $sale_payment[$i] = array_merge($sale_payment[$i], $cash_received);
                $payment_recon_new[$i] = array_merge($payment_recon_new[$i], $cash_received);
                $edit_daybook_cash = array(
//                        'idtable' => $idsale,
                    'amount' => $this->input->post('edit_amount['.$i.']'),
                );
            }
//                $this->Sale_model->edit_sale_payment($idedited_sale_payments[$i], $sale_payment);
//                $this->Sale_model->edit_sale_reconciliation($idedited_sale_payments[$i], $sale_payment);
            $this->Sale_model->save_sale_payment_edit_history($sale_payment_edit_history);
        }
    }
//        die('<pre>'.print_r($sale_payment_edit_history,1).'</pre>');
//        die('<pre>'.print_r($edit_daybook_cash,1).'</pre>');
    if(count($edit_daybook_cash) > 0){
        $this->Sale_model->edit_daybook_cash_byidtable_entry_type($idsale, 1, $edit_daybook_cash);
    }

//        if(count($sale_payment_edit_history) > 0){
//            $this->Sale_model->save_sale_payment_edit_history($sale_payment_edit_history);
//        }
    if(count($sale_payment) > 0){
        $this->Sale_model->batch_edit_sale_payment($sale_payment);
    }
    if(count($payment_recon_new) > 0){
        $this->Sale_model->batch_edit_sale_reconciliation($payment_recon_new);
    }
//        if(count($edit_daybook_cash) > 0){
//            $this->Sale_model->edit_daybook_cash_byidtable_entry_type($edit_daybook_cash);
//        }

    if(array_sum($idremoved_sale_payments) > 0){
        $this->Sale_model->remove_sale_payment($idremoved_sale_payments);
        $this->Sale_model->remove_payment_reconciliation($idremoved_sale_payments);
    } 
//        die('hi');
    if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
    }else{
        $this->db->trans_commit();
        $this->session->set_flashdata('save_data', 'Invoice bill generated');
    }
//        die('<pre>'.print_r($_POST,1).'</pre>');
    return redirect('Sale/sale_details/'.$idsale);
}
public function ajax_check_valid_imei() {
//        die(print_r($_POST));
    $new_imei_no = $this->input->post('new_imei_no');
    $idvariant = $this->input->post('idvariant');
    $idgodown = $this->input->post('idgodown');
    $idbranch  = $this->input->post('idbranch');
    $res = $this->Sale_model->ajax_check_valid_imei($idbranch,$idgodown,$idvariant,$new_imei_no);
//        die(count($res));
    if(count($res)){
        $q = 'Success';
    }else{
        $q = 'Failed';
    }
    echo json_encode($q);
}
public function edit_invoice1(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
    $this->db->trans_begin();
    $date = date('Y-m-d');
    $datetime = date('Y-m-d H:i:s');
    $inv_no = $this->input->post('inv_no');
    $idsale = $this->input->post('idsale');
    $idcustomer = $this->input->post('idcustomer');
    $idremoved_sale_payments = $this->input->post('idremoved_sale_payments');
    $idedited_sale_payments = $this->input->post('idedited_sale_payments');
    $edited_idsaleproduct = $this->input->post('edited_idsaleproduct');
    $edit_types = '';
    if(array_sum($edited_idsaleproduct) > 0){
        $edit_types .= 'Edit Product, ';
    }
    if(array_sum($idedited_sale_payments) > 0){
        $edit_types .= 'Edit Payment, ';
    }
    if(array_sum($idremoved_sale_payments) > 0){
        $edit_types .= 'Remove Payment';
    }
//        die($edit_types);
        // remove entries
    $sale_data = array(
        'inv_no' => $inv_no,
        'idsale' => $idsale,
        'date' => $date,
        'idcustomer' => $idcustomer,
        'basic_total' => $this->input->post('basic_total'),
        'old_basic_total' => $this->input->post('old_basic_total'),
        'discount_total' => $this->input->post('discount_total'),
        'old_discount_total' => $this->input->post('old_discount_total'),
        'final_total' => $this->input->post('final_total'),
        'old_final_total' => $this->input->post('old_final_total'),
        'idbranch' => $this->input->post('idbranch'),
        'entry_time' => $datetime,
        'created_by' => $this->session->userdata('id_users'),
        'edit_types' => $edit_types,
    );
    $sale_product = array();
    if($idsale_history = $this->Sale_model->save_sale_edit_history_data($sale_data)){
        $sale_product_edit_history = array(); 
        for($i=0;$i<count($edited_idsaleproduct);$i++){
            if($edited_idsaleproduct[$i] != 0){
                $qty = $this->input->post('qty');
                $old_qty = $this->input->post('old_qty');
                $price = $this->input->post('price');
                $old_price = $this->input->post('old_price');
                $discount_amt = $this->input->post('discount_amt');
                $old_discount_amt = $this->input->post('old_discount_amt');
                if($qty != $old_qty || $price != $old_price || $discount_amt != $old_discount_amt){
                    $sale_product_edit_history[] = array(
                        'idsale_edit_history' => $idsale_history,
                        'idsale_product' => $edited_idsaleproduct[$i],
                        'idvariant' => $this->input->post('idvariant['.$i.']'),
                        'product_name' => $this->input->post('product_name['.$i.']'),
                        'imei_no' => $this->input->post('imei_no['.$i.']'),
                        'qty' => $qty[$i],
                        'old_qty' => $old_qty[$i],
                        'price' => $price[$i],
                        'old_price' => $old_price[$i],
                        'discount_amt' => $discount_amt[$i],
                        'old_discount_amt' => $old_discount_amt[$i],
                        'basic' => $this->input->post('basic['.$i.']'),
                        'old_basic' => $this->input->post('old_basic['.$i.']'),
                        'total_amount' => $this->input->post('total_amount['.$i.']'),
                        'old_total_amount' => $this->input->post('old_total_amount['.$i.']'),
                    );
                    $sale_product[] = array(
                        'id_saleproduct' => $edited_idsaleproduct[$i],
                        'qty' => $qty[$i],
                        'price' => $price[$i],
                        'discount_amt' => $discount_amt[$i],
                        'basic' => $this->input->post('basic['.$i.']'),
                        'total_amount' => $this->input->post('total_amount['.$i.']'),
                    );
                }
            }
        }
        if(count($sale_product_edit_history) > 0){
//                die('<pre>'.print_r($sale_product_edit_history,1).'</pre>');
            $this->Sale_model->save_sale_product_edit_history($sale_product_edit_history);
        }
        $edit_sale = array(
            'basic_total' => $this->input->post('basic_total'),
            'discount_total' => $this->input->post('discount_total'),
            'final_total' => $this->input->post('final_total'),
        );
        $this->Sale_model->edit_sale($idsale, $edit_sale);
    }
    $sale_payment_edit_history = array();
    for($i=0;$i<count($idedited_sale_payments);$i++){
        if($idedited_sale_payments[$i] != 0){
            $idedi = $idedited_sale_payments[$i];
            $sale_payment_edit_history[] = array(
                'idsale_payment' => $idedited_sale_payments[$i],
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $this->input->post('idpayment_head['.$i.']'),
                'idpayment_mode' => $this->input->post('idpayment_mode['.$i.']'),
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'old_amount' => $this->input->post('old_edit_amount['.$i.']'),
                'transaction_id' => $this->input->post('transaction_id['.$idedi.']'),
                'old_transaction_id' => $this->input->post('old_transaction_id['.$idedi.']'),
                'product_model_name' => $this->input->post('product_model_name['.$idedi.']'),
                'product_imei_no' => $this->input->post('product_imei_no['.$idedi.']'),
                'approved_by' => $this->input->post('approved_by['.$idedi.']'),
                'customer_bank_name' => $this->input->post('customer_bank_name['.$idedi.']'),
                'buyback_vendor_name' => $this->input->post('buyback_vendor_name['.$idedi.']'),
                'old_product_model_name' => $this->input->post('old_product_model_name['.$idedi.']'),
                'old_product_imei_no' => $this->input->post('old_product_imei_no['.$idedi.']'),
                'old_approved_by' => $this->input->post('old_approved_by['.$idedi.']'),
                'old_customer_bank_name' => $this->input->post('old_customer_bank_name['.$idedi.']'),
                'old_buyback_vendor_name' => $this->input->post('old_buyback_vendor_name['.$idedi.']'),
                'entry_type' => 'Edited'
            );
            $sale_payment = array(
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'transaction_id' => $this->input->post('transaction_id['.$idedi.']'),
                'product_model_name' => $this->input->post('product_model_name['.$idedi.']'),
                'product_imei_no' => $this->input->post('product_imei_no['.$idedi.']'),
                'approved_by' => $this->input->post('approved_by['.$idedi.']'),
                'customer_bank_name' => $this->input->post('customer_bank_name['.$idedi.']'),
            );
            $this->Sale_model->edit_sale_payment($idedited_sale_payments[$i], $sale_payment);
            $this->Sale_model->edit_sale_reconciliation($idedited_sale_payments[$i], $sale_payment);
        }
    }
//        $sale_payment_remove_history = array();
    for($i=0;$i<count($idremoved_sale_payments);$i++){
        if($idremoved_sale_payments[$i] != 0){
            $idrem = $idremoved_sale_payments[$i];
            $sale_payment_edit_history[] = array(
                'idsale_payment' => $idremoved_sale_payments[$i],
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $this->input->post('idpayment_head['.$i.']'),
                'idpayment_mode' => $this->input->post('idpayment_mode['.$i.']'),
                'amount' => $this->input->post('edit_amount['.$i.']'),
                'old_amount' => $this->input->post('old_edit_amount['.$i.']'),
                'old_transaction_id' => $this->input->post('old_transaction_id['.$idrem.']'),
                'old_product_model_name' => $this->input->post('old_product_model_name['.$idrem.']'),
                'old_product_imei_no' => $this->input->post('old_product_imei_no['.$idrem.']'),
                'old_approved_by' => $this->input->post('old_approved_by['.$idrem.']'),
                'old_customer_bank_name' => $this->input->post('old_customer_bank_name['.$idrem.']'),
                'old_buyback_vendor_name' => $this->input->post('old_buyback_vendor_name['.$idrem.']'),
                'entry_type' => 'Removed'
            );
        }
        $this->Sale_model->remove_sale_payment($idremoved_sale_payments[$i]);
        $this->Sale_model->remove_payment_reconciliation($idremoved_sale_payments[$i]);
            // remved list need to enter
    }
    if($this->input->post('idpaymentmode')){
        $idpaymentmode = $this->input->post('idpaymentmode');
        $idpaymenthead = $this->input->post('idpaymenthead');
        $tranxid = $this->input->post('tranxid');
        $amount = $this->input->post('amount');
        $credittype = $this->input->post('credittype');
        for($i=0; $i<count($idpaymentmode);$i++){
            $payment_receive=0;$received_amount=0;$pending_amt=$amount[$i];$received_entry_time=NULL;
            if($idpaymentmode[$i] == 1){
                $received_amount = $amount[$i];
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                $srpayment = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'entry_type' => 1,
                    'idbranch' => $this->input->post('idbranch'),
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'amount' => $amount[$i],
                );
                $this->Sale_model->save_daybook_cash_payment($srpayment);
            }
            $payment = array(
                'date' => $date,
                'idsale' => $idsale,
                'amount' => $amount[$i],
                'idpayment_head' => $idpaymenthead[$i],
                'idpayment_mode' => $idpaymentmode[$i],
                'transaction_id' => $tranxid[$i],
                'inv_no' => $inv_no,
                'idcustomer' => $idcustomer,
                'idbranch' => $this->input->post('idbranch'),
                'created_by' => $this->input->post('created_by'),
                'entry_time' => $datetime,
                'received_amount' => $received_amount,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
                'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
            );
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$i] == 0){
                $npayment = array(
                    'idsale_payment' => $id_sale_payment,
                    'inv_no' => $inv_no,
                    'idsale' => $idsale,
                    'date' => $date,
                    'idcustomer' => $idcustomer,
                    'idbranch' => $this->input->post('idbranch'),
                    'amount' => $amount[$i],
                    'idpayment_head' => $idpaymenthead[$i],
                    'idpayment_mode' => $idpaymentmode[$i],
                    'transaction_id' => $tranxid[$i],
                    'created_by' => $this->input->post('created_by'),
                    'entry_time' => $datetime,
                    'received_amount' => $received_amount,
                    'pending_amt' => $pending_amt,
                    'received_entry_time'=>$received_entry_time,
                    'payment_receive' => $payment_receive,
                    'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                    'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                    'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                    'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                    'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
                );
                $this->Sale_model->save_payment_reconciliation($npayment);
            }
            $sale_payment_edit_history[] = array(
                'idsale_payment' => $id_sale_payment,
                'idsale' => $idsale,
                'date' => $date,
                'idsale_edit_history' => $idsale_history,
                'entry_time' => $datetime,
                'inv_no' => $inv_no,
                'idbranch' => $this->input->post('idbranch'),
                'idpayment_head' => $idpaymenthead[$i],
                'idpayment_mode' => $idpaymentmode[$i],
                'transaction_id' => $tranxid[$i],
                'amount' => $amount[$i],
                'product_model_name' => $this->input->post('new_product_model_name['.$i.']'),
                'product_imei_no' => $this->input->post('new_product_imei_no['.$i.']'),
                'approved_by' => $this->input->post('new_approved_by['.$i.']'),
                'customer_bank_name' => $this->input->post('new_customer_bank_name['.$i.']'),
                'buyback_vendor_name' => $this->input->post('new_buyback_vendor_name['.$i.']'),
                'entry_type' => 'New Added'
            );
        }
    }
    if(count($sale_payment_edit_history) > 0){
        $this->Sale_model->save_sale_payment_edit_history($sale_payment_edit_history);
    }
    if(count($sale_product) > 0){
        $this->Sale_model->edit_sale_product($sale_product);
    }
    if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
    }else{
        $this->db->trans_commit();
        $this->session->set_flashdata('save_data', 'Invoice bill generated');
    }
    return redirect('Sale/sale_details/'.$idsale);
}
public function invoice_correction_report() {
    $q['tab_active'] = 'Report';
    $iduser = $_SESSION['id_users'];
    if($_SESSION['level'] == 1){
        $q['branch_data'] = $this->General_model->get_active_branch_data();
    }elseif($_SESSION['level'] == 3){
        $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
    }
    $this->load->view('report/invoice_correction_report', $q);
}
public function ajax_invoice_correction_report(){
    $idbranch = $this->input->post('idbranch');
    $datefrom = $this->input->post('datefrom');
    $dateto = $this->input->post('dateto');
    $branches = $this->input->post('branches');

    $invoice_correction_data = $this->Sale_model->get_invoice_correction_report($idbranch, $datefrom, $dateto, $branches);
//        die('<pre>'.print_r($invoice_correction_data,1).'</pre>');
    if(count($invoice_correction_data) > 0){ ?>
        <thead>
            <th>Sr</th>
            <th>Entry Time</th>
            <th>Invoice Time</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <th>Old Basic</th>
            <th>Corrected Basic</th>
            <th>Old Discount</th>
            <th>Corrected Discount</th>
            <th>Old final total</th>
            <th>Corrected final total</th>
            <th>Edit Type</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($invoice_correction_data as $inv){ ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $inv->entry_time ?></td>
                    <td><?php echo $inv->invoice_date ?></td>
                    <td><?php echo $inv->branch_name ?></td>
                    <td><?php echo $inv->inv_no ?></td>
                    <td><?php echo $inv->old_basic_total ?></td>
                    <td><?php echo $inv->basic_total ?></td>
                    <td><?php echo $inv->old_discount_total ?></td>
                    <td><?php echo $inv->discount_total ?></td>
                    <td><?php echo $inv->old_final_total ?></td>
                    <td><?php echo $inv->final_total ?></td>
                    <td><?php echo $inv->edit_types ?></td>
                    <td><a class="btn btn-floating" href="<?php echo base_url('Sale/invoice_correction_details/'.$inv->id_sale_edit_history) ?>" target="_blank"><i class="fa fa-info"></i></a></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
        <?php }
    }
    public function invoice_correction_details($id_sale_edit_history){
        $q['tab_active'] = 'Report';
        $q['sale_product_edit'] = $this->Sale_model->get_sale_product_edit_history_byid($id_sale_edit_history);
        $q['sale_payment_edit'] = $this->Sale_model->get_sale_payment_edit_history_byid($id_sale_edit_history);
        $q['invoice_edit_details'] = $this->Sale_model->get_invoice_edit_details_byid($id_sale_edit_history);
        $this->load->view('report/invoice_correction_details', $q);
    }

    public function sale_einvoice_report(){
        $q['tab_active'] = 'Report';

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

        $this->load->view('sale/sale_einvoice_report', $q);  
    }
    public function ajax_get_einvoice_sale_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        
        $sale_data = $this->Sale_model->ajax_get_e_invoice_sale_data_byfilter($from, $to, $idcompany, $idpcat, $idbrand);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
                <thead class="fixedelementtop">
                    <th>Sr</th>
                    <th>Invoice No</th>
                    <th>Invoice Type</th>
                    <th>Invoice Date</th>
                    <th>Sale Type</th>
                    <th>Customer GST No</th>
                    <th>Mailing Name(Bill to)</th>
                    <th>Address</th>
                    <th>Place</th>
                    <th>Pincode </th>
                    <th>Branch</th>
                    <th>Supplier State </th>
                    <th>Country</th>
                    <th>Quantity</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>HSN</th>
                    <!--<th>Price</th>-->
                    <th>Discount Amount</th>
                    <th>Base Price</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Round Up</th>
                    <th>Total Amount Per Quantity</th>
                    <th>Month</th>
                    <th>GST Tax</th>
                    <th>Buyer/Consinee State</th>
                    <th>Is Mop</th>
                    <th>Status</th>
                    <th>Old Amount </th>
                    <th>Actual discont </th>
                    <th>Actual Mop </th>
                    <th>Actual Entered Amount </th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $total =0; $total_base=0;$total_cgst=0;$total_sgst=0;$total_igst=0; foreach ($sale_data as $sale) { 
//                    if($sale->date <= '2021-02-26'){
                        if($sale->is_mop == 1){
                            if($sale->total_amount > $sale->mop){
                                $sale_amount = $sale->total_amount;
                            }else{
                                $sale_amount = $sale->mop;
                            }
                        }else{
                            $sale_amount = $sale->total_amount;   
                        }
//                    }else{
//                        if($sale->is_mop == 0){
//                            $sale_amount = $sale->total_amount;   
//                        }else{
//                            $sale_amount = $sale->mop;
//                        }
//                    }
                        
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
                            <td><a href="<?php echo base_url()?>Sale/sale_details/<?php echo $sale->idsale?>" style="color: #3333ff;cursor: pointer;" target="_blank"><?php echo $sale->inv_no;?></a></td>
                            <td><?php echo 'Regular' ?></td>
                            <td><?php echo date('d/m/Y', strtotime($sale->entry_time)) ?></td>
                            <td><?php echo 'Sales ';echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per.'%' ?></td>
                            <td><?php echo $sale->cust_gst_no ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_address; ?></td>
                            <td><?php echo $sale->customer_city ?></td>
                            <td><?php echo $sale->customer_pincode; ?></td>
                            <td><?php echo $sale->branch_name; ?></td>
                            <td><?php echo $sale->branch_state_name; ?></td>
                            <td><?php echo 'India' ?></td>
                            <td><?php echo $sale->sqty; ?></td>
                            <td><?php echo $sale->product_name;  ?></td>
                            <td><?php echo $sale->category_name ?></td>
                            <td><?php echo $sale->hsn; ?></td>
                            <!--<td><?php echo $sale->price; ?></td>-->
                            <td><?php if($sale->is_mop == 1){ echo ($sale_amount - $sale->total_amount);}else{ echo '0';} ?></td>
                            <td><?php $total_base = $total_base + $taxable; echo number_format($taxable,2) ?></td>
                            <td><?php $total_cgst = $total_cgst + $cgst; echo number_format($cgst,2);  ?></td>
                            <td><?php echo number_format($cgst,2);  ?></td>
                            <td><?php $total_igst = $total_cgst + $igst_amount; echo number_format($igst_amount,2); ?></td>
                            <td></td>
                            <td><?php $total = $total + $sale_amount; echo $sale_amount; ?></td>
                            <td><?php echo date('M-Y', strtotime($sale->entry_time)) ?></td>
                            <td><?php echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per ?></td>
                            <td><?php echo $sale->customer_state; ?></td>
                            <td><?php echo $sale->is_mop; ?></td>
                            <td><?php  $ac = $sale->total_amount + $sale->discount_amt; if($sale->is_mop == 1){ if($ac != $sale->mop){ echo 'revised'; } }elseif ($sale->discount_amt > 0){ echo 'Remove Discount'; }?></td>
                            <td><?php  echo $sale->total_amount;  ?></td>
                            <td><?php echo $sale->discount_amt;  ?></td>
                            <td><?php echo $sale->mop;  ?></td>
                            <td><?php echo $sale->price;  ?></td>
                            <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" target="_blank" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                        </tr>
                    <?php } ?>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo number_format($total_base,2); ?></b></td>
                    <td><b><?php echo number_format($total_cgst,2); ?></b></td>
                    <td><b><?php echo number_format($total_cgst,2); ?></b></td>
                    <td><b><?php echo number_format($total_igst,2); ?></b></td>
                    <td></td>
                    <td><b><?php echo number_format($total,2); ?></b></td>
                    <td></td>
                </tr>-->
            </tbody>
        </table>
        <?php 
    }else{ ?>
     <script>
         $(document).ready(function (){
          alert("Data Not Found"); 
      });
  </script> 
<?php }
}

public function tally_sale_report(){
    $q['tab_active'] = 'Report';

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

        $this->load->view('sale/tally_sale_report', $q);  
    }
    
    public function ajax_get_tally_sale_report_priya(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        
        $sale_data = $this->Sale_model->ajax_get_tally_sale_product_data_byfilter($from, $to, $idcompany, $idpcat, $idbrand);
        $payment_mode_data = $this->General_model->get_payment_mode_data();
        $daybook_report = $this->Sale_model->ajax_get_tally_sale_payment($from,$to); 
//        die('<pre>'.print_r($daybook_report,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
                <thead class="fixedelementtop">
                    <th>Sr</th>
                    <th>Invoice No</th>
                    <th>Sale Type</th>
                    <th>Invoice Date</th>
                    <th>Customer Name</th>
                    <th>Customer Mobile</th>
                    <th>Customer GST No</th>
                    <th>Type</th>
                    <th>State</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Base Price</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Round Up</th>
                    <th>Total Amount</th>
                    <?php foreach($payment_mode_data as $pmode){ ?>
                        <th><?php echo $pmode->payment_mode ?></th>
                        <?php if($pmode->tranxid_type != ''){ echo '<th>'.$pmode->tranxid_type.'</th>'; } ?>
                    <?php }?>
                    <th>GST %</th>
                    <th>CGST</th>
                    
                </thead>
                <tbody class="data_1">
                    <?php  $sr=1; $totalround = 0; $total =0; $total_base=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$cmobine=0; $saleid=0;
                    foreach ($sale_data as $sale) { 

                        if($saleid != 0 && $saleid == $sale->idsale){
                            $cmobine = 1;
                        }
                        $saleid = $sale->idsale;
//                    if($sale->date <= '2021-02-26'){
                        if($sale->is_mop == 1){
                            if($sale->total_amount > $sale->mop){
                                $sale_amount = $sale->total_amount;
                            }else{
                                $sale_amount = $sale->mop;
                            }
                        }else{
                            $sale_amount = $sale->total_amount;   
                        }
//                    }else{
//                        if($sale->is_mop == 0){
//                            $sale_amount = $sale->total_amount;   
//                        }else{
//                            $sale_amount = $sale->mop;
//                        } 
//                    }
                        
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
                            <td><a href="<?php echo base_url()?>Sale/sale_details/<?php echo $sale->idsale?>" style="color: #3333ff;cursor: pointer;" target="_blank"><?php echo $sale->inv_no;?></a></td>
                            <td><?php echo 'Sales'; echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per.'%' ?></td>
                            <td><?php echo date('d/m/Y', strtotime($sale->entry_time)) ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->cust_gst_no ?></td>
                            <td><?php if($sale->cust_gst_no == ''){echo 'UNREGISTERED'; }else{ echo 'Regular'; }?></td>
                            <td><?php echo $sale->branch_state_name; ?></td>
                            <td><?php echo $sale->brand_name; ?></td>
                            <td><?php echo $sale->product_name;  ?></td>
                            <td><?php echo $sale->sqty; ?></td>
                            <td><?php if($sale->is_mop == 1){ echo ($sale_amount - $sale->total_amount);}else{ echo '0';} ?></td>
                            <td><?php $total_base = $total_base + $taxable; echo number_format($taxable,3) ?></td>
                            <td><?php $total_cgst = $total_cgst + $cgst; echo number_format($cgst,3);  ?></td>
                            <td><?php echo number_format($cgst,3);  ?></td>
                            <td><?php $total_igst = $total_cgst + $igst_amount; echo number_format($igst_amount,3); ?></td>
                            <td><?php $tobase = $cgst + $cgst + $igst_amount + $taxable;   $trount = number_format(($sale_amount - $tobase),2);  echo $trount; // $totalround = $totalround + $trount;?></td>
                            <td><?php $total = $total + $sale_amount; echo $sale_amount; ?></td>
                            <?php foreach ($daybook_report as $daybook) {
                                if($sale->idsale == $daybook->idsale){ 
                                    for($i=0; $i < count($payment_mode_data); $i++){
                                        ?>
                                        <td><?php if($cmobine == 0){  $mdn = $payment_mode_data[$i]->payment_mode; $mdntrans = 'trans'.$payment_mode_data[$i]->payment_mode; 
                                        echo $daybook->$mdn; } ?>
                                    </td>
                                    <?php if($payment_mode_data[$i]->tranxid_type != NULL){ ?>
                                        <td><?php  echo $daybook->$mdntrans; ?></td>
                                    <?php }  ?>
                                <?php }  ?>
                            <?php  } }  ?>
                            <td><?php echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per ?></td>
                            <td><?php echo $sale->cgst_per ?></td>
                        </tr>
                    <?php } ?>
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
                    <th>CGST(%)</th>
                    <th>SGST(%)</th>
                    <th>IGST(%)</th>
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
                            <td><?php $total = $total + $sale_amount; echo $sale_amount; ?></td>
                            <td><?php echo $sale->cgst_per + $sale->sgst_per + $sale->igst_per ?></td>
                            <td><?php echo 'Output CGST '. $sale->cgst_per.'%' ?></td>                    
                            <td><?php echo 'Output SGST '. $sale->sgst_per.'%' ?></td>                    
                            <td><?php echo 'Output IGST '. $sale->igst_per.'%' ?></td>                    
                            <td><?php echo ($sale_amount-$sale->total_amount) ?></td>    
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


public function gross_revenue_report(){
    $q['tab_active'] = 'Report';
    
    if($this->session->userdata('level') == 1){
        $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('sale/gross_revenue_report', $q);  
    }
    
    public function ajax_get_gross_revenue_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idzone = '';
        $idsaletype = array(0,1);
        $sale_data = $this->Sale_model->ajax_get_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand,$idsaletype, $idzone);
        $sale_return_data = $this->Sale_model->ajax_get_sale_return_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone);
//        die('<pre>'.print_r($sale_return_data,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table class="table table-bordered table-striped table-condensed table-info" id="gross_revenue_report">
                <thead class="fixedelement">
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Partner Type</th>
                    <th>Branch Category</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>GSTIN</th>
                    <th>Sale Promotor</th>
                    <th>Imei</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>MOP</th>
                    <th>MRP</th>
                    <th>Sale Amount</th>
                    <th>NLC Amount</th>
                    <th>Revenue Amount</th>
                    <th>Revenue Percentage</th>
                    <th>Info</th>
                    <!--<th>Print</th>-->
                </thead>
                <tbody class="data_1">
                    <?php $trevenue = 0; $landing=0; $total=0; $sper=0; $tsper=0; $reper = 0; $treper = 0; foreach ($sale_data as $sale) { ?>
                        <tr>
                            <td><?php echo $sale->date ?></td>
                            <td><?php echo $sale->inv_no ?></td>
                            <td><?php echo $sale->branch_name ?></td>
                            <td><?php echo $sale->zone_name ?></td>
                            <td><?php echo $sale->partner_type ?></td>
                            <td><?php echo $sale->branch_category_name ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->customer_gst ?></td>
                            <td><?php echo $sale->user_name ?></td>
                            <td><?php echo $sale->imei_no ?></td>
                            <td><?php echo $sale->product_category_name ?></td>
                            <td><?php echo $sale->brand_name ?></td>
                            <td><?php echo $sale->full_name ?></td>
                            <td><?php echo $sale->mop; ?></td>
                            <td><?php echo $sale->mrp;?></td>
                            <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; //sale amount ?></td>
                            <td><?php echo $sale->nlc_price; $landing = $landing + $sale->nlc_price; //landing amount ?></td>
                            <td><?php  $revenue = $sale->total_amount - $sale->nlc_price; echo $revenue; $trevenue = $trevenue + $revenue;?></td>
                            <td><?php if($sale->nlc_price != 0){ $sper =  round(($revenue * 100 /$sale->nlc_price),2); }else{ $sper = 0;} echo $sper; $tsper = $tsper + $sper; ?></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                            <!--<td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                        </tr>
                    <?php } ?>
                    <tr>
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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                        <td><b><?php echo $landing; ?></b></td>
                        <td><b><?php echo $trevenue; ?></b></td>
                        <td><b><?php  if($landing != 0){ echo round(($trevenue *100 /$landing),2);}else{ echo 0;} ; ?></b></td>
                        <td></td>
                        <!--<td></td>-->
                    </tr>
                    <tr>
                        <td colspan="21" style="background-color: #9999ff;color: #FFFFFF"> <b> Sales Return </b> </td>
                        
                    </tr>
                    <?php $strevenue = 0; $tlanding=0; $stotal=0; foreach ($sale_return_data as $sale_return) { ?>
                        <tr>
                            <td><?php echo $sale_return->date ?></td>
                            <td><?php echo $sale_return->sales_return_invid ?></td>
                            <td><?php echo $sale_return->branch_name ?></td>
                            <td><?php echo $sale->zone_name ?></td>
                            <td><?php echo $sale->partner_type ?></td>
                            <td><?php echo $sale->branch_category_name ?></td>
                            <td><?php echo $sale_return->customer_fname.' '.$sale_return->customer_lname ?></td>
                            <td><?php echo $sale_return->customer_contact ?></td>
                            <td><?php echo $sale_return->customer_gst ?></td>
                            <td><?php echo $sale_return->user_name ?></td>
                            <td><?php echo $sale_return->imei_no ?></td>
                            <td><?php echo $sale_return->product_category_name ?></td>
                            <td><?php echo $sale_return->brand_name ?></td>
                            <td><?php echo $sale_return->full_name ?></td>
                            <td><?php echo $sale_return->mop; ?></td>
                            <td><?php echo $sale_return->mrp;?></td>
                            <td><?php echo $sale_return->total_amount; $stotal = $stotal + $sale_return->total_amount; ?></td>
                            <td><?php  $sale_landing = 0; echo $sale_return->nlc_price;  $tlanding = $tlanding + $sale_return->nlc_price; 
                            if($sale_return->nlc_price != 0){ $sale_landing = $sale_return->nlc_price; }else{ $sale_landing = 0;    } ?>
                        </td>
                        <td><?php  $revenue = $sale_return->total_amount - $sale_landing; echo '-'.$revenue; $strevenue = $strevenue + $revenue;?></td>
                        <td><?php if($sale_landing != 0){ $sper =  round(($revenue * 100 /$sale_landing),2); }else{ $sper = 0;} echo '-'.$sper; $tsper = $tsper + $sper; ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Sales_return/sales_return_details/'.$sale_return->id_salesreturn) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                        <!--<td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                    </tr>
                <?php } ?>
                <tr>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo $stotal; ?></b></td>
                    <td><b><?php echo $tlanding; ?></b></td>
                    <td><b><?php echo '-'.$strevenue; ?></b></td>
                    <td><b><?php if($tlanding !=0){ echo '-'.round(($strevenue * 100 /$tlanding),2);}else{ echo 0;} ?></b></td>
                    <td></td>
                </tr>
                <tr>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Todays Sale</b></td>
                    <td><b><?php echo $total-$stotal; ?></b></td>
                    <td><b><?php echo $landing-$tlanding; ?></b></td>
                    <td><b><?php echo $trevenue-$strevenue; ?></b></td>
                    <td><b><?php if(($landing-$tlanding ) != 0){ echo round((($trevenue-$strevenue)*100)/( $landing-$tlanding),2); } else { echo 0; } ?></b></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php 
    }else{ ?>
     <script>
         $(document).ready(function (){
          alert("Data Not Found"); 
      });
  </script> 
<?php }
}

public function sale_analysis_report(){
    $q['tab_active'] = 'Report';
    $q['product_category'] = $this->General_model->get_product_category_data();
    $q['brand_data'] = $this->General_model->get_active_brand_data();
    $this->load->view('sale/sale_analysis_report', $q);  
}

public function ajax_get_day_sale_analysis_report(){
    $days = $this->input->post('days');
    $idpcat = $this->input->post('idpcat');
    $allpcat = $this->input->post('allpcat');
    $idbrand = $this->input->post('idbrand');
    $allbrand = $this->input->post('allbrand');
    $first = date('Y-m-d');
    $last =  date('Y-m-d', strtotime('-'.$days.' days'));
//        die($last);
    $brand_data = $this->General_model->get_active_brand_data();
    $product_category = $this->General_model->get_product_category_data();
    $stockdata = $this->Sale_model->ajax_stock_analysis_report($idpcat, $idbrand, $allpcat, $allbrand);
    $saledata = $this->Sale_model->ajax_sale_analysis_report($first, $last, $idpcat, $idbrand, $allpcat, $allbrand);
//        die('<pre>'. print_r($stockdata,1).'</pre>');
    ?>
    <table class="table table-bordered" id="sale_analysis_report">
     <thead style="background-color: #75c1f9" class="fixedelementtop">
        <th>Product Category</th>
        <th>Brand</th>
        <th>Total Stock Volume</th>
        <th>Total Stock Value</th>
        <th>Total Sale Stock Volume</th>
        <th>Total Sale Stock Value</th>
        <th>Volume Stock Days</th>
        <th>Value Stock Days</th>
    </thead>
    <tbody class="data_1">
     <?php  
     foreach ($product_category as $pcat){ 
        foreach($brand_data as $bdata){ 
            $sumall=0; $stqty=0;$stamount=0; $saqty=0;$saamount=0; $stock_volume_days=0; $stock_value_days=0;
            foreach($stockdata as $stock){
                if($stock->idproductcategory == $pcat->id_product_category && $stock->idbrand == $bdata->id_brand){
                    $stqty = $stock->stock_qty;
                    $stamount = $stock->stock_amount;
                }
            }
            foreach ($saledata as $sale){
               if($sale->idproductcategory == $pcat->id_product_category && $sale->idbrand == $bdata->id_brand){
                $saqty = $sale->sale_qty;
                $saamount = $sale->sale_amount;
            }
        }
        $sumall = $stqty+$saqty;
        if($stqty != 0){
//                                $stock_volume_days  = ($stqty/$saqty) * $days;
            $stock_volume_days  = ($saqty/$stqty) * $days;
        }
        if($stamount != 0){
//                                $stock_value_days  = ($stamount/$saamount) * $days;
            $stock_value_days  = ($saamount/$stamount) * $days;
        }
        
        if($sumall > 0){ ?>
            <tr>
                <td><?php echo $pcat->product_category_name?></td>
                <td><?php echo $bdata->brand_name?></td>
                <td><?php echo $stqty; ?></td>
                <td><?php echo $stamount; ?></td>
                <td><?php echo $saqty; ?></td>
                <td><?php echo $saamount; ?></td>
                <td><?php echo round($stock_volume_days); ?></td>
                <td><?php echo round($stock_value_days); ?></td>
            </tr>
        <?php } } } ?>
    </tbody>
</table>
<?php 
}

public function ageing_sale_report(){
    $q['tab_active'] = 'Sale';
    
    if($this->session->userdata('level') == 1){
        $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('sale/ageing_sale_report', $q);  
    }
    
    public function ajax_get_ageing_sale_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $sale_data = $this->Sale_model->ajax_get_ageing_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
                <thead class="fixedelementtop">
                    <th>Entry Time</th>
                    <th>Invoice No</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Partner type</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>GSTIN</th>
                    <th>Imei</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>MOP</th>
                    <th>MRP</th>
                    <th>Amount</th>
                    <th>Sale Promotor Brand</th>
                    <th>Sale Promotor</th>
                    <th>Info</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; foreach ($sale_data as $sale) {
                        $userdata = $this->Sale_model->ajax_get_brand_name_byiduser($sale->id_users);
                        ?>
                        <tr>
                            <td><?php echo date('d-m-Y h:i a', strtotime($sale->entry_time)) ?></td>
                            <!--<td><?php // echo $sale->date ?></td>-->
                            <td><?php echo $sale->inv_no ?></td>
                            <td><?php echo $sale->branch_name ?></td>
                            <td><?php echo $sale->zone_name ?></td>
                            <td><?php echo $sale->partner_type ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->customer_gst ?></td>
                            <td>'<?php echo $sale->imei_no ?></td>
                            <td><?php echo $sale->product_category_name ?></td>
                            <td><?php echo $sale->brand_name ?></td>
                            <td><?php echo $sale->product_name ?></td>
                            <td><?php echo $sale->mop ?></td>
                            <td><?php echo $sale->mrp ?></td>
                            <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; ?></td>
                            <td><?php if($userdata){echo $userdata->user_brand_name; }?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                        </tr>
                    <?php } ?>
                    <tr>
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
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <?php 
        }else{ ?>
         <script>
             $(document).ready(function (){
              alert("Data Not Found"); 
          });
      </script> 
  <?php }

}

public function corporate_sale_report(){
    $q['tab_active'] = 'Report';
    
    if($this->session->userdata('level') == 1){
        $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('sale/corporate_sale_report', $q);  
    }
    public function ajax_get_corporate_sale_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $sale_data = $this->Sale_model->ajax_get_corporate_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if(count($sale_data) >0){
            ?>
            <table id="corporate_sale_report" class="table table-bordered table-striped table-condensed table-info">
                <thead class="fixedelementtop">
                    <th>Entry Time</th>
                    <th>Invoice No</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Partner type</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>GSTIN</th>
                    <th>Imei</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>MOP</th>
                    <th>MRP</th>
                    <th>Amount</th>
                    <th>Sale Promotor Brand</th>
                    <th>Sale Promotor</th>
                    <th>Info</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; foreach ($sale_data as $sale) {
                        $userdata = $this->Sale_model->ajax_get_brand_name_byiduser($sale->id_users);
                        ?>
                        <tr>
                            <td><?php echo date('d-m-Y h:i a', strtotime($sale->entry_time)) ?></td>
                            <!--<td><?php // echo $sale->date ?></td>-->
                            <td><?php echo $sale->inv_no ?></td>
                            <td><?php echo $sale->branch_name ?></td>
                            <td><?php echo $sale->zone_name ?></td>
                            <td><?php echo $sale->partner_type ?></td>
                            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                            <td><?php echo $sale->customer_contact ?></td>
                            <td><?php echo $sale->customer_gst ?></td>
                            <td>'<?php echo $sale->imei_no ?></td>
                            <td><?php echo $sale->product_category_name ?></td>
                            <td><?php echo $sale->brand_name ?></td>
                            <td><?php echo $sale->product_name ?></td>
                            <td><?php echo $sale->mop ?></td>
                            <td><?php echo $sale->mrp ?></td>
                            <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; ?></td>
                            <td><?php echo $userdata->user_brand_name; ?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                            <td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                        </tr>
                    <?php } ?>
                    <tr>
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
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <?php 
        }else{ ?>
         <script>
             $(document).ready(function (){
              alert("Data Not Found"); 
          });
      </script> 
  <?php }

}


public function customer_data_list() {
    $q['tab_active'] = '';
    if($_SESSION['level'] == 1){
        $q['branch_data'] = $this->General_model->get_active_branch_data();
    }elseif($_SESSION['level'] == 3){
        $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
    }
    
    $q['zone_data'] = $this->General_model->get_active_zone();
    
    $q['customer_list'] = $this->Sale_model->get_customer_list();
    $this->load->view('sale/customer_list_data',$q);
}
public function ajax_get_customer_list_byidbranch_date(){
    $from = $this->input->post('from');
    $to = $this->input->post('to');
    $idbranch = $this->input->post('idbranch');
    $branches = $this->input->post('branches');
    $price_cat = $this->Report_model->get_price_category_lab_data();
//        die(print_r($_POST));
    $customer_list = $this->Sale_model->get_customer_list_byidbranch_date($idbranch,$branches,$from,$to);
//        die('<pre>'.print_r($customer_list,1).'</pre>');
    
    if(count($customer_list) > 0){ ?>
       <table id="customer_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px;">
        <thead class="bg-info fixheader">
            <th>Sr</th>
            <th>Customer Name</th>
            <th>Contact</th>
<!--                    <th>Email Id</th>
    <th>GSTIN</th>-->
    <th>Branch</th>
    <th>Address</th>
    <th>Pincode</th>
    <th>City</th>
    <th>District</th>
    <th>State</th>
    <th>Price Category</th>
    <th>Product</th>
</thead>
<tbody class="data_1">
    <?php $i=1; foreach ($customer_list as $customer){
       $sale_data = $this->Sale_model->ajax_get_sale_data_by_idcustomer($customer->id_customer);?>
       <tr>
        <td><?php echo $i ?></td>
        <td><?php echo $customer->customer_fname.' '.$customer->customer_lname ?></td>
        <td><?php echo $customer->customer_contact ?></td>
<!--                        <td><?php echo $customer->customer_email ?></td>
    <td><?php echo $customer->customer_gst ?></td>-->
    <td><?php echo $customer->branch_name ?></td>
    <td><?php echo $customer->customer_address ?></td>
    <td><?php echo $customer->customer_pincode ?></td>
    <td><?php echo $customer->customer_city ?></td>
    <td><?php echo $customer->customer_district ?></td>
    <td><?php echo $customer->customer_state ?></td>
    <td><?php foreach($price_cat as $pcat){
        if($sale_data){
            if($sale_data->final_total >= $pcat->min_lab && $sale_data->final_total <= $pcat->max_lab) {
                echo $pcat->lab_name; 
            } 
        }
    }?></td>
    <td>
        <?php if($sale_data){ echo $sale_data->product_name; } ?>
    </td>
</tr>
<?php $i++; } ?>
</tbody>
</table>
<?php }
}

public function ajax_get_customer_list_byidzone_date(){
    $from = $this->input->post('from');
    $to = $this->input->post('to');
    $idzone = $this->input->post('idzone');
    $zones = $this->input->post('zones');
    $price_cat = $this->Report_model->get_price_category_lab_data();
    $customer_list = $this->Sale_model->get_customer_list_byidzone_date($idzone,$zones,$from,$to);
//        die('<pre>'.print_r($customer_list,1).'</pre>');
    
    if(count($customer_list) > 0){ ?>
       <table id="customer_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px;">
        <thead class="bg-info fixheader">
            <th>Sr</th>
            <th>Customer Name</th>
            <th>Contact</th>
<!--                    <th>Email Id</th>
    <th>GSTIN</th>-->
    <th>Branch</th>
    <th>Address</th>
    <th>Pincode</th>
    <th>City</th>
    <th>District</th>
    <th>State</th>
    <th>Price Category</th>
    <th>Product</th>
</thead>
<tbody class="data_1">
    <?php $i=1; foreach ($customer_list as $customer){ 
        $sale_data = $this->Sale_model->ajax_get_sale_data_by_idcustomer($customer->id_customer);?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $customer->customer_fname.' '.$customer->customer_lname ?></td>
            <td><?php echo $customer->customer_contact ?></td>
<!--                        <td><?php echo $customer->customer_email ?></td>
    <td><?php echo $customer->customer_gst ?></td>-->
    <td><?php echo $customer->branch_name ?></td>
    <td><?php echo $customer->customer_address ?></td>
    <td><?php echo $customer->customer_pincode ?></td>
    <td><?php echo $customer->customer_city ?></td>
    <td><?php echo $customer->customer_district ?></td>
    <td><?php echo $customer->customer_state ?></td>
    <td><?php foreach($price_cat as $pcat){
        if($sale_data){
            if($sale_data->final_total >= $pcat->min_lab && $sale_data->final_total <= $pcat->max_lab) {
                echo $pcat->lab_name; 
            } 
        }
    }?></td>
    <td>
        <?php if($sale_data){ echo $sale_data->product_name; } ?>
    </td>
</tr>
<?php $i++; } ?>
</tbody>
</table>
<?php }
}
public function sale_token() {
    $q['tab_active'] = 'Sale';
    $idbranch = $this->session->userdata('idbranch');
        $q['sale_token_data'] = $this->Sale_model->get_pending_sale_token($idbranch); // cash closure data
        $this->load->view('sale/sale_token_data', $q);
    }
    public function ajax_cancel_token() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idrow = $this->input->post('idrow');
        $cancel_remark = $this->input->post('cancel_remark');
        $datetime = date('Y-m-d H:i:s');
        $update_token = array(
            'cancel_remark' => $cancel_remark,
            'status' => 2,
            'update_time' => $datetime,
        );
        $this->Sale_model->update_sale_token_byid($idrow, $update_token);
        $q['result'] = 'Success';
        echo json_encode($q);
    }
    public function save_pdf(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $inv_id = $this->input->post('inv_id');
        $inv_no = $this->input->post('inv_no');
        $do_id = $this->input->post('do_id');
        $inv_date = $this->input->post('inv_date');
        $idbranch = $this->input->post('idbranch');
        $customer_contact = $this->input->post('customer_contact');
        $name="inv_".$idbranch.'_'.$inv_id;
        $month = date('F');
        $year = date('Y');
        if (!file_exists('Invoices/'.$month.$year)) {
            mkdir('Invoices/'.$month.$year, 0777, true);
        }
        if (!file_exists('Invoices/'.$month.$year."/".$name.'.pdf')) {
            if($_FILES['mypdf'] != ''){
                $prodlink = 'Invoices/'.$month.$year;
                $image = preg_replace('/\s+/', '', strtolower($name));
                $newName = $image.".pdf"; 
                $config = array(
                    'image_library' => 'gd2',
                    'upload_path' => $prodlink,
                    'allowed_types' => '*',
                    'file_name' => $newName,                
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                
                if($this->upload->do_upload('mypdf')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;  
                    
                    $ftp_server = "148.72.207.120";
                    $ftp_username="ssmobile";
                    $ftp_userpass="Ssecom@cpanel";
                    $ftp_conn = ftp_connect($ftp_server);
                    
                    
                    if ($ftp_conn){
                        $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);    
                        $dir = 'public_html/Invoices/'.$month.$year;
                        if (ftp_mkdir($ftp_conn, $dir))
                        {
                    //echo "Successfully created $dir";
                            print_r("Successfully created $dir");  
                        }else{
                            print_r("Successfully Not created $dir");  
                        }
                        ftp_pasv($ftp_conn, true);
                        ftp_put($ftp_conn,$dir.'/'.$newName, $path, FTP_BINARY);
                        ftp_close($ftp_conn);
                    }
                    unlink($path);
//                    $b64Doc = chunk_split(base64_encode(file_get_contents($path)));   
//                    $d=array();
//                    $data=array();
//                    $data['id']="";                    
//                    $data['files']=array();
//                    $fi=array();
//                    $fi['name']=$name;
//                    $fi['fileType']="files.pdf";
//                    $fi['docType']="Invoice";
//                    $fi['body']=$b64Doc;
//                   
//                    array_push($data['files'], $fi);
//                    $data['opportunityName']=$do_id;
//                    $data['invoiceNumber']=$inv_no;
//                    $data['invoiceDate']=$inv_date;
//                    $data['txt1']="";
//                    $data['txt2']="";
//                    $data['txt3']="";
//                    array_push($d, $data);
                /*   
//                    if($do_id){
//                        $bfl_data = $this->Sale_model->upload_bfl($d);                        
//                    }
//                    if($bfl_data['status']['responseCode']=='0'){
//                        $bfldata = $this->Sale_model->update_sale($inv_id,array('bfl_upload'=>1));
//                        echo 1;
//                    }
                  */
                
            }else{
                echo 1;
            }
        }
          //  $this->sendsms($customer_contact, 'Invoices/'.$month.$year.'/'.$name.'.pdf');
//            $this->sendsms('8329374968', 'Invoices/'.$month.$year.'/'.$name.'.pdf');
    }else{
        echo 1;
    }
    $sale_id = $inv_id;
       // redirect('invoice_whatspp_api_routes/'.$sale_id);
}
function sendsms($mobileno, $path){    
//        $this->load->model('Api_Model');
//        $longurl=base_url().$path;                
//        $url=$this->Api_Model->short_url($longurl);                
//        $message = "Dear Customer,%0aThank you for shopping with us. Download you invoice from below link.%0a".$url['shortLink'].'%0a- SS MOBILE';
//        $message = str_replace(' ', '%20', $message); // replace all spaces with %20 from message                
//
//        $baseurl_http='http://login.smsozone.com/api/mt/SendSMS?user=sscommunications&password=sscommunications@7654321&senderid=SSMOBS&channel=Trans&DCS=0&flashsms=0&number='.$mobileno.'&text='.$message.'&route=2069';
//
//        $ch=curl_init($baseurl_http);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response=curl_exec($ch);
//
//        curl_close($ch);  

}

    //********* Online Sale *****************

public function online_sale() {
    $q['tab_active'] = 'Sale';
    $idbranch = $_SESSION['idbranch'];
    $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($idbranch);
    $is_web_billing=$q['invoice_no']->online_billing;
      //  $q['payment_head'] = $this->General_model->get_active_payment_head();
    $q['payment_mode'] = $this->General_model->get_active_payment_mode();
    $q['payment_attribute'] = $this->General_model->get_payment_head_has_attributes();
    $q['state_data'] = $this->General_model->get_state_data();
    $q['customer_formdata'] = $this->Customerloyalty_model->get_customer_formdata();
    $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
    $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
//        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
//        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
//        $q['last_date_entry'] = $this->Sale_model->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
    $q['allow_web_billing']=1;
    $q['var_closer'] = $this->verify_cash_closure();
    $date=date('Y-m-d',strtotime("-1 days"));
        $this->Transfer_model->wipe_out_pending_b2b_requests($date,$idbranch); // remove pending b2b request
        $idclaim = $this->input->get('idclaim');        
        if($idtoken = $this->input->get('idtoken')){
            $q['sale_token'] = $this->Sale_model->get_sale_token_byid($idtoken);
            if($q['sale_token']->status != 0){
                $this->session->set_flashdata('reject_data', $idtoken.' Sale token already used or rejected...');
                return redirect('Sale/sale_token');
            }
            $q['sale_token_product'] = $this->Sale_model->get_sale_token_product_byid($idtoken);
            $q['sale_token_payment'] = $this->Sale_model->get_sale_token_payment_byid($idtoken);
        }elseif($idclaim){
            $q['payment_received_data'] = $this->Sale_model->get_advanced_booking_byid_for_sale($idclaim);
            if($q['payment_received_data']->claim > 0){ 
                $this->session->set_flashdata('reject_data', 'Already claimed or refund to customer...');
                return redirect('Payment/recieve_advanced_payment');
            }
        }elseif($q['invoice_no']->online_billing==0){
            $q['allow_web_billing']=0;
        }       
        
         // ******branch wise payment head and credit/custody data *******
        
        $payment_head = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);    
        $credit_data = $this->General_model->get_branch_credit_data($idbranch);
        
        $credit_limit = $q['invoice_no']->credit_limit;
        $credit_days = $q['invoice_no']->credit_days;
        
        $last_date = date('Y-m-d', strtotime('-'.$credit_days.'days'));
        $overall_credit = $credit_data->credit_amount;
        $credit_date = $credit_data->credit_date;
        
        $credit_status = 1;
        if($overall_credit!=NULL && $credit_date!=NULL){
            if($credit_limit > $overall_credit && $last_date < $credit_date){
                $credit_status = 1;                
            }else{
                $credit_status = 0;                
            }
        }
        
        $head_status = 0;
        foreach($payment_head as $head){
            if($head->id_paymenthead == 6){
                $head_status = 1;  
            }
        }
            if($head_status == 1){ //Branch has credit/custudy payment head
                if($credit_status == 1){ //display credit/custudy payment head 
                    $q['payment_head'] = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);
                }else{
                    foreach($payment_head as $head) { //disable credit/custudy payment head 
                        if($head->id_paymenthead != 6){ 
                            $ids[] =  $head->id_paymenthead;
                        } 
                    }
                    $q['payment_head'] = $this->General_model->get_active_payment_head_by_headids($ids);    
                }
            }else{
                $q['payment_head'] = $this->General_model->get_branch_has_paymenthead_byidbranch($idbranch);
            } 
            
            $this->load->view('sale/online_sale',$q);
        }
        public function ajax_get_online_imei_details() {
         $imei = $this->input->post('imei');
         $idbranch = $this->input->post('idbranch'); 
         $skuvariant = $this->input->post('skuvariant'); 
         $idgodown = $this->input->post('idgodown'); 
         $is_dcprint = $this->input->post('is_dcprint'); 
         $sale_type = $this->input->post('sale_type');
//        die(print_r($_POST));
        // Quantity
         if($skuvariant){
            $models = $this->Sale_model->ajax_get_variant_byid_branch_godown_saletype($skuvariant, $idbranch, 1, $sale_type);
            if(count($models)){
                foreach($models as $model){
                    if($sale_type != 2){
                        $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                        if($ageing_data){
                            $ageing = 1;
                        }else{
                            $ageing =0;
                        }

                        $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                        if($focus_data){
                            $focus_status = 1;
                            $focus_amount = $focus_data->incentive_amount;
                        }else{
                            $focus_status = 0;
                            $focus_amount = 0;
                        }
                    }
                    if($sale_type == 2){ 
                        $model->dcprint = 0;
                        $model->mop = 1;
                        $model->landing = 1;
                        $model->is_gst = 1;
                        $model->id_stock = 0;
                        $model->idvendor = 1;
                        $model->qty = 0;
                        $ageing =0;
                        $focus_amount = 0;
                        $focus_status = 0;
                    }
                    if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                        if($is_dcprint == 0){
                            echo '1'; // previous is dc product
                        }else{
                            echo '2'; // previous is invoice product
                        }
                    }else{ $amount_diff = $model->mop - $model->landing; ?>
                        <tr id="m<?php echo $model->id_stock ?>" class="skuqty_row">
                            <td>
                                <?php echo $model->product_name; ?>
                                <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                                <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">Online Godown</small>
                                <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                <?php if($sale_type == 0){ ?>
                                    <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" />
                                <?php }else{ ?>
                                    <input type="text" id="activation_code" name="activation_code[]" class="activation_code form-control input-sm" required="" placeholder="Activation/Reference Code" />
                                <?php } ?>
                            </td>
                            <td>
                                <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo NULL ?>" />
                                <?php if($sale_type == 0){ ?>
                                    <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" />
                                <?php }else{ ?>
                                    <input type="text" id="insurance_imei" name="insurance_imei[]" class="insurance_imei form-control input-sm" required="" placeholder="Insurance IMEI/SRNO" pattern="[a-zA-Z0-9\-]+" />
                                <?php } ?>
                            </td>
                            <td><?php echo $model->qty; ?></td>
                            <td><?php echo $model->mrp; ?></td>
                            <td><?php echo $model->mop; ?></td>
                            <td>
                                <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $sale_type ?>" />
                                <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />-->
                                <?php if($sale_type != 0){ ?>
                                    <input type="hidden" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                    1
                                <?php }else{ ?>
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($sale_type == 2){ ?>
                                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px"/>
                                <?php }else{ ?>
                                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px" max="<?php echo $model->qty; ?>"/>
                                <?php } ?>
                            </td>
                            <td>
                                <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                            </td>
                            <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px" <?php if($model->is_mop == 0){ ?> readonly="" <?php } ?> /></td>
                            <td>
                                <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                                <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                <?php echo $model->igst ?>%
                            </td>
                            <td>
                                <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                            </td>
                            <td>
                                <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                            </td>
                        </tr>
                    <?php }}}else{
                        echo '0';
                    }
        }else{ // IMEI/ SRNO
            $models = $this->Sale_model->ajax_online_stock_data_byimei_branch($imei, $idbranch);
            if(count($models)){
                foreach($models as $model){

                    $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($ageing_data){
                        $ageing = 1;
                    }else{
                        $ageing =0;
                    }
                    
                    $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                    if($focus_data){
                        $focus_status = 1;
                        $focus_amount = $focus_data->incentive_amount;
                    }else{
                        $focus_status = 0;
                        $focus_amount = 0;
                    }
                    
                    if($model->idgodown != 6){
                        echo '3'; // Other that Online Godown not accepted
                    }else{
                        if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                            if($is_dcprint == 0){ 
                                echo '1'; // previous is dc product
                            }else{ 
                                echo '2'; // previous is dc invoice
                            }
                        }else{ $amount_diff = $model->mop - $model->online_price; ?>
                            <tr id="m<?php echo $model->id_stock ?>" class="skuimei_row">
                                <td>
                                    <?php echo $model->product_name; ?>
                                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                    <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                    <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                    <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $model->idgodown ?>" />
                                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                    <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                    <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                    <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                    <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">Online Godown</small>
                                    <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo $imei ?>" />
                                    <?php echo $imei; ?>
                                    <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" />
                                    <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" />
                                </td>
                                <td>1</td>
                                <td><?php echo $model->mrp; ?></td>
                                <td><?php echo $model->mop; ?></td>
                                <td>
                                    <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->online_price ?>" />
                                    <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                    <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                    <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                                    <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                                    <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                                    <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                    <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />-->
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                                    <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $model->sale_type ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>
                                    <span id="spqty" class="spqty">1</span>
                                </td>
                                <td>
                                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                    <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                    <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                                </td>
                                <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" max="<?php echo $amount_diff ?>" step="0.001" style="width: 90px" <?php if($model->is_mop == 0){ ?> readonly="" <?php } ?>/></td>
                                <td>
                                    <input type="hidden" id="isgst" name="isgst[]" class="isgst"/>
                                    <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                    <?php echo $model->igst ?>%
                                </td>
                                <td>
                                    <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                    <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                                </td>
                                <td>
                                    <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                    <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                                </td>
                            </tr>
                        <?php }}}} else{
                            echo '0';
                        }
                    }
                }
                
                public function save_online_sale() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
                    $postedToken = filter_input(INPUT_POST, 'token');
                    if(!empty($postedToken)){
                        if($this->isTokenValid($postedToken)){
                            $this->db->trans_begin();
                            $idbranch = $this->input->post('idbranch');
                            $dcprint = $this->input->post('dcprint');
                            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
                            $invid = $invoice_no->invoice_no + 1; 
                            $y = date('y', mktime(0, 0, 0, 9 + date('m')));
                            $y1 = $y - 1;
                            if($dcprint[0] == 0){
                                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
                            }else{
                                $inv_no = 'DC'.$y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%04d', $invid);
                            }
        //        if($this->input->post('cash_closure') == 1){
        //            $date = date('Y-m-d', strtotime("+1 days"));
        //        }else{
        //        }
                            $date = date('Y-m-d');                
                            $datetime = date('Y-m-d H:i:s');
                            $idstate = $this->input->post('idstate');
                            $idcustomer = $this->input->post('idcustomer');
                            $cust_fname = $this->input->post('cust_fname');
                            $cust_lname = $this->input->post('cust_lname');
                            $cust_idstate = $this->input->post('cust_idstate');
                            $cust_pincode = $this->input->post('cust_pincode');
                            if($this->input->post('idsale_token') == ''){
                                $idsale_token = NULL;
                            }else{
                                $idsale_token = $this->input->post('idsale_token');
                            }
                $gst_type = 0; //cgst
                if($idstate != $cust_idstate){
                    $gst_type = 1; //igst
                }
                $remark = $this->input->post('remark');
                if($id_advance_payment_receive = $this->input->post('id_advance_payment_receive')){
                    if($remark){
                        $remark .= '<hr>';
                    }
                    $remark .= 'Advanced Booking: AdvPay/'. $invoice_no->branch_code.'/'.$id_advance_payment_receive.' ('.$this->input->post('booking_date').'), Amount: '.$this->input->post('booking_amount').'- '.$this->input->post('booking_payment_mode');
                }else{
                    $id_advance_payment_receive = NULL;
                }
                $data = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_idstate' => $cust_idstate,
                    'customer_pincode' => $cust_pincode,
                    'customer_contact' => $this->input->post('cust_mobile'),
                    'customer_address' => $this->input->post('address'),
                    'customer_gst' => $this->input->post('gst_no'),
                    'idsalesperson' => $this->input->post('idsalesperson'),
                    'basic_total' => $this->input->post('gross_total'),
                    'discount_total' => $this->input->post('final_discount'),
                    'final_total' => $this->input->post('final_total'),
                    'gst_type' => $gst_type,
                    'created_by' => $this->input->post('created_by'),
                    'corporate_sale' => 1,
                    'remark' => $remark,
                    'entry_time' => $datetime,
                    'dcprint' => $dcprint[0],
                    'idadvance_payment_receive' => $id_advance_payment_receive,
                    'idsaletoken' => $idsale_token,
                );
                $idsale = $this->Sale_model->save_sale($data);
                // Payment
                $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
                $credittype = $this->input->post('credit_type');
                $amount = $this->input->post('amount');
                $payment_type = $this->input->post('payment_type');
                $tranxid = $this->input->post('tranxid');
                $headattr = $this->input->post('headattr');
                $vin=array();
                foreach ($headattr as $idpayment_head => $attributedata){
                    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
                    $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
                    $attr_value=array();
                    for($m=0;$m<$mode_count;$m++){
                        foreach ($payment_attribute as $attr){
                            $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
                        }
                    }
                    for($j=0; $j < count($idpaymenthead); $j++){
                        if($idpaymenthead[$j] == $idpayment_head){
                            $vin[$j]=$attr_value;
                        }
                    }
                }
                $parr=array();
        //        die('<pre>'.print_r($vin,1).'</pre>');
        //        die('<pre>'.print_r($headattr,1).'</pre>');
                for($j=0; $j < count($idpaymenthead); $j++){
                    $received_amount=0;$pending_amt=$amount[$j];$received_entry_time=NULL;$payment_receive=0;
                    if($idpaymenthead[$j] == 1){
                        $received_amount = $amount[$j];
                        $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                        $srpayment = array(
                            'date' => $date,
                            'inv_no' => $inv_no,
                            'entry_type' => 1,
                            'idbranch' => $idbranch,
                            'idtable' => $idsale,
                            'table_name' => 'sale',
                            'amount' => $received_amount,
                        );
                        $this->Sale_model->save_daybook_cash_payment($srpayment);
                    }
        //            $received_amount=0;
        //            if($payment_type[$j] == 1){
        //                $received_amount = $amount[$j];
        //            }
                    $payment = array(
                        'date' => $date,
                        'idsale' => $idsale,
                        'amount' => $amount[$j],
        //                'received_amount' => $amount[$j],
                        'idpayment_head' => $idpaymenthead[$j],
                        'idpayment_mode' => $payment_type[$j],
                        'transaction_id' => $tranxid[$j],
                        'inv_no' => $inv_no,
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'created_by' => $this->input->post('created_by'),
                        'entry_time' => $datetime,
                        'received_amount' => $received_amount,
                        'received_entry_time'=>$received_entry_time,
                        'payment_receive' => $payment_receive
                    );
                    if(isset($vin[$j])>0){
                        $payment = array_merge($payment, $vin[$j]); 
                    }
        //            die('<pre>'.print_r($payment,1).'</pre>');
                    $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
                    if($credittype[$j] == 0){
                        $npayment = array(
                            'idsale_payment' => $id_sale_payment,
                            'inv_no' => $inv_no,
                            'idsale' => $idsale,
                            'date' => $date,
                            'idcustomer' => $idcustomer,
                            'idbranch' => $idbranch,
                            'amount' => $amount[$j],
                            'idpayment_head' => $idpaymenthead[$j],
                            'idpayment_mode' => $payment_type[$j],
                            'transaction_id' => $tranxid[$j],
                            'created_by' => $this->input->post('created_by'),
                            'entry_time' => $datetime,
                            'received_amount' => $received_amount,
                            'pending_amt' => $pending_amt,
                            'received_entry_time'=>$received_entry_time,
                            'payment_receive' => $payment_receive
                        );
                        if(isset($vin[$j])>0){
                            $npayment = array_merge($npayment, $vin[$j]); 
                        }
                        $this->Sale_model->save_payment_reconciliation($npayment);
                    }
        //            $parr[] = $payment;
                    
                    $qy = "SELECT payment_mode FROM payment_mode WHERE id_paymentmode = $payment_type[$j]";
                    $query = $this->db->query($qy);
                    $payment_mode = $query->result(); 
                    $pay_mode =  $payment_mode[0]->payment_mode;
                    $cuttomer_payment = array(
                        'idcustomer'=>$idcustomer,
                        'inv_no'=>$inv_no,
                        'inv_date'=>$date,
                        'payment_head'=>$this->input->post('headname['.$j.']'),
                        'payment_mode'=>$pay_mode,
                        'amount'=>$amount[$j],
                        'idtransaction'=>$tranxid[$j],
                    );
//                        echo '<pre>';
//                        print_r($cuttomer_payment);die;
                    $this->Customerloyalty_model->save_handset_payment_history($cuttomer_payment);
                    
                }
        //        die('<pre>'.print_r($parr,1).'</pre>');
                //Sale_product
                $idtype = $this->input->post('idtype');
                $idcategory = $this->input->post('idcategory');
                $idbrand = $this->input->post('idbrand');
                $idmodel = $this->input->post('idmodel');
                $idvariant = $this->input->post('idvariant');
                $idgodown = $this->input->post('idgodown');
                $skutype = $this->input->post('skutype');
                $product_name = $this->input->post('product_name');
                $imei = $this->input->post('imei');
                $price = $this->input->post('price');
                $basic = $this->input->post('basic');
                $discount_amt = $this->input->post('discount_amt');
                $total_amt = $this->input->post('total_amt');
                $landing = $this->input->post('landing');
                $mrp = $this->input->post('mrp');
                $mop = $this->input->post('mop');
                $nlc_price = $this->input->post('nlc_price');
                $ageing = $this->input->post('ageing');
                $focusstatus = $this->input->post('focus_st');
                $focus_incentive = $this->input->post('focus_incentive');
                $salesman_price = $this->input->post('salesman_price');
                $qty = $this->input->post('qty');
                $rowid = $this->input->post('rowid');
                $is_gst = $this->input->post('is_gst');
                $idvendor = $this->input->post('idvendor');
                $hsn = $this->input->post('hsn'); 
                $is_mop = $this->input->post('is_mop'); // price on invoice
                $sale_type = $this->input->post('sale_type'); // 0=Normal,1=PurchaseFirst,2=SaleFirst
                $insurance_imei = $this->input->post('insurance_imei'); 
                $activation_code = $this->input->post('activation_code'); 
        //        $imei_history[nest] = array();
                $insurance_recon = array();
                for($i = 0; $i < count($idvariant); $i++){
                    $cgst = 0; $sgst = 0; $igst = 0;
                    if($gst_type == 1){
                        $igst = $this->input->post('igst['.$i.']');
                    }else{
                        $cgst = $this->input->post('cgst['.$i.']');
                        $sgst = $this->input->post('sgst['.$i.']');
                    }
                    $sale_product[$i] = array(
                        'date' => $date,
                        'idsale' => $idsale,
                        'idmodel' => $idmodel[$i],
                        'idvariant' => $idvariant[$i],
                        'imei_no' => $imei[$i],
                        'hsn' => $hsn[$i],
                        'idskutype' => $skutype[$i],
                        'idgodown' => $idgodown[$i],
                        'idproductcategory' => $idtype[$i],
                        'idcategory' => $idcategory[$i],
                        'idbrand' => $idbrand[$i],
                        'product_name' => $product_name[$i],
                        'price' => $price[$i],
                        'landing' => $landing[$i],
                        'mrp' => $mrp[$i],
                        'mop' => $mop[$i],
                        'nlc_price' => $nlc_price[$i],
                        'ageing' => $ageing[$i],
                        'focus' => $focusstatus[$i],
                        'focus_incentive' => $focus_incentive[$i],
                        'salesman_price' => $salesman_price[$i],
                        'inv_no' => $inv_no,
                        'qty' => $qty[$i],
                        'idbranch' => $idbranch,
                        'discount_amt' => $discount_amt[$i],
                        'is_gst' => $is_gst[$i],
                        'is_mop' => $is_mop[$i],
                        'basic' => $basic[$i],
                        'idvendor' => $idvendor[$i],
                        'ssale_type' => $sale_type[$i],
                        'cgst_per' => $cgst,
                        'sgst_per' => $sgst,
                        'igst_per' => $igst,
                        'total_amount' => $total_amt[$i],
                        'entry_time' => $datetime,
                        'insurance_imei_no' => $insurance_imei[$i],
                        'activation_code' => $activation_code[$i],
                    );
                    $creted_by = $this->input->post('created_by');
                    $idsaleproduct = $this->Sale_model->save_sale_product($sale_product[$i]);
                    $idsaleproduct_1 = $this->Customerloyalty_model->save_customer_purchase($sale_product[$i],$idcustomer,$creted_by,$i);
                    if($skutype[$i] == 4){
                //      qunatity
                        if($sale_type[$i] == 2){
                            $this->load->model('Inward_model');
                            $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($idvariant[$i],4,$idbranch,$idgodown[$i]);
                //                $hostock = $this->Inward_model->get_hostock_byidmodel_skutype($product_id[$i], 4, 1);
                            if(count($hostock) == 0){
                                $inward_stock_sku = array(
                                    'date' => $date,
                                    'idgodown' => $idgodown[$i],
                                    'product_name' => $product_name[$i],
                                    'idskutype' => $skutype[$i],
                                    'idproductcategory' => $idtype[$i],
                                    'idcategory' => $idcategory[$i],
                                    'is_gst' => 1,
                                    'idvariant' => $idvariant[$i],
                                    'idbranch' => $idbranch,
                                    'idmodel' => $idmodel[$i],
                                    'idbrand' => $idbrand[$i],
                                    'created_by' => $this->input->post('created_by'),
                                    'idvendor' => $idvendor[$i],
                                    'qty' => -$qty[$i],
                                );
                                $this->Inward_model->save_stock($inward_stock_sku);
                            }else{
                                foreach ($hostock as $hstock){
                                    $updated_qty = $hstock->qty - $qty[$i];
                                    $this->Inward_model->update_stock_byid($hstock->id_stock,$updated_qty);
                                }
                            }
                        }else{
                            $this->Sale_model->minus_stock_byidstock($rowid[$i], $qty[$i]);
                        }
                    }else{
                        $this->Purchase_model->delete_stock_byidstock($rowid[$i]);
                        // IMEI History
                        $imei_history[]=array(
                            'imei_no' => $imei[$i],
                            'entry_type' => 'Sale',
                            'entry_time' => $datetime,
                            'date' => $date,
                            'idbranch' => $idbranch,
                            'idgodown' => $idgodown[$i],
                            'idvariant' => $idvariant[$i],
                            'idimei_details_link' => 4, // Sale from imei_details_link table
                            'idlink' => $idsale,
                            'iduser' => $this->input->post('created_by'),
                        );
                    }
                    if($sale_type[$i] != 0){
                        $insurance_recon[] = array(
                            'date' => $date,
                            'idsale' => $idsale,
                            'idsale_product' => $idsaleproduct,
                            'idmodel' => $idmodel[$i],
                            'idvariant' => $idvariant[$i],
                            'idskutype' => $skutype[$i],
                            'idproductcategory' => $idtype[$i],
                            'idcategory' => $idcategory[$i],
                            'idbrand' => $idbrand[$i],
                            'product_name' => $product_name[$i],
                            'inv_no' => $inv_no,
                            'qty' => $qty[$i],
                            'idbranch' => $idbranch,
                            'idvendor' => $idvendor[$i],
                            'ssale_type' => $sale_type[$i],
                            'total_amount' => $total_amt[$i],
                            'entry_time' => $datetime,
                            'insurance_imei_no' => $insurance_imei[$i],
                            'activation_code' => $activation_code[$i],
                        );
                    }
                }
//                die(print_r($insurance_recon));
                if(count($insurance_recon) > 0){
                    $this->Sale_model->save_batch_insurance_recon($insurance_recon);
                }
                if(count($imei_history) > 0){
                    $this->General_model->save_batch_imei_history($imei_history);
                }

                // BFL integration
                if($this->input->post('bfl_do_id')){
                    $bfl_data = array(
                        'do_id' => $this->input->post('bfl_do_id'),
                        'idsale' => $idsale,
                        'idsale_product' => $idsaleproduct,
                        'bfl_brand' => $this->input->post('bfl_brand'),
                        'bfl_model' => $this->input->post('bfl_model'),
                        'bfl_srno' => $this->input->post('bfl_srno'),
                        'idcustomer' => $idcustomer,
                        'mobile' => $this->input->post('mobile'),
                        'customer_name' => $this->input->post('bfl_customer'),
                        'customer_gst' => $this->input->post('gst_no'),
                        'scheme_code' => $this->input->post('scheme_code'),
                        'scheme' => $this->input->post('scheme'),
                        'mop' => $this->input->post('bfl_mop'),
                        'downpayment' => $this->input->post('bfl_downpayment'),
                        'loan' => $this->input->post('bfl_loan'),
                        'emi_amount' => $this->input->post('bfl_emi_amount'),
                        'tenure' => $this->input->post('bfl_tenure'),
                        'bfl_remark' => $this->input->post('bfl_remark'),
                        'entry_time' => $datetime,
                    );
                    $this->Sale_model->save_bfl($bfl_data);			
                }
                $invoice_data = array( 'invoice_no' => $invid );
                $this->General_model->edit_db_branch($idbranch, $invoice_data);
                if($id_advance_payment_receive){
                    $this->load->model('Reconciliation_model');
                    $update_booking = array(
                        'claim' => 1,
                        'idsale' => $idsale,
                        'inv_no' => $inv_no,
                        'inv_date' => $date,
                    );
                    $this->Reconciliation_model->update_advanced_payment_byid($id_advance_payment_receive, $update_booking);
                }
                if($idsale_token){
                    $update_token = array(
                        'status' => 1,
                        'idsale' => $idsale,
                        'update_time' => $datetime,
                    );
                    $this->Sale_model->update_sale_token_byid($idsale_token, $update_token);
                }
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
                }else{
                    $this->db->trans_commit();
                    $this->session->set_flashdata('save_data', 'Invoice bill generated');
                }
        //        die('<pre>'.print_r($_POST,1).'</pre>');
                if($dcprint[0] == 0){
                    $this->session->set_userdata('idsale_url','invoice_print/'.$idsale);
                    return redirect('Sale/invoice_print/'.$idsale);
                }else{
                    $this->session->set_userdata('idsale_url','dc_print_14april/'.$idsale);
                    return redirect('Sale/dc_print/'.$idsale);
                }
            }else{ ?>
                <script>
                    if (confirm('This message displayed, due to slow network. Entry already submitted... Go and check in invoice search menu. सबमिट बटण वर दोन वेळा क्लिक केले आहे, किंवा नेटवर्क समस्या मुळे हा संदेश प्रदर्शित झाला. चलन आधीच सबमिट झाले आहे... जर प्रिंट ओपन झाली नाही तर Invoice Search मेनूमध्ये तपासणी करा')){
                        window.location = "<?php echo $this->session->userdata('idsale_url') ?>";
                    }else{
                        window.location = "<?php echo $this->session->userdata('idsale_url') ?>";
                    }
                </script>
            <?php }
        }
    }
    
    
    /******** Imei No CHange ******************/
    public function imei_change() {
        $q['tab_active'] = 'Sale';
        $this->load->view('sale/imei_change',$q);
    }
    public function ajax_track_imei_for_change() {
        $imei = $this->input->post('imei');
        $imei_history = $this->Sale_model->get_imei_history($imei);
        $stock_imei = $this->Sale_model->get_imei_from_stock($imei);
//        die(print_r($stock_imei));
        if($stock_imei){ ?>
            <form>
                <div class="clearfix"></div><br>
                <div class="col-md-1 col-md-offset-2"><b>New Imei</b></div>
                <div class="col-md-3">
                    <input type="text" name="new_imei" id="new_imei" class="form-control input-sm" required>
                    <input type="hidden" name="old_imei" id="old_imei" class="form-control input-sm" value="<?php echo $imei; ?>">
                    <input type="hidden" name="idbranch" class="form-control input-sm" value="<?php echo $stock_imei->idbranch; ?>">
                </div>
                <div class="col-md-1"><button class="btn btn-primary checkstock" formmethod="POST" id="checkstock" formaction="<?php echo base_url()?>Sale/update_imei_no">Submit</button></div>
            </form>
            
            <div class="clearfix"></div><br>
        <?php }
        
        if(count($imei_history) > 0){ ?>

            <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                <header>
                    <div class="text-center">
                     <h1><?php echo '<small>'.$imei_history[count($imei_history)-1]->idvariant.']</small> '.$imei_history[count($imei_history)-1]->full_name ?></h1>
                     <!--<h1><?php // echo $imei_history[count($imei_history)-1]->idvariant.'] '.$imei_history[count($imei_history)-1]->full_name ?></h1>-->
                     <p><?php echo $imei; ?></p>
                 </div>
             </header>
         </div><div class="clearfix"></div><br>
         <div class="col-md-10 col-md-offset-1" style="padding: 0;">
            <section class="timeline">
                <div class="">
                    <?php $i=1; foreach ($imei_history as $history){ ?>
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content">
                                <h3 style="margin-top: 10px"><?php echo $history->entry_type ?>
                                <div class="date pull-right"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div>
                            </h3>
                            <hr>
                            <p style="font-size: 16px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="text-muted" style="font-family: Kurale">'.$history->godown_name.'</small>' ?></p>
                            <?php if($history->transfer_from!=NULL){ ?>
                                <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                            <?php } ?>
                            <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android fa-lg"></i> <?php echo $history->full_name ?></p>
                            <div class="clearfix"></div>
                            <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                            <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <?php if($history->url_link){ // Purchase,Purchase return
                                    if($history->idimei_details_link == 4){ // Purchase,Purchase return ?>
                                        <a class="bnt-more" style="right: 70px" target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$history->idlink) ?>">
                                            <i class="fa fa-print fa-lg"></i>
                                        </a>
                                    <?php } ?>
                                    <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                        <i class="fa fa-send-o fa-lg"></i>
                                    </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
        <?php }else{
            echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
            . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }
    }

    public function update_imei_no(){
//        die(print_r($_POST));
        $old_imei = $this->input->post('old_imei');
        $new_imei = $this->input->post('new_imei');
        $idbranch = $this->input->post('idbranch');
        $stock_imei = $this->Sale_model->get_imei_from_stock($new_imei);
        if(!$stock_imei){
            $inward_data  = $this->Sale_model->get_inward_data_byimei($old_imei);
    //        die(print_r($inward_data));
            $imei_arry = $inward_data->imei_srno;
            $imei_update = str_replace($old_imei, $new_imei, $imei_arry);

            $data = array(
                'imei_no' => $new_imei,
            );
            $inward_data_arr = array(
                'imei_srno' => $imei_update,
            );
            $this->Sale_model->update_stock_imei_no($data, $old_imei);
            $this->Sale_model->update_inward_product_imei_no($data, $old_imei);
            $this->Sale_model->update_imei_history_imei_no($data, $old_imei);
            $this->Sale_model->update_inward_data_imei_no($inward_data_arr, $inward_data->id_inward_data);

            $this->session->set_flashdata('save_data', 'Imei Change Done');
            return redirect('Sale/imei_change');
        }else{
            $this->session->set_flashdata('reject_data', 'Failed To Update, You have Entered Duplicate Imei, try with different Imei');
            return redirect('Sale/imei_change');
        }
    }
    
}