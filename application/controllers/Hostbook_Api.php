<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hostbook_Api extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Allocation_model");        
        $this->load->model("General_model");
        $this->load->model("Outward_model");       
        $this->load->model("Inward_model");   
        $this->load->model("Transfer_model");   
        $this->load->model("Hostbook_Model");   
        $this->load->model("common_model");   
        date_default_timezone_set('Asia/Kolkata');
        $Content_Type = "application/json";

    }

    public function generateEwayBill(){
        $from_branch=$this->uri->segment(2);
        $to_branch=$this->uri->segment(3);
        $id_allocation=$this->uri->segment(4);
        $prog_type=$this->uri->segment(5);


        $comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$from_branch));
        $to_comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$to_branch));

        $hb_login_data=$this->hb_login($comp_id['idcompany']);
        $from_comp_data= $this->common_model->getSingleRow('company',array('company_id'=>$comp_id['idcompany']));
        $to_comp_data= $this->common_model->getSingleRow('company',array('company_id'=>$to_comp_id['idcompany']));
        if(substr($to_comp_data['company_gstin'],0,2)!=substr($from_comp_data['company_gstin'],0,2)){

            $response= $this->generateEinvoice($to_comp_id['idcompany'],$id_allocation,$from_branch,$to_branch,$this->uri->segment(5));

        }else{

            if($hb_login_data['isSuccess']==1){
                $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id['idcompany'],'api_type'=>1));

                $hbdata['hb_token']=$hb_login_data['token'];
                $hbdata['hb_buid']=$hb_login_data['buid'];
                $hbdata['hb_userid']=$hb_login_data['userid'];
                $hbdata['hb_gstinid']=$hb_login_data['gstinid'];

                $ins= $this->common_model->updateRow('hostbook_config', $hbdata, array('company_id'=>$comp_id['idcompany'],'api_type'=>1));

                $hb_auth_data=$this->hb_authenticate($comp_id['idcompany']);

                if($hb_auth_data['isSuccess']==1){
                    $item_list=array();
                    $mainhsncode=0;
                    $tot_bill_value=0;
                    $tot_bill_value_tax=0;
                    $itmNO=0;
                    if($this->uri->segment(5)=='transfer'){
                        $transfer_data= $this->common_model->getSingleRow('transfer',array('id_transfer'=>$id_allocation));

                        $transfer_Product_data= $this->common_model->getRecords('transfer_product','*',array('idtransfer'=>$transfer_data['id_transfer']));

                        $transporter_data= $this->common_model->getSingleRow('transport_vendor',array('id_transport_vendor'=>$transfer_data['idtransport_vendor']));
                        $doc_id=$transfer_data['id_transfer'];

                        $doc_date=$transfer_data['date'];
                        foreach($transfer_Product_data as $item_data){
                            $itmNO=$itmNO+1;
                            $product_data= $this->common_model->getSingleRow('model_variants',array('id_variant'=>$item_data['idvariant']));
                            $hsn_data= $this->common_model->getSingleRow('category',array('id_category'=>$item_data['idcategory']));
                            if(substr($to_comp_data['company_gstin'],0,2)==substr($hb_config_data['hb_gstid'],0,2)){
                                $doctype='CHL';
                                $prod_cgst=$item_data['cgst_per'];
                                $prod_sgst=$item_data['sgst_per'];
                                $prod_igst=0;
                                $subSupplyType=5;
                                $userGstin=$hb_config_data['hb_gstid'];
                                $consineeGstin=$hb_config_data['hb_gstid'];

                            }

                            $gst=$prod_cgst+$prod_sgst+$prod_igst;
                            $gst_amount=($item_data['price']*$gst)/($gst+100);
                            $taxeble_amount=$item_data['price']-$gst_amount;
                            if($mainhsncode<$hsn_data['hsn']){
                                $mainhsncode=$hsn_data['hsn'];
                            }
                            $tot_bill_value=$tot_bill_value+$taxeble_amount;
                            $tot_bill_value_tax=$tot_bill_value_tax+$item_data['price'];
                            $item_array=array(
                                "itemNo"=> "$itmNO", 
                                "productName"=> substr($product_data['full_name'],0,95), 
                                "productDesc"=> substr($product_data['full_name'],0,95), 
                                "hsnCode"=> $hsn_data['hsn'], 
                                "quantity"=> $item_data['qty'], 
                                "qtyUnit"=> "NOS", 
                                "taxableAmount"=> $taxeble_amount, 
                                "sgstRate"=> number_format((float)$prod_sgst, 2, '.', ''), 
                                "cgstRate"=> number_format((float)$prod_cgst, 2, '.', ''), 
                                "igstRate"=> number_format((float)$prod_igst, 2, '.', ''), 
                                "cessRate"=> "0.00", 
                                "cessNonAdvol"=> "0.00" );
                            array_push($item_list,$item_array);
                        }

                    }else{
                        $outward_data= $this->common_model->getSingleRow('outward',array('idstock_allocation'=>$id_allocation));

                        $outward_Product_data= $this->common_model->getRecords('outward_product','*',array('idoutward'=>$outward_data['id_outward']));

                        $transporter_data= $this->common_model->getSingleRow('transport_vendor',array('id_transport_vendor'=>$outward_data['idtransport_vendor']));
                        $doc_id=$outward_data['id_outward'];
                        $doc_date=$outward_data['date'];
                        foreach($outward_Product_data as $item_data){
                            $itmNO=$itmNO+1;
                            $product_data= $this->common_model->getSingleRow('model_variants',array('id_variant'=>$item_data['idvariant']));
                            $hsn_data= $this->common_model->getSingleRow('category',array('id_category'=>$item_data['idcategory']));
                            if(substr($to_comp_data['company_gstin'],0,2)==substr($hb_config_data['hb_gstid'],0,2)){
                                $doctype='CHL';
                                $prod_cgst=$item_data['cgst_per'];
                                $prod_sgst=$item_data['sgst_per'];
                                $prod_igst=0;
                                $subSupplyType=5;
                                $userGstin=$hb_config_data['hb_gstid'];
                                $consineeGstin=$hb_config_data['hb_gstid'];

                            }

                            $gst=$prod_cgst+$prod_sgst+$prod_igst;
                            $gst_amount=($item_data['price']*$gst)/($gst+100);
                            $taxeble_amount=$item_data['price']-$gst_amount;
                            if($mainhsncode<$hsn_data['hsn']){
                                $mainhsncode=$hsn_data['hsn'];
                            }
                            $tot_bill_value=$tot_bill_value+$taxeble_amount;
                            $tot_bill_value_tax=$tot_bill_value_tax+$item_data['price'];
                            $item_array=array(
                                "itemNo"=> "$itmNO", 
                                "productName"=> substr($product_data['full_name'],0,95), 
                                "productDesc"=> substr($product_data['full_name'],0,95), 
                                "hsnCode"=> $hsn_data['hsn'], 
                                "quantity"=> $item_data['qty'], 
                                "qtyUnit"=> "NOS", 
                                "taxableAmount"=> $taxeble_amount, 
                                "sgstRate"=> number_format((float)$prod_sgst, 2, '.', ''), 
                                "cgstRate"=> number_format((float)$prod_cgst, 2, '.', ''), 
                                "igstRate"=> number_format((float)$prod_igst, 2, '.', ''), 
                                "cessRate"=> "0.00", 
                                "cessNonAdvol"=> "0.00" );
                            array_push($item_list,$item_array);
                        }

                        
                    }
                    if($transporter_data['transport_vendor_gst']!=''){
                        // $transporterId=$transporter_data['gst'];
                        $transporterId='23AADCK7940H1ZG';
                        $transDocNo='';
                        $transDocDate=date('Y-m-d');
                        $vehicleNo='';
                    }else{
                        $transporterId='';
                        $transDocNo=$outward_data['po_lr_no'];
                        $transDocDate=$outward_data['dispatch_date'];
                        $vehicleNo=$outward_data['vehicle_no'];
                    }

                    $eway_data=array(
                        "userGstin"=>$userGstin,
                        "supplyType"=> "O", 
                        "subSupplyType"=>"$subSupplyType", 
                        "subSupplyDesc"=>"",
                        "docType"=> $doctype, 
                        "docNo"=> $doc_id,
                        "docDate"=>date('d/m/Y',strtotime($doc_date)), 
                        "fromGstin"=> $userGstin, 
                        "fromTrdName"=> substr($to_comp_id['branch_name'],0,95), 
                        "fromAddr1"=> substr($to_comp_id['branch_address'],0,100),
                        "fromAddr2"=> "", 
                        "fromPlace"=> $to_comp_id['branch_city'], 
                        "fromPincode"=> $to_comp_id['branch_pincode'], 
                        "fromStateCode"=> substr($userGstin,0,2), 
                        "actFromStateCode"=>substr($userGstin,0,2), 
                        "toGstin"=> $consineeGstin, 
                        "toTrdName"=> substr($comp_id['branch_name'],0,95),
                        "toAddr1"=> substr($comp_id['branch_address'],0,100),
                        "toAddr2"=> "",
                        "toPlace"=> $comp_id['branch_city'],
                        "toPincode"=> $comp_id['branch_pincode'],
                        "toStateCode"=> substr($consineeGstin,0,2), 
                        "actToStateCode"=> substr($consineeGstin,0,2), 
                        "otherValue"=>"0.00",
                        "totalValue"=>  "$tot_bill_value", 
                        "cgstValue"=> number_format((float)$prod_cgst, 2, '.', ''), 
                        "sgstValue"=> number_format((float)$prod_sgst, 2, '.', ''), 
                        "igstValue"=> number_format((float)$prod_igst, 2, '.', ''), 
                        "cessValue"=> "0.00", 
                        "transMode"=> "1", 
                        "transDistance"=> "11", 
                        "transporterName"=> "", 
// "transporterId"=> $transporter_data['transport_vendor_gst'],
                        "transporterId"=> $transporterId,
                        "transDocNo"=> $transDocNo, 
                        "transDocDate"=> date('d/m/Y',strtotime($transDocDate)),  
                        "vehicleNo"=> $vehicleNo,   
                        "vehicleType"=> "R", 
                        "totInvValue"=> "$tot_bill_value_tax", 
                        "mainHsnCode"=> $mainhsncode, 
                        "cessNonAdvolValue"=> "0.00",
                        "transactionType"=> "1", 
                        "itemlist"=> $item_list, 
                    );

                    $final_data=$eway_data;
                    $hb_gen_eway_bill=$this->hb_generateewaybill($final_data,$hb_config_data['hb_login_secretkey'],$hbdata['hb_token']);

                    if($hb_gen_eway_bill['isSuccess']==1){
                        $eway_ins_data=array(
                            "idoutword_no" => $doc_id,
                            "bill_type" => '0',
                            "ewb_no" => $hb_gen_eway_bill['ewayBillNo'],
                            "ewb_date" => date('Y-m-d H:i:s',strtotime($hb_gen_eway_bill['ewayBillDate']))
                        );
                        $ele = $this->common_model->insertRow($eway_ins_data,'eway_einvoice_data');
                        $response['status']=true;
                        $response['message']='Eway Bill Generated with no '.$hb_gen_eway_bill['ewayBillNo'];
                    }else{
                        $response['status']=false;
                        $response['message']='Eway Bill Not Generated '.$hb_gen_eway_bill['error'];
                    }

                }else{
                    $response['status']=false;
                    $response['message']='Hostbook Authentication Failed';
                }
            }else{
                $response['status']=false;
                $response['message']='Hostbook Login Failed';
            }
        }
        echo json_encode($response);
    }


    public function hb_login($comp_id){

        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>1));
        $Secret_Key=$hb_config_data['hb_login_secretkey'];

        $data=array(
            "loginid"=>$hb_config_data['hb_loginid'],
            "password"=>$hb_config_data['hb_password'],
            "compid"=>$hb_config_data['hb_compid'],
            "gstin"=>$hb_config_data['hb_gstid'],
            "useraccno"=>"");

        return $hb_data= $this->Hostbook_Model->getLoginData($data,$Secret_Key);
    }

    public function hb_authenticate($comp_id){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>1));
        $auth_data=array(
            "userid"=>$hb_config_data['hb_userid'],
            "buid"=>$hb_config_data['hb_buid'],
            "gstinid"=>$hb_config_data['hb_gstinid'],
            "ewbUserID"=>$hb_config_data['ewb_userid'],
            "ewbPassword"=>$hb_config_data['ewb_password']
        );
        $Secret_Key=array('Secret-Key:'.$hb_config_data['hb_login_secretkey'],'Authorization:Bearer '.$hb_config_data['hb_token']);
        return $hb_data= $this->Hostbook_Model->getauthData($auth_data,$Secret_Key);
    }

    public function hb_generateewaybill($data,$Secret_Key,$token){
        $Secret_Key=array('Secret-Key:'.$Secret_Key,'Authorization:Bearer '.$token);
        return $hb_data= $this->Hostbook_Model->getEwayBillData($data,$Secret_Key);
    }


    /********************* EInvoice Api ***************************/

    public function generateEinvoice($comp_id,$id_allocation,$from_branch,$to_branch,$type){

        $hb_login_data=$this->hb_einv_login($comp_id);

        if($hb_login_data['isSuccess']==1){
            $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>2));

            $hbdata['hb_token']=$hb_login_data['hbLoginRes']['token'];
            $hbdata['hb_userid']=$hb_login_data['hbLoginRes']['user_account_no'];
            $ins= $this->common_model->updateRow('hostbook_config', $hbdata, array('company_id'=>$comp_id,'api_type'=>2));
            $hb_auth_data=$this->hb_einv_authenticate($comp_id);
            if($hb_auth_data['isSuccess']==1){
                $hb_einvdata['hb_einvtoken']=$hb_auth_data['hbToken'];
                $hb_einvdata['hb_einvsecretkey']=$hb_auth_data['hbsecretkey'];
                $auth_upd= $this->common_model->updateRow('hostbook_config', $hb_einvdata, array('company_id'=>$comp_id,'api_type'=>2));
                $hb_auth_token_data=$this->hb_einv_auth_token($comp_id);
                if($hb_auth_token_data['isSuccess']==1){

                    $comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$from_branch));
                    $to_comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$to_branch));

                    $from_comp_data= $this->common_model->getSingleRow('company',array('company_id'=>$comp_id['idcompany']));
                    $to_comp_data= $this->common_model->getSingleRow('company',array('company_id'=>$to_comp_id['idcompany']));

                    $item_list=array();
                    $ValDtls=array();
                    $mainhsncode=0;
                    $tot_bill_value=0;
                    $tot_bill_value_tax=0;
                    $itmNO=0;
                    $tot_cgst=0;
                    $tot_sgst=0;
                    $tot_igst=0;
                    if($this->uri->segment(5)=='transfer'){
                        $transfer_data= $this->common_model->getSingleRow('transfer',array('id_transfer'=>$id_allocation));

                        $transfer_Product_data= $this->common_model->getRecords('transfer_product','*',array('idtransfer'=>$transfer_data['id_transfer']));

                        $transporter_data= $this->common_model->getSingleRow('transport_vendor',array('id_transport_vendor'=>$transfer_data['idtransport_vendor']));
                        $doc_id=$transfer_data['id_transfer'];
                        $doc_date=$transfer_data['date'];
                        foreach($transfer_Product_data as $item_data){
                            $itmNO=$itmNO+1;
                            $product_data= $this->common_model->getSingleRow('model_variants',array('id_variant'=>$item_data['idvariant']));
                            $hsn_data= $this->common_model->getSingleRow('category',array('id_category'=>$item_data['idcategory']));

                            $doctype='INV';
                            $prod_cgst=0;
                            $prod_sgst=0;
                            $prod_igst=$item_data['igst_per'];
                            $cgst_amt= 0;
                            $sgst_amt= 0;
                            $gst=$prod_igst;
                            $gst_amount=($item_data['price']*$gst)/($gst+100);
                            $igst_amt= ($gst_amount);
                            $subSupplyType=1;
                            $userGstin=$hb_config_data['hb_gstid'];
                            $consineeGstin=$to_comp_data['company_gstin'];



                            $taxeble_amount=$item_data['price']-$gst_amount;
                            if($mainhsncode<$hsn_data['hsn']){
                                $mainhsncode=$hsn_data['hsn'];
                            }
                            $tot_bill_value=$tot_bill_value+$taxeble_amount;
                            $tot_bill_value_tax=$tot_bill_value_tax+$item_data['price'];

                            $tot_cgst=$tot_cgst+$cgst_amt;
                            $tot_sgst=$tot_sgst+$sgst_amt;
                            $tot_igst=$tot_igst+$igst_amt;
                            $item_array=array(
                                "SlNo"=> "$itmNO", 
                                "PrdDesc"=> substr($product_data['full_name'],0,95), 
                                "IsServc"=> 'N', 
                                "HsnCd"=> $hsn_data['hsn'], 
                                "Qty"=> $item_data['qty'], 
                                "Unit"=> "NOS", 
                                "UnitPrice"=> round($taxeble_amount), 
                                "TotAmt"=> round($taxeble_amount),
                                "AssAmt"=> round($taxeble_amount),
                                "GstRt"=> $gst,
                                "SgstAmt"=> round($sgst_amt), 
                                "CgstAmt"=> round($cgst_amt), 
                                "IgstAmt"=> round($igst_amt), 
                                "TotItemVal"=> $item_data['price']
                            );
                            array_push($item_list,$item_array);

                        }

                    }else{
                        $outward_data= $this->common_model->getSingleRow('outward',array('idstock_allocation'=>$id_allocation));

                        $outward_Product_data= $this->common_model->getRecords('outward_product','*',array('idoutward'=>$outward_data['id_outward']));

                        $transporter_data= $this->common_model->getSingleRow('transport_vendor',array('id_transport_vendor'=>$outward_data['idtransport_vendor']));
                        $doc_id=$outward_data['id_outward'];
                        $doc_date=$outward_data['date'];

                        foreach($outward_Product_data as $item_data){
                            $itmNO=$itmNO+1;
                            $product_data= $this->common_model->getSingleRow('model_variants',array('id_variant'=>$item_data['idvariant']));
                            $hsn_data= $this->common_model->getSingleRow('category',array('id_category'=>$item_data['idcategory']));

                            $doctype='INV';
                            $prod_cgst=0;
                            $prod_sgst=0;
                            $prod_igst=$item_data['igst_per'];
                            $cgst_amt= 0;
                            $sgst_amt= 0;
                            $gst=$prod_igst;
                            $gst_amount=($item_data['price']*$gst)/($gst+100);
                            $igst_amt= ($gst_amount);
                            $subSupplyType=1;
                            $userGstin=$hb_config_data['hb_gstid'];
                            $consineeGstin=$to_comp_data['company_gstin'];


                            $taxeble_amount=$item_data['price']-$gst_amount;
                            if($mainhsncode<$hsn_data['hsn']){
                                $mainhsncode=$hsn_data['hsn'];
                            }
                            $tot_bill_value=$tot_bill_value+$taxeble_amount;
                            $tot_bill_value_tax=$tot_bill_value_tax+$item_data['price'];

                            $tot_cgst=$tot_cgst+$cgst_amt;
                            $tot_sgst=$tot_sgst+$sgst_amt;
                            $tot_igst=$tot_igst+$igst_amt;
                            $item_array=array(
                                "SlNo"=> "$itmNO", 
                                "PrdDesc"=> substr($product_data['full_name'],0,95), 
                                "IsServc"=> 'N', 
                                "HsnCd"=> $hsn_data['hsn'], 
                                "Qty"=> $item_data['qty'], 
                                "Unit"=> "NOS", 
                                "UnitPrice"=> round($taxeble_amount), 
                                "TotAmt"=> round($taxeble_amount),
                                "AssAmt"=> round($taxeble_amount),
                                "GstRt"=> $gst,
                                "SgstAmt"=> round($sgst_amt), 
                                "CgstAmt"=> round($cgst_amt), 
                                "IgstAmt"=> round($igst_amt), 
                                "TotItemVal"=> $item_data['price']
                            );
                            array_push($item_list,$item_array);

                        }  
                    }

                    $ValDtls=array(
                        "AssVal"   => round($tot_bill_value),
                        "SgstVal"  => round($tot_sgst),
                        "CgstVal"  => round($tot_cgst),
                        "IgstVal"  => round($tot_igst),  
                        "TotInvVal"=> round($tot_bill_value_tax)
                    );

                    $itmData=array();
                    $TranDtls=array(
                        "TaxSch"=>"GST",
                        "SupTyp"=>"B2B",
                        "RegRev"=>"N"
                    );
                    $DocDtls=array(
                        "Typ"=> "INV",
                        "No" => "29",
// "No" => $doc_id,
                        "Dt" => date('d/m/Y',strtotime($doc_date))
                    );

                    $SellerDtls=array(
                        "Gstin" => '27AADCK7940H006',
// "Gstin" => $hb_config_data['hb_gstid'],
                        "LglNm" => substr($to_comp_id['branch_name'],0,95),
                        "TrdNm" => substr($to_comp_id['branch_name'],0,95),
                        "Addr1" => substr($to_comp_id['branch_address'],0,95),
                        "Loc"   => $to_comp_id['branch_state_name'],
                        "Pin"   => $to_comp_id['branch_pincode'],
                        "Stcd"  => substr($hb_config_data['hb_gstid'],0,2)

                    );
                    $BuyerDtls=array(
                        "Gstin"=> $from_comp_data['company_gstin'],
                        "LglNm"=> substr($comp_id['branch_name'],0,95),
                        "TrdNm"=> substr($comp_id['branch_name'],0,95),
                        "Pos"  => substr($from_comp_data['company_gstin'],0,2),
                        "Addr1"=> substr($comp_id['branch_address'],0,95),
                        "Loc"  => $comp_id['branch_state_name'],
                        "Pin"  => $comp_id['branch_pincode'],
                        "Stcd" => substr($from_comp_data['company_gstin'],0,2)
                    );

                    $api_data=array(
                        "Version"   => '1.1',
                        "TranDtls"  => $TranDtls,
                        "DocDtls"   => $DocDtls,
                        "SellerDtls"=> $SellerDtls,
                        "BuyerDtls" => $BuyerDtls,
                        "ItemList"  => $item_list,
                        "ValDtls"  => $ValDtls

                    );

                    $Secret_Key=array('Secret-Key:'.$hb_config_data['hb_einvsecretkey'],'Authorization:Bearer '.$hb_config_data['hb_einvtoken']);
                    $hb_einv_irn_data=$this->Hostbook_Model->hb_einv_irn_number($api_data,$Secret_Key);
                    if($hb_einv_irn_data['isSuccess']==1){
                        $irn_eway_bill=array(
                            "Irn"=> $hb_einv_irn_data['respObj']['irn'], 
                            "Distance"=> "0", 
// "TransId"=> $transporter_data['transport_vendor_gst']
                            "TransId"=> "04AADCK7940H005"
                        );
                        $hb_einv_eway_data=$this->Hostbook_Model->hb_einv_eway_data($irn_eway_bill,$Secret_Key);
                        if($hb_einv_eway_data['isSuccess']==1){
                            $eway_ins_data=array(
                                "idoutword_no" => $doc_id,
                                "bill_type" => '1',
                                "ewb_no" => $hb_einv_eway_data['respObj']['ewbNo'],
                                "ewb_date" => date('Y-m-d H:i:s',strtotime($hb_einv_eway_data['respObj']['ewbDt'])),
                                "ewb_irnno" => $hb_einv_irn_data['respObj']['irn'],
                                "ewb_signedInvoice" => $hb_einv_irn_data['respObj']['signedInvoice'],
                                "ewb_signedQRCode" => $hb_einv_irn_data['respObj']['signedQRCode']
                            );
                            $ele = $this->common_model->insertRow($eway_ins_data,'eway_einvoice_data');
                            $response['status']=true;
                            $response['message']='Eway Bill Generated with no '.$hb_einv_eway_data['respObj']['ewbNo'];
                        }else{
                            $response['status']=false;
                            $response['message']='Eway Bill Not Generated';
                        }
                    }else{
                        $response['status']=false;
                        $response['message']='Eway Bill IRN Not Generated'; 
                    }

                }else{
                    $response['status']=false;
                    $response['message']='Host Book Authentication Failed';
                }
            }else{
                $response['status']=false;
                $response['message']='Host Book Authentication Failed';
            }

        }else{
            $response['status']=false;
            $response['message']='Host Book Login Failed';
        }
        return $response;

    }


    public function hb_einv_login($comp_id){

        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>2));
        $Secret_Key=$hb_config_data['hb_login_secretkey'];
        $data=array(
            "loginid"=>$hb_config_data['hb_loginid'],
            "password"=>$hb_config_data['hb_password'],
            "conneectorId"=>$hb_config_data['hb_connectorid']
        );
        return $hb_data= $this->Hostbook_Model->getLoginEinvData($data,$Secret_Key);

    }
    public function hb_einv_authenticate($comp_id){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>2));
        $auth_data=array(
            "user_account_no"=>$hb_config_data['hb_userid'],
            "Connectorid"=>$hb_config_data['hb_connectorid'],

        );
        $Secret_Key=$hb_config_data['hb_login_secretkey'];
        return $hb_data= $this->Hostbook_Model->getAuthEinvData($auth_data,$Secret_Key);
    }

    public function hb_einv_auth_token($comp_id){
        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id,'api_type'=>2));

        $Secret_Key=array('Secret-Key:'.$hb_config_data['hb_einvsecretkey'],'Authorization:Bearer '.$hb_config_data['hb_einvtoken']);

        return $hb_data= $this->Hostbook_Model->getAuthTokenEinvData($hb_config_data['hb_gstid'],$Secret_Key);
    }

    public function printEInvoice()
    {
        $from_branch=$this->uri->segment(3);
        $idoutword=$this->uri->segment(2);
        $comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$from_branch));
        $q['einv_data']= $this->common_model->getSingleRow('eway_einvoice_data',array('idoutword_no'=>$idoutword));
        $idallocation=null;
        $q['tab_active'] = ''; 
        if($q['einv_data']['bill_type']=='3'){
            $q['outward_data']= $this->Hostbook_Model->get_sale_byid($idoutword);

            // $q['sale_product']= $this->common_model->getRecords('sale_product','*',array('idsale'=>$q['outward_data'][0]->id_outward));
                $q['sale_product'] = $this->Hostbook_Model->get_branch_sale_by_id($idallocation,$q['outward_data'][0]->id_outward);
                // print_r($q['sale_product']);die();
        }else{
            $q['outward_data']= $this->Hostbook_Model->get_outword_byid($idoutword);
            if(!empty($q['outward_data'])){

                $q['sale_product'] = $this->Hostbook_Model->get_branch_outword_by_id($idallocation,$q['outward_data'][0]->id_outward);

            }else{
                $q['outward_data']= $this->Hostbook_Model->get_transfer_byid($idoutword);
                $q['sale_product'] = $this->Hostbook_Model->get_branch_transfer_by_id($idallocation,$q['outward_data'][0]->id_outward);

            }
        }
        $q['comp_id']=$comp_id;
        $q['from_comp_data']= $this->common_model->getSingleRow('company',array('company_id'=>$comp_id['idcompany']));

        $this->load->view('allocation/print-e-invoice-report', $q);


    }

    public function printEway()
    {
        $from_branch=$this->uri->segment(3);
        $idoutword=$this->uri->segment(2);
        $comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$from_branch));

        $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id['idcompany'],'api_type'=>2));

        $hb_login_data=$this->hb_einv_login($comp_id['idcompany']);

        if($hb_login_data['isSuccess']==1){

            $hb_auth_data=$this->hb_einv_authenticate($comp_id['idcompany']);
            if($hb_auth_data['isSuccess']==1){

                $eway_type_data= $this->common_model->getSingleRow('eway_einvoice_data',array('ewb_no'=>$idoutword));

                $q['tab_active'] = ''; 
                if(!empty($eway_type_data['ewb_irnno'])){

                    $Secret_Key=array('Secret-Key:'.$hb_auth_data['hbsecretkey'],'Authorization:Bearer '.$hb_auth_data['hbToken'],'buid:'.$hb_login_data['hbLoginRes']['buid']);
                    $eway_btype='IRN';
                }else{
                    $Secret_Key=array('Secret-Key:'.$hb_auth_data['hbsecretkey'],'Authorization:Bearer '.$hb_auth_data['hbToken']);
                    $eway_btype='EWAY';
                }

                $q['eway_print_data']=$this->Hostbook_Model->getewaybillprint($idoutword,$Secret_Key,$eway_btype);

                echo '<embed src="data:application/pdf;base64,'.$q['eway_print_data']['pdfstream'].'" type="application/pdf" width="100%" height="100%" target="_blank"/>';
            }
        }
    }

    function generateEInvoiceBulk(){

        $q['tab_active'] = '';
        $q['company_data'] = $this->General_model->get_company_data();

        $this->load->view('sale/sale_einvoice_generate',$q);

    }

    function generateEInvoiceBulkData(){

        $sale_id='';

        $q['sale_inv_data']=$this->Hostbook_Model->getSaleInvoiceData($this->input->post('from'),$this->input->post('to'),$this->input->post('idcompany'),$sale_id);

        if(!empty($q['sale_inv_data'])){ 
            ?>
            <table class="table table-bordered table-striped table-condensed table-info" id="sale_inv_data">
                <thead style="background: #49c5bf;">
                    <tr>
                        <th>Sr No </th>
                        <th>Sales Id</th>
                        <th>Sales Date</th>
                        <th>Sales Invoice</th>
                        <th>Customer Name</th>
                        <th>Customer GST</th>
                        <th>Action</th>
                        <th>Print</th>
                    </tr>
                </thead>
                <tbody class="data_1">

                    <?php 
                    $srno=0;
                    foreach($q['sale_inv_data'] as $sale_inv){ 
                        $srno=$srno+1;
                        $eway_type_data= $this->common_model->getSingleRow('eway_einvoice_data',array('idoutword_no'=>$sale_inv->id_sale,'bill_type'=>'3'));
                        ?>
                        <tr>
                            <td><input type="checkbox" name="chk_idsale[]" data-id="<?php echo $sale_inv->id_sale;?>"><?php echo ' '.$srno;?></td>
                            <td><?php echo $sale_inv->id_sale;?></td>
                            <td><?php echo $sale_inv->date;?></td>
                            <td><?php echo $sale_inv->inv_no;?></td>
                            <td><?php echo $sale_inv->customer_fname.' '.$sale_inv->customer_lname;?></td>
                            <td><?php echo $sale_inv->customer_gst;?></td>
                            <td><button type="button" class="btn btn-sm btn-info generate-inv" data-id="<?php echo $sale_inv->id_sale;?>">Generate</button></td>

                            <td> <?php if(!empty($eway_type_data)){ ?>
                                <a href="<?php echo base_url().'Print-e-invoice/'.$sale_inv->id_sale.'/1'?>" target="_blank"><button type="button" class="btn btn-sm btn-success" data-id="<?php echo $sale_inv->id_sale;?>">Print</button></a><?php } ?>
                            </td>

                        </tr>
                    <?php }  ?>

                </tbody>
            </table>
        <?php }  

    }

    public function generateEinvoiceB2Bsale(){

        for($g=0;$g<sizeof($this->input->post('id_sale'));$g++){
            $id_sale=$this->input->post('id_sale')[$g];
            $sale_inv_data= $this->common_model->getSingleRow('sale',array('id_sale'=>$id_sale));
            $comp_id= $this->common_model->getSingleRow('branch',array('id_branch'=>$sale_inv_data['idbranch']));

            $hb_login_data=$this->hb_einv_login($comp_id['idcompany']);

            if($hb_login_data['isSuccess']==1){
                $hb_config_data= $this->common_model->getSingleRow('hostbook_config',array('company_id'=>$comp_id['idcompany'],'api_type'=>2));


                $hbdata['hb_token']=$hb_login_data['hbLoginRes']['token'];
                $hbdata['hb_userid']=$hb_login_data['hbLoginRes']['user_account_no'];
                $ins= $this->common_model->updateRow('hostbook_config', $hbdata, array('company_id'=>$comp_id['idcompany'],'api_type'=>2));
                $hb_auth_data=$this->hb_einv_authenticate($comp_id['idcompany']);
                if($hb_auth_data['isSuccess']==1){

                    $hb_einvdata['hb_einvtoken']=$hb_auth_data['hbToken'];
                    $hb_einvdata['hb_einvsecretkey']=$hb_auth_data['hbsecretkey'];
                    $auth_upd= $this->common_model->updateRow('hostbook_config', $hb_einvdata, array('company_id'=>$comp_id['idcompany'],'api_type'=>2));
                    $hb_auth_token_data=$this->hb_einv_auth_token($comp_id['idcompany']);

                    if($hb_auth_token_data['isSuccess']==1){

                        $customer_data= $this->common_model->getSingleRow('customer',array('id_customer'=>$sale_inv_data['idcustomer']));

                        $from_comp_data= $this->common_model->getSingleRow('company',array('company_id'=>$comp_id['idcompany']));


                        $item_list=array();
                        $ValDtls=array();
                        $mainhsncode=0;
                        $tot_bill_value=0;
                        $tot_bill_value_tax=0;
                        $itmNO=0;
                        $tot_cgst=0;
                        $tot_sgst=0;
                        $tot_igst=0;

                        $sale_Product_data= $this->common_model->getRecords('sale_product','*',array('idsale'=>$sale_inv_data['id_sale']));
                        foreach($sale_Product_data as $item_data){
                            $itmNO=$itmNO+1;
                            $product_data= $this->common_model->getSingleRow('model_variants',array('id_variant'=>$item_data['idvariant']));
                            $hsn_data= $this->common_model->getSingleRow('category',array('id_category'=>$item_data['idcategory']));


                            $cgst_amt=$sgst_amt= $igst_amt=0;

                            $doctype='INV';
                            if(substr($customer_data['customer_gst'],0,2)==substr($hb_config_data['hb_gstid'],0,2)){                               
                                $prod_cgst=$item_data['cgst_per'];
                                $prod_sgst=$item_data['sgst_per'];
                                $prod_igst=0;
                                $gst=$prod_cgst+$prod_sgst;
                                $gst_amount=($item_data['price']*$gst)/($gst+100);
                                $cgst_amt= ($gst_amount/2);
                                $sgst_amt= ($gst_amount/2);

                            }else{
                                $prod_cgst=0;
                                $prod_sgst=0;
                                $prod_igst=$item_data['igst_per'];
                                $gst=$prod_igst;
                                $gst_amount=($item_data['price']*$gst)/($gst+100);
                                $igst_amt= ($gst_amount);
                            }            

                            $subSupplyType=1;
                            $taxeble_amount=$item_data['price']-$gst_amount;
                            $tot_bill_value=$tot_bill_value+$taxeble_amount;
                            $tot_bill_value_tax=$tot_bill_value_tax+$item_data['price'];
                            $tot_cgst=$tot_cgst+$cgst_amt;
                            $tot_sgst=$tot_sgst+$sgst_amt;
                            $tot_igst=$tot_igst+$igst_amt;

                            $item_array=array(
                                "SlNo"=> "$itmNO", 
                                "PrdDesc"=> substr($product_data['full_name'],0,95), 
                                "IsServc"=> 'N', 
                                "HsnCd"=> $hsn_data['hsn'], 
                                "Qty"=> $item_data['qty'], 
                                "Unit"=> "NOS", 
                                "UnitPrice"=> round($taxeble_amount), 
                                "TotAmt"=> round($taxeble_amount),
                                "AssAmt"=> round($taxeble_amount),
                                "GstRt"=> $gst,
                                "SgstAmt"=> round($sgst_amt), 
                                "CgstAmt"=> round($cgst_amt), 
                                "IgstAmt"=> round($igst_amt), 
                                "TotItemVal"=> $item_data['price']
                            );
                            array_push($item_list,$item_array);

                        }

                        $ValDtls=array(
                            "AssVal"   => round($tot_bill_value),
                            "SgstVal"  => round($tot_sgst),
                            "CgstVal"  => round($tot_cgst),
                            "IgstVal"  => round($tot_igst),  
                            "TotInvVal"=> round($tot_bill_value_tax)
                        );

                        $itmData=array();
                        $TranDtls=array(
                            "TaxSch"=>"GST",
                            "SupTyp"=>"B2B",
                            "RegRev"=>"N"
                        );
                        $DocDtls=array(
                            "Typ"=> "INV",
                            "No" => $sale_inv_data['inv_no'],
                            "Dt" => date('d/m/Y',strtotime($sale_inv_data['date']))
                        );

                        $SellerDtls=array(
                            "Gstin" => '27AADCK7940H006',
// "Gstin" => $hb_config_data['hb_gstid'],
                            "LglNm" => substr($comp_id['branch_name'],0,95),
                            "TrdNm" => substr($comp_id['branch_name'],0,95),
                            "Addr1" => substr($comp_id['branch_address'],0,95),
                            "Loc"   => $comp_id['branch_state_name'],
                            "Pin"   => $comp_id['branch_pincode'],
                            "Stcd"  => substr($hb_config_data['hb_gstid'],0,2)

                        );
                        $BuyerDtls=array(
                            "Gstin"=> $customer_data['customer_gst'],
                            "LglNm"=> substr($customer_data['customer_fname'],0,95),
                            "TrdNm"=> substr($customer_data['customer_fname'],0,95),
                            "Pos"  => substr($customer_data['customer_gst'],0,2),
                            "Addr1"=> substr($customer_data['customer_address'],0,95),
                            "Loc"  => $customer_data['customer_state'],
                            "Pin"  => $customer_data['customer_pincode'],
                            "Stcd" => substr($customer_data['customer_gst'],0,2)
                        );

                        $api_data=array(
                            "Version"   => '1.1',
                            "TranDtls"  => $TranDtls,
                            "DocDtls"   => $DocDtls,
                            "SellerDtls"=> $SellerDtls,
                            "BuyerDtls" => $BuyerDtls,
                            "ItemList"  => $item_list,
                            "ValDtls"  => $ValDtls

                        );

                        $Secret_Key=array('Secret-Key:'.$hb_config_data['hb_einvsecretkey'],'Authorization:Bearer '.$hb_config_data['hb_einvtoken']);
                        $hb_einv_irn_data=$this->Hostbook_Model->hb_einv_irn_number($api_data,$Secret_Key);


                        if($hb_einv_irn_data['isSuccess']==1){

                            $eway_ins_data=array(
                                "idoutword_no" => $sale_inv_data['id_sale'],
                                "bill_type" => '3',
                                "ewb_irnno" => $hb_einv_irn_data['respObj']['irn'],
                                "ewb_signedInvoice" => $hb_einv_irn_data['respObj']['signedInvoice'],
                                "ewb_signedQRCode" => $hb_einv_irn_data['respObj']['signedQRCode']
                            );

                            $ele = $this->common_model->insertRow($eway_ins_data,'eway_einvoice_data');
                            $response['status']=true;
                            $response['message']='E Invoice Bill Generated with IRN no '.$hb_einv_irn_data['respObj']['irn'];

                        }else{
                            $response['status']=false;
                            $response['message']=$hb_einv_irn_data['txnOutcome'];
                            echo json_encode($response);
                            exit;
                        }

                    }else{
                        $response['status']=false;
                        $response['message']='Host Book Authentication Failed';
                        echo json_encode($response);
                        exit;
                    }
                }else{
                    $response['status']=false;
                    $response['message']='Host Book Authentication Failed';
                    echo json_encode($response);
                    exit;
                }

            }else{
                $response['status']=false;
                $response['message']='Host Book Login Failed';
                echo json_encode($response);
                exit;
            }
        }
        echo json_encode($response);


    }

    function eInvoiceEwayReport(){

        $q['tab_active'] = '';
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('sale/sale_einvoice_eway_report',$q);
    }

    function eInvoiceEwayReportData(){
        $sale_id='';
        $q['sale_inv_data']= $this->common_model->getRecords('eway_einvoice_data','*',array('bill_type'=>$this->input->post('billtype')));
        if(!empty($q['sale_inv_data'])){ ?>
            <table class="table table-bordered table-striped table-condensed table-info" id="sale_inv_data">
                <thead style="background: #49c5bf;">
                    <tr>
                        <th>Sr No </th>
                        <th>Invoice/DC No</th>
                        <th>Date</th>
                        <?php if($this->input->post('billtype')!=3){ ?>
                            <th>EWAY Bill Id</th>
                        <?php } ?>
                        <?php if($this->input->post('billtype')==3 || $this->input->post('billtype')==1 ){ ?>
                            <th>IRN No</th>
                        <?php } ?>
                        <?php if($this->input->post('billtype')!=3){ ?>
                            <th>EWAY Print</th>
                        <?php } ?>
                        <?php if($this->input->post('billtype')==3 || $this->input->post('billtype')==1 ){ ?>
                            <th>E Invoice Print</th>
                        <?php } ?>

                    </tr>
                </thead>
                <tbody class="data_1">

                    <?php 
                    $srno=0;
                    $inv_no=0;
                    $inv_date=0;
                    foreach($q['sale_inv_data'] as $sale_inv){ 
                        $srno=$srno+1;                
                        if(($sale_inv['bill_type']!='3')){
                            $out_data= $this->common_model->getSingleRow('outward',array('id_outward'=>$sale_inv['idoutword_no']));
                            if(empty($out_data)){
                                $out_data= $this->common_model->getSingleRow('transfer',array('id_transfer'=>$sale_inv['idoutword_no']));
                                $inv_no=$out_data['id_transfer'];
                                $inv_date=$out_data['date'];
                            }else{
                                $inv_no=$out_data['id_outward'];
                                $inv_date=$out_data['date'];
                            }

                        }else{
                            $out_data= $this->common_model->getSingleRow('sale',array('id_sale'=>$sale_inv['idoutword_no']));
                            $inv_no=$out_data['inv_no'];
                            $inv_date=$out_data['date'];

                        }


                        ?>

                        <tr>
                            <td><?php echo ' '.$srno;?></td>
                            <td><?php echo $inv_no;?></td>
                            <td><?php echo $inv_date;?></td>
                            <?php if($this->input->post('billtype')!=3){ ?>
                                <td><?php echo $sale_inv['ewb_no'];?></td>
                            <?php } ?>
                            <?php if($this->input->post('billtype')==3 || $this->input->post('billtype')==1 ){ ?>
                                <td><?php echo $sale_inv['ewb_irnno'];?></td>
                            <?php }
                            if(($sale_inv['bill_type']!='3')){ ?>
                                <td> <a href="<?php echo base_url().'Print-e-way/'.$sale_inv['ewb_no'].'/1'?>" target="_blank"><button type="button" class="btn btn-sm btn-success" data-id="<?php echo $sale_inv['idoutword_no'];?>">Print</button></a></td>
                            <?php } ?>                              

                            <td> <?php
                            if(!empty($sale_inv['ewb_irnno'])){
                                if(($sale_inv['bill_type']=='3')){ ?>
                                    <a href="<?php echo base_url().'Print-e-invoice/'.$sale_inv['idoutword_no'].'/1'?>" target="_blank"><button type="button" class="btn btn-sm btn-success" data-id="<?php echo $sale_inv['idoutword_no'];?>">Print</button></a>
                                <?php }else{ ?>

                                    <a href="<?php echo base_url().'Print-e-invoice/'.$sale_inv['idoutword_no'].'/1'?>" target="_blank"><button type="button" class="btn btn-sm btn-success" data-id="<?php echo $sale_inv['idoutword_no'];?>">Print</button></a>
                                <?php } 
                            } ?>
                        </td>


                    </tr>
                    <?php 
                } ?>

            </tbody>
        </table>
    <?php }  

}


}
?>