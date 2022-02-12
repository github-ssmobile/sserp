<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Old_erp extends CI_Controller
{
    public function __construct() {
        parent::__construct();

        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Sale_model');
        $this->load->model('Old_erp_model');
        $this->load->model('General_model');
    }
    public function get_daily_stock_backup(){
        $q['tab_active'] = '';
        $q['daily_stock_data'] = $this->Old_erp_model->get_daily_stock_data();
        $q['daily_transit_stock_data'] = $this->Old_erp_model->get_daily_transit_stock_data();
        $q['daily_rudram_stock_data'] = $this->Old_erp_model->get_daily_stock_data_byidproductcategory(5); // Rudram stock
        $q['daily_transitrudram_stock_data'] = $this->Old_erp_model->get_daily_transit_stock_data_byidproductcategory(5); // Rudram stock
        $this->load->view('old_erp/daily_stock_backup', $q);
    }
    
      public function get_daily_stock_manual(){
        $q['tab_active'] = '';
        $daily_stock_data = $this->Old_erp_model->get_daily_stock_data_manual($this->input->post('report_date'));
        $daily_transit_stock_data = $this->Old_erp_model->get_daily_transit_stock_data_manual($this->input->post('report_date'));

        ?>
        <div class="col-md-10"><center><h3><span class="mdi mdi-upload"></span> Daily Stock Data</h3></center></div><div class="clearfix"></div><hr>
        <button id="stock_download_btn" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('stock_data_opening_<?php echo date('d-m-Y') ?>');"><span class="fa fa-file-excel-o"></span> Export</button>
        <table class="table table-bordered" id="stock_data_opening_<?php echo date('d-m-Y');?>">
            <thead>
                <th>Branch</th>
                <th>Godown</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Product</th>
                <th>IMEI/SRNO</th>
                <th>Qty</th>
                <th>Sender Branch</th>
                <th>Status</th>
                <th>Variant id</th>
                <th>MOP</th>
                <th>Landing</th>
                <th>MRP</th>
            </thead>
            <tbody>
                <?php foreach ($daily_stock_data as $stock){ ?>
                    <tr>
                        <td><?php echo $stock->branch_name ?></td>
                        <td><?php echo $stock->godown_name ?></td>
                        <td><?php echo $stock->product_category_name ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->product_name ?></td>
                        <td>'<?php echo $stock->imei_no ?></td>
                        <td><?php echo $stock->qty ?></td>
                        <td></td>
                        <td>In Branch</td>
                        <td><?php echo $stock->idvariant ?></td>
                        <td><?php echo $stock->mop ?></td>
                        <td><?php echo $stock->landing ?></td>
                        <td><?php echo $stock->mrp ?></td>
                    </tr>
                <?php } ?>
                <?php foreach ($daily_transit_stock_data as $transit){ ?>
                    <tr>
                        <td><?php echo $transit->receiver ?></td>
                        <td><?php echo $transit->godown_name ?></td>
                        <td><?php echo $transit->product_category_name ?></td>
                        <td><?php echo $transit->brand_name ?></td>
                        <td><?php echo $transit->product_name ?></td>
                        <td>'<?php echo $transit->imei_no ?></td>
                        <td><?php echo $transit->qty ?></td>
                        <td><?php echo $transit->sender ?></td>
                        <td>In Transit</td>
                        <td><?php echo $transit->idvariant ?></td>
                        <td><?php echo $transit->mop ?></td>
                        <td><?php echo $transit->landing ?></td>
                        <td><?php echo $transit->mrp ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


        <?php 
    }
    
    public function daily_stock_data(){        
        $q['tab_active'] = '';
        $this->load->view('old_erp/daily_stock_data', $q);
    }
    public function upload_old_sale_by_paymentmode() {
        $q['tab_active'] = '';
        $q['payment_mode'] = $this->General_model->get_payment_mode_data();
        $q['branch_data'] = $this->General_model->get_allbranch_data();
        $this->load->view('old_erp/upload_old_sale_by_paymentmode', $q);
    }
    public function submit_upload_old_sale_by_paymentmode() {
        $this->db->trans_begin();
        $datetime = date('Y-m-d h:i:s');
        $i =0;
        $idbranch = $this->input->post('idbranch');
        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $branch_code = $this->input->post('branch_code');
        $filename=$_FILES["uploadfile"]["tmp_name"];
        // credit
        if($idpayment_head == 6){
            // sale
            $inv_no = 'OLD_Cr/'.$branch_code.'/01';
            $data = array(
                'date' => '2021-01-31',
                'inv_no' => $inv_no,
                'idbranch' => $idbranch,
                'idcustomer' => 1,
                'customer_fname' => 'OLD ERP',
                'customer_lname' => 'Credit',
                'customer_idstate' => 1,
                'customer_pincode' => '416001',
                'customer_contact' => '9111111111',
                'customer_address' => 'SHAHUPURI',
                'customer_gst' => '',
                'idsalesperson' => 65,
                'basic_total' => 0,
                'discount_total' => 0,
                'final_total' => 0,
                'gst_type' => 0,
                'created_by' => $_SESSION['id_users'],
                'remark' => 'Old ERP Credit',
                'entry_time' => $datetime,
                'dcprint' => 0,
            );
            $idsale = $this->Sale_model->save_sale($data);
            
            if($_FILES["uploadfile"]["size"] > 0){
                $file = fopen($filename, "r");
                while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if($i > 0){
                        $amount = $openingdata[9];
                        $invoice_amount = $openingdata[8];
//                        $received_amount = $invoice_amount - $amount;
                        if($amount > 0){
                            $contact = $openingdata[3];
                            $inv_no = $openingdata[4];
                            $customer = $openingdata[2];
                            $invdate = $openingdata[5];
                            if (strpos($invdate, '/') !== false) {
                                $output1 = explode('/', $invdate);
                            }else if(strpos($invdate, '-') !== false) {
                                $output1 = explode('-', $invdate);
                            }
                            $date1 = $output1[2].'-'.$output1[1].'-'.$output1[0];
                            $date = date('Y-m-d', strtotime($date1));
//                            $date = date('Y-m-d', strtotime($openingdata[5]));
                            // histoty
                            $credit_history[] = array(
                                'customer_name' => $customer,
                                'customer_contact' => $contact,
                                'inv_no' => $openingdata[4],
                                'date' => $date,
                                'product_name' => $openingdata[7],
                                'invoice_amount' => $invoice_amount,
                                'amount' => $amount,
                                'referral_name' => $openingdata[11],
                                'idbranch' => $idbranch,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'created_by' => $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                            $contact_list = $this->Sale_model->ajax_get_customer_bycontact($contact);
                            if(count($contact_list) > 0){
                                // Customer exist
                                $idcustomer = $contact_list[0]->id_customer;
                            }else{
                                // Customer create
                                $customer_name = explode(' ', $customer);
                                $customer_data = array(
                                    'customer_fname' => $customer_name[0],
                                    'customer_lname' => $customer_name[1],
                                    'customer_contact' => $contact,
                                    'idstate' => 1,
                                    'idbranch' => $idbranch,
                                    'created_by' => $_SESSION['id_users'],
                                    'entry_time' => $date,
                                );
                                $idcustomer = $this->Sale_model->save_customer($customer_data);
                            }
                            // sale payment
                            $payment[] = array(
                                'date' => $date,
                                'idsale' => $idsale,
                                'amount' => $amount,
//                                'received_amount' => $amount,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'inv_no' => $inv_no,
                                'idcustomer' => $idcustomer,
                                'idbranch' => $idbranch,
//                                'approved_by' => $inv_no.' '.$customer.' '.$contact,
                                'approved_by' => $openingdata[7].' -'.$openingdata[11],
                                'created_by' =>  $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                        }
                    }
                    $i++;
                }
                $this->Old_erp_model->save_bulk_sale_payment($payment);
                $this->Old_erp_model->save_old_credit_data($credit_history);
                fclose($file);
            }
        }elseif($idpayment_head == 7){
            // sale
            $inv_no = 'OLD_Cust/'.$branch_code.'/01';
            $data = array(
                'date' => '2021-01-31',
                'inv_no' => $inv_no,
                'idbranch' => $idbranch,
                'idcustomer' => 1,
                'customer_fname' => 'OLD ERP',
                'customer_lname' => 'Custody',
                'customer_idstate' => 1,
                'customer_pincode' => '416001',
                'customer_contact' => '9111111111',
                'customer_address' => 'SHAHUPURI',
                'customer_gst' => '',
                'idsalesperson' => 65,
                'basic_total' => 0,
                'discount_total' => 0,
                'final_total' => 0,
                'gst_type' => 0,
                'created_by' => $_SESSION['id_users'],
                'remark' => 'Old ERP Custody',
                'entry_time' => $datetime,
                'dcprint' => 0,
            );
            $idsale = $this->Sale_model->save_sale($data);
            
            if($_FILES["uploadfile"]["size"] > 0){
                $file = fopen($filename, "r");
                while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if($i > 0){
                        $invoice_amount = $openingdata[8];
                        $amount = $openingdata[9];
//                        $received_amount = $invoice_amount - $amount;
                        if($amount > 0){
                            $contact = $openingdata[3];
                            $inv_no = $openingdata[4];
                            $customer = $openingdata[2];
                            $invdate = $openingdata[5];
                            if (strpos($invdate, '/') !== false) {
                                $output1 = explode('/', $invdate);
                            }else if(strpos($invdate, '-') !== false) {
                                $output1 = explode('-', $invdate);
                            }
                            $date1 = $output1[2].'-'.$output1[1].'-'.$output1[0];
                            $date = date('Y-m-d', strtotime($date1));
                            // histoty
                            $credit_history[] = array(
                                'customer_name' => $customer,
                                'customer_contact' => $contact,
                                'inv_no' => $openingdata[4],
                                'date' => $date,
                                'product_name' => $openingdata[7],
                                'invoice_amount' => $invoice_amount,
                                'amount' => $amount,
                                'product_model_name' => $openingdata[11],
                                'idbranch' => $idbranch,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'created_by' => $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                            $contact_list = $this->Sale_model->ajax_get_customer_bycontact($contact);
                            if(count($contact_list) > 0){
                                // Customer exist
                                $idcustomer = $contact_list[0]->id_customer;
                            }else{
                                // Customer create
                                $customer_name = explode(' ', $customer);
                                $customer_data = array(
                                    'customer_fname' => $customer_name[0],
                                    'customer_lname' => $customer_name[1],
                                    'customer_contact' => $contact,
                                    'idstate' => 1,
                                    'idbranch' => $idbranch,
                                    'created_by' => $_SESSION['id_users'],
                                    'entry_time' => $date,
                                );
                                $idcustomer = $this->Sale_model->save_customer($customer_data);
                            }
                            // sale payment
                            $payment[] = array(
                                'date' => $date,
                                'idsale' => $idsale,
                                'amount' => $amount,
//                                'received_amount' => $received_amount,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'inv_no' => $inv_no,
                                'idcustomer' => $idcustomer,
                                'idbranch' => $idbranch,
                                'product_model_name' => $openingdata[11],
                                'approved_by' => 'Sale product- '.$openingdata[7],
                                'created_by' =>  $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                        }
                    }
                    $i++;
                }
                $this->Old_erp_model->save_bulk_sale_payment($payment);
                $this->Old_erp_model->save_old_credit_data($credit_history);
                fclose($file);
            }
        }elseif($idpayment_head == 8){
            // sale
            $inv_no = 'OLD_Up/'.$branch_code.'/01';
            $data = array(
                'date' => '2021-01-31',
                'inv_no' => $inv_no,
                'idbranch' => $idbranch,
                'idcustomer' => 1,
                'customer_fname' => 'OLD ERP',
                'customer_lname' => 'Credit',
                'customer_idstate' => 1,
                'customer_pincode' => '416001',
                'customer_contact' => '9111111111',
                'customer_address' => 'SHAHUPURI',
                'customer_gst' => '',
                'idsalesperson' => 65,
                'basic_total' => 0,
                'discount_total' => 0,
                'final_total' => 0,
                'gst_type' => 0,
                'created_by' => $_SESSION['id_users'],
                'remark' => 'Old ERP Credit',
                'entry_time' => $datetime,
                'dcprint' => 0,
            );
            $idsale = $this->Sale_model->save_sale($data);
            
            if($_FILES["uploadfile"]["size"] > 0){
                $file = fopen($filename, "r");
                while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if($i > 0){
                        $amount = $openingdata[9];
                        $invoice_amount = $openingdata[8];
//                        $received_amount = $invoice_amount - $amount;
                        if($amount > 0){
                            $contact = $openingdata[3];
                            $inv_no = $openingdata[4];
                            $customer = $openingdata[2];
                            $invdate = $openingdata[5];
                            if (strpos($invdate, '/') !== false) {
                                $output1 = explode('/', $invdate);
                            }else if(strpos($invdate, '-') !== false) {
                                $output1 = explode('-', $invdate);
                            }
                            $date1 = $output1[2].'-'.$output1[1].'-'.$output1[0];
                            $date = date('Y-m-d', strtotime($date1));
                            // histoty
                            $credit_history[] = array(
                                'customer_name' => $customer,
                                'customer_contact' => $contact,
                                'inv_no' => $openingdata[4],
                                'date' => $date,
                                'product_name' => $openingdata[7],
                                'invoice_amount' => $invoice_amount,
                                'amount' => $amount,
                                'referral_name' => $openingdata[11],
                                'idbranch' => $idbranch,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'created_by' => $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                            $contact_list = $this->Sale_model->ajax_get_customer_bycontact($contact);
                            if(count($contact_list) > 0){
                                // Customer exist
                                $idcustomer = $contact_list[0]->id_customer;
                            }else{
                                // Customer create
                                $customer_name = explode(' ', $customer);
                                $customer_data = array(
                                    'customer_fname' => $customer_name[0],
                                    'customer_lname' => $customer_name[1],
                                    'customer_contact' => $contact,
                                    'idstate' => 1,
                                    'idbranch' => $idbranch,
                                    'created_by' => $_SESSION['id_users'],
                                    'entry_time' => $date,
                                );
                                $idcustomer = $this->Sale_model->save_customer($customer_data);
                            }
                            // sale payment
                            $payment = array(
                                'date' => $date,
                                'idsale' => $idsale,
                                'amount' => $amount,
//                                'received_amount' => $amount,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'inv_no' => $inv_no,
                                'idcustomer' => $idcustomer,
                                'idbranch' => $idbranch,
//                                'approved_by' => $inv_no.' '.$customer.' '.$contact,
                                'transaction_id' => 'Sale product- '.$openingdata[7].' -'.$openingdata[11],
                                'created_by' =>  $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                            $id_sale_payment = $this->Old_erp_model->save_sale_payment($payment);
                            $npayment = array(
                                'idsale_payment' => $id_sale_payment,
                                'inv_no' => $inv_no,
                                'idsale' => $idsale,
                                'date' => $date,
                                'idcustomer' => $idcustomer,
                                'idbranch' => $idbranch,
                                'amount' => $amount,
                                'idpayment_head' => $idpayment_head,
                                'idpayment_mode' => $idpayment_mode,
                                'transaction_id' => 'Sale product- '.$openingdata[7].' Scheme- '.$openingdata[11],
                                'created_by' => $_SESSION['id_users'],
                                'entry_time' => $date,
                            );
                            $this->Old_erp_model->save_payment_reconciliation($npayment);
                        }
                    }
                    $i++;
                }
//                $this->Old_erp_model->save_bulk_sale_payment($payment);
                $this->Old_erp_model->save_old_credit_data($credit_history);
                fclose($file);
            }
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Excel Uploaded.');
        }
        redirect('Old_erp/upload_old_sale_by_paymentmode');
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
        $this->load->view('old_erp/sale_report', $q);  
    }   
    public function ajax_get_sale_report(){
        
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $sale_data = $this->Old_erp_model->ajax_get_sale_data_byfilter($from, $to, $idbranch);
        
        if(count($sale_data) >0){
        ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelementtop">
                <th>DAte</th>
                <th>Invoice No</th>
                <th>Branch</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Imei</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>Amount</th>
                <th>Sale Promotor</th>
                <th>Info</th>
                <th>Print</th>
            </thead>
            <tbody class="data_1">
                <?php $total=0; foreach ($sale_data as $sale) { ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($sale->invoice_date)) ?></td>
                    <!--<td><?php // echo $sale->date ?></td>-->
                    <td><?php echo $sale->invoice_no ?></td>
                    <td><?php echo $sale->branch_name ?></td>
                    <td><?php echo $sale->customer_name; ?> </td>
                    <td><?php echo $sale->customer_mobile ?></td>
                    <td><?php echo $sale->customer_gst_no ?></td>
                    <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                        <td>'<?php echo $sale->serial_no  ?></td>                    
                    <?php }else{ ?>
                        <td>'<?php echo $sale->imei_1_no ?></td>                        
                    <?php } ?>
                    <td><?php echo $sale->category ?></td>
                    <td><?php echo $sale->brand ?></td>
                    <td><?php echo $sale->product_name ?></td>
                    <td><?php echo $sale->settlement_amount; $total = $total + $sale->settlement_amount; ?></td>
                    <td><?php echo $sale->promoter_name; ?></td>
                    <td><a target="_blank" href="<?php echo base_url('Old_erp/sale_details/'.$sale->invoice_no) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <td><a target="_blank" href="<?php echo base_url('Old_erp/invoice_print/'.$sale->invoice_no) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
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
    public function sale_revenue_report(){
        $q['tab_active'] = 'Sale';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }                
        $this->load->view('old_erp/sale_revenue_report', $q);  
    }
    public function ajax_get_sale_revenue_report(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $sale_data = $this->Old_erp_model->ajax_get_sale_data_byfilter($from, $to, $idbranch);
        $sale_return_data=array();
//        $sale_return_data = $this->Sale_model->ajax_get_sale_return_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand);
        if(count($sale_data) >0){ ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelement">
                <th>Date</th>
                <th>Invoice No</th>
                <th>Branch</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Sale Promotor</th>
                <th>Imei</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>Sale Amount</th>
                <th>Landing Amount</th>
                <th>Revenue Amount</th>
                <th>Info</th>
                <!--<th>Print</th>-->
            </thead>
            <tbody class="data_1">
                <?php $trevenue = 0; $landing=0; $total=0; foreach ($sale_data as $sale) { ?>
                <tr>
                    <td><?php echo $sale->invoice_date ?></td>
                    <td><?php echo $sale->invoice_no ?></td>
                    <td><?php echo $sale->branch_name ?></td>
                    <td><?php echo $sale->customer_name ?></td>
                    <td><?php echo $sale->customer_name ?></td>
                    <td><?php echo $sale->customer_gst_no ?></td>
                    <td><?php echo $sale->promoter_name ?></td>
                     <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                        <td>'<?php echo $sale->serial_no  ?></td>      
                    <?php }else{ ?>
                        <td>'<?php echo $sale->imei_1_no ?></td>                  
                    <?php } ?>
                    <td><?php echo $sale->category ?></td>
                    <td><?php echo $sale->brand ?></td>
                    <td><?php echo $sale->product_name ?></td>
                    <td><?php echo $sale->settlement_amount; $total = $total + $sale->settlement_amount; ?></td>
                    <td><?php echo $sale->manager_price; $landing = $landing + $sale->manager_price; ?></td>
                    <td><?php  $revenue = $sale->settlement_amount - $sale->manager_price; echo $revenue; $trevenue = $trevenue + $revenue;?></td>
                    <td><a target="_blank" href="<?php echo base_url('Old_erp/sale_details/'.$sale->invoice_no) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <!--<td><a target="_blank" href="<?php echo base_url('Old_erp/invoice_print/'.$sale->invoice_no) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
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
                    <td><b>Total</b></td>
                    <td><b><?php echo $total; ?></b></td>
                    <td><b><?php echo $landing; ?></b></td>
                    <td><b><?php echo $trevenue; ?></b></td>
                     <td></td>
                    <!--<td></td>-->
                </tr>
                 <?php if(count($sale_return_data)>0){ ?>
                <tr>
                    <td colspan="14" style="background-color: #9999ff;color: #FFFFFF"> <b> Sales Return </b> </td>
                    
                </tr>
                  <?php  $strevenue = 0; $tlanding=0; $stotal=0; foreach ($sale_return_data as $sale_return) { ?>
                <tr>
                    <td><?php echo $sale_return->date ?></td>
                    <td><?php echo $sale_return->sales_return_invid ?></td>
                    <td><?php echo $sale_return->branch_name ?></td>
                    <td><?php echo $sale_return->customer_fname.' '.$sale_return->customer_lname ?></td>
                    <td><?php echo $sale_return->customer_contact ?></td>
                    <td><?php echo $sale_return->customer_gst ?></td>
                    <td><?php echo $sale->user_name ?></td>
                    <td><?php echo $sale_return->imei_no ?></td>
                    <td><?php echo $sale_return->product_category_name ?></td>
                    <td><?php echo $sale_return->brand_name ?></td>
                    <td><?php echo $sale_return->product_name ?></td>
                    <td><?php echo $sale_return->total_amount; $stotal = $stotal + $sale_return->total_amount; ?></td>
                    <td><?php echo $sale_return->landing; $tlanding = $tlanding + $sale_return->landing; ?></td>
                    <td><?php  $srevenue = $sale_return->landing - $sale_return->total_amount; echo $srevenue; $strevenue = $strevenue + $srevenue;?></td>
                    <td><a target="_blank" href="<?php echo base_url('Sales_return/sales_return_details/'.$sale_return->id_salesreturn) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <!--<td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale_return->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
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
                    <td><b>Total</b></td>
                    <td><b><?php echo $stotal; ?></b></td>
                    <td><b><?php echo $tlanding; ?></b></td>
                    <td><b><?php echo $strevenue; ?></b></td>
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
                    <td><b>Overall  Total</b></td>
                    <td><b><?php echo $stotal + $total; ?></b></td>
                    <td><b><?php echo $tlanding + $landing; ?></b></td>
                    <td><b><?php echo $strevenue + $trevenue; ?></b></td>
                    <td></td>
                </tr>
                 <?php } ?>
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
    public function sale_details($inv) {
        $q['tab_active'] = '';
        $q['sale_data'] = $this->Old_erp_model->get_sale_by_inv($inv);        
        $this->load->view('old_erp/sale_details', $q);
    }
    public function invoice_print($inv) {
        $q['tab_active'] = '';
        $q['sale_data'] = $this->Old_erp_model->get_sale_by_inv($inv);         
        $q['printhead'] = $this->Old_erp_model->get_printhead_bid($q['sale_data'][0]->idbranch); 
          
        $this->load->view('old_erp/invoice_print', $q);
    }
    public function search_bill() {
        $q['tab_active'] = '';             
        $this->load->view('old_erp/search_bill', $q);
    }
    public function ajax_search_invoice(){
        
        $invoice = $this->input->post('invoice');
        $imei = $this->input->post('imei');
        $mobile = $this->input->post('mobile');
        $sale_data = $this->Old_erp_model->ajax_get_sale_search_invoice($invoice, $imei, $mobile);
        
        if(count($sale_data) >0){
        ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelementtop">
                <th>DAte</th>
                <th>Invoice No</th>
                <th>Branch</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Imei</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>Amount</th>
                <th>Sale Promotor</th>
                <th>Info</th>
                <th>Print</th>
            </thead>
            <tbody class="data_1">
                <?php $total=0; foreach ($sale_data as $sale) { ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($sale->invoice_date)) ?></td>
                    <!--<td><?php // echo $sale->date ?></td>-->
                    <td><?php echo $sale->invoice_no ?></td>
                    <td><?php echo $sale->branch_name ?></td>
                    <td><?php echo $sale->customer_name; ?> </td>
                    <td><?php echo $sale->customer_mobile ?></td>
                    <td><?php echo $sale->customer_gst_no ?></td>
                    <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                        <td>'<?php echo $sale->serial_no  ?></td>                         
                    <?php }else{ ?>
                        <td>'<?php echo $sale->imei_1_no ?></td>  
                    <?php } ?>
                    <td><?php echo $sale->category ?></td>
                    <td><?php echo $sale->brand ?></td>
                    <td><?php echo $sale->product_name ?></td>
                    <td><?php echo $sale->settlement_amount; $total = $total + $sale->settlement_amount; ?></td>
                    <td><?php echo $sale->promoter_name; ?></td>
                    <td><a target="_blank" href="<?php echo base_url('Old_erp/sale_details/'.$sale->invoice_no) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                    <td><a target="_blank" href="<?php echo base_url('Old_erp/invoice_print/'.$sale->invoice_no) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
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
    
     public function apple_webgdv_report() {
        $q['tab_active'] = 'DMS Report';
        
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-7 days', strtotime($to)));
        
        $q['report_data'] = $this->General_model->get_apple_webgdv_report_excel_data($from, $to);
//        die('<pre>'.print_r($q['report_data'],1).'</pre>');
        $this->load->view('old_erp/apple_webgdv_report', $q);
    }
    
    public function import_invoice() {
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $this->load->view('old_erp/import_invoice',$q);
    }
        
     public function save_import_invoice() {
        $q['tab_active'] = '';
        $invoicedata = $_POST;
        $this->Old_erp_model->save_import_invoice($invoicedata);
        
        return redirect('Old_erp/import_invoice/');
    }
    public function import_invoice_edit() {
        $q['tab_active'] = '';
        $this->load->view('old_erp/import_invoice_edit',$q);
    }
    public function get_import_invoice_data() {
        $import_data = $this->Old_erp_model->get_import_invoice_data();
//        echo '<pre>';
//        print_r($import_data);die;
        ?>
       <div style="font-family: K2D; font-size: 15px;">
        <form id="inward_edit" method="POST" action="<?php echo base_url('Old_erp/save_import_data_edit') ?>">
            <?php foreach($import_data as $data){ ?>
            <div class="col-md-9">
            <div class="p-1">
            <span class="col-md-3 text-muted" style="font-size: 14px;">Invoice no : </span>
                <div class="col-md-9" style="width: 416px;">
                    <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" readonly id="invoice_id" name="invoice_id[]" placeholder="Invoice ID" value="<?php echo $data->invoice_no ?>">
                            </div>
                    </div>
                </div>
            <span class="col-md-3 text-muted" style="font-size: 14px;">Invoice Date : </span>
                <div class="col-md-9" style="width: 416px;">
                    <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" data-provide="datepicker"  id="invoice_date" name="invoice_date[]" placeholder="Invoice Date" value="<?php echo $data->invoice_date ?>">
                            </div>
                    </div>
                </div>
            <span class="col-md-3 text-muted" style="font-size: 14px;">Product Name : </span>
                <div class="col-md-9" style="width: 416px;">
                    <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" readonly  id="product_name" name="product_name[]" placeholder="Product Name" value="<?php echo $data->product_name ?>">
                            </div>
                    </div>
                </div>
            <span class="col-md-3 text-muted" style="font-size: 14px;">IMEI 1 No : </span>
                <div class="col-md-9" style="width: 416px;">
                     <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" readonly id="imei_1_no" name="imei_1_no[]" placeholder="IMEI 1 No" value="<?php echo $data->imei_1_no ?>">
                            </div>
                    </div>
                </div>
             <span class="col-md-3 text-muted" style="font-size: 14px;">IMEI 2 No : </span>
                <div class="col-md-9" style="width: 416px;">
                    <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" readonly id="imei_2_no" name="imei_2_no[]" placeholder="IMEI 2 No" value="<?php echo $data->imei_2_no ?>">
                            </div>
                    </div>
                </div>
             <span class="col-md-3 text-muted" style="font-size: 14px;">Serial No : </span>
                <div class="col-md-9" style="width: 416px;">
                     <div class="input-group">
                            <div class="input-group-btn">
                                <input type="text" class="form-control input-sm" readonly id="serial_no" name="serial_no[]" placeholder="Serial No" value="<?php echo $data->serial_no ?>">
                            </div>
                    </div>
                </div>
            </div>
                <div class="clearfix"></div>
            <hr>
            </div>
            <div class="col-md-3">

            </div>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="col-md-2">
                <input type="submit"  class="btn btn-primary gradient2 waves-effect waves-light btn-sub"  value="Submit">
            </div>
        </form>
       </div>
        <?php
    } 
    public function save_import_data_edit() {
//        echo '<pre>';
//        print_r($_POST);die;
        $this->Old_erp_model->save_import_invoice_edit();
    }
    

}