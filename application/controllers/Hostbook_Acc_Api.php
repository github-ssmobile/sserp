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
                    if($hb_gen_master['status']==200){
                        $this->session->set_flashdata('save_data', 'Vendor Created');
                        return redirect('Master/vendor_details');
                    }else{
                        $response['status']=false;
                        $this->session->set_flashdata('save_data', $hb_gen_master['message']);
                        $response['message']=$hb_gen_master['message'];
                    }
                }else{

                    $contact_Ll=array(

                        "name"=> $vendor_ins_data['vendor_name'],
                        "accountNumber"=> $vendor_ins_data['vendor_contact'],
                        "employee"=> false,
                        "vendor"=> false,
                        "customer"=> true,
                        "primaryType"=> "customer",
                        "openingBalance"=> 0.00,
                        "openingDate"=> "01-04-2019",
                        "status"=> "COAC",
                        "panVerified"=> false,
                    );

                    $p_address=array(
                        array(
                            "address1"=> $vendor_ins_data['vendor_address'],
                            "street"=> $vendor_ins_data['city'],
                            "city"=> $vendor_ins_data['city'],
                            "state"=> $vendor_ins_data['state'],
                            "zip"=> $vendor_ins_data['pincode'],
                            "country"=> "India",
                            "type"=> "PADR"
                        ),
                        array(
                            "address1"=> $vendor_ins_data['vendor_address'],
                            "street"=> $vendor_ins_data['city'],
                            "city"=> $vendor_ins_data['city'],
                            "state"=> $vendor_ins_data['state'],
                            "zip"=> $vendor_ins_data['pincode'],
                            "country"=> "India",
                            "type"=> "BADR"
                        )
                    );

                    $contact_addd=array(
                        "address"=>  $p_address,
                    );

                    $contactTdss=array(
                        "tdsApplicable"=> "false"
                    );
                    $contact_List[]=$contact_Ll;

                    $contactTds[]=$contactTdss;

                    $final_array=array(
                        "contactList"=>$contact_List,
                        "address"=>$p_address,
                        "contactTds"=>$contactTds,
                    );

                    $hb_gen_master=$this->hbGenerateMaster(1,$final_array);
                    if($hb_gen_master['status']==200){
                        $this->session->set_flashdata('save_data', 'Vendor Created');
                        return redirect('Master/vendor_details');
                    }else{
                        $response['status']=false;
                        $this->session->set_flashdata('save_data', $hb_gen_master['message']);
                        $response['message']=$hb_gen_master['message'];
                        return redirect('Master/vendor_details');
                    }
                }
            }else{
                $response['status']=false;
                $response['message']=$hb_valid['message'];
                $this->session->set_flashdata('save_data', $hb_valid['message']);
                return redirect('Master/vendor_details');
            }

        }else{
            $response['status']=false;
            $response['message']=$hb_login['message'];
            $this->session->set_flashdata('save_data', $hb_login['message']);
            return redirect('Master/vendor_details');
        }

    }
    public function HBItem_Master(){
        $product_id=$this->uri->segment(2);
        $hb_login=$this->hb_login(1);
        if($hb_login['status']==200){
            $hbdata['hb_einvtoken']=$hb_login['data']['user']['accessToken'];
            $hbdata['hb_einvsecretkey']=$hb_login['data']['user']['preserveKey'];

            $upd_hb_login= $this->common_model->updateRow('hostbook_config', $hbdata, array('company_id'=>1,'api_type'=>3));

            $hb_valid=$this->hb_valid(1);
            if($hb_valid['status']==200){

                $product_data= $this->common_model->getRecords('model_variants',array('*'),array('idmodel'=>$product_id));
                foreach($product_data as $p_data){
                    $category_data= $this->common_model->getSingleRow('category',array('id_category'=>$p_data['idcategory']));

                    $itemDtl=array(
                        "purchaseAccountName"=> "Purchase Account",
                        "unitName"=> "Pieces",
                        "hsnSacCode"=> $category_data['hsn'],
                    );
                    $itemDetails[]=$itemDtl; 
                    $itemLst=array(
                        "trackflag"=> true,
                        "purchaseflag"=> true,
                        "salesflag"=> true,
                        "itemDetails"=>$itemDetails,
                        "inventoryMethod"=> "FIF",
                        "code"=> $p_data['id_variant'],
                        "name"=> $p_data['full_name'],
                        "inventory"=> "INVTP"

                    );
                    $itemList[]=$itemLst; 
                    $final_array=array(
                        "itemList"=>$itemList,
                    );

                    $hb_gen_master=$this->hbGenerateMaster(1,$final_array);
                    if($hb_gen_master['status']==200){
                        $this->session->set_flashdata('save_data', 'Item Created');
                        return redirect('Catalogue/model_details');
                    }else{
                        $response['status']=false;
                        $this->session->set_flashdata('save_data', $hb_gen_master['message']);
                        $response['message']=$hb_gen_master['message'];
                        return redirect('Catalogue/model_details');
                    }
                }
            }else{
                $this->session->set_flashdata('save_data', $hb_valid['message']);
                return redirect('Catalogue/model_details');
            }
        }else{
            $this->session->set_flashdata('save_data', $hb_login['message']);
            return redirect('Catalogue/model_details');
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