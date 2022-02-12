<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_loyalty extends CI_Controller {
    
   public function __construct() {
        parent::__construct();
      //  if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Customerloyalty_model');
        $this->load->model('Sale_model');
    }

    public function customer_form_data(){
        $q['tab_active'] = '';
        $q['customer_form_data'] = $this->Customerloyalty_model->get_customer_form_data();
//        echo '<pre>';
//        print_r($q);die;
        $this->load->view('customer_loyalty/customer_form_data', $q);
    }
    public function save_cutomer_form_data(){
       
        extract($_POST);
        $q['tab_active'] = '';
        $data = array(
            'field_name' => $field_name,
            'column_name' => $column_name,
            'data_type' => $data_type,
            'status' => $status,
            'sequence' => $sequence,
            'filed_required' => $filed_required,
        );
        //print_r($data);die;
        $dbname = $this->db->database;
        $this->Customerloyalty_model->save_cutomer_form_data($data);
        $insertid = $this->db->insert_id();
       
        if($insertid > 0){
//            $str = "SELECT COLUMN_NAME , ORDINAL_POSITION FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'customer' ORDER BY ORDINAL_POSITION DESC LIMIT 1";
//            $query = $this->db->query($str);
//            $data_array = $query->result();
//            //print_r($data_array[0]->COLUMN_NAME);die;
//            $columnname = $data_array[0]->COLUMN_NAME;
            if($data_type == "varchar"){
               $str = "ALTER TABLE `customer`  ADD `$column_name` VARCHAR(100) NULL";
            }else if($data_type == 'int'){
                $str="ALTER TABLE `customer`  ADD `$column_name` INT NULL";
            }else if($data_type == 'text'){
                $str="ALTER TABLE `customer`  ADD `$column_name` TEXT NULL";
            }else if($data_type == 'date'){
                $str="ALTER TABLE `customer`  ADD `$column_name` DATE NULL";
            }
           
            $query = $this->db->query($str);
            if($query == '1'){
                $this->session->set_flashdata('save_data', 'Customer Form Data Added Successfully');
                //return redirect('customer_loyalty/customer_form_data');
            }
            $cutomer_data = $this->accessories_cutomer_form_data($data);
        }
        
    }
    
     public function accessories_cutomer_form_data($dataa) {
       
     // print_r($url);die;    
    // User data to send using HTTP POST method in curl
   // $data = array('name'=>'New User 123','salary'=>'65000', 'age' => '33');

    // Data should be passed as json format
            $this->url = $this->config->item("erpaccessories_url");
            $newurl = $this->url."Customer_loyalty/accessories_cutomer_form_data/";
           // echo $newurl;die;
            $data['data'] = json_encode($dataa);
            //$data['days']=json_encode($days);
            //die(print_r($data));

            $result = $this->rest->request($newurl,"POST_ACCESSORIES",$data);
            //die(print_r($result));
            $result = json_decode($result, true);
           
            return $result;

  }
    
    public function edit_customer_form_data() {
        //print_r($_POST);die;
        extract($_POST);
        $id_data = $id;
        $data = array(
            'status' => $status,
            'sequence' => $sequence,
            'filed_required' => $filed_required,
        );
        $this->Customerloyalty_model->edit_customer_form_data($id_data,$data);
        $this->session->set_flashdata('save_data', 'Customer Form Data Edit Successfully');
        return redirect('customer_loyalty/customer_form_data');
    }
    
        public function save_customer_accessoires_api(){
        
        $customerdata = json_decode($_POST['data']);
       //print_r($customerdata->customer_fname);die;
         $customer_fullname=$customerdata->customer_fname;
         $customer_contact=$customerdata->customer_contact;
         $customer_pincode=$customerdata->customer_pincode;
         $customer_city=$customerdata->customer_city;
         $customer_district=$customerdata->customer_district;
         $customer_state=$customerdata->customer_state;
         $customer_address=$customerdata->customer_address;
         $customer_gst=$customerdata->customer_gst;
         $customer_email=$customerdata->customer_email;
         $birth_date=$customerdata->birth_date;
         $acc_branchid=$customerdata->acc_branchid;
         
         $state_data = $this->Customerloyalty_model->get_state_bystate_name($customer_state);
         //print_r($state_data[0]['id_state']);die;
         $state_id = $state_data[0]['id_state'];
         $customer_str = explode(" ",$customer_fullname);
//         echo '<pre>';
//         print_r($customer_str);die;
         $customername_cnt = count($customer_str);
         if($customername_cnt == 2){
             $custmer_fname = $customer_str[0];
             $custmer_lname = $customer_str[1];
         }else{
             $custmer_fname = $customer_str[0];
             $custmer_lname = "";
         }
         $branch_data = $this->db->where('acc_branch_id', $acc_branchid)->get('branch')->result_array();
        // print_r($branch_data[0]['id_branch']);die;
         $branchid = $branch_data[0]['id_branch'];
         $data = array(
             'customer_fname'=>$custmer_fname,
             'customer_lname'=>$custmer_lname,
             'customer_contact'=>$customer_contact,
             'customer_pincode'=>$customer_pincode,
             'customer_city'=>$customer_city,
             'customer_district'=>$customer_district,
             'customer_state'=>$customer_state,
             'idstate'=>$state_id,
             'customer_address'=>$customer_address,
             'customer_gst'=>$customer_gst,
             'customer_email'=>$customer_email,
             'birth_date'=>$birth_date,  
             'idbranch'=>$branchid,
             );
        // print_r($data);die;
         $customer_count = $this->Customerloyalty_model->get_customer_contact($customer_contact);
         if($customer_count > 0){
            //$idcustomer = $this->Sale_model->update_customer_accessories($data);
            echo 'aleardy present'; 
         }else{
             $idcustomer = $this->Sale_model->save_customer($data);
             echo json_encode($idcustomer);
         }
         
    }
    public function get_handset_customer_data_api() {
       // echo 'nnnn';die;
        $customer_contact = json_decode($_POST['data']);
       // print_r($customer_contact);die;
        $cust_contact_data = $this->Customerloyalty_model->get_handset_customer_data_api($customer_contact);
        echo json_encode($cust_contact_data);
    }
    
    public function get_customer_form_data_api() {
        $cust_form_data = $this->Customerloyalty_model->get_customer_formdata();
       // print_r($cust_form_data);die;
        echo json_encode($cust_form_data);
    }
    public function get_customer_purchase_data_api() {
        //die('ddd');
        //print_r($_POST['data']);die;
        $branchid = $_POST['branch_id'];
        $customerdata = json_decode($_POST['data'],TRUE);
        $customer_count = $this->Customerloyalty_model->save_customer_purchase_data($customerdata,$branchid);
    }
    public function get_customer_payment_data_api() {
         $paymentdata = json_decode($_POST['data'],TRUE);
//         echo '<pre>';
//         print_r($paymentdata);die;
         $paymentdata_cnt = $this->Customerloyalty_model->save_customer_payment_data($paymentdata);
         
    }
    public function customer_data_report(){
        $q['tab_active'] = '';
         $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['payment_mode_data'] = $this->General_model->get_payment_mode_data();
        $q['price_category_data'] =  $this->General_model->get_price_category_lab();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['payment_head'] = $this->General_model->get_active_payment_head();
        $this->load->view('customer_loyalty/customer_data_report', $q);
    }
    public function ajax_get_crm_report_data(){
       $res = $this->Customerloyalty_model->ajax_get_crm_report_data();
    }
    public function ajax_get_paymentmode_idhead() {
       $res = $this->Customerloyalty_model->ajax_get_paymentmode_idhead();
    }
   
}?>