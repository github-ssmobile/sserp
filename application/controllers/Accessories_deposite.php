<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accessories_deposite extends CI_Controller {
    
   public function __construct() {
        parent::__construct();
      //  if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Customerloyalty_model');
        $this->load->model('Sale_model');
    }

    public function save_accessories_today_deposite(){
      //  print_r('ss');die;
        $deposite_acc = json_decode($_POST['data']);
        $deposite_branch_id=$deposite_acc->branch_id;
        $deposite_date=$deposite_acc->date;
        $deposite_deposit_cash=$deposite_acc->deposit_cash;
        $entry_typr = 10;
        
        $branch_data = $this->db->where('acc_branch_id', $deposite_branch_id)->get('branch')->result();
        //print_r($branch_data[0]->id_branch);die;
        $branch_id = $branch_data[0]->id_branch;
        
        $data = array(
             'idbranch'=>$branch_id,
             'amount'=>$deposite_acc->deposit_cash,
             'date'=>$deposite_acc->date,
             'entry_type'=>$entry_typr
             );
       // print_r($data);die;
        $this->db->insert('daybook_cash',$data);
               
    }
    
    public function get_accessories_daybookamount_cnt() {
        extract($_POST);
        
        $date_acc = $this->db->select('acc_branch_id')->where('id_branch',$idbranch)->get('branch')->result();
        //print_r($date_acc[0]->acc_branch_id);die;
        $acc_branch = $date_acc[0]->acc_branch_id;
        $postData['branch_id']= $acc_branch;
        $postData['date_today']= $today;
        //print_r($postData);die;
        $this->url = $this->config->item("erpaccessories_url");
        $newurl = $this->url."Accessories_deposite/get_accessories_daybookamount_cnt/";
        // echo $newurl;die;
        $data['data'] = json_encode($postData);
        //$data['days']=json_encode($days);
        //die(print_r($data));

        $result = $this->rest->request($newurl,"POST_ACCESSORIES",$data);
        
        $result = json_decode($result, true);
        //die(print_r($result));

        echo $result;
        
    }
    
   
}?>