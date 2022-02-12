<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Invoice_whatsapp_api extends CI_Controller {
    
     public function __construct() {
        parent::__construct();
      //  if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Customerloyalty_model');
        $this->load->model('Sale_model');
    }
    
    public function invoice_whatsapp_api() {
        
        $sale_id = $this->uri->segment(2);
       
        $login_cred = $this->whatspp_api_login();
        $result1 = json_decode($login_cred, true);

        $res= $this->db->query("select CONCAT(customer_fname,' ',customer_lname) AS full_name,customer_fname,customer_lname,customer_contact,idbranch,date as inv_date,inv_no from sale where id_sale=$sale_id");
        
        $response["data"] = array();
        if ($res->num_rows()) {
            $response["data"]=$res->result_array();
          //  print_r($response['data'][0]);die;
        } 
        $full_name = $response['data'][0]['full_name'];
        $idbranch = $response['data'][0]['idbranch'];
        $inv_date = $response['data'][0]['inv_date'];
        $inv_no = $response['data'][0]['inv_no'];
        $expl_date = explode("-",$inv_date);
        $month_no = $expl_date[1];
        $year_no = $expl_date[0];
        $dateObj = DateTime::createFromFormat('!m', $month_no);
        $monthName = $dateObj->format('F');
       
        $to_no1 = $response['data'][0]['customer_contact'];
        $to_no ="91".$to_no1;
        $date_1 = new DateTime();
        $date = $date_1->format('d/m/Y');
       
        $status = $result1['status'];
        $token_data_no = $result1['data']['access_token'];
        $token_data = array($token_data_no);
        //print_r($token_data);die;
        $authorization = array('Bearer :'.$token_data[0]);
        if($status == 1){
            $newurl1 = "https://whatsappapi.engagely.ai/api/msg/list_of_numbers";
            $result_cnt = $this->rest->request($newurl1,"GET_Template",NULL,0,$token_data);
            $result_cnt1 = json_decode($result_cnt, true);
            $form_no_str = $result_cnt1['phone_number_details'][0]['phone_number'];
            $form_no = explode("-",$form_no_str);
          
            $from_no = "91".$form_no[1];
           
            $newurl = "https://whatsappapi.engagely.ai/api/msg/list_of_templates";
            $result = $this->rest->request($newurl,"GET_Template",NULL,0,$token_data);
            $result1 = json_decode($result, true);
            
            $data = $this->db->select('template_id,template_name')
                             ->get('whatsapp_template_id')->result_array();
      
             if(count($data) == 0){
                 for($i=0;$i<count($result1['list_of_templates']);$i++){
                     $template_id=$result1['list_of_templates'][$i]['template_id'];
                     $template_name=$result1['list_of_templates'][$i]['template_name'];
                     $qy = "INSERT INTO whatsapp_template_id (template_id,template_name)VALUES ('$template_id','$template_name')";
                     $query = $this->db->query($qy);       
                 }
             }
              $data_template = $this->db->select('template_name')->where('template_type',1)->get('whatsapp_template_id')->result_array();
              
                 for($i=0;$i<count($data_template);$i++){
                   $newurl = "https://whatsappapi.engagely.ai/api/msg/create_json";
                  
                   $result = $this->rest->request($newurl,"POST_Template",json_encode($data_template[$i]),0,$token_data);
                   $result1 = json_decode($result, true);
                   
                   $template_name = $result1['response']['template_name'];
                   $template_type = $result1['response']['type'];
                   $waid = $result1['response']['waid'];
                   $pdf_name="inv_".$idbranch."_".$sale_id.'.pdf';
                   $folder_name = $monthName.''.$year_no;
                   $pdf_url = 'https://ssmobile.com/Invoices/'.$folder_name.'/'.$pdf_name;
                  // print_r($pdf_url);die;                 
                    $jayParsedAry = [
                        "api" => "WA",
                        "payload" => 
                            [
                                "components" => 
                                    [
                                        "body" => 
                                            [
                                            ],

                                        "header" => 
                                            [
                                                "link" => "$pdf_url",
                                                "type" => "document"
                                            ],

                                    ],

                                "from" => "$from_no",
                                "to" => "$to_no",
                                
                            ],

                        "template_name" => "payment_update",
                        "type" => "template",
                        "version" => "v1",
                        "waid" => "$waid"
                    ];

                    $json_template = json_encode($jayParsedAry);
                  
                    $url_send = "https://whatsappapi.engagely.ai/api/msg/send_template_messages";
                    $result_send = $this->rest->request($url_send,"POST_Template",json_encode($jayParsedAry),0,$token_data);
                    $result1 = json_decode($result_send, true);

                    $sending_id = $result1['response']['messages'][0]['id'];
                    $sens_data=array();
                    $sens_data=array(
                        "id_send"=>$sending_id,
                        "idasle"=>$sale_id,
                        "inv_no"=>$inv_no,
                        "customer_contact"=>$to_no,
                    );
                    //print_r($sens_data);die;
                    $this->db->insert('whatsapp_send_msg_data', $sens_data);
                   
                 }
   
       }
    }
    public function whatspp_api_login() {
        $data = $this->db->select('email,password')
                     ->get('whatsapp_api_login')->result_array();
        $login_data = $data[0];
        $login_json = json_encode($login_data);
        
        $newurl = "https://whatsappapi.engagely.ai/api/auth/login";
        $result = $this->rest->request($newurl,"POST_login",json_encode($login_data));
        
        return $result;
        
    }
    
}