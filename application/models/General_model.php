<?php
class General_model extends CI_Model{
    // Get All
    public function save_print_head($data){
        return $this->db->insert('print_head', $data);
    }
    public function get_print_head_data(){
        return $this->db->get('print_head')->result();
    }
    public function get_partner_type_data(){
        return $this->db->get('partner_type')->result();
    }
    public function update_print_head($data, $id){
        return $this->db->where('id_print_head', $id)->update('print_head', $data);
    }
    public function save_batch_imei_history($data) {
        return $this->db->insert_batch('imei_history',$data);
    }
    public function get_doa_godown(){
        return $this->db->where('id_godown = 3')->where('active = 1')->get('godown')->result();
    }
    public function get_financial_year(){
        return $this->db->select('financial_year')->get('configurations')->row();        
    }
    public function get_stock_receive_type(){
        return $this->db->select('one_click_receive')->get('configurations')->row();        
    }
    public function get_state_data(){
        return $this->db->get('state')->result();
    }
    public function get_state_by_name($branch_name){
        return $this->db->where('state_name', $branch_name)->get('state')->result();
    }
    public function get_product_category_data(){
        return $this->db->where('active', 1)->get('product_category')->result();
    }
	public function get_all_product_category_data(){
        return $this->db->get('product_category')->result();
    }
    public function get_sku_type_data(){
        return $this->db->where('active', 1)->get('sku_type')->result();
    }
    public function get_brand_data(){
        return $this->db->get('brand')->result();
    }
    public function get_active_brand_data(){
        return $this->db->where('active', 1)->get('brand')->result();
    }    
    public function get_userrole_has_menu(){
        return $this->db->where('iduserrole = id_userrole')->from('user_role')
                        ->where('idmenu = id_menu')->from('menu')
                        ->get('userrole_has_menu')->result();        
    }
    public function get_user_byidrole($idrole){
        return $this->db->where('iduserrole', $idrole)->get('users')->result();        
    }
    public function get_userrole_has_menu_byid($idrole){
        return $this->db->where('iduserrole', $idrole)->get('userrole_has_menu')->result();
    }    
    public function get_userrole_has_menu_byrole($idrole){
//         $this->db->select('menu.id_menu,submenu.id_submenu,menu.menu,menu.font,menu.url,submenu.submenu,submenu.font as subfont,submenu.url as suburl')
//                        ->where('iduserrole = id_userrole')->from('user_role')
//                        ->where('idmenu = id_menu')->from('menu')
//                        ->where('idsubmenu = id_submenu')->from('submenu')                        
//                        ->where('menu.active', 1)
//                        ->where('submenu.active', 1)
//                        ->where('uhm.iduserrole', $idrole)
//                        ->order_by('uhm.sequence')
//                        ->get('userrole_has_menu uhm')->result();
            $menus=array();
            $menu=$this->db->distinct()->select('menu.id_menu,menu.menu,menu.font,menu.url')
                        ->where('iduserrole = id_userrole')->from('user_role')
                        ->where('idmenu = id_menu')->from('menu')
                        ->where('menu.active', 1)
                        ->where('iduserrole', $idrole)
                        ->order_by('menu_sequence')
                        ->get('userrole_has_menu')->result();
//            die( $this->db->last_query());
//            die(print_r($menu));
            foreach ($menu as $mnu){
                $tmp=array();
                $tmp['id_menu']=$mnu->id_menu;
                $tmp['menu']=$mnu->menu;
                $tmp['font']=$mnu->font;
                $tmp['url']=$mnu->url;
                $tmp['submenu']=$this->db->distinct()->select('submenu.id_submenu,submenu.submenu,submenu.font,submenu.url')
                        ->where('iduserrole = id_userrole')->from('user_role')
                        ->where('idsubmenu = id_submenu')->from('submenu')
                        ->where('userrole_has_menu.idmenu',$mnu->id_menu)
                        ->where('submenu.active', 1)
                        ->where('iduserrole', $idrole)
                        ->order_by('sequence')
                        ->get('userrole_has_menu')->result();
                array_push($menus,$tmp);
                
            }              
            return $menus;        
        
    }
    
    public function get_all_menu_submenu(){

            $this->db->select('menu.*,submenu.font as subfont,submenu.submenu,submenu.url as suburl,COALESCE(submenu.id_submenu, 0) as id_submenu');
            $this->db->from('menu');
            $this->db->join('submenu', 'submenu.idmenu = menu.id_menu','left');
            $this->db->where('menu.active',1);
            $query = $this->db->get();
            return $query->result();
            
    }
    
    public function get_branch_has_godown_bybranch($idbranch){
        return $this->db->where('idbranch = id_branch')->from('branch')
                        ->where('idbranch', $idbranch)->where('idgodown = id_godown')->from('godown')
                        ->get('branch_has_billing_godown')->result();
    }
    
    public function get_category_data(){
        return $this->db->get('category')->result();
    }
    
    public function get_attribute_type_data(){
        return $this->db->get('attribute_type')->result();
    }
    public function get_attribute_data(){
        return $this->db->where('id_attribute_type  = idattributetype')->from('attribute_type')->get('attribute')->result();
    }
    public function get_attribute_by_id($id){
        return $this->db->where('id_attribute'  , $id)->get('attribute')->row();
    }
    
    public function get_attribute_value_data($id){
        return $this->db->where('id_attribute', $id)->where('id_attribute  = idattribute ')->from('attribute')->get('attribute_values')->result();
    }
    
//    public function get_active_attribute_data(){
//        return $this->db->where('id_attribute_type  = idattributetype')
//                ->where('attribute_type.active',1)
//                ->where('.attribute.active',1)
//                ->where('id_attribute_type  = idattributetype')
//                ->from('attribute_type')->order_by('id_attribute_type','id_attribute')->get('attribute')->result();
//    }
    
      public function get_active_attribute_data(){
            $atribute_type=array();
            $atributetype=$this->db->where('active', 1)->get('attribute_type')->result();
            foreach ($atributetype as $atribute){
                $tmp=array();
                $tmp['id_attribute_type']=$atribute->id_attribute_type;
                $tmp['attribute_type']=$atribute->attribute_type;
                $tmp['attributes']=$this->db->where('active', 1)->where('idattributetype', $atribute->id_attribute_type)->get('attribute')->result();
                array_push($atribute_type,$tmp);                
            }            
            return $atribute_type;        
        
    }
    
    public function get_attribute_values_by_id($ids) {
         return $this->db->where('idattribute', $ids)->get('attribute_values')->result();
         //die( $this->db->last_query());
    }
    
    public function get_all_category_attributes_byid($id){        
         return $this->db->where('at.id_attribute_type = cha.idattributetype')
                        ->where('cha.idcategory',$id)
                        ->where('cha.idattribute  = a.id_attribute')
                        ->where('c.id_category  = cha.idcategory')
                        ->from('attribute_type at')
                        ->from('attribute a')
                        ->from('category c')
                        ->get('category_has_attributes cha')->result();    
         //die( $this->db->last_query());
    }
    public function get_category_has_attributes_byid($id){        
         return $this->db->where('at.id_attribute_type = cha.idattributetype')
                        ->where('cha.idcategory',$id)
                        ->where('cha.is_variant',0)
                        ->where('cha.idattribute  = a.id_attribute')
                        ->where('c.id_category  = cha.idcategory')
                        ->from('attribute_type at')
                        ->from('attribute a')
                        ->from('category c')
                        ->get('category_has_attributes cha')->result();    
         //die( $this->db->last_query());
    }
    public function get_active_variants_id($idvarient) {                
        return $this->db->where('mv.id_variant', $idvarient)  
                ->where('mv.idcategory = category.id_category')->from('category')
                        ->where('mv.active', 1)                        
                        ->get('model_variants mv')->row();
        die( $this->db->last_query());
    }   
    public function get_category_variantid($id){        
         return $this->db->where('cha.idcategory',$id)
                        ->where('cha.is_variant',1)
                        ->where('cha.idattribute  = a.id_attribute')
                        ->from('attribute a')
                        ->order_by('variant_sequence')
                        ->get('category_has_attributes cha')->result(); 
    }
    public function ajax_get_vendor_has_brand_products($idvendor) {
        return $this->db->select('mv.id_variant, mv.modelname, mv.full_name, product_category.product_category_name')
                        ->where('vendor_has_brand.idvendor', $idvendor)
                        ->where('brand.id_brand = vendor_has_brand.idbrand')->from('brand')
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->where('vendor_has_brand.idbrand  = mv.idbrand')->from('vendor_has_brand')
                        ->get('model_variants mv')->result();
    }
    public function ajax_get_vendor_has_brand_bysku($idvendor, $idskutype) {
        return $this->db->select('mv.id_variant, mv.modelname, mv.full_name, product_category.product_category_name')
                        ->where('vendor_has_brand.idvendor', $idvendor)
                        ->where('mv.idsku_type', $idskutype)
                        ->where('brand.id_brand = vendor_has_brand.idbrand')->from('brand')
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->where('vendor_has_brand.idbrand  = mv.idbrand')->from('vendor_has_brand')
                        ->get('model_variants mv')->result();
    }
    public function ajax_get_model_variant_byidskutype($idskutype) {
        return $this->db->select('mv.id_variant, mv.sale_type, mv.modelname, mv.full_name, product_category.product_category_name')
                        ->where('mv.active = 1')
                        ->where('mv.idsku_type', $idskutype)
                        ->where('product_category.active = 1')
                        ->where('product_category.id_product_category  = mv.idproductcategory')->from('product_category')
                        ->get('model_variants mv')->result();
    }
    
    public function save_db_attribute_type($data) {
        return $this->db->insert('attribute_type', $data);
    }
    
    public function save_db_attribute($data) {
        return $this->db->insert('attribute', $data);
    }
    public function save_db_attribute_value($data) {
        return $this->db->insert('attribute_values', $data);
    }    
    public function edit_db_attribute_type($id,$data) {
        return $this->db->where('id_attribute_type ', $id)->update('attribute_type', $data);
    }
    public function edit_db_attribute($id,$data) {
        return $this->db->where('id_attribute', $id)->update('attribute', $data);
    }
    public function edit_db_attribute_value($id,$data) {
        return $this->db->where('id_attribute_value', $id)->update('attribute_values', $data);
    }
    public function detele_db_attribute_value($id) {         
        $this->db->trans_begin();
        $this->db->where('id_attribute_value', $id)->delete('attribute_values');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return 0;
        } else {                  
            $this->db->trans_commit();
            return 1;
        }
    }
    
    public function get_category_all_data(){
        return $this->db->where('id_product_category  = idproductcategory')->from('product_category')->get('category')->result();
    }
    
    public function get_category_all_data_by_id($id){
        return $this->db->where('id_category',$id)->where('id_product_category  = idproductcategory')->from('product_category')->get('category')->row();
    }
    
    public function get_model_data(){
        return $this->db->get('model')->result();
    }
    public function get_model_by_id($id){
        return $this->db->where('id_model', $id)
                        ->where('id_product_category  = model.idtype')->from('product_category')
                        ->where('id_brand = idbrand')->from('brand')
                        ->where('id_category = idcategory')->from('category')
                        ->where('id_sku_type = idsku_type')->from('sku_type')
                        ->get('model')->result();
    }
    public function get_model_byid_branch_availablity($id, $branch,$idgodown){
        return $this->db->where('id_model', $id)
                        ->where('id_product_category  = category.idtype')->from('product_category')
                        ->where('id_brand = model.idbrand')->from('brand')
                        ->where('id_category = model.idcategory')->from('category')
                        ->where('id_sku_type = idsku_type')->from('sku_type')
                        ->where('stock.idmodel', $id)->where('stock.idgodown', $idgodown)->where('stock.idbranch', $branch)->from('stock')
                        ->get('model', 1)->result();
    }
    public function get_model_all_data(){        
        return $this->db->select('*, mv.idmodel as id_model')                        
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->where('id_brand = mv.idbrand')->from('brand')
                        ->where('id_category = mv.idcategory')->from('category')
                        ->join('model_variant_images mvi', 'mvi.idvariant = mv.id_variant','left')
                        ->join('model_images mi', 'mi.idmodel = mv.idmodel','left')
                        ->group_by('mv.id_variant')
                        ->order_by('mv.id_variant',' DESC')
                        ->limit(200)
                        ->get('model_variants mv')->result();
    }
    public function get_model_variant_data(){        
        return $this->db->select('mv.id_variant, mv.modelname, mv.full_name,mv.idbrand,product_category.product_category_name')
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->get('model_variants mv')->result();
    }
    public function get_model_variant_data_byidvariant($idvariant){      
        return $this->db->where('id_variant', $idvariant)->get('model_variants')->row();
    }
    
    public function get_all_models_by_PCB($category,$brand,$product_category){   
                        $where=array('mv.idproductcategory' => $product_category);
                        if($category > 0 && $brand == 0 ){
                            $where=array('mv.idproductcategory' => $product_category,'mv.idcategory'=>  $category);                           
                        }else if($category == 0 && $brand > 0 ){
                            $where=array('mv.idproductcategory' => $product_category,'mv.idbrand'=>  $brand);                           
                        }else if ($category > 0 && $brand > 0 ){
                            $where=array('mv.idproductcategory' => $product_category,'mv.idbrand'=>  $brand,'mv.idcategory'=>  $category);                           
                        }  
            return $this->db->select('*,mv.idmodel as id_model')   
                        ->where($where)   
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->where('id_brand = mv.idbrand')->from('brand')
                        ->where('id_category = mv.idcategory')->from('category')
                        ->join('model_variant_images mvi', 'mvi.idvariant = mv.id_variant','left')
                        ->join('model_images mi', 'mi.idmodel = mv.idmodel','left')
                        ->group_by('mv.id_variant')
                        ->order_by('mv.id_variant',' DESC')
                        ->get('model_variants mv')->result();
//         die(print_r($this->db->last_query()));
    }
    
    
    public function get_recent_models(){        
        return $this->db->select('*')    
                        ->where('model_variants.active = 1')
                        ->where('id_product_category  = model_variants.idproductcategory')->from('product_category')
                        ->where('id_brand = idbrand')->from('brand')
                        ->where('id_category = idcategory')->from('category')
                        ->order_by('id_variant',' DESC')
//                        ->limit(100)
                        ->get('model_variants')->result();
        
    }
    
    public function ajax_get_active_model_by_PCB($category,$brand,$product_category){   
                        $where=array('model_variants.idproductcategory' => $product_category);
                        if($category > 0 && $brand == 0 ){
                            $where=array('model_variants.idproductcategory' => $product_category,'model_variants.idcategory'=>  $category);                           
                        }else if($category == 0 && $brand > 0 ){
                            $where=array('model_variants.idproductcategory' => $product_category,'model_variants.idbrand'=>  $brand);                           
                        }else if ($category > 0 && $brand > 0 ){
                            $where=array('model_variants.idproductcategory' => $product_category,'model_variants.idbrand'=>  $brand,'model_variants.idcategory'=>  $category);                           
                        }        
        return $this->db->select('*')    
                        ->where('model_variants.active = 1')
                        ->where($where)                        
                        ->where('id_product_category  = model_variants.idproductcategory')->from('product_category')
                        ->where('id_brand = idbrand')->from('brand')
                        ->where('id_category = idcategory')->from('category')
                        ->order_by('id_variant',' DESC')
                        ->get('model_variants')->result();
         
    }
    
     public function get_model_data_by_id($id){
            $data=array();
            
            /////////// select Model details /////
            $data['model'] = $this->db->where('model.id_model', $id)
                        ->where('id_product_category  = model.idproductcategory')->from('product_category')
                        ->where('id_brand = idbrand')->from('brand')
                        ->where('idsku_type = id_sku_type')->from('sku_type')
                        ->where('id_category = idcategory')->from('category')                        
                        ->get('model')->row();
            
            /////////// select Model Attributes /////
            
            $query = $this->db->query("SELECT * FROM `category_has_attributes` `e` LEFT JOIN `model_attribute` `ue` ON `ue`.`idcategoryattribute`= `e`.`id_category_attribute` AND `ue`.`idmodel` = $id inner join attribute a on e.idattribute = a.id_attribute inner join attribute_type att on att.id_attribute_type = e.idattributetype WHERE e.is_variant=0 and `e`.`idcategory` =".$data['model']->idcategory  );
            $data['model_attributes'] =$query->result();
            
            /////////// select Model Images /////
            
            $data['model_images'] = $this->db->where('idmodel',$id)->get('model_images')->result();
            
            /////////// select Model Videos /////
            
            $data['model_videos'] = $this->db->where('idmodel',$id)->get('model_videos')->result();
                        
            /////////// Select Variant attributes/////
            
            $query = $this->db->query("SELECT avt.*,mv.active FROM `model_variants_attribute` avt,model_variants mv WHERE mv.id_variant=avt.idvariant and avt.idvariant in  (select id_variant from model_variants where idmodel=$id) order by avt.idvariant,avt.id_model_variant_attribute asc");
            $data['model_variants_attribute'] =$query->result();
            
            /////////// Select Variant Images/////
            
            $query = $this->db->query("SELECT * FROM `model_variant_images` WHERE `idvariant` in (select id_variant from model_variants where idmodel=$id)  order by idvariant asc");
            $data['model_variant_images'] =$query->result();
            
            /////////// Select All Category attributes/////
            
            $data['variant_attribute']= $category_variant = $this->get_category_variantid($data['model']->id_category);
            $data['subbrand_models']=array();
            
            /////////// Select Sub-Brand  and Sub-Model/////
            
            if($data['model']->subidbrand > 0){         
                $idcategory=$this->db->select('idcategory')->where('id_model', $data['model']->subidmodel)->get('model')->row()->idcategory;
                $data['subbrand_models']= $this->db->select('id_model,model_name')->where('idbrand',$data['model']->subidbrand)->where('idcategory',$idcategory)->get('model')->result();  
                
                /////////// Select Variant/////
            }            
                $data['model_variants'] = $this->db->where('idmodel',$id)->get('model_variants')->result();
            return $data;
    }
    public function ajax_get_model_bycategory($category){
        return $this->db->where('model.idcategory', $category)
                        ->where('id_product_category  = model.idtype')->from('product_category')
                        ->where('id_brand = idbrand')->from('brand')
                        ->where('id_category = idcategory')->from('category')
                        ->get('model')->result();
    }
    public function get_user_role(){
        return $this->db->get('user_role')->result();
    }
    public function get_user_role_byid($id){
        return $this->db->where('id_userrole', $id)->get('user_role')->result();
    }
    public function get_user_data(){
        return $this->db->get('users')->result();
    }
    public function get_user_all_data(){
        return $this->db->select('users.*,b.id_branch, b.branch_name, user_role.*')
                        ->where('iduserrole=id_userrole')->from('user_role')
                        ->join('branch b','users.idbranch = b.id_branch','left')
                        ->get('users')->result();
    }
    public function get_user_all_data_byid($iduser){
        return $this->db->select('users.*,b.id_branch, b.branch_name, user_role.*')
                        ->where('id_users', $iduser)
                        ->where('iduserrole=id_userrole')->from('user_role')
                        ->join('branch b','users.idbranch = b.id_branch','left')
                        ->get('users')->result();
    }
    public function get_active_salesperson_bybranch($iduserrole, $branch){
        return $this->db->where('iduserrole', $iduserrole)->where('idbranch', $branch)->get('users')->result();
    }
    public function get_recent_price_data(){
                        $this->db->where('idvariant = id_variant')->from('model_variants');
                        $this->db->where('id_category = price.idcategory')->from('category');
                        $this->db->where('id_product_category  = price.idproductcategory')->from('product_category');
                        $this->db->where('id_brand = price.idbrand')->from('brand');
                        $this->db->order_by('price.timestamp', 'desc');
                        $this->db->limit(100);
                        return $this->db->get('price')->result();
    }    
    public function get_price_data($category,$brand,$product_category,$from,$to){
        $where=array();
        if($product_category){
            $where=array('price.idproductcategory' => $product_category);
        }
        if($category > 0 && $brand == 0 ){
            $where=array('price.idproductcategory' => $product_category,'price.idcategory'=>  $category);                           
        }else if($category == 0 && $brand > 0 ){
            $where=array('price.idproductcategory' => $product_category,'price.idbrand'=>  $brand);                           
        }else if ($category > 0 && $brand > 0 ){
            $where=array('price.idproductcategory' => $product_category,'price.idbrand'=>  $brand,'price.idcategory'=>  $category);                           
        }        
        $this->db->where('idvariant = id_variant')->from('model_variants');
        $this->db->where('id_category = price.idcategory')->from('category');
        $this->db->where('id_product_category  = price.idproductcategory')->from('product_category');
        $this->db->where('id_brand = price.idbrand')->from('brand');     
        if($from && $to){
           $this->db->where('DATE(price.entry_time) >=', $from);
            $this->db->where('DATE(price.entry_time) <=', $to);
        }
        if(count($where)>0){
        $this->db->where($where);                        
        }else{
         $this->db->limit(100);   
        }
        $this->db->order_by('price.entry_time', 'desc');
        return $this->db->get('price')->result();
                         
    }
    
    public function get_category_by_product_category($id){
        return $this->db->where('idproductcategory', $id)->get('category')->result();
    }
    public function get_get_model_by_brand($id){
        return $this->db->where('idbrand', $id)->get('model')->result();
    }
    
    
    public function get_category_by_id($id){
        return $this->db->where('id_category', $id)->get('category')->result();
    }
    public function get_active_payment_head(){
        return $this->db->where('active = 1')->get('payment_head')->result();
    }
    public function get_active_payment_head_by_headids($ids){
        return $this->db->where('active = 1')->where_in('id_paymenthead',$ids)->get('payment_head')->result();        
    }
    public function get_active_payment_head_by_credit_receive_type(){
        return $this->db->where('credit_receive_type = 1')->where('active = 1')->get('payment_head')->result();
    }
    public function get_active_corporate_payment_head(){
        return $this->db->where('corporate_sale = 1')->get('payment_head')->result();
    }
    public function get_active_payment_mode(){
        return $this->db->where('active = 1')->get('payment_mode')->result();
    }
    public function get_active_payment_mode_head(){
        return $this->db->where('pm.active = 1')->where('ph.active = 1')
                        ->where('pm.idpaymenthead = ph.id_paymenthead')->from('payment_head ph')
                        ->get('payment_mode pm')->result();
    }
    public function get_payment_head_data(){
        return $this->db->get('payment_head')->result();
    }
    public function get_payment_head_for_receivables(){
        return $this->db->where('credit_type = 1')->get('payment_head')->result();
    }
    public function get_payment_mode_for_receivables(){
        return $this->db->select('payment_mode.payment_mode, payment_mode.id_paymentmode, payment_head.payment_head')->where('payment_head.credit_type = 0')
                        ->where('payment_mode.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                        ->get('payment_mode')->result();
    }
    public function get_payment_head_byid($head){
        return $this->db->where('id_paymenthead',$head)->get('payment_head')->row();
    }
    public function get_payment_head_has_attributes(){
        return $this->db->where('payment_head_has_attributes.idpayment_attribute = payment_attribute.id_payment_attribute')->from('payment_attribute')
                        ->get('payment_head_has_attributes')->result();
    }
    public function get_payment_head_has_attributes_byhead($head){
        return $this->db->where('payment_head_has_attributes.idpayment_head', $head)
                        ->where('payment_head_has_attributes.idpayment_attribute = payment_attribute.id_payment_attribute')
                        ->from('payment_attribute')
                        ->get('payment_head_has_attributes')->result(); 
    }
    public function get_payment_attribute_data(){
        return $this->db->where('status = 1')->get('payment_attribute')->result();
    }
    public function get_payment_mode_data(){
        return $this->db->where('idpaymenthead = id_paymenthead')->from('payment_head')->get('payment_mode')->result();
    }
    
    public function get_active_payment_mode_data(){
        return $this->db->where('payment_mode.active = 1')->where('idpaymenthead = id_paymenthead')->from('payment_head')->get('payment_mode')->result();
    }
    public function ajax_get_model_by_category_brand($category, $brand) {
        return $this->db->where('idcategory', $category)->where('idbrand', $brand)->get('model')->result();
    }
    public function ajax_get_payment_mode_byhead($idhead){
        return $this->db->where_in('idpaymenthead', $idhead)->get('payment_mode')->result();
    }
    public function get_payment_mode_bymode($idmode){
        return $this->db->where('id_paymentmode', $idmode)->get('payment_mode')->row();
    }
    public function get_subbrand_data() {
        return $this->db->where('active = 1')->get('subbrand')->result();
    }
    // Get active
    public function get_active_suppliers(){
        return $this->db->where('active = 1')->where('vendor_type', 1)->get('vendor')->result();
    }
    public function get_active_godown(){
        return $this->db->where('active = 1')->get('godown')->result();
    }
    public function get_billing_godown(){
        return $this->db->where('isforbilling = 1')->where('active = 1')->get('godown')->result();
    }
    public function get_b_to_b_godown(){
        return $this->db->where('b_to_b_allowed = 1')->where('active = 1')->get('godown')->result();
    }
    public function get_allowed_for_allocation_godowns(){
        return $this->db->where('allowed_for_allocation = 1')->where('active = 1')->get('godown')->result();
    }
    public function get_active_users_byrole($role){
        return $this->db->where('iduserrole', $role)->where('active = 1')->get('users')->result();
    }
    public function get_active_users_byrole_branch($role,$idbranch){
        return $this->db->where('iduserrole', $role)->where('idbranch', $idbranch)->where('active = 1')->get('users')->result();
    }
    public function get_petti_cash_bydate($from,$to,$branch){
        if($branch==0){
            return $this->db->where('petti_cash.date >=', $from)
                            ->where('petti_cash.date <=', $to)
                            ->where('idbranch=id_branch')->from('branch')
                            ->order_by('id_petti_cash','desc')
                            ->get('petti_cash')->result();
        }else{
            return $this->db->where('petti_cash.date >=', $from)
                            ->where('petti_cash.date <=', $to)
                            ->where('idbranch', $branch)
                            ->where('idbranch=id_branch')->from('branch')
                            ->order_by('id_petti_cash','desc')
                            ->get('petti_cash')->result();
        }
    }
    public function get_sum_petti_cash_bybranch_date($from,$to) {
        return $this->db->select('idbranch, sum(amount) as sum_petti_cash')
                        ->where('date >=', $from)->where('date <=', $to)
                        ->group_by('idbranch')
                        ->get('petti_cash')->result();
    }
    public function get_sum_expense_bybranch_date($from,$to) {
        return $this->db->select('idbranch, sum(expense_amount) as sum_expense')
                        ->where('entry_date >=', $from)->where('entry_date <=', $to)
                        ->group_by('idbranch')
                        ->get('expense')->result();
    }
    public function get_sum_petti_cash_bybranch_month($branch,$from,$to) {
        if($branch==0){
            return $this->db->select('sum(amount) as sum_petti_cash')
                            ->where('date >=', $from)->where('date <=', $to)
                            ->get('petti_cash')->row();
        }else{
            return $this->db->select('sum(amount) as sum_petti_cash')
                            ->where('idbranch', $branch)
                            ->where('date >=', $from)->where('date <=', $to)
                            ->get('petti_cash')->row();
        }
    }
    public function get_sum_expense_bybranch_month($branch,$from,$to) {
        if($branch==0){
            return $this->db->select('sum(expense_amount) as sum_expense')
                            ->where('entry_date >=', $from)->where('entry_date <=', $to)
                            ->get('expense')->row();
        }else{
            return $this->db->select('sum(expense_amount) as sum_expense')
                            ->where('idbranch', $branch)
                            ->where('entry_date >=', $from)->where('entry_date <=', $to)
                            ->get('expense')->row();
            }
    }
    // Save
    
    public function save_category($data) {
        $this->db->trans_begin();
        $this->db->insert('category', $data);
        $insert_id = $this->db->insert_id();
        $idattributetype_array = $this->input->post('id_attribute_type');
        $idattribute_array = $this->input->post('id_attribute');
        $attributename_array = $this->input->post('attribute_name');
        $count= count($idattributetype_array);
        $data_att=array();
          for ($i=0; $i<$count; $i++) {
            $id_attribute_type=$idattributetype_array[$i];   
            $id_attribute=$idattribute_array[$i];
                if(isset($_POST['id_attribute'.$i])){
                    if($_POST['id_attribute'.$i]=='on'){  
                        $is_variant=0;
                        if(isset($_POST['is_variant'.$i])){
                            if($_POST['is_variant'.$i]=='on'){
                                $is_variant=1;
                                $attribute_name = preg_replace('/\s+/', '', strtolower($attributename_array[$i]));                                
                                if ($this->db->field_exists($attribute_name, 'model_variants'))
                                {
                                    
                                    
                                }else{
                                    
                                    $this->db->query('ALTER TABLE `model_variants` ADD '.$attribute_name.' VARCHAR(100) NULL AFTER `active`; ');
                                }
                            }
                        }
                        $data_att[$i] = array(
                            'idattributetype ' => $id_attribute_type,
                            'idattribute' => $id_attribute,
                            'idcategory ' => $insert_id,
                            'is_variant ' => $is_variant
                        );
                    }     
                }    
        }
        if(count($data_att)>0){
            $this->db->insert_batch('category_has_attributes', $data_att);                
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return FALSE;
        } else {                  
            $this->db->trans_commit();
            return TRUE;
        }
        
        
        
    }
    public function save_db_model_images($data) {
        return $this->db->insert_batch('model_images', $data);
    }
    
    public function save_db_variant_images($data) {
        return $this->db->insert_batch('model_variant_images', $data);
    }
    
    public function remove_model_image($id,$type) { 
        if($type==1){
           return $this->db->where('id_variant_image', $id)->delete('model_variant_images'); 
        }else{
            return $this->db->where('id_model_image', $id)->delete('model_images'); 
        }
               
    }
    
  public function edit_db_model($id_model) {
        $this->db->trans_begin();  
        $subidmodel = 0;
        $subidbrand = 0;
        $result['model_id'] =$id_model;
        $result['variant_ids']=array();
        $ismop = 0;
        if($this->input->post('idbrand')==1 || $this->input->post('idbrand')==2 || $this->input->post('idbrand')==20){
            $ismop = 1;    
        }
        if ($this->input->post('has_sub_brand') == 1) {
            $subidmodel = $this->input->post('model1');
            $subidbrand = $this->input->post('subbrand');
        }
        $model_name = $this->input->post('model_name');
        $old_model_name = $this->input->post('old_model_name');
        
        $partnumbers = $this->input->post('partnumber');      
        ////////// Update model //////////
        
        $data = array(
            'idsku_type' => $this->input->post('sku_type'),            
            'model_name' => $this->input->post('model_name'),
            'description' => $this->input->post('description'),
            'subidmodel' => $subidmodel,
            'subidbrand' => $subidbrand
        );
        $this->db->where('id_model', $id_model)->update('model', $data);

        ////////// Update variants to active/deactive //////////
        $d=0;
         if(isset($_POST['variant'])){
            $variant=$this->input->post('variant');
            if(count($variant)>0){
                $arr=array();
                
                foreach ($variant as $key=>$val){
                    //$result['variant_ids'][] =$key;
                    $value=(($val=='on')?1:0);
                    $arr[]=array('id_variant' => $key,'active' => $value,'part_number' => $partnumbers[$d] );
                    $d++;
                }
                  
                $this->db->where('idmodel', $id_model)->update_batch('model_variants', $arr,'id_variant');
            }                
            }
          
        ////////// Update all variant names //////////
            
        if($model_name!=$old_model_name){
            //$this->db->query("UPDATE `model_variants` SET `modelname`= '$model_name' ,`full_name`=REPLACE(full_name,'$old_model_name','$model_name') WHERE `idmodel`=$id_model");
        }    
     
        ////////// Add new model variant //////////
        
        $variant_att_names=array();
       if(isset($_POST['variant_att_names']))
        {
                $variant_att_names = $this->input->post('variant_att_names');                
        }        
        $variant_array = array();
        if (count($variant_att_names) > 0) {
            
                $variant_data = $this->input->post('variant_data');
                $j=0;
                $attributes=array();
                foreach ($variant_data as $key=>$value){
                    $ids = explode("_", $key);
                    $k=0;
                    foreach ($value as $val){
                        $attributes[$k][$j] = array(
                        'idcategoryattribute' => $ids[0],
                        'idattributetype' => $ids[1],
                        'idattribute' => $ids[2],
                        'attribute_value' => $val,
                    ); 
                    $k++;
                    }
                   $j++;
                }
            $count= count($attributes);
            for ($i = 0; $i < $count; $i++) {
                $full_name="";
                if($this->input->post('is_model_name')){
                    $full_name.=trim($this->input->post('category_name'))." ";
                }
                $full_name.=trim($this->input->post('brand_name'))." ".trim($this->input->post('model_name'))." ";                
                $full_name.= trim($variant_att_names[$i]);
                $variant_array[] = array(
                    'idsku_type' => $this->input->post('sku_type'),
                    'idproductcategory ' => $this->input->post('product_category'),
                    'idcategory' => $this->input->post('category'),
                    'modelname' => $this->input->post('model_name'),
                    'full_name' => $full_name,
                    'part_number' => $partnumbers[$d],
                    'idbrand' => $this->input->post('idbrand'),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('id_users'),
                    'subidmodel' => $subidmodel,
                    'subidbrand' => $subidbrand,
                    'idmodel' => $id_model,
                    'is_mop' => $ismop
                );
                $d++;
            }
        } else {
           $full_name="";
                if($this->input->post('is_model_name')){
                    $full_name.=trim($this->input->post('category_name'))." ";
                }
                $full_name.=trim($this->input->post('brand_name'))." ".trim($this->input->post('model_name'))." ";    
            $variant_arr = array(
                'subidmodel' => $subidmodel,
                'subidbrand' => $subidbrand,
                'idsku_type' => $this->input->post('sku_type'),
                'modelname' => $this->input->post('model_name'),
                'full_name ' => $full_name,
                'is_mop' => $ismop
            );
            $this->db->where('idmodel', $id_model)->update('model_variants', $variant_arr);
        }
        $idcategory=$this->input->post('category');
        if (count($variant_array) > 0) {
            $this->db->insert_batch('model_variants', $variant_array);
            $variant_id = $this->db->insert_id();
            $variantid = $variant_id;
            $this->load->model('Stock_model');
            $var_data=array();            
            foreach ($attributes as $arr){     
                foreach ($arr as $att){                   
                   $var_data[]= array_merge($att,array('idvariant' => $variant_id,'idmodel' => $id_model));                   
                }             
                $result['variant_ids'][] = $variant_id;
                $variant_id++;
            }
            $var_allid = $result['variant_ids'];
            if (count($var_data) > 0) {
                $this->db->insert_batch('model_variants_attribute', $var_data);
            }
            
            if(count($var_allid) > 0){
                foreach ($var_allid as $variantid){
                    if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel_af_fc($id_model,$idcategory);
                    }else{
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid_af_fc($variantid,$idcategory);
                    }
//                    echo '<pre>';
//                    print_r($allvariants);die;
                    $idvs_c = $allvariants[0]->id_variant;
                    $str = "SELECT *  FROM `focus_model_stock` WHERE `idvariant`=$idvs_c";
                    $focus_model = $this->db->query($str)->result();
                    $idpcat = $this->input->post('product_category');
                    $idbrand = $this->input->post('idbrand');
                    
                    if(count($focus_model) > 0){
                        for($i=0; $i<count($focus_model); $i++){    
                        $data = array(
                                    'idproductcategory' => $idpcat,
                                    'idbrand' => $idbrand,
                                    'idmodel' => $id_model,
                                    'idvariant' => $variantid,
                                    'idbranch' => $focus_model[$i]->idbranch,
                                    'incentive_amount' => $focus_model[$i]->incentive_amount,
                                    'created_by' => $_SESSION['id_users'],
                                );
                                $this->Stock_model->save_focus_model_stock_data($data);
                        }      
                    }
                    
                    $str1 = "SELECT *  FROM `ageing_stock` WHERE `idvariant`=$idvs_c";
                    $ageing_model = $this->db->query($str1)->result();
                    
                    if(count($ageing_model) > 0){
                        for($i=0; $i<count($ageing_model); $i++){
                            $data = array(
                                    'idproductcategory' => $idpcat,
                                    'idbrand' => $idbrand,
                                    'idmodel' => $id_model,
                                    'idvariant' => $variantid,
                                    'idbranch' => $ageing_model[$i]->idbranch,
                                    'created_by' => $_SESSION['id_users'],
                                );
                            $this->Stock_model->save_ageing_store_stock($data);
                        }
                     }
                    
                }
            }
        }

        ////////// Delete old model videos urls  //////////
        

        $this->db->where('idmodel', $id_model)->delete('model_videos');

        ////////// Insert new model videos urls //////////
       
        if(isset($_POST['video'])){
            
            $videos = $this->input->post('video');
            $urls = array();
            foreach ($videos as $url) {
                $urls[] = array(
                    'idmodel' => $id_model,
                    'model_video_path' => $url
                );
            }
            if (count($urls) > 0) {

                $this->db->insert_batch('model_videos', $urls);
            }
        }
        
        
        ////////// Delete old model attributes  //////////
        
        
        $this->db->where('idmodel', $id_model)->delete('model_attribute'); 
        
       ////////// Insert new model attributes  //////////
        
        if(isset($_POST['attributes_names'])){
            $category_attribute = $_POST['attributes_names'];
            $attributes = array();
            foreach ($category_attribute as $name) {
                if (isset($_POST[$name])) {
                    $ids = explode("_", $name);
                    $attributes[] = array(
                        'idmodel' => $id_model,
                        'idattributetype' => $ids[1],
                        'idattribute' => $ids[2],
                        'idcategoryattribute' => $ids[0],
                        'value' => $_POST[$name],
                    );
                }
            }
            if (count($attributes) > 0) {
                $this->db->insert_batch('model_attribute', $attributes);
            }
        }       

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['model_id']=0;
            return $result;
        } else {
            $this->db->trans_commit();
            return $result;
        }
    }
    
    public function save_db_model() {
        $result=array();
        $this->db->trans_begin();
        $subidmodel = 0;
        $subidbrand = 0;
        $ismop = 0;
        if($this->input->post('idbrand')==1 || $this->input->post('idbrand')==2 || $this->input->post('idbrand')==20){
            $ismop = 1;    
        }
        if ($this->input->post('has_sub_brand') == 1) {
            $subidmodel = $this->input->post('model1');
            $subidbrand = $this->input->post('subbrand');
        }
        $data = array(
            'idsku_type' => $this->input->post('sku_type'),
            'idproductcategory ' => $this->input->post('product_category'),
            'idcategory ' => $this->input->post('category'),
            'model_name' => $this->input->post('model_name'),
            'description' => $this->input->post('description'),
            'idbrand' => $this->input->post('idbrand'),
            'timestamp' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('id_users'),
            'subidmodel' => $subidmodel,
            'subidbrand' => $subidbrand,
        );

        $this->db->insert('model', $data);
        $model_id = $this->db->insert_id();
        $result['model_id'] =$model_id;
        $result['variant_ids']=array();
        $full_name="";
        if($this->input->post('is_model_name')){
            $full_name.=trim($this->input->post('category_name'))." ";
        }
        $full_name.=trim($this->input->post('brand_name'))." ".trim($this->input->post('model_name'))." ";
                
        $category_variant = $this->get_category_variantid($this->input->post('category'));
         
       $variant_att_names=array();
       if(isset($_POST['variant_att_names']))
        {
                $variant_att_names = $this->input->post('variant_att_names');                
        }  
        $partnumbers = $this->input->post('partnumber');      
        
        $variant_array = array();
        if (count($variant_att_names) > 0) {
            
                $variant_data = $this->input->post('variant_data');
                $j=0;
                $attributes=array();
                foreach ($variant_data as $key=>$value){
                    $ids = explode("_", $key);
                    $k=0;
                    foreach ($value as $val){
                        $attributes[$k][$j] = array(
                        'idcategoryattribute' => $ids[0],
                        'idattributetype' => $ids[1],
                        'idattribute' => $ids[2],
                        'attribute_value' => $val,
                    ); 
                    $k++;
                    }
                   $j++;
                }
            $count= count($attributes);
            for ($i = 0; $i < $count; $i++) {
                $full_name="";
                if($this->input->post('is_model_name')){
                    $full_name.=trim($this->input->post('category_name'))." ";
                }
                $full_name.=trim($this->input->post('brand_name'))." ".trim($this->input->post('model_name'))." ";                
                $full_name.= trim($variant_att_names[$i]);
                $variant_array[] = array(
                    'idsku_type' => $this->input->post('sku_type'),
                    'idproductcategory ' => $this->input->post('product_category'),
                    'idcategory ' => $this->input->post('category'),
                    'modelname ' => $this->input->post('model_name'),
                    'full_name ' => $full_name,
                    'part_number' => $partnumbers[$i],
                    'idbrand' => $this->input->post('idbrand'),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('id_users'),
                    'subidmodel' => $subidmodel,
                    'subidbrand' => $subidbrand,
                    'idmodel' => $model_id,
                     'is_mop' => $ismop
                );

            }
        } else { 
            $variant_arr = array(
                'idsku_type' => $this->input->post('sku_type'),
                'idproductcategory ' => $this->input->post('product_category'),
                'idcategory ' => $this->input->post('category'),
                'modelname' => $this->input->post('model_name'),
                'idbrand' => $this->input->post('idbrand'),
                'full_name' => $full_name,                
                'timestamp' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('id_users'),
                'subidmodel' => $subidmodel,
                'subidbrand' => $subidbrand,
                'idmodel' => $model_id,
                 'is_mop' => $ismop
            );
            $this->db->insert('model_variants', $variant_arr);
            $variant_id = $this->db->insert_id();
            $result['variant_ids'][] = $variant_id;
        }
        $cnut =count($variant_array);
        if ($cnut > 0) {
            $this->db->insert_batch('model_variants', $variant_array);            
            $variant_id = $this->db->insert_id();
            $var_data=array();            
            foreach ($attributes as $arr){     
                foreach ($arr as $att){                   
                   $var_data[]= array_merge($att,array('idvariant' => $variant_id,'idmodel' => $model_id));                   
                }             
                $result['variant_ids'][] = $variant_id;
                $variant_id++;
            }
            if (count($var_data) > 0) {
                $this->db->insert_batch('model_variants_attribute', $var_data);
            }
        }

        $videos = $this->input->post('video');
        $urls = array();
        foreach ($videos as $url) {
            $urls[] = array(
                'idmodel' => $model_id,
                'model_video_path' => $url
            );
        }
        if (count($urls) > 0) {
            $this->db->insert_batch('model_videos', $urls);
        }

        $category_attribute = $this->get_category_has_attributes_byid($this->input->post('category'));


        $attributes = array();
        foreach ($category_attribute as $attri) {
            $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
            $name = $attri->id_category_attribute . '_' . $attri->idattributetype . '_' . $attri->idattribute . '_' . $na_me;
            if (isset($_POST[$name])) {
                $ids = explode("_", $name);
                $attributes[] = array(
                    'idmodel' => $model_id,
                    'idattributetype' => $ids[1],
                    'idattribute' => $ids[2],
                    'idcategoryattribute' => $ids[0],
                    'value' => $_POST[$name],
                );
            }
        }

        if (count($attributes) > 0) {
            $this->db->insert_batch('model_attribute', $attributes);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['model_id']=0;
            return $result;
        } else {
            $this->db->trans_commit();
            return $result;
        }
    }

    
    public function save_role($data) {
        return $this->db->insert('user_role', $data);
    }
    public function save_user($data) {
        $this->db->insert('users', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function save_login($data) {
        return $this->db->insert('login', $data);
    }
    public function save_price($data) {
        return $this->db->insert('price', $data);
    }
     public function update_model_variants_byidvariant($data, $idvariant){
        return $this->db->where('id_variant',$idvariant)->update('model_variants', $data);
    }
    public function update_db_price() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));

        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        $dprince = array(
            'idvariant' => $id_variant,
            'idcategory' => $idcategory,
            'idmodel' => $idmodel,
            'idproductcategory'  => $idproductcategory,
            'idbrand' => $idbrand,
            'planding' => $this->input->post('landing'),
            'pmop' => $this->input->post('mop'),
            'pmrp' => $this->input->post('mrp'),           
            'created_by' => $_SESSION['id_users'],
            'cgst' => $this->input->post('cgst'),
            'sgst' => $this->input->post('sgst'),
            'igst' => $this->input->post('igst'),
            'psalesman_price' => $this->input->post('salesman'),
            'timestamp' => $timestamp,
            'p_emiprice' => $this->input->post('emi'),
            'ponline_price' => $this->input->post('online_price'),    
            'pcorporate_sale' => $this->input->post('wholesale_price'),    
        );
        $this->db->insert('price', $dprince);        
        $data = array(
                'landing' => $this->input->post('landing'),
                'mop' => $this->input->post('mop'),
                'mrp' => $this->input->post('mrp'),
                'cgst' => $this->input->post('cgst'),
                'sgst' => $this->input->post('sgst'),
                'igst' => $this->input->post('igst'),
                'is_mop' => $this->input->post('is_mop'),
                'salesman_price' => $this->input->post('salesman'),
                'm_variant_lmt' => $timestamp,
                'm_variant_lmb' => $_SESSION['id_users'],
                'best_emi_price' => $this->input->post('emi'),
                'online_price' => $this->input->post('online_price'),
                'corporate_sale_price' => $this->input->post('wholesale_price'),
                'is_online' => $this->input->post('is_online'),
                
            );
        $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $q['result'] = 'no';
        } else {
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    
    public function update_db_all_variant_price() {
       $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));
        
        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        
        if($idproductcategory == 1){
            
            if($idcategory == 2 || $idcategory == 28 || $idcategory == 31){
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and idmodel = $idmodel";
            }else{
            //get idvariant of same attribute value of model
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE id_variant IN( SELECT v.idvariant FROM ( SELECT `idvariant`, `idmodel`, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute GROUP BY `idvariant`, `idmodel` ) AS v INNER JOIN( SELECT `idvariant`, idmodel, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute WHERE `idvariant` = $id_variant GROUP BY `idvariant` ) AS b ON b.ram = v.ram AND b.rom = v.rom WHERE v.`idmodel` = b.`idmodel` ) AND active = 1 GROUP BY mv.id_variant DESC ";
            }
            $idvar =  $this->db->query($sql)->result();
            
            //store idvariant in array 
            foreach ($idvar as $idvv){
                $idva[] = $idvv->id_variant;
            }
            for($i=0; $i<count($idva);$i++){
                $dprince = array(
                    'idvariant' => $idva[$i],
                    'idcategory' => $idcategory,
                    'idmodel' => $idmodel,
                    'idproductcategory'  => $idproductcategory,
                    'idbrand' => $idbrand,
                    'planding' => $this->input->post('landing'),
                    'pmop' => $this->input->post('mop'),
                    'pmrp' => $this->input->post('mrp'),           
                    'created_by' => $_SESSION['id_users'],
                    'cgst' => $this->input->post('cgst'),
                    'sgst' => $this->input->post('sgst'),
                    'igst' => $this->input->post('igst'),
                    'psalesman_price' => $this->input->post('salesman'),
                    'timestamp' => $timestamp,
                    'p_emiprice' => $this->input->post('emi'),
                    'ponline_price' => $this->input->post('online_price'),    
                    'pcorporate_sale' => $this->input->post('wholesale_price'),    
                );
                $this->db->insert('price', $dprince);        
                $data = array(
                    'landing' => $this->input->post('landing'),
                    'mop' => $this->input->post('mop'),
                    'mrp' => $this->input->post('mrp'),
                    'cgst' => $this->input->post('cgst'),
                    'sgst' => $this->input->post('sgst'),
                    'igst' => $this->input->post('igst'),
                    'is_mop' => $this->input->post('is_mop'),
                    'salesman_price' => $this->input->post('salesman'),
                    'm_variant_lmt' => $timestamp,
                    'm_variant_lmb' => $_SESSION['id_users'],
                    'best_emi_price' => $this->input->post('emi'),
                    'online_price' => $this->input->post('online_price'),
                    'corporate_sale_price' => $this->input->post('wholesale_price'),
                    'is_online' => $this->input->post('is_online'),
                );
                $this->db->where('id_variant', $idva[$i])->update('model_variants', $data);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            echo json_encode($q);
            
        }else{
            
            $dprince = array(
                'idvariant' => $id_variant,
                'idcategory' => $idcategory,
                'idmodel' => $idmodel,
                'idproductcategory'  => $idproductcategory,
                'idbrand' => $idbrand,
                'planding' => $this->input->post('landing'),
                'pmop' => $this->input->post('mop'),
                'pmrp' => $this->input->post('mrp'),           
                'created_by' => $_SESSION['id_users'],
                'cgst' => $this->input->post('cgst'),
                'sgst' => $this->input->post('sgst'),
                'igst' => $this->input->post('igst'),
                'psalesman_price' => $this->input->post('salesman'),
                'timestamp' => $timestamp,
                'p_emiprice' => $this->input->post('emi'),
                'ponline_price' => $this->input->post('online_price'),    
                'pcorporate_sale' => $this->input->post('wholesale_price'),    
            );
            $this->db->insert('price', $dprince);        
            $data = array(
                    'landing' => $this->input->post('landing'),
                    'mop' => $this->input->post('mop'),
                    'mrp' => $this->input->post('mrp'),
                    'cgst' => $this->input->post('cgst'),
                    'sgst' => $this->input->post('sgst'),
                    'igst' => $this->input->post('igst'),
                    'is_mop' => $this->input->post('is_mop'),
                    'salesman_price' => $this->input->post('salesman'),
                    'm_variant_lmt' => $timestamp,
                    'm_variant_lmb' => $_SESSION['id_users'],
                    'best_emi_price' => $this->input->post('emi'),
                    'online_price' => $this->input->post('online_price'),
                    'corporate_sale_price' => $this->input->post('wholesale_price'),
                    'is_online' => $this->input->post('is_online'),

                );
            $this->db->where('id_variant', $id_variant)->update('model_variants', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            echo json_encode($q);
        }
        
//        else{
//            $this->db->trans_complete();
//            $this->db->trans_rollback();
//            $q['result'] = 'no';
//            
//            echo json_encode($q);
//        }
       
    }
    
    public function update_nlc_db_price() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));

        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        $dprince = array(
            'idvariant' => $id_variant,
            'idcategory' => $idcategory,
            'idmodel' => $idmodel,
            'idproductcategory'  => $idproductcategory,
            'idbrand' => $idbrand,
            'pmop' => $this->input->post('mop'),
            'pmrp' => $this->input->post('mrp'),           
            'created_by' => $_SESSION['id_users'],
            'nlc_price' => $this->input->post('nlc_price'),
            'dp_price' => $this->input->post('dp_price'),
            'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
            'timestamp' => $timestamp,
        );
        $this->db->insert('price', $dprince);        
        $data = array(
                'mop' => $this->input->post('mop'),
                'mrp' => $this->input->post('mrp'),
                'nlc_price' => $this->input->post('nlc_price'),
                'dp_price' => $this->input->post('dp_price'),
            'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
                'm_variant_lmt' => $timestamp,
                'm_variant_lmb' => $_SESSION['id_users'],
            );
        $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $q['result'] = 'no';
        } else {
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    
    
    public function update_nlc_db_price_allvariant() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));
    
        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        if( $idproductcategory == 1){
            if($idcategory == 2 || $idcategory == 28 || $idcategory == 31){
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and idmodel = $idmodel";
            }else{
                //get idvariant of same attribute value of model
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and id_variant IN( SELECT v.idvariant FROM ( SELECT `idvariant`, `idmodel`, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute GROUP BY `idvariant`, `idmodel` ) AS v INNER JOIN( SELECT `idvariant`, idmodel, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute WHERE `idvariant` = $id_variant GROUP BY `idvariant` ) AS b ON b.ram = v.ram AND b.rom = v.rom WHERE v.`idmodel` = b.`idmodel` ) AND active = 1 GROUP BY mv.id_variant DESC";
            }
            $idvar =  $this->db->query($sql)->result();
//            $idva = [];
            //store idvariant in array 
            foreach ($idvar as $idvv){
                $idva[] = $idvv->id_variant;
            }
            for($i=0; $i<count($idva);$i++){
                
                $dprince = array(
                    'idvariant' => $idva[$i],
                    'idcategory' => $idcategory,
                    'idmodel' => $idmodel,
                    'idproductcategory'  => $idproductcategory,
                    'idbrand' => $idbrand,
                    'pmop' => $this->input->post('mop'),
                    'pmrp' => $this->input->post('mrp'),           
                    'created_by' => $_SESSION['id_users'],
                    'nlc_price' => $this->input->post('nlc_price'),
                    'dp_price' => $this->input->post('dp_price'),
                    'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
                    'timestamp' => $timestamp,
                );
             
                $this->db->insert('price', $dprince);        
                $data = array(
                    'mop' => $this->input->post('mop'),
                    'mrp' => $this->input->post('mrp'),
                    'nlc_price' => $this->input->post('nlc_price'),
                    'dp_price' => $this->input->post('dp_price'),
                    'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
                    'm_variant_lmt' => $timestamp,
                    'm_variant_lmb' => $_SESSION['id_users'],
                );
                $this->db->where('id_variant', $idva[$i])->update('model_variants', $data);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            
            echo json_encode($q);
        }else{
             $dprince = array(
                'idvariant' => $id_variant,
                'idcategory' => $idcategory,
                'idmodel' => $idmodel,
                'idproductcategory'  => $idproductcategory,
                'idbrand' => $idbrand,
                'pmop' => $this->input->post('mop'),
                'pmrp' => $this->input->post('mrp'),           
                'created_by' => $_SESSION['id_users'],
                'nlc_price' => $this->input->post('nlc_price'),
                'dp_price' => $this->input->post('dp_price'),
                 'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
                'timestamp' => $timestamp,
            );
            $this->db->insert('price', $dprince);        
            $data = array(
                    'mop' => $this->input->post('mop'),
                    'mrp' => $this->input->post('mrp'),
                    'nlc_price' => $this->input->post('nlc_price'),
                    'dp_price' => $this->input->post('dp_price'),
                'scheme_amount' => $this->input->post('scheme_price'),
            'sale_kitty' => $this->input->post('sale_kitty'),
                    'm_variant_lmt' => $timestamp,
                    'm_variant_lmb' => $_SESSION['id_users'],
                );
            $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            echo json_encode($q);
        }
    }
    
    public function update_db_on_price() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));

        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        $dprince = array(
            'idvariant' => $id_variant,
            'idcategory' => $idcategory,
            'idmodel' => $idmodel,
            'idproductcategory'  => $idproductcategory,
            'idbrand' => $idbrand,
            'p_emiprice' => $this->input->post('emi'),
            'ponline_price' => $this->input->post('online_price'),            
            'created_by' => $_SESSION['id_users'],
            'timestamp' => $timestamp,
        );
        $this->db->insert('price', $dprince);        
        $data = array(
                'best_emi_price' => $this->input->post('emi'),
                'online_price' => $this->input->post('online_price'),
                'is_online' => $this->input->post('is_online'),
                'm_variant_lmt' => $timestamp,
                'm_variant_lmb' => $_SESSION['id_users'],
                
            );
        $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 'no';
        } else {
            $this->db->trans_commit();
            return 'yes';
        }
    }
    public function get_menu_data(){
        return $this->db->where('active', 1)->get('menu')->result();
    }    
    public function save_menu($data) {
        return $this->db->insert('menu', $data);
    }
    public function edit_menu($id,$data) {
        return $this->db->where('id_menu', $id)->update('menu', $data);
    }    
    public function edit_role($id,$data) {
        return $this->db->where('id_userrole', $id)->update('user_role', $data);
    }
    public function edit_user($id,$data) {
         $this->db->where('id_users', $id)->update('users', $data);
         
    }    
    public function save_submenu($data) {
        return $this->db->insert('submenu', $data);
    }
    public function edit_submenu($id,$data) {
        return $this->db->where('id_submenu', $id)->update('submenu', $data);
    }
    public function get_menu_submenu_data(){        
        return $this->db->where('idmenu = id_menu')->from('menu')
                        ->get('submenu')->result();
    }    
    public function save_userrole_menu($data,$id) {
                $this->db->trans_begin();
                $this->db->where('iduserrole', $id)->delete('userrole_has_menu');
                if(count($data)>0){
                    $this->db->insert_batch('userrole_has_menu', $data);                
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {                  
                    $this->db->trans_rollback();
                    return FALSE;
              } else {                  
                    $this->db->trans_commit();
                    return TRUE;
              }
    }
    public function update_menu_sequence($data,$id) {
        $this->db->where('iduserrole', $id)->update_batch('userrole_has_menu', $data,'idmenu');
//        die(print_r($this->db->last_query()));
    }
    public function delete_userrole_menu($id) {
        return $this->db->where('iduserrole', $id)->delete('userrole_has_menu');
    }
    
    public function save_branch_godown($data) {
        return $this->db->insert('branch_has_billing_godown', $data);
    }
    public function save_product_category($data) {
        return $this->db->insert('product_category', $data);
    }
    public function save_product_brand($data) {
        return $this->db->insert('brand', $data);
    }
    public function save_payment_mode($data) {
        return $this->db->insert('payment_mode', $data);
    }
    public function save_payment_head($data) {
        $this->db->insert('payment_head', $data);
        return $this->db->insert_id();
    }
    public function save_payment_head_has_attributes($data) {
        return $this->db->insert_batch('payment_head_has_attributes', $data);
    }
    public function edit_payment_mode($id, $data) {
        return $this->db->where('id_paymentmode', $id)->update('payment_mode', $data);
    }
    public function save_customer($data) {
        $this->db->insert('customer', $data);
        return $this->db->insert_id();
    }
    public function save_customer_edit_history($data) {
        return $this->db->insert('customer_edit_history', $data);
    }
    public function edit_customer_byid($idcustomer, $data) {
        $this->db->where('id_customer', $idcustomer)->update('customer', $data);
    }
    public function update_customer_edit_count($idcustomer) {
        $this->db->where('id_customer', $idcustomer)->set('customer_edit_count', 'customer_edit_count + '. 1, false)->update('customer');
    }
    public function save_petti_cash($data) {
        return $this->db->insert('petti_cash', $data);
    }
    // Edit
   
    public function edit_product_category($id,$data) {
        return $this->db->where('id_product_category ', $id)->update('product_category', $data);
    }
    public function edit_brand($id,$data) {
        return $this->db->where('id_brand', $id)->update('brand', $data);
    }
    
     public function edit_db_category($status, $data, $tra_type) {
        $this->db->trans_begin();
        $ids = explode("_", $data);

        $id_category = $ids[2];
        $id_attribute = $ids[0];
        $id_attribute_type = $ids[1];
        
        
        if ($tra_type == 1) {
            if ($status == 1) {
                $data_att = array(
                    'idattributetype ' => $id_attribute_type,
                    'idattribute' => $id_attribute,
                    'idcategory ' => $id_category
                );                
                $this->db->insert('category_has_attributes', $data_att);
            } else {
                $this->db->where('idcategory', $id_category)->where('idattribute', $id_attribute)->where('idattributetype', $id_attribute_type)->delete('category_has_attributes');
            }
        } else {
            $attribute_name = $ids[3];
            $is_variant = 0;
            $variant_sequence=0;
            if ($status == 1) {
                $rw = $this->db->select_max('variant_sequence')->where('idcategory', $id_category)->get('category_has_attributes')->row();
                $variant_sequence=$rw->variant_sequence+1;
                $is_variant = 1;
                if ($this->db->field_exists($attribute_name, 'model_variants'))
                {
                    
                }else{
                    $this->db->query('ALTER TABLE `model_variants` ADD '.$attribute_name.' VARCHAR(100) NULL AFTER `active`; ');
                }
                
            }
            $data_att = array(
                'is_variant' => $is_variant,
                'variant_sequence' => $variant_sequence
            );
            $this->db->where('idcategory', $id_category)->where('idattribute', $id_attribute)->where('idattributetype', $id_attribute_type)->update('category_has_attributes', $data_att);
        }
        

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 0;
        } else {
            $this->db->trans_commit();
            return 1;
        }
    }
    public function edit_category($id, $data) {        
//$this->db->where('idcategory', $id)->delete('category_has_attributes');
//        $idattributetype_array = $this->input->post('id_attribute_type');
//        $idattribute_array = $this->input->post('id_attribute');
//        $count = count($idattributetype_array);
//        $data_att = array();
//        for ($i = 0; $i < $count; $i++) {
//            $id_attribute_type = $idattributetype_array[$i];
//            $id_attribute = $idattribute_array[$i];
//            if (isset($_POST['id_attribute' . $i])) {
//                if ($_POST['id_attribute' . $i] == 'on') {
//                    $is_variant = 0;
//                    if (isset($_POST['is_variant' . $i])) {
//                        if ($_POST['is_variant' . $i] == 'on') {
//                            $is_variant = 1;
//                        }
//                    }
//                    $data_att[$i] = array(
//                        'idattributetype ' => $id_attribute_type,
//                        'idattribute' => $id_attribute,
//                        'idcategory ' => $id,
//                        'is_variant' => $is_variant
//                    );
//                }
//            }
//        }
//        if (count($data_att) > 0) {
//            $this->db->insert_batch('category_has_attributes', $data_att);
//        }
        $this->db->trans_begin();
        $this->db->where('id_category', $id)->update('category', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
    public function edit_model($id,$data) {
        return $this->db->where('id_model', $id)->update('model', $data);
    }
    public function update_branch_data($idbranch, $data){
        return $this->db->where('id_branch', $idbranch)->update('branch', $data);
    }

    public function edit_customer($id,$data) {
        return $this->db->where('id_customer', $id)->update('customer', $data);
    }
//    public function edit_branch_petti_cash($id,$amount,$expamount) {
//        $due = $amount - $expamount;
//        return $this->db->where('id_branch', $id)
//                        ->set('total_petti_cash', 'total_petti_cash + ' . $amount, false)
//                        ->set('due_petti_cash', 'due_petti_cash + ' . $due , false)
//                        ->set('used_petti_cash', 'used_petti_cash + ' . $expamount, false)
//                        ->update('branch');
//    }
    // Delete
    public function remove_menu_role($id) {
        return $this->db->where('id_userrole_has_menu', $id)->delete('userrole_has_menu');
    }
    public function remove_branch_godown($id) {
        return $this->db->where('id_branch_has_billing_godown', $id)->delete('branch_has_billing_godown');
    }
    public function get_company_data(){
        return $this->db->get('company')->result();
    }
    public function save_db_company($data){
       return $this->db->insert('company', $data);
    }
    public function edit_db_comapny($id,$data){
      return $this->db->where('company_id', $id)->update('company', $data);
    }
    public function get_active_comapny(){
       return $this->db->where('active', 1)->get('company')->result();
    }
    public function get_zone_data(){
        return $this->db->get('zone')->result();
    }
    public function save_db_zone($data){
       return $this->db->insert('zone', $data);
    }
    public function edit_db_zone($id,$data){
      return $this->db->where('id_zone', $id)->update('zone', $data);
    }
    public function get_active_zone(){
      return $this->db->where('active', 1)->get('zone')->result();
    }
    public function get_bank_data(){
        return $this->db->get('bank')->result();
    }
    public function get_active_denomination(){
        return $this->db->where('active', 1)->get('denomination')->result();
    }
    public function save_db_bank($data){
       return $this->db->insert('bank', $data);
    }
    public function edit_db_bank($id,$data){
      return $this->db->where('id_bank', $id)->update('bank', $data);
    }
    public function get_active_bank(){
      return $this->db->where('active', 1)->get('bank')->result();
    }
    public function get_bank_byid($id_bank){
      return $this->db->where('id_bank', $id_bank)->get('bank')->row();
    }
    public function get_route_data(){
        return $this->db->where('active', 1)->get('route')->result();
    }
    public function save_db_route($data){
       return $this->db->insert('route', $data);
    }
    public function edit_db_route($id,$data){
      return $this->db->where('id_route', $id)->update('route', $data);
    }
    public function get_active_route(){
        return $this->db->where('active', 1)->get('route')->result();
    }
    public function get_branch_category_data(){
        return $this->db->get('branch_category')->result();
    }
    public function save_db_branch_category($data){
       return $this->db->insert('branch_category', $data);
    }
    public function edit_db_branch_category($id,$data){
      return $this->db->where('id_branch_category', $id)->update('branch_category', $data);
    }
    public function get_active_branch_category(){
        return $this->db->where('active', 1)->get('branch_category')->result();
    }
    public function get_warehouse_data(){
        return $this->db->where('is_warehouse', 1)->get('branch')->result();
    }    
    public function get_active_warehouse_data(){
        return $this->db->where('is_warehouse', 1)->where('active', 1)->get('branch')->result();
    }
    public function save_db_warehouse($data){
       return $this->db->insert('branch', $data);
    }
    public function edit_db_warehouse($id,$data){
      return $this->db->where('id_branch ', $id)->update('branch', $data);
    }
    public function save_db_branch($data) {
       $this->db->insert('branch', $data);
         return $this->db->insert_id();
    }
    public function edit_db_branch($id,$data) {
        return $this->db->where('id_branch', $id)->update('branch', $data);
    }
    public function get_active_branch_data(){
        return $this->db->where('is_warehouse', 0)->where('active', 1)->get('branch')->result();
    }
    public function get_active_branch_data_warehouse($idwarehouse){
        return $this->db->where('is_warehouse', 0)->where('idwarehouse', $idwarehouse)->where('active', 1)->get('branch')->result();
    }
    public function get_branch_data(){
//        return $this->db->where('is_warehouse', 0)->get('branch')->result();
          return $this->db->select('zone.zone_name,branch_category.branch_category_name,partner_type.partner_type,branch.*')
//                        ->where('is_warehouse', 0)
                        ->join('zone','idzone = zone.id_zone','left')
                        ->join('branch_category','idbranchcategory = branch_category.id_branch_category','left')
                        ->join('partner_type','idpartner_type = partner_type.id_partner_type','left')
                        ->order_by('zone.id_zone,branch.id_branch')
                        ->get('branch')->result();
    }
    public function get_allbranch_data(){
        return $this->db->get('branch')->result();
    }
    public function get_branch_byid($branch){
        return $this->db->where('id_branch', $branch)->get('branch')->row();
    }
    public function get_branch_array_byid($branch){
        return $this->db->where('id_branch', $branch)->get('branch')->result();
    }
    public function get_branch_byids($branch_array) {
        $branches = implode(",", $branch_array);
        $this->db->select('b.*,c.company_name,c.company_gstin');
        $order = sprintf('FIELD(id_branch, %s)', $branches);
        $this->db->order_by($order);
        return $this->db->where('c.company_id=b.idcompany')->from('company c')->where_in('b.id_branch', $branch_array)->get('branch b')->result();
        
    }
    public function get_company_byids($comapny_array) {
        $branches = implode(",", $comapny_array);
        $this->db->select('c.*');
        $order = sprintf('FIELD(c.company_id, %s)', $branches);
        $this->db->order_by($order);
        return $this->db->where_in('c.company_id', $comapny_array)->get('company c')->result();
        
    }
   

    public function get_active_branch_byid($idbranch){
        return $this->db->where('id_branch', $idbranch)->where('active', 1)->where('is_warehouse', 0)
                        ->get('branch')->result();
    }
    public function save_db_godown($data) {
        return $this->db->insert('godown', $data);
    }
    public function get_godown_data(){
        return $this->db->get('godown')->result();
    }
    public function get_active_godown_data(){
        return $this->db->where('active', 1)->get('godown')->result();
    }
    public function edit_db_godown($id,$data) {
        return $this->db->where('id_godown', $id)->update('godown', $data);
    }
    public function get_dispatch_type(){
        return $this->db->get('dispatch_type')->result();
    }
    public function save_dispatch_type($data){
        return $this->db->insert('dispatch_type', $data);
    }
    public function edit_dispatch_type($data, $id){
        return $this->db->where('id_dispatch_type', $id)
                        ->update('dispatch_type', $data);
    }

    public function get_transport_vendor_data(){
        return $this->db->get('transport_vendor')->result();
    }
    public function get_transport_vendor_data_byidbranch($id){
        if($_SESSION['branch_warehouse'] == 0){
            return $this->db->where('idbranch',$id)
                        ->get('transport_vendor')->result();
        }else{
            return $this->db->where('idbranch',$id)
                        ->or_where('idbranch',$_SESSION['branch_warehouse'])
                        ->get('transport_vendor')->result();
        }
    }
      public function save_transport_vendor($data) {
       return $this->db->insert('transport_vendor',$data);
    }
     public function edit_transport_vendor($id,$data) {
        return $this->db->where('id_transport_vendor', $id)->update('transport_vendor', $data);
    }
    public function get_vendor_data(){
        return $this->db->get('vendor')->result();
    }
    public function get_vendor_byid($id){
        return $this->db->where('id_vendor', $id)->get('vendor')->result();
    }
    public function get_active_vendor_data(){
        return $this->db->where('active', 1)->where('vendor_type', 1)->get('vendor')->result();
    }
    public function save_vendor($data) {
        $this->db->insert('vendor',$data);
        return $this->db->insert_id();
    }
    public function save_vendor_has_branch($data) {
        return $this->db->insert_batch('vendor_has_brand', $data);
    }
    public function get_vendor_has_brand_byidvendor($id) {
        return $this->db->where('idvendor', $id)->get('vendor_has_brand')->result();
    }
    public function delete_vendor_has_brand($id){
        return $this->db->where('idvendor', $id)->delete('vendor_has_brand');
    }

    public function edit_db_vendor($id,$data) {
        return $this->db->where('id_vendor', $id)->update('vendor', $data);
    }
    public function save_user_has_branch($data) {
        return $this->db->insert_batch('user_has_branch', $data);
    }
    public function save_user_has_brand($data) {
        return $this->db->insert_batch('user_has_brand', $data);
    }
    public function save_user_has_product_category($data) {
        return $this->db->insert_batch('user_has_product_category', $data);
    }
    public function save_user_has_payment_mode($data) {
        return $this->db->insert_batch('user_has_payment_mode', $data);
    }
    public function save_user_has_wallet_type($data) {
        return $this->db->insert_batch('user_has_wallet_type', $data);
    }
    public function save_user_has_costing_headers($data) {
        return $this->db->insert_batch('user_has_costing_headers', $data);
    }
    //////////////////////////allocation////////////
    
    public function get_product_category_by_user($id) {
        return  $this->db->select('pc.*, user_has_product_category.id_user_category')
                        ->where('active', 1)
                        ->where('iduser', $id)
                        ->where('pc.id_product_category = idproductcategory')->from('user_has_product_category')
                        ->get('product_category pc')->result();
    }
    public function get_brands_by_user($id) {
        return  $this->db->select('b.*, user_has_brand.id_user_has_brand')
                        ->where('active', 1)
                        ->where('iduser', $id)
                        ->where('b.id_brand = idbrand')->from('user_has_brand')
                        ->get('brand b')->result();
    }
    
    public function get_route_by_userid($iduser) {
        return  $this->db->select('r.*')
                        ->where('r.active', 1)
                        ->where('ur.iduser', $iduser)
                        ->where('r.id_route = ur.idroute')->from('user_has_route ur')
                        ->get('route r')->result();        
    } 
    public function get_route_by_warehouse_user_id($idwarehouse,$userid) {
            $this->db->select('r.*')->where('r.active', 1);                        
            if($idwarehouse){
                $this->db->where('r.idwarehouse', $idwarehouse);
            }
            if($userid){
                 $this->db->where('ur.iduser', $userid);
            }
            $this->db->where('r.id_route = ur.idroute')->from('user_has_route ur');
            return $this->db->get('route r')->result();
    } 
    public function get_branches_by_routeid($routeid) {
        return  $this->db->select('b.*')
                        ->where('b.active', 1)                        
                        ->where('b.idroute', $routeid)
                        ->order_by('b.id_branch')
                        ->get('branch b')->result();
    }
    public function get_branches_by_user($id) {
//        return  $this->db->select('b.*, ub.id_user_has_branch')->where('b.active', 1)->where('ub.iduser', $id)->where('b.is_warehouse', 0)->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')->get('branch b')->result();
        return  $this->db->select('b.*,zone.zone_name, ub.id_user_has_branch')
                        ->where('b.active', 1)
                        ->where('ub.iduser', $id)
                        ->where('b.is_warehouse', 0)
                        ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                        ->where('b.idzone = zone.id_zone')
                        ->order_by('zone.id_zone,b.id_branch')
                        ->from('zone')->get('branch b')->result();
    }   
    public function get_warehouse_by_user($id) {
          return $this->db->select('b.* , ub.id_user_has_branch')
                        ->where('b.active', 1)
                        ->where('ub.iduser', $id)
                        ->where('b.is_warehouse', 1)
                        ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                        ->get('branch b')->row();          
    }
    public function get_warehouses_by_user($id) {
          return $this->db->select('b.*')
                        ->where('b.active', 1)
                        ->where('ub.iduser', $id)
                        ->where('b.is_warehouse', 1)
                        ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                        ->get('branch b')->result();
    }
    public function get_payment_modes_by_user($id) {
        return  $this->db->select('pm.*, payment_head.*, user_has_payment_mode.id_user_has_paymentmode')
                        ->where('pm.active', 1)
                        ->where('iduser', $id)
                        ->where('pm.id_paymentmode = user_has_payment_mode.idpaymentmode')->from('user_has_payment_mode')
                        ->where('pm.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                        ->get('payment_mode pm')->result();
    }  
    public function get_wallet_type_by_user($iduser){
        return $this->db->where('idusers', $iduser)
                        ->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('user_has_wallet_type')->result();
    }
    public function get_active_model_by_brand_product_category($product_category,$brand) {        
        $where=array('model.idbrand' => $brand);
        if($product_category){
           $where=array('model.idbrand' => $brand,'model.idproductcategory' => $product_category); 
        }
        return $this->db->where($where)     
                        ->where('model.active', 1)
                        ->where('id_brand = idbrand')->from('brand')
                        ->get('model')->result();
    }
    public function get_active_variants_by_brand_product_category($product_category,$brand) {        
        $where=array('mv.idbrand' => $brand);
        if($product_category){
           $where=array('mv.idbrand' => $brand,'mv.idproductcategory' => $product_category); 
        }
        return $this->db->where($where)  
                        ->where('mv.active', 1)
                        ->where('id_brand = idbrand')->from('brand')
                        ->get('model_variants mv')->result();
    }       
    public function get_active_branchs(){
        return $this->db->where('b.is_warehouse', 0)  
                        ->where('b.active', 1)
                        ->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc')
                        ->where('b.idzone = z.id_zone')->from('zone z')
                        ->order_by('b.idzone,b.id_branch')
                        ->get('branch b')->result();
    } 
    public function get_branches_by_warehouseid($warehouse){
        return $this->db->where('b.is_warehouse', 0)  
                        ->where('b.active', 1)
                        ->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc')
                        ->where('b.idwarehouse', $warehouse)
                        ->where('b.idzone = z.id_zone')->from('zone z')
                        ->order_by('b.idzone,b.id_branch')
                 
                        ->get('branch b')->result();
    } 
    public function get_my_branches_n_warehouses($warehouse){ //get mapped branches and other warehouses of warehouse
        return $this->db->where('b.active', 1)
                        ->where('b.idwarehouse='.$warehouse.' or (b.id_branch!='.$warehouse.' and b.is_warehouse=1)')
                        ->order_by('b.is_warehouse desc')                 
                        ->get('branch b')->result();
    } 
    public function get_devices_byidmode($idmode) {
        return $this->db->where('idpayment_mode', $idmode)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->get('payment_mode_has_devices')->result();
    }
    public function save_payment_mode_has_devices($data) {
        return $this->db->insert('payment_mode_has_devices', $data);
    }
    public function edit_payment_mode_has_devices($idrow, $data) {
        return $this->db->where('id_payment_mode_has_devices', $idrow)->update('payment_mode_has_devices', $data);
    }
    
    public function delete_user_has_warehouse($id){
        return $this->db->where('id_user_has_branch', $id)->delete('user_has_branch');
    }
    public function delete_user_has_warehouse_byiduser($id){
        return $this->db->where('user_has_branch.iduser', $id)->where('user_has_branch.idbranch in (select id_branch from branch where is_warehouse=1)', NULL, FALSE)->delete('user_has_branch');
    }
    public function delete_user_has_branch_byiduser($id){
        return $this->db->where('user_has_branch.iduser', $id)->where('user_has_branch.idbranch in (select id_branch from branch where is_warehouse=0)', NULL, FALSE)->delete('user_has_branch');
    }
    public function delete_user_has_product_category($id){
        return $this->db->where('id_user_category', $id)->delete('user_has_product_category');
    }
    public function delete_user_has_product_category_byiduser($id){
        return $this->db->where('iduser', $id)->delete('user_has_product_category');
    }
    public function delete_user_has_brand($id){
        return $this->db->where('id_user_has_brand', $id)->delete('user_has_brand');
    }
    public function delete_user_has_brand_byiduser($id){
        return $this->db->where('iduser', $id)->delete('user_has_brand');
    }
    public function delete_user_has_payment_mode($id){
        return $this->db->where('id_user_has_paymentmode', $id)->delete('user_has_payment_mode');
    }
     public function delete_user_has_wallet_type($id){
        return $this->db->where('id_user_has_wallet_type', $id)->delete('user_has_wallet_type');
    }
    public function delete_user_has_payment_mode_byiduser($id){
        return $this->db->where('iduser', $id)->delete('user_has_payment_mode');
    }
    public function get_branch_by_role_user_level($role_type,$level,$id_users){

        $idbranch=$this->session->userdata('idbranch');                    
            if($role_type==1){
                if($level==1){
                    return $this->db->select('id_branch')->where('is_warehouse', 0)->where('idwarehouse', $idbranch)->where('active', 1)->get('branch')->result();
                }else{                    
                    return $this->db->select('b.id_branch')->where('b.active', 1)->where('ub.iduser', $id_users)->where('b.is_warehouse', 0)->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')->get('branch b')->result();
                }
            }elseif($role_type==2){
                    return $this->db->select('b.id_branch')->where('id_branch', $idbranch)->get('branch')->result();                
            }else{
                if($level==1){
                    return $this->db->select('b.id_branch')->where('b.active', 1)->where('b.is_warehouse', 0)->get('branch b')->result();                
                }else{
                    return $this->db->select('b.id_branch')->where('b.active', 1)->where('ub.iduser', $id_users)->where('b.is_warehouse', 0)->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')->get('branch b')->result();                
                }
            }   
//             die(print_r($this->db->last_query()));
    }
    public function get_user_has_branch_by_idrole_iduser($idrole,$idbranch){
        return $this->db->select('users.user_name,users.id_users')
                        ->where('users.iduserrole',$idrole)
                        ->where('user_has_branch.idbranch',$idbranch)
                        ->where('user_has_branch.iduser = users.id_users')->from('users')
                        ->get('user_has_branch')->result();
        die($this->db->last_query());
    }
   
    public function get_user_has_idpayment_mode($iduser) {
        return $this->db->select('idpaymentmode')
                        ->where('iduser', $iduser)
                        ->where('payment_mode.id_paymentmode = user_has_payment_mode.idpaymentmode')
                        ->where('payment_mode.active', 1)->from('payment_mode')
                        ->get('user_has_payment_mode')->result();
        
    }
    public function get_payment_modes_for_reconciliation() {
        return  $this->db->select('pm.payment_mode, pm.id_paymentmode, payment_head.payment_head')
                        ->where('pm.active', 1)->where('payment_head.payment_reconciliation = 1')
                        ->where('pm.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                        ->get('payment_mode pm')->result();
    }   
    public function get_payment_modes_for_credit_received_report() {
        return  $this->db->select('pm.payment_mode, pm.id_paymentmode, payment_head.payment_head')
                        ->where('pm.active', 1)->where('payment_head.credit_receive_type = 1')
                        ->where('pm.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                        ->get('payment_mode pm')->result();
    }   
    public function get_user_has_idbranch_mode($iduser) {
        if($_SESSION['level'] == 1){
            return $this->db->select('idbranch')->where('is_warehouse', 0)
                            ->where('active', 1)->get('branch')->result();
        }elseif($_SESSION['level'] == 3){
            return $this->db->select('idbranch')->where('iduser', $iduser)
                            ->where('user_has_branch.idbranch = branch.id_branch')
                            ->where('branch.active', 1)->from('branch')
                            ->get('user_has_branch')->result();
        }
    }
    public function get_bank_recon_payment_modes($iduser) {
        return $this->db->select('payment_mode.payment_mode, payment_mode.id_paymentmode, payment_head.payment_head')
                        ->where('uhpm.iduser', $iduser)
                        ->where('payment_mode.active', 1)
                        ->where('payment_mode.id_paymentmode = uhpm.idpaymentmode')
                        ->where('payment_head.bank_reconciliation = 1')
                        ->where('payment_mode.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')->from('payment_mode')
                        ->get('user_has_payment_mode uhpm')->result();
    }
    public function get_old_model_data(){
        return $this->db->where('idvariant',0)->join('model_variants','idvariant = model_variants.id_variant','left')->limit(300)->order_by('old_model_name','ASC')->get('old_product_model_data')->result();
    }
    public function get_allold_model_data(){
        return $this->db->join('model_variants','idvariant = model_variants.id_variant','left')->get('old_product_model_data')->result();
    }
    public function update_old_model($idoldmodel, $data){
        return $this->db->where('id_old_model_data', $idoldmodel)->update('old_product_model_data', $data);
    }
    public function save_opening_data($opening){
        $this->db->insert('opening', $opening);
        return $this->db->insert_id();
    }
    public function save_opening_stock_test_data($data){
        return $this->db->insert_batch('opening_stock_test', $data);
    }
    public function delete_opening_stock_test_data($idbranch, $datetime, $iduser){
        return $this->db->where('opening_stock_test.idbranch', $idbranch)
                        ->where('opening_stock_test.datetime', $datetime)
                        ->where('opening_stock_test.uploaded_by', $iduser)
                        ->where('opening_stock_test.name = old_product_model_data.old_model_name')->from('old_product_model_data')
                        ->where('old_product_model_data.idvariant = model_variants.id_variant')->from('model_variants')
                        ->delete('opening_stock_test');
    } 
    public function delete_upl_opening_stock_test($id){
         return $this->db->where_in('id_opening_stock_test', $id)->delete('opening_stock_test');
    }

//    public function delete_opening_stock_test_data($idgodown, $idbranch, $datetime, $iduser){
//        return $this->db->where('idgodown', $idgodown)
//                        ->where('idbranch', $idbranch)
//                        ->where('datetime', $datetime)
//                        ->where('uploaded_by', $_SESSION['id_users'])
//                        ->delete('opening_stock_test');
//    }
    public function delete_scanned_opening_stock_test($idgodown, $idbranch, $iduser){
        return $this->db->where('idgodown', $idgodown)
                        ->where('idbranch', $idbranch)
                        ->where('uploaded_by', $_SESSION['id_users'])
                        ->delete('opening_stock_test');
    }
//    public function get_opening_stock_test_data($idgodown, $idbranch, $datetime, $iduser){
//        return $this->db->where('opening_stock_test.idgodown', $idgodown)
//                        ->where('opening_stock_test.idbranch', $idbranch)
//                        ->where('opening_stock_test.datetime', $datetime)
//                        ->where('opening_stock_test.uploaded_by', $iduser)
//                        ->where('opening_stock_test.name = old_product_model_data.old_model_name')->from('old_product_model_data')
//                        ->where('old_product_model_data.idvariant = model_variants.id_variant')->from('model_variants')
//                        ->get('opening_stock_test')->result();
//    }
    public function get_opening_stock_test_data($idbranch, $datetime, $iduser){
         
        $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE  opening_stock_test.`idbranch` = $idbranch and opening_stock_test.uploaded_by = $iduser and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
        return $this->db->query($str)->result();
    }
    public function get_remaining_opening_stock_test_data($idbranch, $datetime, $iduser){
        return $this->db->where('opening_stock_test.idbranch', $idbranch)
                        ->where('opening_stock_test.datetime', $datetime)
                        ->where('opening_stock_test.uploaded_by', $iduser)
                        ->join('branch', 'branch.id_branch = opening_stock_test.idbranch', 'left')
                        ->get('opening_stock_test')->result();
    }
    
    
    public function ajax_get_remain_opening($idbranch, $idgodown){
         if($idbranch == 0){
            
            if($idgodown == 'ALL'){
                $str = "Select * from  opening_stock_test";
            }else{
                $str = "SELECT * FROM `opening_stock_test` WHERE `godown_name` = '".$idgodown."' ";
            }
        }else{
            if($idgodown == 'ALL'){
                $str = "Select * from  opening_stock_test where opening_stock_test.idbranch = $idbranch ";

            }else{
                $str = "SELECT * FROM `opening_stock_test` WHERE `godown_name` = '".$idgodown."' AND `idbranch` = $idbranch ";
            }
        }
        return $this->db->query($str)->result();
      
    }
     public function get_remaining_opening_stock($idbranch, $idgodown){
         if($idgodown == 'ALL'){
             $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE `idbranch` = $idbranch and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }else{
            $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE `godown_name` = '".$idgodown."' AND `idbranch` = $idbranch and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }
//        die($str);
        return $this->db->query($str)->result();
    }
     public function get_remaining_opening_stock_data($idbranch, $idgodown, $datetime){
         if($idgodown == 'ALL'){
             $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE `idbranch` = $idbranch and datetime LIKE '".$datetime."%' and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }else{
            $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE `godown_name` = '".$idgodown."' AND `idbranch` = $idbranch and datetime LIKE '".$datetime."%' and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }
//        die($str);
        return $this->db->query($str)->result();
    }
     public function get_remaining_qtyopening_stock_data($idgodown, $datetime){
         if($idgodown == 'ALL'){
             $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE datetime LIKE '".$datetime."%' and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }else{
            $str = "SELECT * FROM `opening_stock_test`, old_product_model_data, model_variants WHERE `godown_name` = '".$idgodown."' and datetime LIKE '".$datetime."%' and opening_stock_test.name = old_product_model_data.old_model_name and old_product_model_data.idvariant = model_variants.id_variant ";
         }
//        die($str);
        return $this->db->query($str)->result();
    }
    
    
    public function get_scan_opening_stock_test_data($idgodown, $idbranch, $iduser){
        return $this->db->where('opening_stock_test.idgodown', $idgodown)
                        ->where('opening_stock_test.idbranch', $idbranch)
                        ->where('opening_stock_test.uploaded_by', $iduser)
                        ->get('opening_stock_test')->result();
    }
    public function save_stock_data($data){
        return $this->db->insert_batch('stock', $data);
    }
    public function save_opening_stock_data($data){
        return $this->db->insert_batch('opening_data', $data);
    }
    public function ajax_get_opening_data($from, $to, $idbranch, $allbranches){
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
         return $this->db->where('opening.entry_date >=',$from)
                        ->where('opening.entry_date <=',$to)
                        ->where_in('opening.idbranch', $branches)
                        ->where('opening.idbranch = branch.id_branch')->from('branch')
                        ->join('users','opening.uploaded_by = users.id_users', 'left')
                        ->get('opening')->result();
//        return $this->db->where('opening.datetime between "'. $from.'" and "'. $to .'"')
//                        ->where_in('opening.idbranch', $branches)
//                        ->where('opening.idbranch = branch.id_branch')->from('branch')
//                        ->join('users','opening.uploaded_by = users.id_users', 'left')
//                        ->get('opening')->result();
    }
    public function get_opening_data_byidopening($id){
        return $this->db->where('opening_data.idopening', $id)
                        ->join('godown','opening_data.idgodown = godown.id_godown','left')
                        ->join('product_category','opening_data.idproductcategory = product_category.id_product_category','left')
                        ->join('category','opening_data.idcategory = category.id_category','left')
                        ->join('model_variants','opening_data.idvariant = model_variants.id_variant','left')
                        ->join('brand','opening_data.idbrand = brand.id_brand','left')
                        ->join('branch','opening_data.idbranch = branch.id_branch','left')
                        ->get('opening_data')->result();
    }
    
     public function ajax_get_model_variant_alldata(){        
        return $this->db->get('model_variants')->result();
    }
    public function ajax_get_address_bypincode($pincode){
        return $this->db->where('pincode', $pincode)->get('pincode')->result();
    }
    public function ajax_stock_by_imei($imei){
        return $this->db->where('imei_no', $imei)                        
                        ->get('stock')->row();   
    }
    public function ajax_get_refurbished_model_variant(){        
        return $this->db->where('active', 1)->like('full_name', '(Refurbished)', 'both')->get('model_variants')->result();
    }
    
     public function inward_product_bydate($f){
        //$cat = array(1,24,25,30);
         $cat = array(1,31,32,33,34);
        return $this->db->where('inward_product.date', $f)
                        ->where('inward_product.idbrand', 23)
                        ->where('inward_product.idskutype !=', 4)
                        ->where_in('inward_product.idcategory', $cat)
                        ->where('model_variants.part_number IS NOT NULL')
                        ->where('inward_product.idinward = inward.id_inward')->from('inward')
                        ->where('inward_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('inward_product.idcategory = category.id_category')->from('category')
                        ->get('inward_product')->result();
    }
    public function sale_product_bydate($f){
//        $cat = array(1,24,25,30);
        $cat = array(1,31,32,33,34);
        return $this->db->where('sale.date', $f)
                        ->where('sale_product.idbrand', 23)
                        ->where('sale_product.idskutype !=', 4)
                        ->where_in('sale_product.idcategory', $cat)
                        ->where('model_variants.part_number IS NOT NULL')
                        ->where('sale_product.idsale = sale.id_sale')->from('sale')
                        ->where('sale_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('sale_product.idbranch = branch.id_branch')->from('branch')
                        ->where('sale_product.idcategory = category.id_category')->from('category')
                        ->get('sale_product')->result();
    }
    public function sale_return_product_bydate($f){
//        $cat = array(1,24,25,30);
        $cat = array(1,31,32,33,34);
        return $this->db->where('sales_return_product.date', $f)
			->where('sales_return_product.idskutype !=', 4)
			->where('sales_return_product.idbrand', 23)
                        ->where_in('sales_return_product.idcategory', $cat)
                        ->where('model_variants.part_number IS NOT NULL')
                        ->where('sales_return_product.idsales_return = sales_return.id_salesreturn')->from('sales_return')
                        ->where('sales_return_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('sales_return_product.idbranch = branch.id_branch')->from('branch')
                        ->where('sales_return_product.idcategory = category.id_category')->from('category')
                        ->get('sales_return_product')->result();
    }
    
    public function save_partner_type($data){
        return $this->db->insert('partner_type', $data);
    }
    public function edit_partner_type($data, $id){
        return $this->db->where('id_partner_type', $id)->update('partner_type', $data);
    }
    
     public function save_time_slot($data){
        return $this->db->insert('time_slots', $data);
    }
    public function get_time_slot_data(){
        return $this->db->get('time_slots')->result();
    }
    public function edit_time_slot($data, $id){
        return $this->db->where('id_time_slab', $id)->update('time_slots', $data);
    }
     public function get_cluster_head_data(){
        return $this->db->select('users.user_name as clust_name,user_has_branch.idbranch as clustbranch')
                        ->where('users.iduserrole',26)
                        ->where('user_has_branch.iduser = users.id_users')->from('users')
                        ->get('user_has_branch')->result();
    }
    public function get_role_byid($id){
        return $this->db->where('id_userrole', $id)->get('user_role')->row();
    }
    public function get_product_by_sale_type($sale_type) {
        return $this->db->select('mv.id_variant, mv.sale_type, mv.modelname, mv.full_name, product_category.product_category_name')
                        ->where('mv.active = 1')
                        ->where_in('mv.sale_type', $sale_type)
                        ->where('product_category.active = 1')
                        ->where('product_category.id_product_category  = mv.idproductcategory')->from('product_category')
                        ->get('model_variants mv')->result();
    }
    public function get_user_details_byidrole($idrole){
        return $this->db->select('users.*,b.id_branch, b.branch_name, user_role.*')
                        ->where('iduserrole', $idrole)
                        ->where('iduserrole=id_userrole')->from('user_role')
                        ->join('branch b','users.idbranch = b.id_branch','left')
                        ->get('users')->result();
    }
    public function get_user_data_byidzone($idzone){
        return $this->db->select('DISTINCT(iduser)')
                        ->where('branch.idzone', $idzone)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->get('user_has_branch')->result();
    }
    public function update_price_category_slab($data, $id){
        return $this->db->where('id_price_category_lab', $id)->update('price_category_lab', $data);
    }
    public function save_price_category_slab($data){
        return $this->db->insert('price_category_lab', $data);
    }
    
    public function get_zone_branch_data(){
        $this->db->select('zone.zone_name,branch.*');
        $this->db->where('branch.is_warehouse', 0);
        $this->db->where('branch.active', 1);
        $this->db->where('idzone = zone.id_zone');
        $this->db->order_by('zone.id_zone, branch.id_branch');
        $this->db->from('zone');
        $this->db->from('branch');
        $query = $this->db->get();  
        return $query->result();
        
    }
    
    
    // VENDOR SKU
    
    public function get_vendor_sku_data(){                
        return $this->db->where('vendors_sku.active', 1)->get('vendors_sku')->result();
    }
    public function get_vendor_sku_data_byid($id){                
        return $this->db->where('vendors_sku.id_vendors_sku', $id)->get('vendors_sku')->row();
    }
    public function save_vendor_sku($data,$column) {
        $this->db->query("ALTER TABLE `model_variants` ADD $column VARCHAR(200) NULL AFTER `sale_type`;"); 
        return $this->db->insert('vendors_sku', $data);
    }
    public function update_model_variants_byidvariant_bulk($data){
        return $this->db->update_batch('model_variants',$data, 'id_variant');         
    }
    
    //////BILING MODE //////
    
    
    
    public function get_billing_mode_data(){                
        return $this->db->where('billing_modes.active', 1)->get('billing_modes')->result();
    }
    public function get_billing_mode_data_byid($id){                
        return $this->db->where('billing_modes.id_billing_mode', $id)->get('billing_modes')->row();
    }
    public function save_billing_modes($data,$column) {
        $this->db->query("ALTER TABLE `branch` ADD $column INT NOT NULL COMMENT '0 - off, 1 - on' AFTER `apple_store_id`"); 
        return $this->db->insert('billing_modes', $data);
    }
    public function update_branch_byid_branch_bulk($data){
                $str="UPDATE `branch` SET ";
                $ary=$this->db->where('billing_modes.active', 1)->get('billing_modes')->result();
                foreach ($ary as $mode){
                    $str.= " $mode->billing_mode_column_name = 0,";
                }
                $str=rtrim($str,',');
                $this->db->query($str);
        return $this->db->update_batch('branch',$data, 'id_branch');         
    }
    
      public function update_gst_db_price() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));

        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        $dprince = array(
            'idvariant' => $id_variant,
            'idcategory' => $idcategory,
            'idmodel' => $idmodel,
            'idproductcategory'  => $idproductcategory,
            'idbrand' => $idbrand,
            'planding' => $this->input->post('landing'),
            'pmop' => $this->input->post('mop'),
            'pmrp' => $this->input->post('mrp'),           
            'created_by' => $_SESSION['id_users'],
            'cgst' => $this->input->post('cgst'),
            'sgst' => $this->input->post('sgst'),
            'igst' => $this->input->post('igst'),
            'psalesman_price' => $this->input->post('salesman'),
            'timestamp' => $timestamp,
            'p_emiprice' => $this->input->post('emi'),
            'ponline_price' => $this->input->post('online_price'),    
            'pcorporate_sale' => $this->input->post('wholesale_price'),    
        );
        $this->db->insert('price', $dprince);        
        $data = array(
            'cgst' => $this->input->post('cgst'),
            'sgst' => $this->input->post('sgst'),
            'igst' => $this->input->post('igst'),
        );
        $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $q['result'] = 'no';
        } else {
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    
    public function update_gst_db_all_variant_price() {
        $this->db->trans_begin();  
        $ids = explode("_", $this->input->post('ids'));
        
        $id_variant = $ids[0];
        $idmodel = $ids[1];
        $idbrand = $ids[2];
        $idcategory = $ids[3];
        $idproductcategory = $ids[4];
        $timestamp = time();
        
        if($idproductcategory == 1){
            
            if($idcategory == 2 || $idcategory == 28 || $idcategory == 31){
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and idmodel = $idmodel";
            }else{
            //get idvariant of same attribute value of model
                $sql = "SELECT mv.id_variant FROM model_variants mv WHERE id_variant IN( SELECT v.idvariant FROM ( SELECT `idvariant`, `idmodel`, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute GROUP BY `idvariant`, `idmodel` ) AS v INNER JOIN( SELECT `idvariant`, idmodel, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute WHERE `idvariant` = $id_variant GROUP BY `idvariant` ) AS b ON b.ram = v.ram AND b.rom = v.rom WHERE v.`idmodel` = b.`idmodel` ) AND active = 1 GROUP BY mv.id_variant DESC ";
            }
            $idvar =  $this->db->query($sql)->result();
            
            //store idvariant in array 
            foreach ($idvar as $idvv){
                $idva[] = $idvv->id_variant;
            }
            for($i=0; $i<count($idva);$i++){
                $dprince = array(
                    'idvariant' => $idva[$i],
                    'idcategory' => $idcategory,
                    'idmodel' => $idmodel,
                    'idproductcategory'  => $idproductcategory,
                    'idbrand' => $idbrand,
                    'planding' => $this->input->post('landing'),
                    'pmop' => $this->input->post('mop'),
                    'pmrp' => $this->input->post('mrp'),           
                    'created_by' => $_SESSION['id_users'],
                    'cgst' => $this->input->post('cgst'),
                    'sgst' => $this->input->post('sgst'),
                    'igst' => $this->input->post('igst'),
                    'psalesman_price' => $this->input->post('salesman'),
                    'timestamp' => $timestamp,
                    'p_emiprice' => $this->input->post('emi'),
                    'ponline_price' => $this->input->post('online_price'),    
                    'pcorporate_sale' => $this->input->post('wholesale_price'),    
                );
                $this->db->insert('price', $dprince);        
                $data = array(
                    'cgst' => $this->input->post('cgst'),
                    'sgst' => $this->input->post('sgst'),
                    'igst' => $this->input->post('igst'),
                );
                $this->db->where('id_variant', $idva[$i])->update('model_variants', $data);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            echo json_encode($q);
            
        }else{
            $dprince = array(
                'idvariant' => $id_variant,
                'idcategory' => $idcategory,
                'idmodel' => $idmodel,
                'idproductcategory'  => $idproductcategory,
                'idbrand' => $idbrand,
                'planding' => $this->input->post('landing'),
                'pmop' => $this->input->post('mop'),
                'pmrp' => $this->input->post('mrp'),           
                'created_by' => $_SESSION['id_users'],
                'cgst' => $this->input->post('cgst'),
                'sgst' => $this->input->post('sgst'),
                'igst' => $this->input->post('igst'),
                'psalesman_price' => $this->input->post('salesman'),
                'timestamp' => $timestamp,
                'p_emiprice' => $this->input->post('emi'),
                'ponline_price' => $this->input->post('online_price'),    
                'pcorporate_sale' => $this->input->post('wholesale_price'),    
            );
            $this->db->insert('price', $dprince);        
            $data = array(
                'cgst' => $this->input->post('cgst'),
                'sgst' => $this->input->post('sgst'),
                'igst' => $this->input->post('igst'),
            );
            $this->db->where('id_variant', $id_variant)->update('model_variants', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $q['result'] = 'no';
            } else {
                $this->db->trans_commit();
                $q['result'] = 'yes';
            }
            echo json_encode($q);
        }
    }
    
    public function get_apple_webgdv_report_data($from, $to){
        $nextday = date('Y-m-d', strtotime('+1 day', strtotime($to)));
        $cats = array(1,31,32,33,34);
        $this->db->select('b.id_branch,b.branch_name,b.apple_store_id,mv.id_variant,mv.part_number,mv.full_name,stk.stock_qty,spr.sale_qty,rsa.ret_qty,intstk.intrastock_qty');
        $this->db->where('b.apple_store_id !=', NULL);
        $this->db->where('mv.idsku_type !=', 4);
        $this->db->where('mv.idbrand', 23);
        $this->db->where('mv.part_number !=', NULL);
        $this->db->where('mv.part_number !=', '');
        $this->db->where_in('mv.idcategory', $cats);
        $this->db->join("(select sum(s.qty) as stock_qty, s.idvariant,s.idbranch from copy_daily_stock s where s.idgodown = 1 and s.date = '$nextday' and s.idbrand = 23 group by s.idvariant,s.idbranch)stk",'stk.idvariant = mv.id_variant and stk.idbranch = b.id_branch','left');
        $this->db->join("(select sum(s.qty) as intrastock_qty, s.idvariant,s.temp_idbranch from copy_daily_stock s where s.idgodown = 1 and s.date = '$to' and s.idbrand = 23 group by s.idvariant,s.temp_idbranch)intstk",'intstk.idvariant = mv.id_variant and intstk.temp_idbranch = b.id_branch','left');
        $this->db->join("(select count(sp.id_saleproduct) as sale_qty,sp.idvariant,sp.idbranch from sale_product sp where sp.idgodown = 1 and sp.idbrand = 23 and sp.date between '$from' and '$to' group by sp.idvariant,sp.idbranch)spr",'spr.idvariant = mv.id_variant and spr.idbranch = b.id_branch','left');
        $this->db->join("(select count(srp.id_salesreturnproduct) as ret_qty, srp.idvariant,srp.idbranch from sales_return_product srp where srp.idgodown = 1 and srp.idbrand = 23 and srp.sales_return_type = 3 and srp.date between '$from' and '$to' group by srp.idvariant,srp.idbranch)rsa",'rsa.idvariant = mv.id_variant and rsa.idbranch = b.id_branch','left');
        $this->db->from('model_variants mv');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
//                    die( $this->db->last_query());
    }           
    public function get_apple_webgdv_report_excel_data($from, $to){
        $cats = array(1,31,32,33,34);
        $this->db->select('b.id_branch,b.branch_name,b.apple_store_id,mv.id_variant,mv.part_number,mv.full_name,stk.stock_qty,spr.sale_qty,rsa.ret_qty,intstk.intrastock_qty');
        $this->db->where('b.apple_store_id !=', NULL);
        $this->db->where('mv.idsku_type !=', 4);
        $this->db->where('mv.idbrand', 23);
        $this->db->where('mv.part_number !=', NULL);
        $this->db->where('mv.part_number !=', '');
        $this->db->where_in('mv.idcategory', $cats);
        $this->db->join("(select sum(s.qty) as stock_qty, s.idvariant,s.idbranch from stock s where s.idgodown = 1 and s.idbrand = 23 group by s.idvariant,s.idbranch)stk",'stk.idvariant = mv.id_variant and stk.idbranch = b.id_branch','left');
        $this->db->join("(select sum(s.qty) as intrastock_qty, s.idvariant,s.temp_idbranch from stock s where s.idgodown = 1 and s.idbrand = 23 group by s.idvariant,s.temp_idbranch)intstk",'intstk.idvariant = mv.id_variant and intstk.temp_idbranch = b.id_branch','left');
        $this->db->join("(select count(sp.id_saleproduct) as sale_qty,sp.idvariant,sp.idbranch from sale_product sp where sp.idgodown = 1 and sp.idbrand = 23 and sp.date between '$from' and '$to' group by sp.idvariant,sp.idbranch)spr",'spr.idvariant = mv.id_variant and spr.idbranch = b.id_branch','left');
        $this->db->join("(select count(srp.id_salesreturnproduct) as ret_qty, srp.idvariant,srp.idbranch from sales_return_product srp where srp.idgodown = 1 and srp.idbrand = 23 and srp.sales_return_type = 3 and srp.date between '$from' and '$to' group by srp.idvariant,srp.idbranch)rsa",'rsa.idvariant = mv.id_variant and rsa.idbranch = b.id_branch','left');
        $this->db->from('model_variants mv');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }         
     public function delete_branch_phead_configuration(){
        return $this->db->empty_table('branch_has_payment_modes');
    }

    public function save_branch_phead_configuration($data){
        return $this->db->insert_batch('branch_has_payment_modes', $data);
    }
    
    //Invoice credit / custody
    
     public function get_branch_has_paymenthead_byidbranch($idbranch){
        return $this->db->select('payment_head.*')
                        ->where('idbranch', $idbranch)
                        ->where('idhead = payment_head.id_paymenthead')->from('payment_head')
                        ->get('branch_has_payment_modes')->result();
    }
    public function get_branch_credit_data($idbranch){
        $ids = array(6); //,7  6 credit , 7 custody
        return $this->db->select('(sum(amount) - sum(received_amount)) as credit_amount, max(date) as credit_date')
                        ->where('idbranch', $idbranch)
                        ->where_in('idpayment_head', $ids)
//                        ->where('payment_receive', 0)
                        ->get('sale_payment')->row();
    }
    public function get_branch_payment_head_data(){
        return $this->db->get('branch_has_payment_modes')->result();
    }
    public function get_branchandwarehouse_data(){
//        return $this->db->where('is_warehouse', 0)->get('branch')->result();
          return $this->db->select('zone.zone_name,branch_category.branch_category_name,partner_type.partner_type,branch.*')                        
                        ->join('zone','idzone = zone.id_zone','left')
                        ->join('branch_category','idbranchcategory = branch_category.id_branch_category','left')
                        ->join('partner_type','idpartner_type = partner_type.id_partner_type','left')
                        ->get('branch')->result();
    }
    public function get_active_billing_payment_mode_byhead($idhead){
        return $this->db->where('payment_mode.id_paymentmode not in (17,18)')->where('payment_mode.active = 1')->where('idpaymenthead', $idhead)->get('payment_mode')->result();
    }
    public function get_brand_byid($id){
        return $this->db->where('id_brand', $id)->get('brand')->result();
    }
    public function get_active_branch_service_executive(){
        return $this->db->select('b.branch_name,b.id_branch,b.idservice_executive,z.zone_name,users.user_name,users.id_users')->where('b.is_warehouse', 0)  
                        ->where('b.active', 1)                        
                        ->where('b.idzone = z.id_zone')->from('zone z')
                        ->join('users','b.idservice_executive = users.id_users','left')
                        ->order_by('b.idzone,b.id_branch')
                        ->get('branch b')->result();
    } 
    public function get_service_executive(){
        return $this->db->select('users.user_name as clust_name,user_has_branch.idbranch as clustbranch')
                        ->where('users.iduserrole',26)
                        ->where('user_has_branch.iduser = users.id_users')->from('users')
                        ->get('user_has_branch')->result();
    }
    public function get_price_category_lab(){
      return $this->db->where('active', 0)->get('price_category_lab')->result();
    }
    public function get_same_variants_for_allocation($variantid,$modelid,$type) { 
        $qy="";
        if($type==0){
            $qy="select  mv.id_variant from model_variants mv "
                . "where id_variant in (select v.idvariant from (select `idvariant`,`idmodel`, MAX(CASE WHEN `idattribute`=9 THEN `attribute_value` END) ram, MAX(CASE WHEN `idattribute`=8 THEN `attribute_value` END) rom from model_variants_attribute group by `idvariant`,`idmodel`) as v inner join (select `idvariant`,idmodel, MAX(CASE WHEN `idattribute`=9 THEN `attribute_value` END) ram, MAX(CASE WHEN `idattribute`=8 THEN `attribute_value` END) rom from model_variants_attribute where `idvariant`=$variantid group by `idvariant`) as b on b.ram=v.ram and b.rom=v.rom WHERE v.`idmodel`=b.`idmodel`) and active=1  group by mv.id_variant ORDER BY FIELD(mv.id_variant, $variantid) DESC";
        }else{
            $qy="select mv.id_variant from model_variants mv  " 
                . "where mv.idmodel=$modelid  and active=1  group by mv.id_variant ORDER BY FIELD(mv.id_variant, $variantid) DESC";
        } 
        $query = $this->db->query($qy); 
        return $query->result();   
    }
}
?> 