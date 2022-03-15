<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bfl_Model extends CI_Model {

    var $groupcode = "54429";
    var $header1 = "Acceskeyid:WDk4YVQrdGw4OVd2T3hhM2p6K1RsbTlqZ1pIVVNGR0ZXa3BQL29KVmpBNU9YSFZBRVpma3RvZVU3RGlLRzV4Uw==";
    var $header2 = "MarketPlaceId:TmtKNFVNeGI2VFBRMER0NHBzL2tpdzRwbk9IUEhLRmhYSlRZUWo5RmJqdmVFbkNmWkJlYy9jNHlVYmw5VkdERA==";
    var $header3 = "SecretAccessKey:Zk1paHo2TXFqaEd3Sm13alkyNGFJdkFOK1o5SzFZVXA4Mms3K1I4QjNmNk1TWG5PR2Q0YmtnRzEreEVUd21jYg==";
    
    ///// UAT /////
    var $header11 = "Acceskeyid:UC8vbEllVnNYY1c1T3ljakY4NmRsTEtBUjluNXd5L1YwUkpyYS95NWsxa080Zk8xMTVGUVphMUtjaFNtZEg1eA==";
    var $header22 = "MarketPlaceId:MEdhWENyWUtYQlg5YWNicnpKRFJLM2tnNWliNGhBK2ZWL1Mwd25JTm0va0ZwVGdvc3VldXN4NGo3Nldxc2k4aA==";
    var $header33 = "SecretAccessKey:NEE2TVpCOXI2eG1UWW1yUTQwSjQyNGh2SDdibjRNUmxxbHduaG9iUFN1QnlVam02a3gvNGs5Y2lWbHhxemVPZw==";
    
    //////// QA ////////
    var $updateinventorypricestatus_inuse = "https://bfl.qa.emimalls.in/dps/web/api/updateinventorypricestatus";
    var $getskudetails_inuse = "https://bfl.qa.emimalls.in/dps/web/api/getskudetails";
    var $getdealersagainstgroup_inuse = "https://bfl.qa.emimalls.in/dps/web/api/getdealersagainstgroup";
    //////// UAT ////////
    var $updateinventorypricestatus_uat = "https://bfsd.qa.bfsgodirect.com/dps/web/api/updateinventorypricestatus";
    var $getdealersagainstgroup_uat = "https://bfsd.uat.bfsgodirect.com/dps/web/api/getdealersagainstgroup";
    var $getdealerdetailsbygroup_uat = "https://bfsd.uat.bfsgodirect.com/dps/web/api/getdealerdetailsbygroup";
    //////// LIVE ////////
    var $updateinventorypricestatus_prod = "https://www.bajajfinservmarkets.in/dps/web/api/updateinventorypricestatus";
    var $getdealersagainstgroup_prod = "https://www.bajajfinservmarkets.in/dps/web/api/getdealersagainstgroup";
    var $getskudetails_prod = "https://www.bajajfinservmarkets.in/dps/web/api/getskudetails";
    
    
    function Group_code_to_child_dealer_mapping_API() {
        $data['dealer_grpid']= $this->groupcode;        
        $authorization = array( "$this->header1", "$this->header2", "$this->header3","Content-Type:application/json");
        $result = $this->rest->request($this->getdealersagainstgroup_prod, "POST_BFL", json_encode($data), 0, $authorization);
        $result = json_decode($result);
        return $result;
    }
    function Group_code_to_SKU_details_API ($page=1) {
        $data['groupcode']= $this->groupcode;
        $data['page']= $page;
        $authorization = array( "$this->header1", "$this->header2", "$this->header3","Content-Type:application/json");
        $result = $this->rest->request($this->getskudetails_prod, "POST_BFL", json_encode($data), 0, $authorization);
        return json_decode($result);
        
    }    
    function Update_inventory_price_status ($data) {
        die(json_encode($data));
        $authorization = array( "$this->header1", "$this->header2", "$this->header3","Content-Type:application/json");
        $result = $this->rest->request($this->updateinventorypricestatus_prod, "POST_BFL", json_encode($data), 0, $authorization);
        $result = json_decode($result, true);
        return $result;
    }
    
    public function get_model_variant_data($sku_column){        
        return $this->db->select('mv.id_variant, mv.full_name,'.$sku_column)->where('active  = 1')
                        ->get('model_variants mv')->result();
    }
    public function update_sku_branch_mapping_bulk($data){
        return $this->db->insert_batch('bfl_branch_sku_mapping', $data);
    }
    public function delete_sku_branch_mapping_bulk($id){
         return $this->db->where_in('idvariant', $id)->delete('bfl_branch_sku_mapping');
    }
    public function get_bfl_stock_by_branch($idbranch) { 
            $this->db->select('sum(stock.qty) as qty,mv.mop,bfl.bfl_sku,bfl.bfl_idbranch,bfl.idbranch');
            $this->db->where('bfl.idbranch',$idbranch)
            ->where('bfl.idvariant = mv.id_variant')->from('model_variants mv')            
            ->where('stock.idvariant = bfl.idvariant')->where('stock.idbranch = bfl.idbranch')->from('stock')
            ->where('stock.idgodown in (1,6)')
            ->group_by('stock.idbranch,stock.idvariant'); 
        return $this->db->get('bfl_branch_sku_mapping bfl')->result(); 
         die($this->db->last_query());
    }


}
