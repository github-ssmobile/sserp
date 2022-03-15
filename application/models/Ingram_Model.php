<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ingram_Model extends CI_Model {

    var $grant_type = "client_credentials";
    var $auth_token = "https://api.ingrammicro.com:443/oauth/oauth20/token?";
    var $header1 = "IM-CustomerNumber:40-SSSEPV";
    var $header2 = "IM-CorrelationID:55667788";
    var $header3 = "IM-CountryCode:IN";
    var $header4 = "IM-SenderID:SampleUser";
    //////// UAT ////////
    var $client_id = "xplu0OXk3GAkDVra8BYAYjuAYggYrngW";
    var $client_secret = "ABZcWNWeUdh86YEI";
    //////// LIVE ////////
    var $client_id_inuse = "0RDIfDI2vLVPFG3dTjtvjbaucEI9VeOO";
    var $client_secret_inuse = "XtEWr0IWqdJAUvsJ";
    //////// UAT ////////
    var $priceandavailability = "https://api.ingrammicro.com:443/sandbox/resellers/v5/catalog/priceandavailability";
    var $ordercreate_v6 = "https://api.ingrammicro.com:443/sandbox/resellers/v6/orders";
    //////// LIVE ////////
    var $priceandavailability_inuse = "https://api.ingrammicro.com:443/resellers/v5/catalog/priceandavailability";
    var $ordercreate_v6_inuse = "https://api.ingrammicro.com:443/resellers/v6/orders";

    function getToken() {
//        if(!isset($_SESSION['access_token']) || $_SESSION['access_token']==null){
        $data = array();
        $data['grant_type'] = $this->grant_type;
        $data['client_id'] = $this->client_id_inuse;
        $data['client_secret'] = $this->client_secret_inuse;
        $fields_string = "";
        if ($data) {
            foreach ($data as $key => $value) {
                $fields_string .= $key . '=' . $value . '&';
            }
        }
        rtrim($fields_string, '&');
        $result = $this->rest->request($this->auth_token . $fields_string, "GET", $data);
        $result = json_decode($result, true);
        $this->db->where('id_vendors_sku', 1)->set('access_token', $result['access_token'])->update('vendors_sku');
//        die($this->db->last_query());
//        $_SESSION['access_token']= $result['access_token'];
//        $_SESSION['token_type']= $result['token_type']; 
        return $result;
//        }
    }

    function getPriceAndAvailability($data, $access_token) {

        $authorization = array("Authorization: Bearer " . $access_token);
        $result = $this->rest->request($this->priceandavailability_inuse, "POST", json_encode($data), 0, $authorization);
        $result = json_decode($result, true);
        return $result;
    }

    function OrderCreate_v6($data, $access_token) {
        $authorization = array("Authorization: Bearer " . $access_token, $this->header1, $this->header2, $this->header3, $this->header4);
        $result = $this->rest->request($this->ordercreate_v6_inuse, "POST", json_encode($data), 0, $authorization);
        $result = json_decode($result, true);
        return $result;
    } 
    public function ajax_get_booked_qty($variant, $idbranch,$idgodown) {
         return $this->db->select("SUM(qty) AS booked_qty")
                        ->where('sale_token_product.idvariant', $variant)                        
                        ->where('sale_token_product.idgodown', $idgodown)
                        ->where('sale_token.ingram_status', 2)
                        ->where('sale_token_product.idsaletoken = sale_token.id_sale_token')->from('sale_token')
                        ->get('sale_token_product')->row();
         die($this->db->last_query());
    }
    public function ajax_get_variant_byid_branch_godown($variant, $idbranch,$idgodown) { 
        return $this->db->select("sum(qty) as avail_qty")
                        ->where('stock.idvariant', $variant)
                        ->where('stock.idbranch', $idbranch)                
                        ->where('stock.idgodown', $idgodown)
                        ->get('stock', 1)->row();
    } 
    public function update_vendor_products($data) {
        return $this->db->update_batch('vendor_po_product', $data, 'id_vendor_po_product');
    } 
    public function update_vendor_po($data) {
        return $this->db->update_batch('vendor_po', $data, 'id_vendor_po');
    } 
    public function save_vendor_po($data) {
        $this->db->insert('vendor_po', $data);
        return $this->db->insert_id();
    }

    public function save_vendor_po_products($data) {
        return $this->db->insert_batch('vendor_po_product', $data);
    } 
    public function delete_sale_by_tokenid($id) {
        return $this->db->where('idsaletoken', $id)->delete('sale');
    } 
    public function update_vendor_products_byid_vendorpo($data) {
        return $this->db->update_batch('vendor_po_product', $data, 'idvendor_po');
    }
public function save_ingram_order_history($data){
        $this->db->insert('ingram_order_history', $data);
        return $this->db->insert_id();
    }
    public function ajax_get_purchaseorder_databy_idpo($id_po) {
        return $this->db->select('vendor_po_product.remark as p_remark,mv.landing,mv.cgst,mv.sgst,mv.igst,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,mv.idmodel,vendor_po_product.*,sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.id_vendor_po', $id_po)
                        ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                        ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                        ->join('model_variants mv', 'mv.id_variant=vendor_po_product.idvariant')
                        ->where('vendor_po.id_vendor_po = vendor_po_product.idvendor_po')->from('vendor_po_product')
                        ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                        ->order_by('vendor_po.id_vendor_po')
                        ->get('vendor_po')->result();
    }

    public function ajax_get_purchase_order_databy_idpo($id_po, $idsaletoken = 0) {
        return $this->db->select('s.stkids,vendor_po_product.remark as p_remark,mv.landing,mv.cgst,mv.sgst,mv.igst,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,mv.idmodel,vendor_po_product.*,sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.id_vendor_po', $id_po)
                        ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                        ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                        ->join('model_variants mv', 'mv.id_variant=vendor_po_product.idvariant')
                        ->join('(select GROUP_CONCAT(sale_token_product.id_saletokenproduct) as stkids,idvariant from sale_token_product where sale_token_product.idsaletoken = ' . $idsaletoken . ' group by sale_token_product.idvariant) s', 's.idvariant=vendor_po_product.idvariant', 'left')
                        ->where('vendor_po.id_vendor_po = vendor_po_product.idvendor_po')->from('vendor_po_product')
                        ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                        ->where('sale_token_product.idsaletoken = sale_token.id_sale_token')->from('sale_token_product')
                        ->order_by('vendor_po.id_vendor_po')
                        ->group_by(' vendor_po_product.id_vendor_po_product')
                        ->get('vendor_po')->result();
    }
    public function ajax_get_branch_order_databy_idsaletoken($idsaletoken) {
        return $this->db->select('sale_token_product.*,mv.ingram as vendor_sku,mv.landing,mv.cgst,mv.sgst,mv.igst,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,mv.idmodel,sale_token.*,branch.branch_name,branch.id_branch')->where('sale_token.id_sale_token', $idsaletoken)
                        ->where('sale_token.idbranch = branch.id_branch')->from('branch')                        
                        ->join('model_variants mv', 'mv.id_variant=sale_token_product.idvariant')                        
                        ->where('sale_token_product.idsaletoken = sale_token.id_sale_token')->from('sale_token_product')                                                
                        ->get('sale_token')->result();
    }
    public function get_purchase_order_byid($idpo) {
        return $this->db->select('vendor_po.*,b.branch_name as branchname,wb.id_branch as id_warehouse,wb.*,vendor.*')
                        ->where('vendor_po.id_vendor_po', $idpo)
                        ->where('vendor_po.idbranch = b.id_branch')->from('branch b')
                        ->join('(select bb.* from branch bb) wb', 'wb.id_branch=vendor_po.idwarehouse', 'left')
                        ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                        ->get('vendor_po')->row();
    }
    public function get_purchase_order_product_byid($idpo) {
        return $this->db->where('vendor_po_product.idvendor_po', $idpo)
                        ->where('model_variants.idsku_type = sku_type.id_sku_type')->from('sku_type')
                        ->where('vendor_po_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('vendor_po_product')->result();
//        die($this->db->last_query());
    }
    public function update_purchase_order($id, $data) {
        return $this->db->where('id_vendor_po', $id)->update('vendor_po', $data);
    }
    public function update_sale_payment_reconciliation($id_saletokenpayment, $inv_no, $data) {
        return $this->db->where("idsale_payment", $id_saletokenpayment)->where("inv_no", $inv_no)->update('payment_reconciliation', $data);
    }
    public function delete_stock_by_imei($imei_no) {
        return $this->db->where('imei_no', $imei_no)->delete('stock');
    }
    public function ajax_get_purchase_order_data($status, $from, $to) {
        if ($from == '' && $to == '' && $status != '') {
            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.status', $status)
                            ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                            ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                            ->where('vendor_po.id_vendor_po = vendor_po_product.idvendor_po')->from('vendor_po_product')
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        } elseif ($from != '' && $to != '' && $status == '') {
            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.date >=', $from)
                            ->where('vendor_po.date <=', $to)
                            ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                            ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        } elseif ($from != '' && $to != '' && $status != '') {
            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.status', $status)
                            ->where('vendor_po.date >=', $from)
                            ->where('vendor_po.date <=', $to)
                            ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                            ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        } else {

            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor.vendor_name,vendor_po.*')->where('vendor_po.status', $status)
                            ->where('vendor_po.idbranch = branch.id_branch')->from('branch')
                            ->where('vendor_po.idvendor = vendor.id_vendor')->from('vendor')
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        }
    }

    public function get_pending_sale_token_data($status, $idbranch, $from, $to) {
        $this->db->select('ingram_order_history.*,st.*,customer.customer_fname,customer.customer_lname,customer.customer_contact,users.user_name,branch.*,sale_token_product.idvariant,sale_token_product.qty,mv.ingram as sku,mv.part_number,mv.full_name');
        if ($status != '') {
            $status = explode(',', $status);
            $this->db->where_in('st.ingram_status', $status);
        }
        if ($idbranch > 0) {
            $this->db->where('st.idbranch', $idbranch);
        }
        $this->db->where('sale_token_product.idsaletoken = st.id_sale_token')->from('sale_token_product');
        $this->db->where('sale_token_product.idvariant = mv.id_variant')->from('model_variants mv');
        $this->db->where('st.idbranch = branch.id_branch')->from('branch') ;
        if ($from != '' && $to != '') {
            $this->db->where('st.date >=', $from)->where('st.date <=', $to);
        }
        $this->db->where('st.ingram_status > 0')
                ->where('st.idcustomer = customer.id_customer')->from('customer')
                ->where('st.id_sale_token = ingram_order_history.idsaletoken')->from('ingram_order_history')
                ->where('st.idsalesperson = users.id_users')->from('users');               
         $this->db->get('sale_token st')->result();

        die($this->db->last_query());
    }
    public function get_ingram_order_history_idsaletoken($idsaletoken) {
        return $this->db->where('idsaletoken',$idsaletoken)->get('ingram_order_history')->result();
    } 
    public function get_saletoken_byid($idsaletoken) {
            return $this->db->select('ingram_order_history.*,sale_token.*,  sale_token.entry_time as invoice_date,users.user_name,print_head.*,customer.*,branch.*')->where('id_sale_token',$idsaletoken)
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('sale_token.idbranch=branch.id_branch')->from('branch')
                        ->where('sale_token.idcustomer=customer.id_customer')->from('customer')
                        ->where('sale_token.idsalesperson=users.id_users')->from('users')    
                        ->where('sale_token.id_sale_token = ingram_order_history.idsaletoken')->from('ingram_order_history')                    
                        ->order_by('id_sale_token','desc')
                        ->get('sale_token')->result();
    }
    public function get_pending_sale_token_new($status, $idbranch, $from, $to) {
        $this->db->select('sale.inv_no,st.*,customer.customer_fname,customer.customer_lname,customer.customer_contact ,branch.branch_name,sale_token_product.idvariant,sum(sale_token_product.qty) as qty,mv.ingram as sku,mv.part_number,mv.full_name');
        if ($status != '') {
            $status = explode(',', $status);
            $this->db->where_in('st.ingram_status', $status);
        }
        if ($idbranch > 0) {
            $this->db->where('st.idbranch', $idbranch);
        }
        $this->db->join('sale_token_product','sale_token_product.idsaletoken = st.id_sale_token');
        $this->db->join('model_variants mv','sale_token_product.idvariant = mv.id_variant');
        $this->db->join('branch','st.idbranch = branch.id_branch');
        $this->db->join('customer','st.idcustomer = customer.id_customer');
        $this->db->join('sale','st.id_sale_token = sale.idsaletoken','left');
        if ($from != '' && $to != '') {
            $this->db->where('st.date >=', $from)->where('st.date <=', $to);
        }
        $this->db->group_by("sale_token_product.idsaletoken");
        $this->db->where('st.ingram_status > 0');              
//                ->where('st.id_sale_token = ingram_order_history.idsaletoken')->from('ingram_order_history')
//                ->where('st.idsalesperson = users.id_users')->from('users');               
        return $this->db->get('sale_token st')->result();

        die($this->db->last_query());
    }
    public function get_pending_sale_token($status, $idbranch, $from, $to) {
        $this->db->select('sale.inv_no,ingram_order_history.*,st.*,customer.customer_fname,customer.customer_lname,customer.customer_contact,users.user_name,branch.*,sale_token_product.idvariant,sum(sale_token_product.qty) as qty,mv.ingram as sku,mv.part_number,mv.full_name');
        if ($status != '') {
            $status = explode(',', $status);
            $this->db->where_in('st.ingram_status', $status);
        }
        if ($idbranch > 0) {
            $this->db->where('st.idbranch', $idbranch);
        }
        $this->db->where('sale_token_product.idsaletoken = st.id_sale_token')->from('sale_token_product');
        $this->db->where('sale_token_product.idvariant = mv.id_variant')->from('model_variants mv');
        $this->db->where('st.idbranch = branch.id_branch')->from('branch') ;
//        $this->db->where('sale.idsaletoken=st.id_sale_token')->from('sale') ;
         $this->db->join('sale', 'sale.idsaletoken=st.id_sale_token', 'left');
        if ($from != '' && $to != '') {
            $this->db->where('st.date >=', $from)->where('st.date <=', $to);
        }
        $this->db->group_by("sale_token_product.idsaletoken");
        $this->db->where('st.ingram_status > 0')
                ->where('st.idcustomer = customer.id_customer')->from('customer')
                ->where('st.id_sale_token = ingram_order_history.idsaletoken')->from('ingram_order_history')
                ->where('st.idsalesperson = users.id_users')->from('users');               
        return $this->db->get('sale_token st')->result();

        die($this->db->last_query());
    }

    public function ajax_get_branch_ingram_order_data($idbranch, $status, $from, $to) {
        if ($from == '' && $to == '' && $status != '') {
            return $this->db->select('customer.*,vendor_po_product.vendor_sku as sku,vendor_po_product.confirmed_qty as qty,sale_token.deliver_at,sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor_po.*')
                            ->where('vendor_po.ingram_order_status', $status)
                            ->where('vendor_po.order_type', 1)
                            ->where('vendor_po.idbranch', $idbranch)->where('vendor_po.idbranch = branch.id_branch')->from('branch')                            
                            ->where('sale_token.idcustomer = customer.id_customer')->from('customer')
                            ->where('vendor_po_product.idvendor_po = vendor_po.id_vendor_po')->from('vendor_po_product')
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->group_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
            
        } elseif ($from != '' && $to != '' && $status == '') {
            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor_po.*')->where('vendor_po.date >=', $from)
                            ->where('vendor_po.date <=', $to)
                            ->where('vendor_po.order_type', 1)
                            ->where('vendor_po.idbranch', $idbranch)->where('vendor_po.idbranch = branch.id_branch')->from('branch')                            
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        } elseif ($from != '' && $to != '' && $status != '') {
            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor_po.*')->where('vendor_po.ingram_order_status', $status)
                            ->where('vendor_po.date >=', $from)
                            ->where('vendor_po.date <=', $to)
                            ->where('vendor_po.order_type', 1)
                            ->where('vendor_po.idbranch', $idbranch)->where('vendor_po.idbranch = branch.id_branch')->from('branch')                            
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        } else {

            return $this->db->select('sale_token.id_sale_token,branch.branch_name,branch.id_branch,vendor_po.*')->where('vendor_po.ingram_order_status', $status)
                            ->where('vendor_po.idbranch', $idbranch)
                            ->where('vendor_po.order_type', 1)
                            ->where('vendor_po.idbranch = branch.id_branch')->from('branch')                            
                            ->where('concat_ws("",vendor_po.financial_year,vendor_po.id_vendor_po) = sale_token.token_uid')->from('sale_token')
                            ->order_by('vendor_po.id_vendor_po')
                            ->get('vendor_po')->result();
        }
    }

    public function ajax_get_ingram_purchase_order_data($status, $from, $to) {

        $this->db->select('GROUP_CONCAT(vendor_po_product.vendor_sku) as sku,GROUP_CONCAT(vendor_po_product.ordered_qty) as oqty,GROUP_CONCAT(vendor_po_product.confirmed_qty) as qty,branch.branch_name,branch.id_branch,vendor_po.*');
        if ($status != '') {
            $status = explode(',', $status);           
            $this->db->where_in('vendor_po.status', $status);
        }
        if ($from != '' && $to != '') {
            $this->db->where('vendor_po.date >=', $from)->where('vendor_po.date <=', $to);
        }
       return $this->db->where('vendor_po.idwarehouse = branch.id_branch')->from('branch')
//                ->where('vendor_po_product.confirmed_qty > 0')
                ->where('vendor_po_product.idvendor_po = vendor_po.id_vendor_po')->from('vendor_po_product')
                ->order_by('vendor_po.id_vendor_po')
                ->group_by('vendor_po.id_vendor_po')
                ->get('vendor_po')->result();
       die($this->db->last_query());
    }

    public function ajax_get_ingram_sku_by_model_variant($id, $colum) {
        return $this->db->select($colum)->where("id_variant", $id)->get('model_variants')->row();
    }

    public function ajax_get_ingram_sku($brand_id, $colum,$limit=0,$start=0) {
        $this->db->select('*')->where("idbrand", $brand_id)->where($colum . "!=''")->where($colum . "!='NULL'");
        if($limit==0){}else{
            $this->db->limit($limit, $start);                
        }
        return $this->db->get('model_variants')->result();
        die($this->db->last_query());
    }

    public function update_sale_idsaletoken($idsaletoken, $data) {
        $this->db->where('idsaletoken', $idsaletoken)->update('sale', $data);
    }

    public function update_sale_payment($data, $inv_no) {
        return $this->db->where("inv_no", $inv_no)->update_batch('sale_payment', $data, 'idsale_payment');
    }

    public function update_sale_token_product($data) {
        return $this->db->update_batch('sale_token_product', $data, 'id_saletokenproduct');
    }

    public function update_sale_token($data) {
        return $this->db->update_batch('sale_token', $data, 'id_sale_token');
    }
    public function update_batch_ingram_order_history($data) {
        return $this->db->update_batch('ingram_order_history', $data, 'idsaletoken');
    }
    public function update_ingram_order_history($id, $data) {
        return $this->db->where('idsaletoken', $id)->update('ingram_order_history', $data);
    }
    public function get_sale_by_tokenid($idsaletoken) {
        return $this->db->select('sale.*, sale.customer_fname as sale_customer_fname, sale.customer_lname as sale_customer_lname, sale.entry_time as invoice_date')->where('idsaletoken', $idsaletoken)       //,customer.*,branch.*                 
//                        ->where('sale.idbranch=branch.id_branch')->from('branch')
//                        ->where('sale.idcustomer=customer.id_customer')->from('customer')                        
                        ->order_by('id_sale', 'desc')
                        ->get('sale')->result();
        
    }
    public function get_apob_branch_stock_by_variant($variantid,$idgodown,$idwarehouse) { 
        $this->db->select('z.zone_name,mv.id_variant,mv.idcategory,mv.full_name,mv.idmodel,mv.idproductcategory,`b`.`id_branch`, `b`.`branch_name`, sum(stk.qty) as stock_qty');        
        $this->db->where('stk.idbranch=b.id_branch')->from('branch b');
        $this->db->where('stk.idbranch',$idwarehouse)->where('stk.idgodown',$idgodown)->where('stk.idvariant',$variantid)->from('stock stk');        
        $this->db->where('mv.id_variant',$variantid)->from('model_variants mv'); 
        $this->db->where('z.id_zone=b.idzone')->from('zone z'); 
        $query = $this->db->get(); 
        return $query->result();
    }

    function short_url($url) {
        $data = array("longDynamicLink" => "https://ssmobile.page.link/?link=" . $url);
        $e = json_encode($data);
        $newurl = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyCsV4OaLPu_okJ0qZN8e7uh49oGc8T55go";
        $result = $this->rest->request($newurl, "POST", $e);
        $result = json_decode($result, true);
        return $result;
    }

}
