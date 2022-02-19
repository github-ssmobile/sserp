<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hostbook_Acc_Api extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("General_model");
        $this->load->model("Hostbook_Acc_Model");   
        $this->load->model("common_model");   
        date_default_timezone_set('Asia/Kolkata');
        $Content_Type = "application/json";

    }
    public function HBVendour_Master(){
        $vendor_data=$this->uri->segment(2);

        $hb_login=$this->hb_login(1);
        if($hb_login['status']==200){
            $hbdata['hb_einvtoken']=$hb_login['data']['user']['accessToken'];
            $hbdata['hb_einvsecretkey']=$hb_login['data']['user']['preserveKey'];

            $upd_hb_login= $this->common_model->updateRow('hostbook_config', $hbdata, array('company_id'=>1,'api_type'=>3));

            $hb_valid=$this->hb_valid(1);
            if($hb_valid['status']==200){
                $vendor_ins_data= $this->common_model->getSingleRow('vendor',array('id_vendor'=>$this->uri->segment(2)));
              
                $is_customer=false;
                $is_vendor=true;

                if($vendor_ins_data['vendor_gst']!=''){

                    $contact_add=array(
                        "addressGSTIN"=> $vendor_ins_data['vendor_gst'],
                        "address1"=> $vendor_ins_data['vendor_address'],
                        "address2"=> null,
                        "street"=> null,
                        "city"=> $vendor_ins_data['city'],
                        "state"=> $vendor_ins_data['state'],
                        "zip"=> $vendor_ins_data['pincode'],
                        "country"=> "INDIA",
                        "mobile"=> $vendor_ins_data['vendor_contact'],
                        "pan"=> null,
                        "tan"=> null,
                        "gstin"=> $vendor_ins_data['vendor_gst'],
                        "cin"=> null,
                        "telephone"=> null,
                        "type"=> "PADR"
                    );
                    $contact_Gstbadd=array(

                        "addressGSTIN"=>  $vendor_ins_data['vendor_gst'],
                        "address1"=>  $vendor_ins_data['vendor_contact'],
                        "address2"=> null,
                        "street"=> null,
                        "city"=>  $vendor_ins_data['city'],
                        "state"=>  $vendor_ins_data['state'],
                        "zip"=>  $vendor_ins_data['pincode'],
                        "country"=> "INDIA",
                        "mobile"=> $vendor_ins_data['vendor_contact'],
                        "pan"=> null,
                        "tan"=> null,
                        "gstin"=> null,
                        "cin"=> null,
                        "telephone"=> null,
                        "type"=> "BADR"

                    );
                    $contact_Gstsadd=array(
                        "addressGSTIN"=>  $vendor_ins_data['vendor_gst'],
                        "address1"=>  $vendor_ins_data['vendor_contact'],
                        "address2"=> null,
                        "street"=> null,
                        "city"=>  $vendor_ins_data['city'],
                        "state"=>  $vendor_ins_data['state'],
                        "zip"=>  $vendor_ins_data['pincode'],
                        "country"=> "INDIA",
                        "mobile"=> $vendor_ins_data['vendor_contact'],
                        "pan"=> null,
                        "tan"=> null,
                        "gstin"=> null,
                        "cin"=> null,
                        "telephone"=> null,
                        "type"=> "SADR"
                    );
                    $contact_G=array(
                        "number"=> $vendor_ins_data['vendor_gst'],
                        "verified"=> false,
                        "billingAddress"=> $contact_Gstbadd,
                        "shippingAddress"=> $contact_Gstsadd,
                        "defaultGstin"=> false,

                    );
                    $contact_Gstin[]=$contact_G;
                    $contact_address[]=$contact_add;
                    $contact_Ll=array(
                        "name"=> $vendor_ins_data['vendor_name'],
                        "accountNumber"=> $vendor_ins_data['vendor_contact'],
                        "employee"=> false,
                        "vendor"=> $is_vendor,
                        "customer"=> $is_customer,
                        "primaryType"=> "Vendor",
                         "pan"=> null,
                        "creditLimit"=> null,
                        "email"=> null,
                        "phone"=> null,
                        "mobile"=> $vendor_ins_data['vendor_contact'],
                        "skype"=> null,
                        "website"=> null,
                        "address"=>$contact_address,
                        "contactGstin"=>$contact_Gstin,
                        "openingBalance"=> 0,
                        "openingDate"=> null,
                        "notes"=> null,
                        "termsAndCondition"=> null,
                        "status"=> "COAC",
                        "cinNumber"=> null,
                        "panVerified"=> false,
                        "fax"=> null
                    );
                  

                    $contact_List[]=$contact_Ll;
                    
                   
                    $final_array=array(
                        "contactList"=>$contact_List,
                        
                    );


                    $hb_gen_master=$this->hbGenerateMaster(1,$final_array);
                    print_r(($hb_gen_master));die();
                }else{
                    print_r('else');die();
                }


                print_r($vendor_ins_data);die(); 
            }else{
                $response['status']=false;
                $response['message']=$hb_valid['message'];
            }

        }else{
            $response['status']=false;
            $response['message']=$hb_login['message'];
        }

    }
    public function hb_login($comp_id){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>3));
        $data=array(
            "username"=>$hb_config_data['hb_loginid'],
            "password"=>$hb_config_data['hb_password'],
        );
        return $hb_data= $this->Hostbook_Acc_Model->getLoginData($data);
    }

    public function hb_valid($comp_id){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>'1','api_type'=>3));
        $Secret_Key=array('x-version:IND','x-preserveKey:'.$hb_config_data['hb_einvsecretkey'],'x-company:'.$hb_config_data['hb_compid'],'x-forwarded-portal:True');

        return $hb_data= $this->Hostbook_Acc_Model->getauthData($Secret_Key);
    }

    public function hbGenerateMaster($comp_id,$fdata){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>'1','api_type'=>3));
        $Secret_Key=array('x-preserveKey:'.$hb_config_data['hb_einvsecretkey'],'x-company:'.$hb_config_data['hb_compid'],'x-auth-token:'.$hb_config_data['hb_einvtoken']);
        $data=$fdata;

        return $hb_data= $this->Hostbook_Acc_Model->hbGenerateMaster($data,$Secret_Key);
    }

}
?>