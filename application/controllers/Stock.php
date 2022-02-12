<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Stock_model");
        $this->load->model("Allocation_model"); 
        $this->load->model("Outward_model");      
        $this->load->model("Ingram_Model");       
        date_default_timezone_set('Asia/Kolkata');
    }
    public function store_stock_report() {    
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');        
        
        $q['tab_active'] = '';
                
        $q['active_godown'] = $this->General_model->get_active_godown();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();          
        if($role_type==1){
            if($level==1){
                $idwarehouse=$this->session->userdata('idbranch');
                $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();   // all branches for temp
                //$q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                    
            }                   
        }elseif($role_type==0){
            if($level==3){
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();                        
            }
        }else{          
            $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
        }            
        $this->load->view('stock/stock_report', $q);
    }
    
    public function stock_search() {    
        $user_id=$this->session->userdata('id_users');         
        $q['tab_active'] = '';                
        $q['active_godown'] = $this->General_model->get_active_godown();
        $q['brand_data'] = $this->General_model->get_active_brand_data();            
        $this->load->view('stock/stock_search', $q);
    }
    public function w_stock_report() {     
        $user_id=$this->session->userdata('id_users');   
        $role_type=$this->session->userdata('role_type');
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');        
        $q['active_godown'] = $this->General_model->get_active_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idwarehouse=$this->session->userdata('idbranch');        
        if($role_type==1){            
            $q['branch_data'] = $this->General_model->get_branch_array_byid($idwarehouse);  
                $q['product_category'] = $this->General_model->get_product_category_data();            
//            if($level==1){
//            }elseif($level==2){            
//                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
//            }
        }else{
            if($level==3){
                $q['branch_data'] = $this->General_model->get_warehouses_by_user($user_id);   
            }else{                
                $q['branch_data'] = $this->General_model->get_active_warehouse_data();  
            }
                $q['product_category'] = $this->General_model->get_product_category_data();  
        }    
        $this->load->view('stock/w_stock_report', $q);
    }
    
    public function ageing_store_stock_analysis() {     
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idbranch = $this->session->userdata('idbranch');
        if($role_type==1){
            if($level==1){        
                $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_data();
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();  // all branches for temp
//                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
            }
        }else{
            if($level==1){
                $q['branch_data'] = $this->General_model->get_active_branch_data();
                $q['product_category'] = $this->General_model->get_product_category_data();
            }elseif($level==2){    
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
            }elseif($level==3){            
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
            }
        }                
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();        
        }
        $q['type']='store';
        $this->load->view('stock/ageing_store_stock_analysis', $q);
    }
     public function ajax_ageing_store_stock_analysis_report(){
        $user_id=$this->session->userdata('id_users'); 
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');        
        $days = $this->input->post('days');
        $idgodown = $this->input->post('idgodown');
        $idbrand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $idproductcategory = $this->input->post('idproductcategory'); 
        
        
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$user_id);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }        
        $stock_data = $this->Stock_model->get_store_stock_analysis($idgodown,$idbranch,$idbranchs,$days,$idbrand,$idproductcategory,'store','','');
        
//         die('<pre>'.print_r($stock_data,1).'</pre>');
        if($idbranch!=0) { ?>
            <thead class="fixedelement" style="text-align: center;position: none !important;">
            <th colspan="3" class="textalign"> Branch Name - <?php echo $stock_data[0]->branch_name ?> </th>
            <th colspan="3" class="textalign"> Zone Name - <?php echo $stock_data[0]->zone_name ?> </th>
            <th colspan="3" class="textalign"> Branch Category - <?php echo $stock_data[0]->branch_category_name ?> </th>
            <th colspan="2" class="textalign"> Add To Ageing </th>
            </thead>
        <?php } ?>
            <thead class="fixedelement" style="text-align: center;position: none !important;">
                <?php if($idbranch==0){ ?>
                    <th>Zone</th>
                    <th>Branch Category</th>
                    <th>Branch</th>
                <?php } ?>
                <th>Product Category</th> 
                <th>Brand Name</th>
                <th>Model Name</th>                           
                <th>Branch Stock</th>
                <th>inTransit Stock</th>
                <th>Total Stock</th>
                <th><?php echo 'Last '.$days.' Days Sale'?></th>
                <th>Enough for days</th>
                <th>Order Prediction</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php $i=0; $sumqty=0; foreach ($stock_data as $data){
                    $ageing_data = $this->Stock_model->get_ageing_stock_data($data->id_product_category, $data->id_brand, $data->idmodel,$data->id_variant, $data->id_branch);
                    if($ageing_data){
                        $status = 1;
                    }else{
                        $status = 0;
                    }
                    
                    $sumqty = $data->stock_qty +  $data->sale_qty + $data->intra_stock_qty;
                    $days_for_1_qty = 0;
                    $ned=0;
                    if ($data->sale_qty > 0) {
                        $days_for_1_qty = ($days / $data->sale_qty);
                    }
                    $total=($data->stock_qty+$data->intra_stock_qty);
                    $enough_for_days = round($total * $days_for_1_qty);
                    if ($days_for_1_qty <= 0) {
                        $ned = 0;
                    } else {
                        $ned = ($days / $days_for_1_qty);
                    }
                    $purchase_pre = round($ned - $total);
                    
                    if($sumqty > 0){ ?>
                        <tr>    
                            <?php if($idbranch==0){ ?>
                                <th><?php echo $data->zone_name ?></th>
                                <th><?php echo $data->branch_category_name ?></th>
                                <th><?php echo $data->branch_name ?></th>
                            <?php } ?>
                            <td><?php echo $data->product_category_name ?></td>
                            <td><?php echo $data->brand_name?></td>
                            <td><?php echo $data->full_name?></td>                  
                            <td><?php echo $data->stock_qty ?></td>
                            <td><?php echo $data->intra_stock_qty ?></td>
                            <td><?php echo $total ?></td>
                            <td><?php echo $data->sale_qty ?></td>
                            <td><?php echo $enough_for_days ?></td>
                            <td><?php echo $purchase_pre ?></td>
                            <td><div id="idstatus"><?php if($status == 1){ echo 'Ageing'; } ?></div></td>
                            <td><input type="hidden" name="idpcat" id="idpcat" value="<?php echo $data->id_product_category ?>">
                                <input type="hidden" name="idbrand" id="idbrand" value="<?php echo $data->id_brand ?>">
                                <input type="hidden" name="idmodel" id="idmodel" value="<?php echo $data->idmodel ?>">
                                <input type="hidden" name="idvariant" id="idvariant" value="<?php echo $data->id_variant ?>">
                                <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $data->id_branch ?>">
                                <input type="hidden" name="idsta" id="idsta" value="<?php echo $status; ?>">
                                
                                <div class="test">
                                <?php  if($status == 1){ ?>
                                        <a class="btn btn-warning btn-sm btnremove"><b>Remove</b></a>
                                    <?php  }?>
                                        <?php if($status == 0){ ?>
                                    <a class="btn btn-info btn-sm btnageing"><b>Add To Ageing</b></a> 
                                <?php  }  ?>
                                </div>
                                <div class="tshow" style="display: none">
                                    <a class="btn btn-info btn-sm btnageing"><b>Add To Ageing</b></a> 
                                    <a class="btn btn-warning btn-sm btnremove"><b>Remove</b></a>
                                </div>
                            </td>
                        </tr>
                    <?php } 
                $i++; } ?>
            </tbody>         
            <script>
                $(document).ready(function (){
                  
                    $('.btnageing').click(function (){
                       var closediv =  $(this).closest('div').parent('td');
                        var idpcat = closediv.find('#idpcat') .val();
                        var idbrand = closediv.find('#idbrand') .val();
                        var idmodel = closediv.find('#idmodel') .val();
                        var idvariant = closediv.find('#idvariant') .val();
                        var idbranch = closediv.find('#idbranch') .val();
                        if(confirm("Do You Want To Add This Model In Ageing Stock ? ")){
                            $.ajax({
                                url:"<?php echo base_url() ?>Stock/ajax_save_ageing_stock",
                                method:"POST",
                                data:{ idpcat:idpcat, idbrand: idbrand, idmodel: idmodel, idvariant:idvariant, idbranch:idbranch},
                                success:function(data)
                                {
                                    if(data == '1' || data == 1){ 
                                        closediv.find('.btnremove').show();
                                        closediv.find('.btnageing').hide();
                                        var st = 'Ageing';
                                        closediv.parent('tr').find('#idstatus').html(st);
                                        closediv.parent('tr').find('.test').attr("style", "display:none");
                                        closediv.parent('tr').find('.tshow').attr("style", "display:block") ;
//                                        closediv.parent('tr').find('.get_status').html('<a class="btn btn-warning btn-sm btnremove" ><b>Remove</b></a>');
//                                        $(this).remove();
                                    }else{
                                        alert("Failed to add in ageing");
                                        return false;
                                    }
                                }
                            }); 
                        }else{
                            return false;
                        }
                   }); 
                   
                   $('.btnremove').click(function (){
                       var closediv =  $(this).closest('div').parent('td');
                        var idpcat = closediv.find('#idpcat') .val();
                        var idbrand = closediv.find('#idbrand') .val();
                        var idmodel = closediv.find('#idmodel') .val();
                        var idvariant = closediv.find('#idvariant') .val();
                        var idbranch = closediv.find('#idbranch') .val();
                        if(confirm("Do You Want To Remove This Model In Ageing Stock ? ")){
                            $.ajax({
                                url:"<?php echo base_url() ?>Stock/ajax_remove_ageing_stock",
                                method:"POST",
                                data:{ idpcat:idpcat, idbrand: idbrand, idmodel: idmodel, idvariant:idvariant, idbranch:idbranch},
                                success:function(data)
                                {
                                    if(data == '1' || data == 1){ 
                                        closediv.find('.btnageing').show();
                                        closediv.find('.btnremove').hide();
                                        var st = '';
                                        closediv.parent('tr').find('#idstatus').html(st);
                                         closediv.parent('tr').find('.test').attr("style", "display:none");
                                        closediv.parent('tr').find('.tshow').attr("style", "display:block") ;
//                                        closediv.parent('tr').find('.get_status').html('<a class="btn btn-info btn-sm btnageing"><b>Add To Ageing</b></a> ');
//                                        $(this).remove();
                                    }else{
                                        alert("Failed To Remove From Ageing Stock");
                                        return false;
                                    }
                                }
                            }); 
                        }else{
                            return false;
                        }
                   }); 
                });
            </script>
<?php         
    }
    
    public function ajax_save_ageing_stock(){
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        $idvariant = $this->input->post('idvariant');
        $idbranch = $this->input->post('idbranch');
        
        $data = array(
            'idproductcategory' => $idpcat,
            'idbrand' => $idbrand,
            'idmodel' => $idmodel,
            'idvariant' => $idvariant,
            'idbranch' => $idbranch,
            'created_by' => $_SESSION['id_users'],
        );
        if($this->Stock_model->save_ageing_store_stock($data)){
            $res = 1;
        }else{
            $res = 0;
        }
        echo $res;
        
    }
    public function ajax_remove_ageing_stock(){
        $idproductcategory = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        $idvariant = $this->input->post('idvariant');
        $idbranch = $this->input->post('idbranch');
        
        if($this->Stock_model->remove_ageing_store_stock($idproductcategory, $idbrand, $idmodel,$idvariant, $idbranch)){
            $res = 1;
        }else{
            $res = 0;
        }
        echo $res;
        
    }
    
    public function stock_analysis_report() {     
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        
        $q['active_godown'] = $this->General_model->get_active_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data(); 
        $role_type=$this->session->userdata('role_type');
        if($role_type==0){
            $q['branch_data'] = $this->General_model->get_active_warehouse_data();                     
        }else{
            $q['branch_data'] = $this->General_model->get_warehouses_by_user($user_id);         
        }
        
        if($level==1){
            $q['product_category'] = $this->General_model->get_product_category_data();            
        }elseif($level==2){            
            $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
        }elseif($level==3){            
            $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
        }        
        $this->load->view('stock/stock_analysis', $q);
    }
    public function set_stock_norms() { 
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['godown_data'] = $this->General_model->get_active_godown();
        $this->load->view('stock/set_stock_norms', $q);
    }

 public function stock_norms_details() { 
        $q['days'] = 40;
        if (isset($_POST['days'])) {
            $q['days'] = $_POST['days'];
        }
        $q['tab_active'] = '';
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $q['norms_data'] = $this->Stock_model->get_all_branch_stocknorms($q['product_category'], $q['days']);        
//        $q['branch_data'] = $this->General_model->get_active_branch_data();
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['zone_data'] = $this->General_model->get_zone_data();
        $q['productcategory'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('stock/stock_norms_details', $q);
    }

   /* public function stock_norms_details() {
        $q['days'] = 40;
        if (isset($_POST['days'])) {
            $q['days'] = $_POST['days'];
        }
        $q['tab_active'] = '';
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $q['norms_data'] = $this->Stock_model->get_all_branch_stocknorms($q['product_category'], $q['days']);        
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('stock/stock_norms_details', $q);
    }*/

    public function ajax_save_model_stock_norms() {

        if (isset($_POST['id_vatriant']) && count($_POST['id_vatriant']) > 0) {
            $data_att = array();
            $vatriant = array();
            $i = 0;
            foreach ($_POST['id_vatriant'] as $id_vatriant) {
                $data_att[$i] = array(
                    'idproductcategory ' => $_POST['idproductcategory'],
                    'idcategory' => $_POST['idcategory'],
                    'idbrand ' => $_POST['idbrand'],
                    'idbranch ' => $_POST['idbranch'],
                    'idvariant ' => $id_vatriant,
                    'quantity ' => $_POST['quantity'][$i],
                    'norm_lmb ' => $this->session->userdata('id_users'),
                    'norm_lmt ' => date('Y-m-d H:i:s')
                );
                $i++;
            }

            if (count($data_att) > 0) {
                $result = $this->Stock_model->save_db_branch_stocknorms($_POST['idbranch'], $_POST['id_vatriant'], $data_att);
                if ($result) {
                    $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
                    die($output);
                } else {

                    $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                    die($output);
                }
            } else {
                $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                die($output);
            }
        } else {
            $output = json_encode(array("result" => "true", "data" => "select_model", "message" => ""));
            die($output);
        }
    }

    public function ajax_get_model_stock_norms() {
        $model_data = $this->Stock_model->get_branch_modelstocknorms_by_PCB($this->input->post('category'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'), $this->input->post('days'));
        ?>
        <thead class="fixedelementtop">
            <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_name; ?></th>
            <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_category_name; ?></th>
            <th colspan="4"></th>
        </thead>
        <thead class="fixedelement1"> 
            <th>Sr</th>            
            <th>Product Category</th>  
            <th>Category</th>
            <th>Brand</th>            
            <th>Model</th>
            <th>Current Stock</th>  
            <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
            <th>Quantity</th>
        </thead>
        <tbody class="data_1">

        <input type="hidden" name="idproductcategory" value="<?php echo $model_data[0]->idproductcategory; ?>" />
        <input type="hidden" name="idcategory" value="<?php echo $model_data[0]->idcategory; ?>" />
        <input type="hidden" name="idbrand" value="<?php echo $model_data[0]->idbrand; ?>" />            
        <input type="hidden" name="idbranch" value="<?php echo $model_data[0]->id_branch; ?>" />
        <?php $i = 1;
        foreach ($model_data as $model) { ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->product_category_name; ?></td>                                
                <td><?php echo $model->category_name; ?></td>
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->stock_qty; ?></td>
                <td><?php echo $model->sale_qty; ?></td>
                <td>
                    <input type="hidden" name="id_vatriant[]" value="<?php echo $model->id_variant; ?>" />
                    <input type="text" name="quantity[]" class="quantity form-control input-sm" value="<?php echo $model->quantity; ?>" />
                </td>

            </tr>

            <?php $i++;
        } ?>
        </tbody>


        <?php
    }
	
	 
    public function ajax_get_branch_stocknorms() {
        $days = $this->input->post('days');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $allbranches = $this->input->post('allbranches');
        $idzone = $this->input->post('idzone');
        $model_data = $this->Stock_model->get_branch_stocknorms($idbranch, $idpcat, $days, $allbranches, $idzone);
        ?>
        <table id="norms_data" class="table table-condensed table-full-width table-bordered table-hover text-center">
            <thead class="fixedelementtop ">
                <th class="text-center">Sr</th>            
                <th class="text-center">Zone</th>  
                <th class="text-center">Branch</th>  
                <th class="text-center">Branch Category</th>
                <th class="text-center">Stock</th>     
                <th class="text-center">Last <?php echo $days;?> days Sale</th>             
                <th class="text-center">Placement Norm</th>
                <th class="text-center">Gap</th>  
                <th class="text-center">Ach %</th>  
            </thead>
            <tbody class="data_1 text-center" >
                <?php $sr=1; $stk=0;$sale=0;$qty=0; $intra=0;$gap=0; $ach=0;$tot_stk=0;
                $bstk=0;$bsale=0;$bqty=0;$bgap=0; $bach=0;
                $tstk=0;$tsale=0;$tqty=0;$tgap=0;$tach=0;
                $old_idbranch = $model_data[0]->id_zone; 
                foreach ($model_data as $mdata){
                    if($mdata->stock_qty){ $stk = $mdata->stock_qty;}else{ $stk =0;}
                    if($mdata->intra_qty){ $intra = $mdata->intra_qty;}else{ $intra =0;}
                    if($mdata->sale_qty){ $sale = $mdata->sale_qty;}else{ $sale =0;}
                    if($mdata->norm_qty){ $qty = $mdata->norm_qty;}else{ $qty =0;}
                    $tot_stk = $stk + $intra;
                    $gap = $tot_stk - $qty;
                    if($mdata->norm_qty){ $ach = ($tot_stk / $qty)*100;}else{ $ach=0;}
                    
                    // *** Zone Wise Total Cal ****
                    if($old_idbranch == $mdata->id_zone){
                        $bstk = $bstk + $tot_stk;
                        $bsale = $bsale + $sale;
                        $bqty = $bqty + $qty;
                        $bgap = $bstk - $bqty;
                       if($bqty > 0){ $bach = ($bstk /$bqty )*100;} else{ $bach = 0;}
                    }else{?>
                        <tr style="background-color: #ffffcc">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $bstk; ?></b></td>
                            <td><b><?php echo $bsale; ?></b></td>
                            <td><b><?php echo $bqty; ?></b></td>
                            <td><b><?php echo $bgap; ?></b></td>
                            <td><b><?php echo round($bach,2).'%'; ?></b></td>
                        </tr>
                   <?php     $bstk=0;$bsale=0;$bqty=0;$bgap=0; $bach=0;
                        $bstk = $bstk + $tot_stk;
                        $bsale = $bsale + $sale;
                        $bqty = $bqty + $qty;
                        $bgap = $bstk - $bqty;
                       if($bqty > 0){ $bach = ($bstk /$bqty )*100;} else{ $bach = 0;} 
                    }
                    ?>
                  
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $mdata->zone_name; ?></td>
                        <td><?php echo $mdata->branch_name; ?></td>
                        <td><?php echo $mdata->branch_category_name; ?></td>
                        <td><?php echo $tot_stk; $tstk = $tstk + $tot_stk; ?></td>
                        <td><?php echo $sale; $tsale = $tsale + $sale; ?></td>
                        <td><?php echo $qty; $tqty = $tqty + $qty; ?></td>
                        <td><?php echo $gap; $tgap = $tgap + $gap; ?></td>
                        <td><?php echo round($ach,2).'%';  ?></td>
                        
                    </tr>
                <?php $old_idbranch = $mdata->id_zone;  } ?>
                <tr style="background-color: #ffffcc">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo $bstk; ?></b></td>
                    <td><b><?php echo $bsale; ?></b></td>
                    <td><b><?php echo $bqty; ?></b></td>
                    <td><b><?php echo $bgap; ?></b></td>
                    <td><b><?php echo round($bach,2).'%'; ?></b></td>
                </tr>
                <tr style="background-color: #ffffcc">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Overall Total</b></td>
                    <td><b><?php echo $tstk; ?></b></td>
                    <td><b><?php echo $tsale; ?></b></td>
                    <td><b><?php echo $tqty; ?></b></td>
                    <td><b><?php echo $tgap; ?></b></td>
                    <td><b><?php 
                     if($tqty > 0){ $tach = ($tstk /$tqty )*100;} else{ $tach = 0;}
                     echo round($tach,2).'%'; 
                    ?></b></td>
                </tr>
            </tbody>
        </table>
        <?php
    }


   /* public function ajax_get_branch_stocknorms() {
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $model_data = $this->Stock_model->get_branch_stocknorms($_POST['branch'], $q['product_category'], $_POST['days']);
        ?>
        <thead class="fixedelementtop">
        <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_name; ?></th>
        <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_category_name; ?></th>
        <th colspan="4"></th>
        </thead>
        <thead class="fixedelement1">
        <th>Sr</th>
        <th>Brand</th>            
        <th>Stock</th>     
        <th>Last <?php echo $_POST['days']; ?> days Sale</th>             
        <th>Stock Norm</th>
        <th>Completion Status</th>  

        </thead>
        <tbody class="data_1">


        <?php $i = 1;
        foreach ($model_data as $model) { ?>
                <tr>
                    <td><?php echo $i; ?></td>                 
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->stock_qty; ?></td>
                    <td><?php echo $model->sale_qty; ?></td>
                    <td><?php echo $model->norm_qty; ?></td>                
                    <td>
            <?php
            if ($model->all_models > 0) {
                $c = round((($model->setup_cnt / $model->all_models) * 100), 2);
                echo $c . '%';
            } else {
                echo '0%';
            }
            ?>
                    </td>

                </tr>

            <?php $i++;
        } ?>
        </tbody>


        <?php
    }*/

    public function ajax_export_branch_stock_norms() {
        
        $q['product_category'] = $this->General_model->get_product_category_by_user($this->session->userdata('id_users'));
        $model_data = $this->Stock_model->get_branch_modelstocknorms($q['product_category'], $_POST['idbranch'], $_POST['days']);
        ?>
        <thead>
        <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_name; ?></th>
        <th colspan="2" style="text-align: center;"><?php echo $model_data[0]->branch_category_name; ?></th>
        <th colspan="4"></th>
        </thead>
        <thead>
        <th>Sr</th>            
        <th>Product Category</th>  
        <th>Category</th>
        <th>Brand</th>            
        <th>Model</th>
        <th>Current Stock</th>  
        <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
        <th>Quantity</th>            
        </thead>
        <tbody class="data_1">
        <?php $i = 1;
        $bsum_stk=0;$bsum_plc=0;$bsum_sale=0;
        $old_name=$model_data[0]->brand_name;
        foreach ($model_data as $model) { 
            
            if($old_name==$model->brand_name){
                $bsum_stk=$bsum_stk+$model->stock_qty;
                $bsum_sale=$bsum_sale+$model->sale_qty;
                $bsum_plc=$bsum_plc+$model->quantity;
            }else{ ?>
                <tr>
                    <td></td>
                    <td></td>                         
                    <td></td>     
                    <td></td>     
                    <td><b>Total</b></td>            
                    <td class="textalign"><?php echo $bsum_stk; ?></td>                                    
                    <td class="textalign"><?php echo $bsum_sale; ?></td>
                    <td class="textalign"><?php echo $bsum_plc; ?></td>
                </tr>
            <?php   $bsum_sale=0;$bsum_stk=0;$bsum_plc=0;
                    $bsum_stk=$bsum_stk+$model->stock_qty;
                    $bsum_sale=$bsum_sale+$model->sale_qty;
                    $bsum_plc=$bsum_plc+$model->quantity;
                }
            ?>
            
                <tr>
                    <td><?php echo $i; ?></td> 
                    <td><?php echo $model->product_category_name; ?></td>                                
                    <td><?php echo $model->category_name; ?></td>
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->full_name; ?></td>
                    <td><?php echo $model->stock_qty; ?></td>
                    <td><?php echo $model->sale_qty; ?></td>  
                    <td><?php echo $model->quantity; ?></td>  
                </tr>
            <?php $i++; $old_name=$model->brand_name;
        } ?>
            <tr>
                <td></td>
                <td></td>     
                <td></td>     
                <td></td>                     
                <td><b>Total</b></td>            
                <td class="textalign"><?php echo $bsum_stk; ?></td>                                    
                <td class="textalign"><?php echo $bsum_sale; ?></td>
                <td class="textalign"><?php echo $bsum_plc; ?></td>
            </tr>
        </tbody>    
            <?php
        }
    public function ajax_quantity_stock() {        
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }       
//         die('<pre>'.print_r($idbranchs,1).'</pre>');
        $model_data = $this->Stock_model->get_quantity_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs);
        ?>        
        <thead class="fixedelementtop">
        <th>Sr</th>
        <th>Branch</th>
        <th>Godown</th>
        <th>Product Category</th> 
        <th>Brand</th>            
        <th>Model</th>
        <th>Stock</th>  
        <th>InTransit Stock</th>  
        <th>Total</th>
        </thead>
        <tbody class="data_1">
        <?php $i = 1;$qty=0;$qty_in=0;
        foreach ($model_data as $model) { ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->stock_qty; ?></td>
                <td><?php echo $model->intra_stock_qty; ?></td>
                <td><?php echo (($model->stock_qty)+($model->intra_stock_qty)); ?></td>     
                <?php 
                $qty=$qty+$model->stock_qty;
                $qty_in=$qty_in+$model->intra_stock_qty;
                ?>
            </tr>
            <?php $i++;
        } ?>
            <tr>
                <td colspan='6'>Total</td>                 
                <td><?php echo $qty; ?></td>
                <td><?php echo $qty_in; ?></td>
                <td><?php echo ($qty+$qty_in); ?></td>            
            </tr>
        </tbody>
        <?php
    }  
    
    public function ajax_imei_stock() {
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');         
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }
        $model_data = $this->Stock_model->get_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,1); /// for current stock
//        $model_data1 = $this->Stock_model->get_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,0); /// for in transit stock      
//        $data=array_merge($model_data,$model_data1);
        ?>        
        <thead class="fixedelementtop">
            <th>Sr</th>
            <th>Branch</th>
            <th>Godown</th>
            <th>Product Category</th> 
            <th> Category</th> 
            <th>Brand</th>            
            <th>Model</th>
            <th>Quantity</th> 
            <th>IMEI/SRNO</th> 
            <th>Days in stock</th> 
            <!--<th>isIntransit</th>-->
        </thead>
        <tbody class="data_1">
        <?php $i = 1;
        $qty=0;
        foreach ($model_data as $model) {
             if(!empty($model->transfer_time)){
                 $date1 = strtotime($model->transfer_time); 
            }else if(!empty($model->outward_time)){
                  $date1 = strtotime($model->outward_time);
            }else{
                $date1 =strtotime($model->date);
            }
            
                $date2 = strtotime(date('Y-m-d H:i:s'));
                $secs = $date2 - $date1;
                $days = $secs / 86400;
            if(($model->doa_return_type==3 && $model->idgodown==3 ) || $model->doa_status==2){ ?>
            <tr style="color:red">    
           <?php  }else{ ?>
            <tr>    
           <?php  } ?> 
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->qty; $qty=$qty+$model->qty?></td>
                 <td><?php if($model->doa_return_type==3  && $model->idgodown==3){ echo $model->imei_no.' - Force DOA'; }elseif($model->doa_status==2){ echo $model->imei_no.' - Sent to Vendor'; }else{ echo $model->imei_no; }  ?></td> 
                  <td><?php echo floor($days); ?></td>
                <!--<td><?php // echo (($model->temp_idbranch==null || $model->temp_idbranch==0)?'':'InTransit'); ?></td>-->                
            </tr>
            <?php $i++;
        } ?>
            <tr>
                <td colspan='7'>Total</td>                 
                <td><?php echo $qty; ?></td>
                <td></td>
                <td></td>                
            </tr>
        </tbody>
        <?php
    }  
    
    public function ajax_imei_stock_intransit() {
//        die(print_r($_POST));
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');         
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }
        $model_data = $this->Stock_model->get_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,0); /// /// for in transit stock      
//        $model_data1 = $this->Stock_model->get_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,1); /// for current  stock      
//        $data=array_merge($model_data,$model_data1);
        ?>        
        <thead class="fixedelementtop">
            <th>Sr</th>
            <th>Branch</th>
            <th>Godown</th>
            <th>Product Category</th> 
            <th>Brand</th>            
            <th>Model</th>
            <th>Quantity</th> 
            <th>IMEI/SRNO</th> 
              <th>Days in stock</th> 
            <!--<th>isIntransit</th>-->
        </thead>
        <tbody class="data_1">
        <?php $i = 1;
        $qty=0;
        foreach ($model_data as $model) { 
            if(!empty($model->transfer_time)){
                 $date1 = strtotime($model->transfer_time); 
            }else if(!empty($model->outward_time)){
                  $date1 = strtotime($model->outward_time);
            }else{
                $date1 =strtotime(date('Y-m-d H:i:s'));
            }
          
                $date2 = strtotime(date('Y-m-d H:i:s'));
                $secs = $date2 - $date1;
                $days = $secs / 86400; 
            ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->qty; $qty=$qty+$model->qty?></td>
                <td><?php echo $model->imei_no; ?></td>
                 <td><?php echo floor($days); ?></td>
                <!--<td><?php // echo (($model->temp_idbranch==null || $model->temp_idbranch==0)?'':'InTransit'); ?></td>-->                
            </tr>
            <?php $i++;
        } ?>
            <tr>
                <td colspan='6'>Total</td>                 
                <td><?php echo $qty; ?></td>
                <td></td>
                <td></td>                
            </tr>
        </tbody>
        <?php
    }  
    
      public function ajax_stock_analysis_report(){
        $user_id=$this->session->userdata('id_users');
        $days = $this->input->post('days'); 
        $idgodown = $this->input->post('idgodown'); 
        $idbrand = $this->input->post('brand'); 
        $warehouse = $this->input->post('warehouse'); 
        $idproductcategory = $this->input->post('idproductcategory');
        
//        $warehouse=$this->session->userdata('idbranch');             
        $variants = $this->Allocation_model->get_warehouse_stock_data_fr_analysis($idproductcategory,$idbrand,$idgodown,$warehouse);   
         $branch_data = $this->General_model->get_branches_by_warehouseid($warehouse);            
        $model_data=array();
        $counts= count($variants); 
        ?>
                
        <thead class="fixedelementtop" style="text-align: center;position: none !important;">
        <th colspan='3'></th>    
        <?php
        $idbranch=0; //All branch
        foreach ($variants as $variant){ 
            $stock_qty=$variant->ho_stock_qty;
		$model_data[] = $this->Allocation_model->get_variants_allocation_data($warehouse,$idbranch,$days,$variant->id_variant,$idgodown,'');
            ?>
                <th colspan='4' style="text-align: center;"><input type="hidden" name="variants[]" value="<?php echo $variant->id_variant; ?>" /><?php echo $variant->full_name;?>
                    <?php   $ho_stock_qty=$variant->ho_stock_qty;
                    $allocated_qty=$variant->allocated_qty;
                    $available=($ho_stock_qty-$allocated_qty);                
                ?>
                    <br><?php echo "Warehouse - ".(($ho_stock_qty==null)?0:$ho_stock_qty); ?>&nbsp;&nbsp;
                    <?php echo "Allocated - ".(($allocated_qty==null)?0:$allocated_qty); ?>&nbsp;&nbsp;
                    <?php echo "Available - ".(($available==null)?0:$available); ?>&nbsp;&nbsp;
                
                </th>
        <?php }         
        ?>
        </thead>
        <thead class="fixedelement2" style="text-align: center;position: none !important;">
        <th>Zone</Placementth><th>Branch Category</Placementth> <th>Branch</Placementth>   
        <?php
        foreach ($variants as $variant){ 
            $stock_qty=$variant->ho_stock_qty;
             $full_name = preg_replace('/\s+/', '', strtolower($variant->full_name)); 
            ?>
            <th>Placement Norm</th>
            <th>Stock</th>
            <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
           
            <th>Quantity </th>            
          
        <?php } ?>
        </tr></thead>   
        <tbody>
        <?php  
        $html="";
        $i=0;
        foreach ($branch_data as $branch){ ?>
            <tr>
                <td><?php echo $branch->zone_name; ?></td>                    
                <td><?php echo $branch->branch_category_name; ?></td>
                <td><?php echo $branch->branch_name; ?></td>
            <?php                            
                for($j=0;$j<$counts; $j++){
                   $data=$model_data[$j][$i];   
                   $full_name = preg_replace('/\s+/', '', strtolower($data->full_name));                                   
                   $stock=($data->stock_qty + $data->intra_stock_qty);                   
            ?>
                <td><?php echo $data->norm_qty; ?></td>                    
                <td><?php echo ($stock); ?></td>
                <td><?php echo $data->sale_qty; ?></td>
               
                <td></td>                                   
            <?php } ?>                
            </tr>                
       <?php $i++; } ?>
       </tbody>                     
        
<?php         
    }  
    public function ajax_check_valid_barcode_for_transfer() {         
        $user_id=$this->session->userdata('id_users');
        $imei = $this->input->post('val');
        $idvariant = $this->input->post('idvariant');
        $branch = $this->input->post('idbranch');
        $idgodown = $this->input->post('idgodown');        
        if($this->Stock_model->ajax_check_valid_barcode($imei, $idvariant, $branch,$idgodown)){
            $output = json_encode(array("error" => false, "data" => "Success", "message" => ""));
            
        }else{
            $output = json_encode(array("error" => true, "data" => "Fail", "message" => ""));
        }        
        die($output);
    }
    public function ajax_check_valid_barcode() {         
        $user_id=$this->session->userdata('id_users');
        $imei = $this->input->post('val');
        $idvariant = $this->input->post('idvariant');
        $branch = $this->input->post('idbranch');
        $idgodown = $this->input->post('idgodown');
        
        if($idgodown == 6){
            $idgodown = 1;
        }
        
        if($this->Stock_model->ajax_check_valid_barcode($imei, $idvariant, $branch,$idgodown)){
            $output = json_encode(array("error" => false, "data" => "Success", "message" => ""));
            
        }else{
            $output = json_encode(array("error" => true, "data" => "Fail", "message" => ""));
        }        
        die($output);
    }
    
    public function ajax_get_branch_stock_by_variant() { 
        $idvariant = $this->input->post('variant');
        $idgodown = $this->input->post('idgodown');
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->session->userdata('idbranch');  
        $level=$this->session->userdata('level');   
        $idwarehouse=0; 
        if($role_type==2){
            $idwarehouse=$this->General_model->get_branch_byid($idbranch)->idwarehouse;    
        }elseif($role_type==0 && $level==3){
            $idwarehouse=-1;
        }
        
        $stock_data=$this->Stock_model->get_branch_stock_by_variant($idvariant, $idgodown,$idwarehouse, $level); 
       
            $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
            $sku_column=$sku_data->column_name;
            $ingram_sku= $this->Ingram_Model->ajax_get_ingram_sku_by_model_variant($idvariant,$sku_column);
            
            $ingram_html="";
            if($ingram_sku->ingram==NULL || $ingram_sku->ingram=="" ){}else{
                $apob= $this->Ingram_Model->get_apob_branch_stock_by_variant($idvariant,$idgodown, INGRAM_IDWAREHOUSE);
                 $ingram_html.='<tr>
                        <td></td>
                        <td>'.$apob[0]->zone_name.'</td>                                    
                        <td>'.$apob[0]->branch_name.'</td>            
                        <td class="textalign">'.$apob[0]->stock_qty.'</td>                                    
                        <td class="textalign"></td>
                        </tr>';
            }
            
            ?>
                        
       
       <thead class="textalign" class="fixedelement" style="text-align: center;position: none !important;">
       <th class="textalign" colspan=5""><?php echo $stock_data[0]->full_name; ?></th>
       </thead>
        <thead class="fixedelement" style="text-align: center;position: none !important;">
        <th>SrNo</th><th>Zone</th><th>Branch</th><th>Current Stock</th> <th>In-Transit Stock</th>  
        </thead>   
        <tbody class="data_1">
           <?php  
        $html="";
        $i=1;
        $s_total=0;$in_total=0;
        $zsum_in=0;$zsum_cu=0;
        $old_name=$stock_data[0]->zone_name;
        foreach ($stock_data as $stock){ 
            $s_total=$s_total+$stock->stock_qty;
            $in_total=$in_total+$stock->intra_stock_qty;
            if($old_name==$stock->zone_name){
                $zsum_cu=$zsum_cu+$stock->stock_qty;
                $zsum_in=$zsum_in+$stock->intra_stock_qty;
            }else{ ?>
                <tr>
                    <td></td>
                    <td></td>                                    
                    <td><b>Total</b></td>            
                    <td class="textalign"><?php echo $zsum_cu; ?></td>                                    
                    <td class="textalign"><?php echo $zsum_in; ?></td>
                </tr>
            <?php   $zsum_in=0;$zsum_cu=0;
                    $zsum_cu=$zsum_cu+$stock->stock_qty;
                    $zsum_in=$zsum_in+$stock->intra_stock_qty;
                }
            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $stock->zone_name; ?></td>                                    
                <td><?php echo $stock->branch_name; ?></td>            
                <td class="textalign"><?php echo $stock->stock_qty; ?></td>                                    
                <td class="textalign"><?php echo $stock->intra_stock_qty; ?></td>
            </tr>                
       <?php    $i++;
                $old_name=$stock->zone_name;
            } ?>
            <tr>
                    <td></td>
                    <td></td>                                    
                    <td><b>Total</b></td>            
                    <td class="textalign"><?php echo $zsum_cu; ?></td>                                    
                    <td class="textalign"><?php echo $zsum_in; ?></td>
                </tr>
            <tr>
                <td colspan="3"><b>Total</b></td>
                <td class="textalign"><?php echo $s_total; ?></td>            
                <td class="textalign"><?php echo $in_total; ?></td>  
            </tr>
            <?php echo $ingram_html ?>
       </tbody>  
    <?php     
    }

    public function store_stock_analysis_report() {     
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idbranch = $this->session->userdata('idbranch');
        if($role_type==1){
            if($level==1){        
                $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_data();
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();  // all branches for temp
//                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
            }
        }else{
            if($level==1){
                $q['branch_data'] = $this->General_model->get_active_branch_data();
                $q['product_category'] = $this->General_model->get_product_category_data();
            }elseif($level==2){    
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
            }elseif($level==3){            
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
            }
        }                
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();        
        }
        $q['type']='store';
        $this->load->view('stock/store_stock_analysis', $q);
    }
    public function w_stock_analysis_report() {     
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';        
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idbranch = $this->session->userdata('idbranch');
        if($role_type==1){
            if($level==1){        
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_data();
            }else{
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                  
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
            }
        }elseif($role_type==0){            
                $q['branch_data'] = $this->General_model->get_warehouse_data();
                $q['product_category'] = $this->General_model->get_product_category_data();            
        }                
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();        
        } 
        $this->load->view('stock/w_stock_analysis', $q);
    }
    
    public function ajax_store_stock_analysis_report(){
        $user_id=$this->session->userdata('id_users'); 
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');        
//        $days = $this->input->post('days');
        $idgodown = $this->input->post('idgodown');
        $idbrand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
                 
        $earlier = new DateTime($datefrom);
        $later = new DateTime($dateto);        
        $days = $later->diff($earlier)->format("%a")+1;      
        $idproductcategory = $this->input->post('idproductcategory');          
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$user_id);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }        
        $stock_data = $this->Stock_model->get_store_stock_analysis($idgodown,$idbranch,$idbranchs,$days,$idbrand,$idproductcategory,'store',$datefrom,$dateto);
         
        if($idbranch!=0)
        {
        ?>
            <thead class="fixedelement" style="text-align: center;position: none !important;">
            <th colspan="3" class="textalign"> Branch Name - <?php echo $stock_data[0]->branch_name ?> </th>
            <th colspan="3" class="textalign"> Zone Name - <?php echo $stock_data[0]->zone_name ?> </th>
            <th colspan="4" class="textalign"> Branch Category - <?php echo $stock_data[0]->branch_category_name ?> </th>
            </thead>
        <?php } ?>
        <thead class="fixedelement" style="text-align: center;position: none !important;">
        <?php if($idbranch==0){ ?>
                <th>Zone</th>
                <th>Branch Category</th>
                <th>Branch</th>
        <?php } ?>
                <th>Product Category</th> 
                <th>Brand Name</th>
                <th>Model Name</th>                           
                <th>Branch Stock</th>
                <th>inTransit Stock</th>
                <th>Total Stock</th>
                <th><?php echo 'Last '.$days.' Days Sale'?></th>
                <th>Enough for days</th>
                <th>Order Prediction</th>
            </thead>
            <tbody>
                <?php $i=0; foreach ($stock_data as $data){
                    $days_for_1_qty = 0;
                    $ned=0;
                    if ($data->sale_qty > 0) {
                        $days_for_1_qty = ($days / $data->sale_qty);
                    }
                    $total=($data->stock_qty+$data->intra_stock_qty);
                    $enough_for_days = round($total * $days_for_1_qty);
                    if ($days_for_1_qty <= 0) {
                        $ned = 0;
                    } else {
                        $ned = ($days / $days_for_1_qty);
                    }
                    $purchase_pre = round($ned - $total);
                    ?>
                <tr>
                <?php if($idbranch==0){ ?>
                    <th><?php echo $data->zone_name ?></th>
                    <th><?php echo $data->branch_category_name ?></th>
                    <th><?php echo $data->branch_name ?></th>
                <?php } ?>
                    <td><?php echo $data->product_category_name ?></td>
                    <td><?php echo $data->brand_name?></td>
                    <td><?php echo $data->full_name?></td>                  
                    <td><?php echo $data->stock_qty ?></td>
                    <td><?php echo $data->intra_stock_qty ?></td>
                    <td><?php echo $total ?></td>
                    <td><?php echo $data->sale_qty ?></td>
                    <td><?php echo $enough_for_days ?></td>
                    <td><?php echo $purchase_pre ?></td>
                                   
                </tr>
                <?php $i++; } ?>
            </tbody>         
<?php         
    }  
    
    public function ajax_w_stock_analysis_report(){
        $user_id=$this->session->userdata('id_users'); 
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');        
        $days = $this->input->post('days');
        $idgodown = $this->input->post('idgodown');
        $idbrand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        
        $idproductcategory = $this->input->post('idproductcategory');          
        $idbranchs=array();        
           $branchs=$this->General_model->get_branches_by_warehouseid($idbranch);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }                
        $stock_data = $this->Stock_model->get_store_stock_analysis($idgodown,0,$idbranchs,$days,$idbrand,$idproductcategory,'warehouse','','');
        $stock_w_data = $this->Stock_model->get_warehouse_stock_by_PBG($idproductcategory,$idbrand,$idgodown,$idbranch);
        ?>
        <thead class="fixedelement" style="text-align: center;position: none !important;">
                <th>Product Category</th> 
                <th>Brand Name</th>
                <th>Model Name</th>                 
                <th>Warehouse Stock</th>                
                <th>Branch Stock</th>
                <th>inTransit Stock</th>
                <th>Total Stock</th>
                <th><?php echo 'Last '.$days.' Days Sale'?></th>
                <th>Enough for days</th>
                <th>Order Prediction</th>
            </thead>
            <tbody>
                <?php $i=0; 
				
				$t_ho_stock_qty=0;
				$t_stock_qty=0;
				$t_intra_stock_qty=0;
				$t_total=0;
				$t_sale_qty=0;
				
				foreach ($stock_data as $data){
                    $days_for_1_qty = 0;
                    $ned=0;
                    if ($data->sale_qty > 0) {
                        $days_for_1_qty = ($days / $data->sale_qty);
                    }
                    $total=($data->stock_qty+$data->intra_stock_qty+$stock_w_data[$i]->ho_stock_qty);
                    $enough_for_days = round($total * $days_for_1_qty);
                    if ($days_for_1_qty <= 0) {
                        $ned = 0;
                    } else {
                        $ned = ($days / $days_for_1_qty);
                    }
                    $purchase_pre = round($ned - $total);
                    ?>
                <tr>
                    <td><?php echo $data->product_category_name ?></td>
                    <td><?php echo $data->brand_name?></td>
                    <td><?php echo $data->full_name?></td>                    
                    <td><?php echo $stock_w_data[$i]->ho_stock_qty ?></td>                    
                    <td><?php echo $data->stock_qty ?></td>
                    <td><?php echo $data->intra_stock_qty ?></td>
                    <td><?php echo $total ?></td>
                    <td><?php echo $data->sale_qty ?></td>
                    <td><?php echo $enough_for_days ?></td>
                    <td><?php echo $purchase_pre ?></td>
                                   
                </tr>
                <?php 
				$t_ho_stock_qty=$t_ho_stock_qty+$stock_w_data[$i]->ho_stock_qty;
				$t_stock_qty=$t_stock_qty+$data->stock_qty;
				$t_intra_stock_qty=$t_intra_stock_qty+$data->intra_stock_qty;
				$t_total=$t_total+$total;
				$t_sale_qty=$t_sale_qty+$data->sale_qty;
				
				$i++;

				}
					$days_for_1_qty = 0;
                    $ned=0;
                    if ($t_sale_qty > 0) {
                        $days_for_1_qty = ($days / $t_sale_qty);
                    }
					$enough_for_dayss = round($t_total * $days_for_1_qty);
                    if ($days_for_1_qty <= 0) {
                        $ned = 0;
                    } else {
                        $ned = ($days / $days_for_1_qty);
                    }
                    $purchase_pree = round($ned - $t_total);

				?>
				<thead>
                    <td></td>
                    <td></td>
                    <td> Total </td>                    
                    <td><?php echo $t_ho_stock_qty; ?></td>                    
                    <td><?php echo $t_stock_qty; ?></td>
                    <td><?php echo $t_intra_stock_qty; ?></td>
                    <td><?php echo $t_total; ?></td>
                    <td><?php echo $t_sale_qty; ?></td>
                    <td><?php echo $enough_for_dayss ?></td>
                    <td><?php echo $purchase_pree ?></td>
                                   
                </thead>
				
            </tbody>         
<?php         
    }  
    public function stock_vs_sale_analysis_report() {             
        $q['tab_active'] = '';
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $q['product_category'] = $this->General_model->get_product_category_data();                
        $q['active_godown'] = $this->General_model->get_billing_godown();  
        $this->load->view('stock/stock_vs_sale_analysis_report', $q);
    }
    public function ajax_sale_stock_analysis_report(){                    
//        $days = $this->input->post('days');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idgodown = $this->input->post('idgodown');
        $idbrand = $this->input->post('brand');
        $idproductcategory = $this->input->post('idproductcategory');    
        $idcategory = $this->input->post('category');          
        $earlier = new DateTime($datefrom);
        $later = new DateTime($dateto);        
        $days = $later->diff($earlier)->format("%a")+1;        
        $stock_data = $this->Stock_model->get_sale_stock_analysis($idgodown,$datefrom,$dateto,$idbrand,$idproductcategory,$idcategory);
        $warehouse_data = $this->General_model->get_active_warehouse_data();
        $warehouse_count= count($warehouse_data);
            $ware=array();?>
            <thead class="fixtop" style="text-align: center;position: none !important;background-color: #84b8f7">            
                    <th>Product Category</th> 
                    <th>Category Name</th> 
                    <th>Brand Name</th>
                    <th>Model Name</th>  
                    <th>Manager Price Per/Qty</th>
                    <?php $i=0; foreach ($warehouse_data as $warehouse) { 
                         $ware[$i]=0; ?>
                        <th><?php echo $warehouse->branch_name; ?></th>                          
                   <?php $i++; } ?>
                    <th>Branch Stock</th>
                    <th>Branch inTransit Stock</th>
                    <th>Total Stock</th>
                    <th><?php echo 'Last '.$days.' Days Sale'?></th>
                    <th>Enough for days</th>
                    <th>Order Prediction</th>
                </thead>
                <tbody>
                    <?php
                    $i=0;
                    $total_stock=0;$total_instock=0;$total_instk=0;$total_sale=0;
                     foreach ($stock_data as $data){
                        
                        $days_for_1_qty = 0;
                        $ned=0;
                        if ($data->sale_qty > 0) {
                            $days_for_1_qty = ($days / $data->sale_qty);
                        }
                        $wqty=0;
                        for($j=0;$j<$warehouse_count;$j++){
                            $a='warehouse'.$j;
                            $wqty=$wqty+$data->$a;
                             $ware[$j] +=$data->$a;
                        }
                        $total_stock +=$data->stock_qty;
                        $total_instock +=$data->intra_stock_qty;                        
                        $total=($data->stock_qty+$data->intra_stock_qty+$wqty);
                        $total_instk +=$total;
                        $total_sale +=$data->sale_qty;
                        $enough_for_days = round($total * $days_for_1_qty);
                        if ($days_for_1_qty <= 0) {
                            $ned = 0;
                        } else {
                            $ned = ($days / $days_for_1_qty);
                        }
                        $purchase_pre = round($ned - $total);
                        ?>
                    <tr>
                    
                        <td><?php echo $data->product_category_name ?></td>
                        <td><?php echo $data->category_name ?></td>
                        <td><?php echo $data->brand_name ?></td>
                        <td><?php echo $data->full_name ?></td>                  
                        <td><?php echo $data->landing ?></td>                  
                         <?php for($m=0;$m<$warehouse_count;$m++){   $a='warehouse'.$m; ?>
                        <th><?php echo $data->$a; ?></th>                          
                        <?php  } ?>
                        <td><?php echo $data->stock_qty ?></td>
                        <td><?php echo $data->intra_stock_qty ?></td>
                        <td><?php echo $total ?></td>
                        <td><?php echo $data->sale_qty ?></td>
                        <td><?php echo $enough_for_days ?></td>
                        <td><?php echo $purchase_pre ?></td>

                    </tr>
                    <?php $i++; } ?>
                    <tr style="text-align: center;">                    
                        <td colspan="3"></td>
                        <td>Total</td>
                         <?php for($m=0;$m<$warehouse_count;$m++){ ?>
                        <th><?php echo $ware[$m]; ?></th>                          
                        <?php  } ?>
                        <td><?php echo $total_stock ?></td>
                        <td><?php echo $total_instock ?></td>
                        <td><?php echo $total_instk ?></td>
                        <td><?php echo $total_sale ?></td>
                        <td colspan="2"></td>
                    </tr>
                    
                </tbody>         
    <?php         
        }    
    
    public function stock_summary_report() {             
        $q['tab_active'] = '';
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $q['product_category'] = $this->General_model->get_product_category_data();        
        $this->load->view('stock/stock_summary', $q);
    }
    public function ajax_stock_summary(){
        $user_id=$this->session->userdata('id_users');
        $days = $this->input->post('days'); 
        $idgodown = $this->input->post('idgodown'); 
        $idbrand = $this->input->post('brand'); 
        $reporttype = $this->input->post('reporttype'); 
        $idproductcategory = $this->input->post('idproductcategory');
        
        $stock_data = $this->Stock_model->get_store_stock_analysis($idgodown,$idbranch,$days,$idbrand,$idproductcategory,'','','');        
        ?>
       <thead class="fixedelement" style="text-align: center;position: none !important;">   
            <th colspan="3" class="textalign"> Branch Name - <?php echo $stock_data[0]->branch_name ?> </th>       
            <th colspan="3" class="textalign"> Zone Name - <?php echo $stock_data[0]->zone_name ?> </th>                 
            <th colspan="3" class="textalign"> Branch Category - <?php echo $stock_data[0]->branch_category_name ?> </th>                 
        </thead>
       <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Product Category    </th> 
                <th>Brand Name</th>
                <th>Model Name</th>
                <th>Stock</th>
                <th>inTransit Stock</th>
                <th>Total Stock</th>
                <th><?php echo 'Last '.$days.' Days Sale'?></th>
                <th>Enough for days</th>
                <th>Order Prediction</th>               
            </thead>
            <tbody>
                <?php foreach ($stock_data as $data){ 
                    $days_for_1_qty = 0;
                    $ned=0;
                    if ($data->sale_qty > 0) {
                        $days_for_1_qty = ($days / $data->sale_qty);
                    }
                    $total=($data->stock_qty+$data->intra_stock_qty);
                    $enough_for_days = round($total * $days_for_1_qty);
                    if ($days_for_1_qty <= 0) {
                        $ned = 0;
                    } else {
                        $ned = ($days / $days_for_1_qty);
                    }
                    $purchase_pre = round($ned - $total);                    
                    ?>
                <tr>                    
                    <td><?php echo $data->product_category_name ?></td>     
                    <td><?php echo $data->brand_name?></td>
                    <td><?php echo $data->full_name?></td>
                    <td><?php echo $data->stock_qty ?></td>
                    <td><?php echo $data->intra_stock_qty ?></td>
                    <td><?php echo $total ?></td>
                    <td><?php echo $data->sale_qty ?></td>
                    <td><?php echo $enough_for_days ?></td>
                    <td><?php echo $purchase_pre ?></td>
                </tr>
                <?php } ?>
            </tbody>         
    <?php }
    
    //======================================== OPENING STOCK =========================================
    public function opening_stock(){
        $q['tab_active'] = '';                        
        $q['branch_data'] = $this->General_model->get_allbranch_data();               
        $q['godown_data'] = $this->General_model->get_godown_data();            
        $this->load->view('stock/opening_stock',$q);
    }
    public function opening_stock_report(){
        $q['tab_active'] = '';                        
        $q['branch_data'] = $this->General_model->get_allbranch_data();               
        $q['godown_data'] = $this->General_model->get_godown_data();            
        $this->load->view('stock/opening_stock_report',$q);
    }
    
//    public function upload_opening_stock_excel(){
//        $this->db->trans_begin();
//        $idgodown = $this->input->post('idgodown');
//        $idbranch = $this->input->post('idbranch');
//        $datetime = date('Y-m-d h:i:s');
//        $i =0;
//        $filename=$_FILES["uploadfile"]["tmp_name"];
//        if($_FILES["uploadfile"]["size"] > 0){
//            $file = fopen($filename, "r");
//            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
//                if($i > 0){ 
//                    $data[] = array(
//                        'product_id' => $openingdata[0],
//                        'name' => $openingdata[1],
//                        'imei' => $openingdata[2],
//                        'idgodown' => $idgodown,
//                        'idbranch' => $idbranch,
//                        'datetime' => $datetime,
//                        'uploaded_by' => $_SESSION['id_users'],
//                    );
//                }
//                $i++;
//            }
////            die(print_r($data));
//            $this->General_model->save_opening_stock_test_data($data);
//            fclose($file);
//            $temp_opening_data = $this->General_model->get_opening_stock_test_data($idgodown, $idbranch, $datetime, $_SESSION['id_users']);
//            
//            if(count($temp_opening_data) > 0){
//                $opening = array(
//                    'idbranch' => $idbranch,
//                    'idgodown' => $idgodown,
//                    'uploaded_by' => $_SESSION['id_users'],
//                    'datetime' => $datetime,
//                    'entry_date' => date('Y-m-d'),
//                );
//                $id_openingdata = $this->General_model->save_opening_data($opening);
//                if($id_openingdata){
//                    for($j=0;$j < count($temp_opening_data); $j++){
//                        $opening_data[] = array(
//                            'date' => date('Y-m-d'),
//                            'idopening' => $id_openingdata,
//                            'imei_no' => $temp_opening_data[$j]->imei,
//                            'idskutype' => $temp_opening_data[$j]->idsku_type,
//                            'idgodown' => $idgodown,
//                            'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
//                            'idcategory' => $temp_opening_data[$j]->idcategory,
//                            'idvariant' => $temp_opening_data[$j]->id_variant,
//                            'idmodel' => $temp_opening_data[$j]->idmodel,
//                            'idbrand' => $temp_opening_data[$j]->idbrand,
//                            'product_name' => $temp_opening_data[$j]->name,
//                            'idbranch' => $idbranch,
//                            'qty' => 1,
//                            'created_by' => $_SESSION['id_users'],
//                        );
//                        $stock[] = array(
//                            'date' => date('Y-m-d'),
//                            'imei_no' => $temp_opening_data[$j]->imei,
//                            'idskutype' => $temp_opening_data[$j]->idsku_type,
//                            'idgodown' => $idgodown,
//                            'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
//                            'idcategory' => $temp_opening_data[$j]->idcategory,
//                            'idvariant' => $temp_opening_data[$j]->id_variant,
//                            'idmodel' => $temp_opening_data[$j]->idmodel,
//                            'idbrand' => $temp_opening_data[$j]->idbrand,
//                            'product_name' => $temp_opening_data[$j]->full_name,
//                            'idbranch' => $idbranch,
//                            'qty' => 1,
//                            'created_by' => $_SESSION['id_users'],
//                        );
//                        $imei_histroy[] = array(
//                            'imei_no' => $temp_opening_data[$j]->imei,
//                            'entry_type' => 'Opening Stock',
//                            'entry_time' => $datetime,
//                            'date' => date('Y-m-d'),
//                            'idbranch' => $idbranch,
//                            'idgodown' => $idgodown,
//                            'idvariant' => $temp_opening_data[$j]->id_variant,
//                            'model_variant_full_name' => $temp_opening_data[$j]->full_name,
//                            'idimei_details_link' => 2,
//                            'idlink' => $id_openingdata,
//                        );
//                    }
//                    $this->General_model->save_opening_stock_data($opening_data);
//                    $this->General_model->save_stock_data($stock);
//                    $this->General_model->save_batch_imei_history($imei_histroy);
//                    $this->General_model->delete_opening_stock_test_data($idgodown, $idbranch, $datetime, $_SESSION['id_users']);
//                }
//            }
//        }
//        if ($this->db->trans_status() === FALSE){
//            $this->db->trans_rollback();
//            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
//        }else{
//            $this->db->trans_commit();
//            $this->session->set_flashdata('save_data', 'Opening Stock Uploaded Successfully');
//        }
//        redirect('Stock/opening_stock');
//    }
     public function upload_opening_stock_excel(){
         $q['tab_active'] = '';         
        $this->db->trans_begin();
//        $idgodown = $this->input->post('idgodown');
        $idbranch = $this->input->post('idbranch');
        $datetime = date('Y-m-d h:i:s');
         $dd = date('Y-md');
        $i =0;$imei='';
        $filename=$_FILES["uploadfile"]["tmp_name"];
        if($_FILES["uploadfile"]["size"] > 0){
            $file = fopen($filename, "r");
            
            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){ 
                    $imei1 = trim($openingdata[4],"'");
                    $imei2 = trim($openingdata[5],"'");
                    $imei3 = trim($openingdata[6],"'");
                    if($imei1 == '' && $imei2 == '' || ($imei1 == NULL && $imei2 == NULL)){
                        $imei = $imei3;
                    }
                    if($imei2 == '' && $imei3 == '' || ($imei2 == NULL && $imei3 == NULL)){
                        $imei = $imei1;
                    }
                    if($imei1 == '' && $imei3 == '' || ($imei1 == NULL && $imei3 == NULL)){
                        $imei = $imei2;
                    }
                        
                        $data[] = array(
                            'name' => $openingdata[1],
                            'godown_name' => $openingdata[3],
                            'imei' => $imei,
                            'idbranch' => $idbranch,
                            'datetime' => $datetime,
                            'uploaded_by' => $_SESSION['id_users'],
                        );
                }
                $i++;
            }
            
            $this->General_model->save_opening_stock_test_data($data);
            fclose($file);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
             //redirect('Stock/upload_excel_opening_data/'.$idbranch.'/'.$dd);
             redirect('Stock/opening_stock');
        }
        
    }
    public function upload_excel_opening_data($idbranch, $dd){
        $godown = "ALL";
        $datetime = date('Y-m-d h:i:s');
        $temp_opening_data = $this->General_model->get_remaining_opening_stock_data($idbranch, $godown, $dd);
//        die('<pre>'.print_r($temp_opening_data,1).'</pre>');
        if(count($temp_opening_data) > 0){
            $opening = array(
                'idbranch' => $idbranch,
                'uploaded_by' => $_SESSION['id_users'],
                'datetime' => $datetime,
                'entry_date' => date('Y-m-d'),
            );
            $id_openingdata = $this->General_model->save_opening_data($opening);
            if($id_openingdata){
                for($j=0; $j<count($temp_opening_data); $j++){
                    if($temp_opening_data[$j]->godown_name == 'DEMO'){
                        $idgodown = 2;
                    }
                    if($temp_opening_data[$j]->godown_name == 'NEW'){
                        $idgodown = 1;
                    }
                    if($temp_opening_data[$j]->godown_name == 'DOA'){
                        $idgodown = 3;
                    }
                    if($temp_opening_data[$j]->godown_name == 'SERVICE'){
                        $idgodown = 4;
                    }

                    $opening_data[] = array(
                        'date' => date('Y-m-d'),
                        'idopening' => $id_openingdata,
                        'imei_no' => $temp_opening_data[$j]->imei,
                        'idskutype' => $temp_opening_data[$j]->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                        'idcategory' => $temp_opening_data[$j]->idcategory,
                        'idvariant' => $temp_opening_data[$j]->id_variant,
                        'idmodel' => $temp_opening_data[$j]->idmodel,
                        'idbrand' => $temp_opening_data[$j]->idbrand,
                        'product_name' => $temp_opening_data[$j]->name,
                        'idbranch' => $idbranch,
                        'qty' => 1,
                        'created_by' => $_SESSION['id_users'],
                    );
                    $stock[] = array(
                        'date' => date('Y-m-d'),
                        'imei_no' => $temp_opening_data[$j]->imei,
                        'idskutype' => $temp_opening_data[$j]->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                        'idcategory' => $temp_opening_data[$j]->idcategory,
                        'idvariant' => $temp_opening_data[$j]->id_variant,
                        'idmodel' => $temp_opening_data[$j]->idmodel,
                        'idbrand' => $temp_opening_data[$j]->idbrand,
                        'product_name' => $temp_opening_data[$j]->full_name,
                        'idbranch' => $idbranch,
                        'qty' => 1,
                        'created_by' => $_SESSION['id_users'],
                    );
                    $imei_histroy[] = array(
                        'imei_no' => $temp_opening_data[$j]->imei,
                        'entry_type' => 'Opening Stock',
                        'entry_time' => $datetime,
                        'date' => date('Y-m-d'),
                        'idbranch' => $idbranch,
                        'idgodown' => $idgodown,
                        'idvariant' => $temp_opening_data[$j]->id_variant,
                        'model_variant_full_name' => $temp_opening_data[$j]->full_name,
                        'idimei_details_link' => 2,
                        'iduser' => $_SESSION['id_users'],
                        'idlink' => $id_openingdata,
                    );
                    $delete_uploaded_opening_stock_test[] = $temp_opening_data[$j]->id_opening_stock_test;
                }
                $this->General_model->save_opening_stock_data($opening_data);
                $this->General_model->save_stock_data($stock);
                $this->General_model->save_batch_imei_history($imei_histroy);
                $this->General_model->delete_upl_opening_stock_test($delete_uploaded_opening_stock_test);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Opening Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $remain_upload = $this->General_model->ajax_get_remain_opening($idbranch, $godown);
            if(count($remain_upload) > 0){
                 $q['tab_active'] = '';    
                $q['remain_upload'] = $remain_upload;
                $this->load->view('stock/remaining_opening_stock_test',$q);
            }else{
                $this->session->set_flashdata('save_data', 'Opening Stock Uploaded Successfully');
                redirect('Stock/opening_stock');
            }

        }
    }
    public function remaining_opening_stock(){
        $q['tab_active'] = '';                        
        $q['branch_data'] = $this->General_model->get_allbranch_data();               
        $q['godown_data'] = $this->General_model->get_godown_data();   
        $this->load->view('stock/remaining_opening_stock_report',$q);
    }
    
    public function ajax_get_remaining_opening_stock_test_data(){
        $idbranch = $this->input->post('idbranch');
        $idgodown = $this->input->post('idgodown');
        
        $opening_data = $this->General_model->ajax_get_remain_opening($idbranch, $idgodown);
//        die('<pre>'.print_r($opening_data,1).'</pre>');
        if($opening_data){
        ?>
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #ffcccc">
                    <th>Sr.</th>
                    <th>Product Name</th>
                    <th>Imei</th>
                    <th>Qty</th>
                    <!--<th>Branch</th>-->
                    <th>Godown</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    <?php $i =1;  foreach ($opening_data as $ope){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $ope->name; ?></td>
                        <td><?php echo $ope->imei; ?></td>
                        <td><?php echo $ope->qty; ?></td>
                        <!--<td><?php echo $ope->branch_name; ?></td>-->
                        <td><?php echo $ope->godown_name; ?></td>
                        <td><?php echo date('Y-m-d h:i:sa', strtotime($ope->datetime)); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <form>
                <input type="hidden" name="idbranch" value="<?php echo $idbranch ?>">
                <input type="hidden" name="idgodown" value="<?php echo $idgodown ?>">
                <button class="btn btn-primary btnsubmit" formmethod="POST" formaction="<?php echo base_url()?>Stock/upload_remaining_opening_stock" style="position: fixed;top: 580px;right: 20px;">Upload Opening</button>
            </form>
            <script>
                $(document).ready(function (){
                   $('.btnsubmit').click(function (){
                      if(!confirm("Do You Want To Upload Opening Stock?")){
                          return false;
                      } 
                   }); 
                });
            </script>
        <?php 
        }
        
    }
    
    public function upload_remaining_opening_stock(){
        $idbranch = $this->input->post('idbranch');
        $godown = $this->input->post('idgodown');
        $datetime = date('Y-m-d h:i:s');
        $temp_opening_data = $this->General_model->get_remaining_opening_stock($idbranch, $godown);
//        die('<pre>'.print_r($temp_opening_data,1).'</pre>');
        if(count($temp_opening_data) > 0){
                $opening = array(
                    'idbranch' => $idbranch,
                    'uploaded_by' => $_SESSION['id_users'],
                    'datetime' => $datetime,
                    'entry_date' => date('Y-m-d'),
                );
                $id_openingdata = $this->General_model->save_opening_data($opening);
                if($id_openingdata){
                    for($j=0; $j<count($temp_opening_data); $j++){
                        if($temp_opening_data[$j]->godown_name == 'DEMO'){
                            $idgodown = 2;
                        }
                        if($temp_opening_data[$j]->godown_name == 'NEW'){
                            $idgodown = 1;
                        }
                        if($temp_opening_data[$j]->godown_name == 'DOA'){
                            $idgodown = 3;
                        }
                        if($temp_opening_data[$j]->godown_name == 'SERVICE'){
                            $idgodown = 4;
                        }
                        
                        $opening_data[] = array(
                            'date' => date('Y-m-d'),
                            'idopening' => $id_openingdata,
                            'imei_no' => $temp_opening_data[$j]->imei,
                            'idskutype' => $temp_opening_data[$j]->idsku_type,
                            'idgodown' => $idgodown,
                            'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                            'idcategory' => $temp_opening_data[$j]->idcategory,
                            'idvariant' => $temp_opening_data[$j]->id_variant,
                            'idmodel' => $temp_opening_data[$j]->idmodel,
                            'idbrand' => $temp_opening_data[$j]->idbrand,
                            'product_name' => $temp_opening_data[$j]->name,
                            'idbranch' => $idbranch,
                            'qty' => 1,
                            'created_by' => $_SESSION['id_users'],
                        );
                        $stock[] = array(
                            'date' => date('Y-m-d'),
                            'imei_no' => $temp_opening_data[$j]->imei,
                            'idskutype' => $temp_opening_data[$j]->idsku_type,
                            'idgodown' => $idgodown,
                            'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                            'idcategory' => $temp_opening_data[$j]->idcategory,
                            'idvariant' => $temp_opening_data[$j]->id_variant,
                            'idmodel' => $temp_opening_data[$j]->idmodel,
                            'idbrand' => $temp_opening_data[$j]->idbrand,
                            'product_name' => $temp_opening_data[$j]->full_name,
                            'idbranch' => $idbranch,
                            'qty' => 1,
                            'created_by' => $_SESSION['id_users'],
                        );
                        $imei_histroy[] = array(
                            'imei_no' => $temp_opening_data[$j]->imei,
                            'entry_type' => 'Opening Stock',
                            'entry_time' => $datetime,
                            'date' => date('Y-m-d'),
                            'idbranch' => $idbranch,
                            'idgodown' => $idgodown,
                            'idvariant' => $temp_opening_data[$j]->id_variant,
                            'model_variant_full_name' => $temp_opening_data[$j]->full_name,
                            'idimei_details_link' => 2,
                            'iduser' => $_SESSION['id_users'],
                            'idlink' => $id_openingdata,
                        );
                        $delete_uploaded_opening_stock_test[] = $temp_opening_data[$j]->id_opening_stock_test;
                    }
//                    die('<pre>'.print_r($stock,1).'</pre>');
                    $this->General_model->save_opening_stock_data($opening_data);
                    $this->General_model->save_stock_data($stock);
                    $this->General_model->save_batch_imei_history($imei_histroy);
                    $this->General_model->delete_upl_opening_stock_test($delete_uploaded_opening_stock_test);
                }
            }
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->session->set_flashdata('save_data', 'Opening Upload is aborted. Try again with same details');
            }else{
                $this->db->trans_commit();
                $remain_upload = $this->General_model->ajax_get_remain_opening($idbranch, $godown);
                if(count($remain_upload) > 0){
                     $q['tab_active'] = '';    
                    $q['remain_upload'] = $remain_upload;
                    $this->load->view('stock/remaining_opening_stock_test',$q);
                }else{
                    $this->session->set_flashdata('save_data', 'Opening Stock Uploaded Successfully');
                    redirect('Stock/remaining_opening_stock');
                }

            }
        
    }


    public function ajax_opening_stock_data(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('branchid');
        $branches = $this->input->post('branches');
        
        $openings = $this->General_model->ajax_get_opening_data($from, $to, $idbranch, $branches);
        if(count($openings) > 0){?>
            <table class="table table-bordered table-condensed" id="opening_stock_data">
                <thead style="background-color: #ffffcc">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Opening By</th>
                    <th>Info</th>
                </thead>
                <tbody id="myTable">
                    <?php $i=1; foreach ($openings as $op){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('Y-m-d h:i:sa', strtotime($op->datetime)); ?></td>
                        <td><?php echo $op->branch_name?></td>
                        <td><?php echo $op->user_name?></td>
                        <td><a class="btn btn-primary btn-floating" target="_blank" href="<?php echo base_url()?>Stock/opening_stock_data/<?php echo $op->id_opening;?>"><span class="fa fa-info"></span></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
    }
    public function opening_stock_data($id){
        
        $q['tab_active'] = '';                        
        $q['opening_data'] = $this->General_model->get_opening_data_byidopening($id);
        $this->load->view('stock/opening_stock_details',$q);
    }
    
    public function scan_opening_stock(){
        $q['tab_active'] = '';                        
        $q['branch_data'] = $this->General_model->get_allbranch_data();               
        $q['godown_data'] = $this->General_model->get_active_godown(); 
        $q['brand_data'] = $this->General_model->get_active_brand_data(); 
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['model_data'] = $this->General_model->get_model_variant_data();
        $_SESSION['scan_barcodes'] = array();
        $this->load->view('stock/scan_opening_stock',$q);
    }
    
    public function scan_opening_stock_imei(){
        $q['tab_active'] = '';  
        $q['idgodown'] = $this->input->get('idgodown');
        $q['idbranch'] = $this->input->get('idbranch');
        $q['idpcat'] = $this->input->get('idpcat');
        $q['idbrand'] = $this->input->get('idbrand');
        $q['idmodel'] = $this->input->get('idmodel');
        $datetime = date('Y-m-d h:i:s');
        $q['datetime'] = $datetime;
        $q['branch_data'] = $this->General_model->get_branch_byid($this->input->get('idbranch'));
        $q['model_data'] = $this->General_model->get_model_variant_data_byidvariant($this->input->get('idmodel'));
        
        $q['opening_stock_test_data'] = $this->General_model->get_scan_opening_stock_test_data($this->input->get('idgodown'),  $this->input->get('idbranch'), $_SESSION['id_users']);
        $this->load->view('stock/opening_stock_imei_scan',$q);
    }
    
    public function ajax_scan_opening_imei(){
     
        $barcode = $this->input->post('scan_barcode');
        $branch_name = $this->input->post('branch_name');
        $model_name = $this->input->post('model_name');
        $datetime = $this->input->post('datetime');
        $idgodown = $this->input->post('idgodown');
        $idbranch = $this->input->post('idbranch');
        
        
       if(!in_array($barcode, $_SESSION['scan_barcodes'])){ ?>
            <tr>
                <td><?php echo $barcode; ?></td>
                <td><?php echo $model_name; ?></td>
                <td><?php echo $branch_name; ?></td>
            </tr>
            <?php
            $data[] = array(
                'imei' => $barcode,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'datetime' => $datetime,
                'uploaded_by' => $_SESSION['id_users'],
            );
            $this->General_model->save_opening_stock_test_data($data);
            array_push($_SESSION['scan_barcodes'],$barcode);  
        }else{ ?>
            <script>
                alert("Barcode Already Exsists");
            </script>
        <?php }
        
    }
    
    public function save_scanned_opening_stock(){
//        die(print_r($_POST));
        $this->db->trans_begin();
        $barcode = $this->input->post('scan_barcode');
        $datetime = $this->input->post('datetime');
        
        $idgodown = $this->input->post('idgodown');
        $idbranch = $this->input->post('idbranch');
        $idmodel = $this->input->post('idmodel');
        $idbrand = $this->input->post('idbrand');
        $idpcat = $this->input->post('idpcat');
        
        $datetime = date('Y-m-d h:i:s');
        $model_data = $this->General_model->get_model_variant_data_byidvariant($idmodel);
//        die(print_r($model_data));
        $opening_stock_test_data = $this->General_model->get_scan_opening_stock_test_data($idgodown, $idbranch, $_SESSION['id_users']);
        
        if(count($opening_stock_test_data) > 0){
            
            $opening = array(
                'idbranch' => $idbranch,
                'idgodown' => $idgodown,
                'uploaded_by' => $_SESSION['id_users'],
                'datetime' => $datetime,
                'entry_date' => date('Y-m-d'),
            );
            $id_openingdata = $this->General_model->save_opening_data($opening);
            if($id_openingdata){
                for($j=0;$j < count($opening_stock_test_data); $j++){
                    $opening_data[] = array(
                        'date' => date('Y-m-d'),
                        'idopening' => $id_openingdata,
                        'imei_no' => $opening_stock_test_data[$j]->imei,
                        'idskutype' => $model_data->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $idpcat,
                        'idcategory' => $model_data->idcategory,
                        'idvariant' => $model_data->id_variant,
                        'idmodel' => $model_data->idmodel,
                        'idbrand' => $idbrand,
                        'product_name' => $model_data->full_name,
                        'idbranch' => $idbranch,
                        'qty' => 1,
                        'created_by' => $_SESSION['id_users'],
                    );
                    $stock[] = array(
                        'date' => date('Y-m-d'),
                        'imei_no' => $opening_stock_test_data[$j]->imei,
                        'idskutype' => $model_data->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $idpcat,
                        'idcategory' => $model_data->idcategory,
                        'idvariant' => $model_data->id_variant,
                        'idmodel' => $model_data->idmodel,
                        'idbrand' => $idbrand,
                        'product_name' => $model_data->full_name,
                        'idbranch' => $idbranch,
                        'qty' => 1,
                        'created_by' => $_SESSION['id_users'],
                    );
                    $imei_histroy[] = array(
                        'imei_no' => $opening_stock_test_data[$j]->imei,
                        'entry_type' => 'Opening Stock',
                        'entry_time' => $datetime,
                        'date' => date('Y-m-d'),
                        'idbranch' => $idbranch,
                        'idgodown' => $idgodown,
                        'idvariant' => $model_data->id_variant,
                        'model_variant_full_name' => $model_data->full_name,
                        'idimei_details_link' => 2,
                        'iduser' => $_SESSION['id_users'],
                        'idlink' => $id_openingdata,
                    );
                }
                $this->General_model->save_opening_stock_data($opening_data);
                $this->General_model->save_stock_data($stock);
                $this->General_model->save_batch_imei_history($imei_histroy);
                $this->General_model->delete_scanned_opening_stock_test($idgodown, $idbranch, $_SESSION['id_users']);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Opening Stock Uploaded Successfully');
        }
        redirect('Stock/scan_opening_stock');
        
    }
    
    //Qty Stock Upload
    public function upload_qty_stock_excel(){
         $q['tab_active'] = '';         
        $this->db->trans_begin();
        $datetime = date('Y-m-d h:i:s');
        $dd = date('Y-m-d');
        $i =0;$imei='';
        $filename=$_FILES["qtyfile"]["tmp_name"];
        if($_FILES["qtyfile"]["size"] > 0){
            $file = fopen($filename, "r");
            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){ 
                    $data[] = array(
                        'name' => $openingdata[1],
                        'godown_name' => 'NEW',
                        'qty' => $openingdata[2],
                        'idbranch' => $openingdata[0],
                        'datetime' => $datetime,
                        'uploaded_by' => $_SESSION['id_users'],
                    );
                }
                $i++;
            }
            fclose($file);
        }
        $this->General_model->save_opening_stock_test_data($data);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            redirect('Stock/upload_qtyexcel_opening_data/'.$dd);
        }
        
    }
    
    public function upload_qtyexcel_opening_data($dd){
        $godown = "ALL";
        $temp_opening_data = $this->General_model->get_remaining_qtyopening_stock_data($godown, $dd);
        $datetime = date('Y-m-d h:i:s');
//        die('<pre>'.print_r($temp_opening_data,1).'</pre>');
        if(count($temp_opening_data) > 0){
            $opening = array(
                'idbranch' => 63,
                'uploaded_by' => $_SESSION['id_users'],
                'datetime' => $datetime,
                'entry_date' => date('Y-m-d'),
            );
            $id_openingdata = $this->General_model->save_opening_data($opening);
            if($id_openingdata){
                for($j=0; $j<count($temp_opening_data); $j++){
                    if($temp_opening_data[$j]->godown_name == 'DEMO'){
                        $idgodown = 2;
                    }
                    if($temp_opening_data[$j]->godown_name == 'NEW'){
                        $idgodown = 1;
                    }
                    if($temp_opening_data[$j]->godown_name == 'DOA'){
                        $idgodown = 3;
                    }
                    if($temp_opening_data[$j]->godown_name == 'SERVICE'){
                        $idgodown = 4;
                    }

                    $opening_data[] = array(
                        'date' => date('Y-m-d'),
                        'idopening' => $id_openingdata,
                        'imei_no' => NULL,
                        'idskutype' => $temp_opening_data[$j]->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                        'idcategory' => $temp_opening_data[$j]->idcategory,
                        'idvariant' => $temp_opening_data[$j]->id_variant,
                        'idmodel' => $temp_opening_data[$j]->idmodel,
                        'idbrand' => $temp_opening_data[$j]->idbrand,
                        'product_name' => $temp_opening_data[$j]->name,
                        'idbranch' => $temp_opening_data[$j]->idbranch,
                        'qty' => $temp_opening_data[$j]->qty,
                        'created_by' => $_SESSION['id_users'],
                    );
                    $stock[] = array(
                        'date' => date('Y-m-d'),
                        'imei_no' => NULL,
                        'idskutype' => $temp_opening_data[$j]->idsku_type,
                        'idgodown' => $idgodown,
                        'idproductcategory' => $temp_opening_data[$j]->idproductcategory,
                        'idcategory' => $temp_opening_data[$j]->idcategory,
                        'idvariant' => $temp_opening_data[$j]->id_variant,
                        'idmodel' => $temp_opening_data[$j]->idmodel,
                        'idbrand' => $temp_opening_data[$j]->idbrand,
                        'product_name' => $temp_opening_data[$j]->full_name,
                        'idbranch' => $temp_opening_data[$j]->idbranch,
                        'qty' => $temp_opening_data[$j]->qty,
                        'created_by' => $_SESSION['id_users'],
                    );
//                    $imei_histroy[] = array(
//                        'imei_no' => $temp_opening_data[$j]->imei,
//                        'entry_type' => 'Opening Stock',
//                        'entry_time' => $datetime,
//                        'date' => date('Y-m-d'),
//                        'idbranch' => $temp_opening_data[$j]->idbranch,
//                        'idgodown' => $idgodown,
//                        'idvariant' => $temp_opening_data[$j]->id_variant,
//                        'model_variant_full_name' => $temp_opening_data[$j]->full_name,
//                        'idimei_details_link' => 2,
//                        'idlink' => $id_openingdata,
//                    );
                    $delete_uploaded_opening_stock_test[] = $temp_opening_data[$j]->id_opening_stock_test;
                }
//                  die('<pre>'.print_r($stock,1).'</pre>');
                $this->General_model->save_opening_stock_data($opening_data);
                $this->General_model->save_stock_data($stock);
//                $this->General_model->save_batch_imei_history($imei_histroy);
                $this->General_model->delete_upl_opening_stock_test($delete_uploaded_opening_stock_test);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Opening Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $idbranch = 0;
            $remain_upload = $this->General_model->ajax_get_remain_opening($idbranch, $godown);
            if(count($remain_upload) > 0){
                 $q['tab_active'] = '';    
                $q['remain_upload'] = $remain_upload;
                $this->load->view('stock/remaining_opening_stock_test',$q);
            }else{
                $this->session->set_flashdata('save_data', 'Opening Stock Uploaded Successfully');
                redirect('Stock/opening_stock');
            }

        }
    }
    
      public function ageing_model_report(){
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idbranch = $this->session->userdata('idbranch');
        if($role_type==1){
            if($level==1){        
                $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_data();
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();  // all branches for temp
//                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
            }
        }else{
            if($level==1){
                $q['branch_data'] = $this->General_model->get_active_branch_data();
                $q['product_category'] = $this->General_model->get_product_category_data();
            }elseif($level==2){    
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
            }elseif($level==3){            
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
            }
        }                
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();        
        }
        $this->load->view('stock/ageing_model_report', $q);
    }
    public function ajax_ageing_model_data(){
        $idbrand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $idproductcategory = $this->input->post('idproductcategory'); 
        $allbranch = $this->input->post('allbranch'); 
        $allbrand = $this->input->post('allbrand'); 
        $allpcat = $this->input->post('allpcat'); 
        
        $ageing_data = $this->Stock_model->ajax_get_ageing_stock_data($idbrand, $idproductcategory, $idbranch, $allbranch, $allbrand, $allpcat);
        if(count($ageing_data) > 0){ ?>
            <table class="table table-bordered table-condensed" id="Ageing_data">
                <thead style="background-color: #84b8f7">
                    <th>Sr.</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Ageing Date</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($ageing_data as $adata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $adata->branch_name ?></td>
                        <td><?php echo $adata->product_category_name ?></td>
                        <td><?php echo $adata->brand_name ?></td>
                        <td><?php echo $adata->full_name ?></td>
                        <td><?php echo $adata->ageing_datetime ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found") ;
                });
            </script>
        <?php }
    }
    
     public function ageing_stock_report() {    
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');        
        
        $q['tab_active'] = '';
                
        $q['active_godown'] = $this->General_model->get_billing_godown();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();          
        if($role_type==1){
            if($level==1){
                $idwarehouse=$this->session->userdata('idbranch');
                $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();   // all branches for temp
                //$q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                    
            }                   
        }elseif($role_type==0){
            if($level==3){
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();                        
            }
        }else{          
            $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
        }            
        $this->load->view('stock/ageing_stock_report', $q);
    }
    
    public function ajax_ageing_quantity_stock() {        
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }       
        
        $model_data = $this->Stock_model->get_ageing_quantity_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs);
        ?>        
        <thead class="fixedelementtop">
        <th>Sr</th>
        <th>Branch</th>
        <th>Godown</th>
        <th>Product Category</th> 
        <th>Brand</th>            
        <th>Model</th>
        <th>Stock</th>  
        <th>InTransit Stock</th>  
        <th>Total</th>
        </thead>
        <tbody class="data_1">
        <?php $i = 1;$qty=0;$qty_in=0;
        foreach ($model_data as $model) { ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->stock_qty; ?></td>
                <td><?php echo $model->intra_stock_qty; ?></td>
                <td><?php echo (($model->stock_qty) +($model->intra_stock_qty)); ?></td>     
                <?php 
                $qty=$qty+$model->stock_qty;
                $qty_in=$qty_in+$model->intra_stock_qty;
                ?>
            </tr>
            <?php $i++;
        } ?>
            <tr>
                <td colspan='6'>Total</td>                 
                <td><?php echo $qty; ?></td>
                <td><?php echo $qty_in; ?></td>
                <td><?php echo ($qty+$qty_in); ?></td>            
            </tr>
        </tbody>
        <?php
    }  
    
    public function ajax_ageing_imei_stock() {
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');         
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }
        $model_data = $this->Stock_model->get_ageing_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,1); /// for current stock

        ?>        
        <thead class="fixedelementtop">
            <th>Sr</th>
            <th>Branch</th>
            <th>Godown</th>
            <th>Product Category</th> 
            <th>Brand</th>            
            <th>Model</th>
            <th>Quantity</th> 
            <th>IMEI/SRNO</th> 
            <th>Days In Stock</th> 
            <!--<th>isIntransit</th>-->
        </thead>
        <tbody class="data_1">
        <?php $i = 1;
        $qty=0;
        foreach ($model_data as $model) {
            if(!empty($model->transfer_time)){
                 $date1 = strtotime($model->transfer_time); 
             }else if(!empty($model->outward_time)){
              $date1 = strtotime($model->outward_time);
          }else{
            $date1 =strtotime($model->date);
        }
         $date2 = strtotime(date('Y-m-d H:i:s'));
        $secs = $date2 - $date1;
        $days = $secs / 86400;
        ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->qty; $qty=$qty+$model->qty?></td>
                <td><?php echo $model->imei_no; ?></td>
                <td><?php echo floor($days); ?></td>
                <!--<td><?php // echo (($model->temp_idbranch==null || $model->temp_idbranch==0)?'':'InTransit'); ?></td>-->                
            </tr>
            <?php $i++;
        } ?>
            <tr>
                <td colspan='6'>Total</td>                 
                <td><?php echo $qty; ?></td>
                <td></td>
                <td></td>                
            </tr>
        </tbody>
        <?php
    } 
    
    //    *************Ageing Model for Multiple branch****************
    
    public function ageing_model() {    
        $user_id=$this->session->userdata('id_users');         
        $q['tab_active'] = '';                
        $q['active_godown'] = $this->General_model->get_billing_godown();
        $q['brand_data'] = $this->General_model->get_active_brand_data();       
        $q['product_category'] = $this->General_model->get_product_category_data();
        $this->load->view('stock/ageing_stock_add', $q);
    }
    
     public function ajax_get_ageing_branch_stock_by_variant() { 
        $idvariant = $this->input->post('variant');
        $idbrand = $this->input->post('brand');
        $idgodown = $this->input->post('idgodown');
        $days = $this->input->post('days');
        
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->session->userdata('idbranch');        
        $idwarehouse=0; 
        if($role_type==2){
            $idwarehouse=$this->General_model->get_branch_byid($idbranch)->idwarehouse;    
        }
        $stock_data=$this->Stock_model->get_agening_branch_stock_by_variant($idvariant, $idgodown,$idwarehouse,$days);
        
        $idmodel = $stock_data[0]->idmodel;
        $idcategory = $stock_data[0]->idcategory;
        $pcat = $stock_data[0]->idproductcategory;
                
        ?>
        <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
            <thead class="fixheader" style="text-align: center;position: none !important;">
                 <th class="textalign" colspan=11><?php echo $stock_data[0]->full_name; ?></th>
            </thead>
            <thead class="fixheader1" style="text-align: center;position: none !important;">
                 <th>SrNo</th>
                 <th>Zone</th>
                 <th>Branch</th>
                 <th>Current Stock</th>
                 <th>In-Transit Stock</th>  
                 <th>Total Stock</th>  
                 <th>Last 30Days Sale</th>  
                 <th>Enough for days</th>  
                 <th>Order Prediction</th>  
                 <th>Status</th>  
                 <th>Action
                     <input type="checkbox" class="form-control input-sm" id="checkAll">
                 </th>  
             </thead>   
             <tbody class="data_1">
                    <?php  $i=1;
                    foreach ($stock_data as $stock){ 
                        $days_for_1_qty = 0;
                        $ned=0;
                        if ($stock->sale_qty > 0) {
                            $days_for_1_qty = ($days / $stock->sale_qty);
                        }
                        $total=($stock->stock_qty+$stock->intra_stock_qty);
                        $enough_for_days = round($total * $days_for_1_qty);
                        if ($days_for_1_qty <= 0) {
                            $ned = 0;
                        } else {
                            $ned = ($days / $days_for_1_qty);
                        }
                        $purchase_pre = round($ned - $total);    
                        
                        //Ageing Stock data
                        $status = 0;
                        $ageing_data = $this->Stock_model->get_ageing_stock_data($pcat, $idbrand, $idmodel, $idvariant, $stock->id_branch);
                        if($ageing_data){
                            $status = 1;
                        }else{
                            $status = 0;
                        }
                        ?>
                        <tr>
                             <td><?php echo $i++; ?></td>
                             <td><?php echo $stock->zone_name; ?></td>                                    
                             <td><?php echo $stock->branch_name; ?></td>            
                             <td class="textalign"><?php echo $stock->stock_qty; ?></td>                                    
                             <td class="textalign"><?php echo $stock->intra_stock_qty; ?></td>
                             <td><?php echo $total ?></td>
                             <td class="textalign"><?php echo $stock->sale_qty; ?></td>     
                             <td class="textalign"><?php echo $enough_for_days; ?></td>     
                             <td class="textalign"><?php echo $purchase_pre; ?></td>     
                             <td><?php if($status == 1){ echo 'Ageing'; }?></td>
                             <td><input type="checkbox" class="form-control input-sm " <?php if($status == 1){?> checked <?php }?> name="idcheck[]" value="<?php echo $stock->id_branch; ?>"></td>
                         </tr>                
                     <?php } ?>
             </tbody>
        </table>
        <input type="hidden" name="idvariant" value="<?php echo $idvariant; ?>">
        <input type="hidden" name="idmodel" value="<?php echo $idmodel; ?>">
        <input type="hidden" name="idpcat" value="<?php echo $pcat; ?>">
        <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>">
        <input type="hidden" name="idcategory" value="<?php echo $idcategory; ?>">
        
        <!--<button class="btn btn-warning btnageing_add pull-left fixonright" formmethod="POST" formaction="<?php echo base_url()?>Stock/remove_ageing_model_data">Remove</button>-->
        <button class="btn btn-primary btnageing_remove pull-right fixonleft" formmethod="POST" formaction="<?php echo base_url()?>Stock/save_ageing_model_data">Submit</button>
        <script>
            $(document).ready(function (){
                $("#checkAll").change(function(){
                    $('input:checkbox').not(this).prop('checked', this.checked);
                }); 
            });
        </script>
    <?php     
    }
    
    public function save_ageing_model_data(){
        $idvariant = $this->input->post('idvariant');
        $idmodel = $this->input->post('idmodel');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idcheck');
        $idcategory = $this->input->post('idcategory');
        
        $resp =1;
        
        if($idpcat == 1){
            if($idcategory == 2 || $idcategory == 28  || $idcategory == 31 || $idcategory == 33){
                $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
            }else{
               $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
            }
            
            foreach ($allvariants as $idvv){
                $idva[] = $idvv->id_variant;
            }

            
            if($this->Stock_model->remove_ageing_store_stock_byidvariant($idva)){
                $resp =0;
            }

            if($this->input->post('idcheck')){
                for($i=0; $i<count($idbranch); $i++){
                    for($j=0; $j<count($idva); $j++){
                       $data = array(
                            'idproductcategory' => $idpcat,
                            'idbrand' => $idbrand,
                            'idmodel' => $idmodel,
                            'idvariant' => $idva[$j],
                            'idbranch' => $idbranch[$i],
                            'created_by' => $_SESSION['id_users'],
                        );
                        $this->Stock_model->save_ageing_store_stock($data);
                    }
                }
                $resp =0;
            }
            
        }else{
            
            if($this->Stock_model->remove_ageing_store_stock_byidvariant($idvariant)){
                $resp =0;
            }
            
            for($i=0; $i<count($idbranch); $i++){
                $data = array(
                    'idproductcategory' => $idpcat,
                    'idbrand' => $idbrand,
                    'idmodel' => $idmodel,
                    'idvariant' => $idvariant,
                    'idbranch' => $idbranch[$i],
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Stock_model->save_ageing_store_stock($data);
            }
            $resp =0;
        }
        if($resp == 0){
            $this->session->set_flashdata('save_data', 'Ageing model saved Successfully');
            redirect('Stock/ageing_model');
        }else{
            $this->session->set_flashdata('reject_data', 'Ageing Model Failed To Save');
            redirect('Stock/ageing_model');
        }
        
    }
    
    /*public function remove_ageing_model_data(){
        $idvariant = $this->input->post('idvariant');
        $idmodel = $this->input->post('idmodel');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idcheck');
        if(count($idbranch) > 0){
            for($i=0; $i<count($idbranch); $i++){
                $ageing_data = $this->Stock_model->get_ageing_stock_data($idpcat, $idbrand, $idmodel, $idvariant, $idbranch[$i]);
                if($ageing_data){
                    $status = 1;
                }else{
                    $status = 0;
                }
                if($status == 1){
                    $this->Stock_model->remove_ageing_store_stock($idpcat, $idbrand, $idmodel,$idvariant, $idbranch[$i]);
                }
            }
            $this->session->set_flashdata('save_data', 'Ageing model Removed Successfully');
            redirect('Stock/ageing_model');
        } else{ ?>
            <script>
                alert("Select At Least One Branch");
            </script>
        <?php
            redirect('Stock/ageing_model');
        }
    }*/
    
     //*********Daily Stock *********************
    
    public function stock_value_report(){
        $q['tab_active'] = '';                
        $this->load->view('stock/stock_value_report', $q);
    }
    public function monthly_stock_value_report(){
        $q['tab_active'] = '';                
        $this->load->view('stock/monthly_stock_value_report', $q);
    }
    public function ajax_get_stock_value_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $stock_data = $this->Stock_model->ajax_get_stock_value_report($from, $to);
        if(count($stock_data)>0){ ?>
            <table class="table table-bordered table-condensed" id="stock_value_report">
                <thead style="background-color: #84b8f7">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Product Category</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Volume</th>
                    <th>Manager Value</th>
                    <th>Purchase Manager</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tvolume=0;$tmanager=0;$tpurchase=0; foreach ($stock_data as $sdata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sdata->stock_date ?></td>
                        <td><?php echo $sdata->product_category_name ?></td>
                        <td><?php echo $sdata->category_name ?></td>
                        <td><?php echo $sdata->brand_name ?></td>
                        <td><?php echo $sdata->volume; $tvolume = $tvolume + $sdata->volume; ?></td>
                        <td><?php echo $sdata->manager_value; $tmanager = $tmanager + $sdata->manager_value; ?></td>
                        <td><?php echo $sdata->purchase_value; $tpurchase = $tpurchase + $sdata->purchase_value; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tvolume; ?></b></td>
                        <td><b><?php echo $tmanager; ?></b></td>
                        <td><b><?php echo $tpurchase; ?></b></td>
                    </tr>
                </tbody> 
            </table>
        <?php } else{ ?>
        <script>
            $(document).ready(function (){
               alert("Data Not Found"); 
            });
        </script>
        <?php }
    }
    
    public function ajax_get_monthly_stock_value_report(){
        $monthyear = $this->input->post('monthyear');
//        die($monthyear);
        $from = "$monthyear-01" ;
        $to =  date("Y-m-t", strtotime($from));
        $cdat = date('Y-m-d');
        
//        if($cdat <= $to){
//            $days = date('d');
//        }
//        if($cdat > $to){
//            $days = date('d', strtotime($to));
//        }
//        
        $stock_data = $this->Stock_model->ajax_get_monthly_stock_value_report($from, $to);
        $days_cnt = $this->Stock_model->ajax_get_date_count($from, $to);
        $days = $days_cnt->days;
//        die('<pre>'.print_r($days_cnt,1).'</pre>');
        if(count($stock_data)>0){ ?>
            <table class="table table-bordered table-condensed" id="stock_value_report_monthly_summary">
                <thead style="background-color: #84b8f7">
                    <th>Sr.</th>
                    <th>Product Category</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Volume</th>
                    <th>Manager Value</th>
                    <th>Purchase Manager</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $volume=0; $land=0; $purchase=0; $tvolume=0;$tmanager=0;$tpurchase=0; foreach ($stock_data as $sdata){ 
                        if($days != 0 || $days != ''){ 
                            $volume = $sdata->volume/$days; 
                            $land = $sdata->manager_value/$days; 
                            $purchase =$sdata->purchase_value/$days;
                        }
                        ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sdata->product_category_name ?></td>
                        <td><?php echo $sdata->category_name ?></td>
                        <td><?php echo $sdata->brand_name ?></td>
                        <td><?php echo round($volume); $tvolume = $tvolume + $volume; ?></td>
                        <td><?php echo round($land); $tmanager = $tmanager + $land; ?></td>
                        <td><?php echo round($purchase); $tpurchase = $tpurchase + $purchase; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($tvolume,2); ?></b></td>
                        <td><b><?php echo round($tmanager,2); ?></b></td>
                        <td><b><?php echo round($tpurchase,2); ?></b></td>
                    </tr>
                </tbody> 
            </table>
        <?php } else{ ?>
        <script>
            $(document).ready(function (){
               alert("Data Not Found"); 
            });
        </script>
        <?php }
    }
    
     //***************Focus Model***************************
    public function focus_model() {    
        $user_id = $this->session->userdata('id_users');         
        $q['tab_active'] = '';                
        $q['active_godown'] = $this->General_model->get_billing_godown();
        $q['brand_data'] = $this->General_model->get_active_brand_data();       
        $q['product_category'] = $this->General_model->get_product_category_data();
        $this->load->view('focus_model/focus_model_add', $q);
    }
    
     public function ajax_get_focus_branch_stock_by_variant() { 
        $idvariant = $this->input->post('variant');
        $idbrand = $this->input->post('brand');
        $idgodown = $this->input->post('idgodown');
        $days = $this->input->post('days');
        
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->session->userdata('idbranch');        
        $idwarehouse=0; 
        
        if($role_type==2){
            $idwarehouse=$this->General_model->get_branch_byid($idbranch)->idwarehouse;    
        }
        
        $stock_data = $this->Stock_model->get_get_branch_stock_by_variant($idvariant, $idgodown,$idwarehouse,$days);
        
        $incentive = $this->Stock_model->ajax_get_focus_incentive_data_byidvariant($idvariant);
        
        $idmodel = $stock_data[0]->idmodel;
        $idcategory = $stock_data[0]->idcategory;
        $pcat = $stock_data[0]->idproductcategory;
                
        ?>
        
        <div class="col-md-2 col-md-offset-3"><b>Incentive Amount</b></div>
        <div class="col-md-2"><input type="text" class="form-control" name="incentive_amount" required value="<?php if($incentive){ echo $incentive->incentive_amount;}else{ echo '0';}?>"></div>
        <div class="clearfix"></div><br>
        <div style="overflow-x: auto;height: 700px;"> 
            <table id="focus_model_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
                <thead class="fixheader" style="text-align: center;position: none !important;">
                <th class="textalign" colspan="11"> <?php echo $stock_data[0]->full_name; ?></th>
                </thead>
                <thead class="fixheader1" style="text-align: center;position: none !important;">
                    <th>SrNo</th>
                    <th>Zone</th>
                    <th>Branch</th>
                    <th>Current Stock</th>
                    <th>In-Transit Stock</th>  
                    <th>Total Stock</th>  
<!--                    <th>Last 30Days Sale</th>  
                    <th>Enough for days</th>  
                    <th>Order Prediction</th>  -->
                    <th>Status</th>  
                    <th>Action
                        <input type="checkbox" class="form-control input-sm" id="checkAll">
                    </th>  
                </thead>   
                <tbody class="data_1">

                    <?php $i=1; foreach ($stock_data as $stock){ 
                        $days_for_1_qty = 0;
                        $ned=0;
                        if ($stock->sale_qty > 0) {
                            $days_for_1_qty = ($days / $stock->sale_qty);
                        }
                        $total=($stock->stock_qty+$stock->intra_stock_qty);
                        $enough_for_days = round($total * $days_for_1_qty);
                        if ($days_for_1_qty <= 0) {
                            $ned = 0;
                        } else {
                            $ned = ($days / $days_for_1_qty);
                        }
                        $purchase_pre = round($ned - $total);    

                        //Ageing Stock data
                        $status = 0;
                        $ageing_data = $this->Stock_model->get_focus_stock_data($pcat, $idbrand, $idmodel, $idvariant, $stock->id_branch);
                        if($ageing_data){
                            $status = 1;
                        }else{
                            $status = 0;
                        }
                        ?>
                        <tr>
                             <td><?php echo $i++; ?></td>
                             <td><?php echo $stock->zone_name; ?></td>                                    
                             <td><?php echo $stock->branch_name; ?></td>            
                             <td class="textalign"><?php echo $stock->stock_qty; ?></td>                                    
                             <td class="textalign"><?php echo $stock->intra_stock_qty; ?></td>
                             <td><?php echo $total ?></td>
<!--                             <td class="textalign"><?php echo $stock->sale_qty; ?></td>     
                             <td class="textalign"><?php echo $enough_for_days; ?></td>     
                             <td class="textalign"><?php echo $purchase_pre; ?></td>     -->
                             <td><?php if($status == 1){ echo 'Focus Model'; }?></td>
                             <td><input type="checkbox" class="form-control input-sm " <?php if($status == 1){?> checked <?php }?> name="idcheck[]" value="<?php echo $stock->id_branch; ?>"></td>
                         </tr>                
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div><br>
        <input type="hidden" name="idvariant" value="<?php echo $idvariant; ?>">
        <input type="hidden" name="idmodel" value="<?php echo $idmodel; ?>">
        <input type="hidden" name="idpcat" value="<?php echo $pcat; ?>">
        <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>">
        <input type="hidden" name="idcategory" value="<?php echo $idcategory; ?>">
        
        <button class="btn btn-primary btnageing_remove pull-right fixonleft" formmethod="POST" formaction="<?php echo base_url()?>Stock/save_focus_model_data">Submit</button>
        <script>
            $(document).ready(function (){
                $("#checkAll").change(function(){
                    $('input:checkbox').not(this).prop('checked', this.checked);
                }); 
            });
        </script>
    <?php     
    }
    
    public function save_focus_model_data(){
        $idvariant = $this->input->post('idvariant');
        $idmodel = $this->input->post('idmodel');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idcheck');
        $idcategory = $this->input->post('idcategory');
        $incentive_amount = $this->input->post('incentive_amount');
        $resp =1; 
        
        if($idpcat == 1){
            if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
            }else{
               $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
            }
            foreach ($allvariants as $idvv){
                $idva[] = $idvv->id_variant;
            }
            if($this->Stock_model->remove_focus_model_stock_byidvariant($idva)){
                $resp =0;
            }
            if($this->input->post('idcheck')){
                for($i=0; $i<count($idbranch); $i++){
                    for($j=0; $j<count($idva); $j++){
                       $data = array(
                            'idproductcategory' => $idpcat,
                            'idbrand' => $idbrand,
                            'idmodel' => $idmodel,
                            'idvariant' => $idva[$j],
                            'idbranch' => $idbranch[$i],
                            'incentive_amount' => $incentive_amount,
                            'created_by' => $_SESSION['id_users'],
                        );
                        $this->Stock_model->save_focus_model_stock_data($data);
                    }
                }
                $resp =0;
            }
        }else{
            if($this->Stock_model->remove_focus_model_stock_byidvariant($idvariant)){
                $resp =0;
            }
            for($i=0; $i<count($idbranch); $i++){
                $data = array(
                    'idproductcategory' => $idpcat,
                    'idbrand' => $idbrand,
                    'idmodel' => $idmodel,
                    'idvariant' => $idvariant,
                    'incentive_amount' => $incentive_amount,
                    'idbranch' => $idbranch[$i],
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Stock_model->save_focus_model_stock_data($data);
            }
            $resp =0;
        }
        if($resp == 0){
            $this->session->set_flashdata('save_data', 'Focus model saved Successfully');
            redirect('Stock/focus_model');
        }else{
            $this->session->set_flashdata('reject_data', 'Focus Model Failed To Save');
            redirect('Stock/focus_model');
        }
        
    }
    
    public function focus_model_report(){
        $user_id=$this->session->userdata('id_users');   
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $role_type=$this->session->userdata('role_type');
        $q['active_godown'] = $this->General_model->get_billing_godown();                
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        $idbranch = $this->session->userdata('idbranch');
        if($role_type==1){
            if($level==1){        
                $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_data();
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();  // all branches for temp
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);            
            }
        }else{
            if($level==1){
                $q['branch_data'] = $this->General_model->get_active_branch_data();
                $q['product_category'] = $this->General_model->get_product_category_data();
            }elseif($level==2){    
                $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);                
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
            }elseif($level==3){            
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                      
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
            }
        }                
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();        
        }
        $this->load->view('focus_model/focus_model_report', $q);
    }
    public function ajax_focus_model_data(){
        $idbrand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $idproductcategory = $this->input->post('idproductcategory'); 
        $allbranch = $this->input->post('allbranch'); 
        $allbrand = $this->input->post('allbrand'); 
        $allpcat = $this->input->post('allpcat'); 
        
        $ageing_data = $this->Stock_model->ajax_get_focus_model_data($idbrand, $idproductcategory, $idbranch, $allbranch, $allbrand, $allpcat);
        if(count($ageing_data) > 0){ ?>
            <table class="table table-bordered table-condensed" id="Ageing_data">
                <thead style="background-color: #84b8f7">
                    <th>Sr.</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Ageing Date</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($ageing_data as $adata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $adata->branch_name ?></td>
                        <td><?php echo $adata->product_category_name ?></td>
                        <td><?php echo $adata->brand_name ?></td>
                        <td><?php echo $adata->full_name ?></td>
                        <td><?php echo $adata->focus_datetime ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found") ;
                });
            </script>
        <?php }
    }
    
    public function focus_model_stock_report() {    
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');        
        
        $q['tab_active'] = '';
                
        $q['active_godown'] = $this->General_model->get_billing_godown();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();          
        if($role_type==1){
            if($level==1){
                $idwarehouse=$this->session->userdata('idbranch');
                $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();   // all branches for temp
            }                   
        }elseif($role_type==0){
            if($level==3){
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();                        
            }
        }else{          
            $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);  
        }            
        $this->load->view('focus_model/focus_model_stock_report', $q);
    }
    
     public function ajax_focus_model_quantity_stock() {        
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');   
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }       
        $model_data = $this->Stock_model->get_focus_model_quantity_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs);
        ?>        
        <thead class="fixedelementtop">
        <th>Sr</th>
        <th>Branch</th>
        <th>Godown</th>
        <th>Product Category</th> 
        <th>Brand</th>            
        <th>Model</th>
        <th>Stock</th>  
        <th>InTransit Stock</th>  
        <th>Total</th>
        <th>Incentive Amount</th>
        </thead>
        <tbody class="data_1">
        <?php $i = 1;$qty=0;$qty_in=0;$total_inc=0;
            foreach ($model_data as $model) { ?>
                <tr>
                    <td><?php echo $i; ?></td> 
                    <td><?php echo $model->branch_name; ?></td>    
                    <td><?php echo $model->godown_name; ?></td>    
                    <td><?php echo $model->product_category_name; ?></td> 
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->full_name; ?></td>
                    <td><?php echo $model->stock_qty; ?></td>
                    <td><?php echo $model->intra_stock_qty; ?></td>
                    <td><?php echo (($model->stock_qty)+($model->intra_stock_qty)); ?></td>     
                    <td><?php echo $model->incentive_amount; ?></td>     
                    <?php 
                    $qty=$qty+$model->stock_qty;
                    $qty_in=$qty_in+$model->intra_stock_qty;
                    $total_inc = $total_inc + $model->incentive_amount;
                    ?>
                </tr>
        <?php $i++; } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Total</b></td>                 
                <td><b><?php echo $qty; ?></b></td>
                <td><b><?php echo $qty_in; ?></b></td>
                <td><b><?php echo ($qty+$qty_in); ?></b></td>            
                <td><b><?php echo $total_inc; ?></b></td>            
            </tr>
        </tbody>
        <?php
    }  
    
    public function ajax_focus_model_imei_stock() {
        $id_users=$this->session->userdata('id_users');
        $level=$this->session->userdata('level');
        $role_type=$this->session->userdata('role_type');         
        $idbranch=$this->input->post('branch');   
        $idbranchs=array();
        if($idbranch==0){
           $branchs=$this->General_model->get_branch_by_role_user_level($role_type,$level,$id_users);                
           foreach ($branchs as $idb){
               $idbranchs[]=$idb->id_branch;
           }
        }
        $model_data = $this->Stock_model->get_focus_model_imei_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('brand'), $this->input->post('product_category'), $this->input->post('branch'),$this->input->post('iswarehouse'),$idbranchs,1); /// for current stock
        ?>        
        <thead class="fixedelementtop">
            <th>Sr</th>
            <th>Branch</th>
            <th>Godown</th>
            <th>Product Category</th> 
            <th>Brand</th>            
            <th>Model</th>
            <th>Quantity</th> 
            <th>IMEI/SRNO</th> 
            <th>Days In Stock</th> 
            <th>Incentive Amount</th> 
        </thead>
        <tbody class="data_1">
        <?php $i = 1; $qty = 0;$inct = 0; foreach ($model_data as $model) {
             if(!empty($model->transfer_time)){
                 $date1 = strtotime($model->transfer_time); 
             }else if(!empty($model->outward_time)){
              $date1 = strtotime($model->outward_time);
          }else{
            $date1 =strtotime($model->date);
        }
        
        $date2 = strtotime(date('Y-m-d H:i:s'));
        $secs = $date2 - $date1;
        $days = $secs / 86400;
            ?>
            <tr>
                <td><?php echo $i; ?></td> 
                <td><?php echo $model->branch_name; ?></td>    
                <td><?php echo $model->godown_name; ?></td>    
                <td><?php echo $model->product_category_name; ?></td> 
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->qty; $qty=$qty+$model->qty?></td>
                <td><?php echo $model->imei_no; ?></td>
                 <td><?php echo floor($days); ?></td>
                <td><?php echo $model->incentive_amount; $inct = $inct + $model->incentive_amount;  ?></td>     
            </tr>
        <?php $i++; } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Total</b></td>                 
                <td><b><?php echo $qty; ?></b></td>
                <td></td>
                <td><b><?php echo $inct; ?></b></td>                
            </tr>
        </tbody>
        <?php
    }  
    
    public function focus_sale_report(){
        $q['tab_active'] = '';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('focus_model/focus_sale_report', $q);  
    }
    
    public function ajax_get_focus_model_sale_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $idpcat = $this->input->post('idpcat');
        $idbrand = $this->input->post('idbrand');
        $sale_data = $this->Stock_model->ajax_get_focus_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand);
        if(count($sale_data) >0){
        ?>
        <table id="sale_report" class="table table-bordered table-striped table-condensed table-info">
            <thead class="fixedelementtop">
                <th>Entry Time</th>
                <th>Invoice No</th>
                <th>Branch</th>
                <th>Zone</th>
                <th>Partner type</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Imei</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>MOP</th>
                <th>MRP</th>
                <th>Amount</th>
                <th>Sale Promotor Brand</th>
                <th>Sale Promotor</th>
                <!--<th>Incentive Amount</th>-->
                <th>Info</th>
                <th>Print</th>
            </thead>
            <tbody class="data_1">
                <?php $total=0; foreach ($sale_data as $sale) {
                    $userdata = $this->Stock_model->ajax_get_brand_name_byiduser($sale->id_users);
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y h:i a', strtotime($sale->entry_time)) ?></td>
                        <td><?php echo $sale->inv_no ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php echo $sale->zone_name ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
                        <td><?php echo $sale->customer_contact ?></td>
                        <td><?php echo $sale->customer_gst ?></td>
                        <td>'<?php echo $sale->imei_no ?></td>
                        <td><?php echo $sale->product_category_name ?></td>
                        <td><?php echo $sale->brand_name ?></td>
                        <td><?php echo $sale->product_name ?></td>
                        <td><?php echo $sale->mop ?></td>
                        <td><?php echo $sale->mrp ?></td>
                        <td><?php echo $sale->total_amount; $total = $total + $sale->total_amount; ?></td>
                        <td><?php if($userdata){echo $userdata->user_brand_name; }?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <!--<td><?php echo $sale->incentive_amount; ?></td>-->
                        <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$sale->idsale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
                        <td><a target="_blank" href="<?php echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo $total; ?></b></td>
                    <td></td>
                    <td></td>
                    <!--<td></td>-->
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
    
    //Brand Placement 
     //Brand Wise Stock Norms
    public function brand_placement_norms() { 
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_zone_branch_data();
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $this->load->view('stock/brand_placement_norms', $q);
    }
    
    public function ajax_get_brand_stock_norms(){
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        
        $brand_data = $this->Stock_model->get_brand_wise_placement_norms($days,$product_category,$branch);
        $category_data = $this->Stock_model->get_category_wise_placement_norms($days,$product_category,$branch);
        $mix_data = $this->Stock_model->get_allbrand_wise_placement_norms($days,$product_category,$branch);
        $mix_qty = $this->Stock_model->get_mix_brand_data($product_category,$branch);
        $placement_data = $this->Stock_model->get_stock_placement_data_byid($product_category, $branch);
//        die('<pre>'.print_r($placement_data,1).'</pre>');
        if($placement_data){ ?> 
            <form>
                <table class="table table-bordered table-condensed text-center">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                         <th>Sr.</th>
                         <th>Promotor Count</th>
                         <th>Brand</th>
                         <th>Current Stock</th>
                         <th>Last <?php echo $days;?> Days Sale</th>
                         <th>Placement Norm</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $curr_stock = 0; $sale_qty =0;$intra_qty=0; $total_stk=0;
                        $t_cstk=0;$t_sale=0; $tplac_qty=0;
                        foreach ($brand_data as $bdata){ 
                            if($bdata->curr_stock){ $curr_stock = $bdata->curr_stock; }else{ $curr_stock =0; }
                            if($bdata->sale_qty){ $sale_qty = $bdata->sale_qty; }else{ $sale_qty =0; }
                            if($bdata->intra_stock){ $intra_qty = $bdata->intra_stock; }else{ $intra_qty =0; }
                            ?>
                                <tr class="tr_brand">
                                    <td><?php echo $sr++; ?></td>
                                    <td><?php echo $bdata->promotor; ?></td>
                                    <td><?php echo $bdata->brand_name; ?></td>
                                    <td><?php $total_stk = $curr_stock + $intra_qty; echo $total_stk;  $t_cstk = $t_cstk + $total_stk;  ?></td>
                                    <td><?php echo $sale_qty; $t_sale = $t_sale + $sale_qty;  ?></td>
                                    <td>
                                      <input type="text" class="form-control input-sm pnorm text-center" id="pnorm" name="pnorm[]" value="<?php echo $bdata->quantity; ?>">
                                      <?php $tplac_qty = $tplac_qty + $bdata->quantity; ?>
                                      <input type="hidden" name="id_stock_norm[]" value="<?php echo $bdata->id_stock_norm; ?>">
                                      <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]" value="<?php echo $bdata->id_brand; ?>">
                                      <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]">

                                   </td>
                            </tr>
                        <?php } 
                            foreach ($category_data as $cdata){ 
                                if($cdata->curr_stock){ $curr_stock = $cdata->curr_stock; }else{ $curr_stock =0; }
                                if($cdata->sale_qty){ $sale_qty = $cdata->sale_qty; }else{ $sale_qty =0; }
                                if($cdata->intra_stock){ $intra_qty = $cdata->intra_stock; }else{ $intra_qty =0; }
                                $total_stk = $curr_stock + $intra_qty;
                                ?>
                                <tr class="tr_brand">
                                    <td><?php echo $sr++; ?></td>
                                    <td><?php //echo $bdata->promotor; ?></td>
                                    <td><?php echo $cdata->category_name; ?></td>
                                    <td><?php  $total_stk = $curr_stock + $intra_qty; echo $total_stk;  $t_cstk = $t_cstk + $total_stk; ?></td>
                                    <td><?php echo $sale_qty; $t_sale = $t_sale + $sale_qty;  ?></td>
                                    <td><input type="text" class="form-control input-sm pnorm text-center" id="pnorm" name="pnorm[]" value="<?php echo $cdata->quantity ?>">
                                        <input type="hidden" name="id_stock_norm[]" value="<?php echo $cdata->id_stock_norm; ?>">
                                         <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]"  >
                                         <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]" value="<?php echo $cdata->id_category; ?>">
                                    </td>
                                </tr>
                          <?php } 
                          foreach ($mix_data as $mdata){ 
                             if($mdata->curr_stock){ $curr_stock = $mdata->curr_stock; }else{ $curr_stock =0; }
                             if($mdata->sale_qty){ $sale_qty = $mdata->sale_qty; }else{ $sale_qty =0; } 
                             if($mdata->intra_stock){ $intra_qty = $mdata->intra_stock; }else{ $intra_qty =0; }
                             $total_stk = $curr_stock + $intra_qty;
                             ?>
                                <tr class="tr_brand">
                                    <td><?php echo $sr++; ?></td>
                                    <td><?php echo $mdata->promotor; ?></td>
                                    <td><?php echo 'MIX BB' ?></td>
                                    <td><?php $total_stk = $curr_stock + $intra_qty;  echo $total_stk; $t_cstk = $t_cstk + $total_stk;  ?></td>
                                    <td><?php echo $sale_qty; $t_sale = $t_sale + $sale_qty;  ?></td>
                                    <td><input type="text" class="form-control input-sm pnorm text-center" id="pnorm" name="pnorm[]" value="<?php echo $mix_qty->quantity?>">
                                         <input type="hidden" name="id_stock_norm[]" value="<?php echo $mix_qty->id_stock_norm; ?>">
                                        <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]" value="76">
                                        <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]" >
                                    </td>
                                 </tr>
                         <?php } ?>
                    </tbody>
                    
<!--                    <tbody class="data_1">
                       <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0; $t_cstk=0;$t_sale=0;$inta_qty=0;$tot_stock=0;
                       $tplac_qty = 0;
                       foreach($placement_data as $plac){ 
                            $idcat = $plac->idcategory;
                            $idbrand = $plac->idbrand;
                            $branddata = $this->Stock_model->get_brand_wise_placement_norms_byidbrand($days,$product_category,$idbrand,$plac->idbranch, $idcat);
                            if($branddata->curr_stock){ $stk_qty = $branddata->curr_stock; }else{ $stk_qty =0; }
                            if($branddata->sale_qty){ $saleqt = $branddata->sale_qty; }else{ $saleqt =0; }
                            if($branddata->intra_stock){ $inta_qty = $branddata->intra_stock; }else{ $inta_qty =0; }
                           
                            if($plac->idbrand != 0 && $plac->idbrand != 76){ 
                               $brand_name = $plac->brand_name; 
                               $pcnt = $branddata->promotor;
                           }
                           if($plac->idcategory > 0){ 
                               $brand_name = $plac->category_name; 
                               $pcnt = '';
                           }
                           if($plac->idbrand == 76){ 
                               $brand_name = $plac->brand_name; 
                               $pcnt = $branddata->promotor;
                           }
                           
                           $tot_stock = $stk_qty + $inta_qty;
                           ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pcnt;  ?></td>
                            <td><?php echo $brand_name; ?></td>
                            <td><?php echo $tot_stock; $t_cstk = $t_cstk + $tot_stock;  ?></td>
                            <td><?php echo $saleqt; $t_sale = $t_sale + $saleqt; ?></td>
                            <td><input type="text" class="form-control input-sm pqty" id="pqty" name="qty[]" value="<?php echo $plac->quantity; ?>">
                                <input type="hidden" name="id_stock_norm[]" value="<?php echo $plac->id_stock_norm; ?>">
                                 <?php $tplac_qty = $tplac_qty + $plac->quantity; ?>
                            </td>
                        </tr>
                       <?php } ?>
                    </tbody>-->
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>TOTAL</b></td>
                        <td><b><?php echo $t_cstk; ?></b></td>
                        <td><b><?php echo $t_sale; ?></b></td>
                        <td><b><div class="tpqty" style="text-align: center"><?php  echo $tplac_qty; ?></div></b></td>
                    </tr>
                </table>
                 <input type="hidden" name="idbranch" value="<?php echo $branch; ?>">
                    <input type="hidden" name="idproductcategory" value="<?php echo $product_category; ?>">
                <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Stock/update_brand_placement_norms">Update</button>
           </form>
           <script>
               $(document).on('change', 'input[id=pnorm]', function() {
                       var  total_basic_sum = 0;
                        $('tr').each(function () {
                            $(this).find('.pnorm').each(function () {
                                var total_basic = $(this).val();
//                                alert(total_basic);
                                if (!isNaN(total_basic) && total_basic.length !== 0) {
                                    total_basic_sum += parseFloat(total_basic);
                                }
                            });
                            $('.tpqty').html(total_basic_sum);
                       });
                    });
           </script>
        <?php }else{ 
            if($brand_data){ ?>
                <form>
                    <table class="table table-bordered table-condensed text-center">
                        <thead style="background-color: #84b8f7" class="fixedelementtop">
                              <th>Sr.</th>
                              <th>Promotor Count</th>
                              <th>Brand</th>
                              <th>Current Stock</th>
                              <th>Last <?php echo $days;?> Days Sale</th>
                              <th>Placement Norm</th>
                        </thead>
                        <tbody class="data_1">
                            <?php $sr=1; $curr_stock = 0; $sale_qty =0;$inta_qty=0;$tot_stock=0;  $t_cstk=0;$t_sale=0;
                            
                            foreach ($brand_data as $bdata){ 
                                if($bdata->curr_stock){ $curr_stock = $bdata->curr_stock; }else{ $curr_stock =0; }
                                if($bdata->sale_qty){ $sale_qty = $bdata->sale_qty; }else{ $sale_qty =0; }
                                if($bdata->intra_stock){ $inta_qty = $bdata->intra_stock; }else{ $inta_qty =0; }
                                $tot_stock = $curr_stock + $inta_qty;
                                ?>
                                   <tr class="tr_brand">
                                      <td><?php echo $sr++; ?></td>
                                      <td><?php echo $bdata->promotor; ?></td>
                                      <td><?php echo $bdata->brand_name; ?></td>
                                      <td><?php echo $tot_stock;  $t_cstk = $t_cstk + $tot_stock;  ?></td>
                                      <td><?php echo $sale_qty; $t_sale = $t_sale + $sale_qty;  ?></td>
                                      <td ><input type="text" class="form-control input-sm pnorm" id="pnorm" name="pnorm[]" >
                                          <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]" value="<?php echo $bdata->id_brand; ?>">
                                          <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]">
                                      </td>
                                  </tr>
                             <?php } 
                                foreach ($category_data as $cdata){ 
                                    if($cdata->curr_stock){ $curr_stock = $cdata->curr_stock; }else{ $curr_stock =0; }
                                    if($cdata->sale_qty){ $sale_qty = $cdata->sale_qty; }else{ $sale_qty =0; }
                                    if($cdata->intra_stock){ $inta_qty = $cdata->intra_stock; }else{ $inta_qty =0; }
                                     $tot_stock = $curr_stock + $inta_qty;
                                    ?>
                                  
                                    <tr class="tr_brand">
                                        <td><?php echo $sr++; ?></td>
                                        <td><?php //echo $bdata->promotor; ?></td>
                                        <td><?php echo $cdata->category_name; ?></td>
                                        <td><?php echo $tot_stock; $t_cstk = $t_cstk + $tot_stock;   ?></td>
                                        <td><?php echo $sale_qty; $t_sale = $t_sale + $sale_qty;  ?></td>
                                        <td><input type="text" class="form-control input-sm pnorm" id="pnorm" name="pnorm[]" >
                                             <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]"  >
                                             <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]" value="<?php echo $cdata->id_category; ?>">
                                        </td>
                                    </tr>
                              <?php } 
                              foreach ($mix_data as $mdata){ 
                                 if($mdata->curr_stock){ $curr_stock = $mdata->curr_stock; }else{ $curr_stock =0; }
                                 if($mdata->sale_qty){ $sale_qty = $mdata->sale_qty; }else{ $sale_qty =0; } 
                                 if($mdata->intra_stock){ $inta_qty = $mdata->intra_stock; }else{ $inta_qty =0; }
                                  $tot_stock = $curr_stock + $inta_qty;
                                 ?>
                                    <tr class="tr_brand">
                                        <td><?php echo $sr++; ?></td>
                                        <td><?php echo $mdata->promotor; ?></td>
                                        <td><?php echo 'MIX BB' ?></td>
                                        <td><?php echo $tot_stock; $t_cstk = $t_cstk + $tot_stock  ?></td>
                                        <td><?php echo $sale_qty;  $t_sale = $t_sale + $sale_qty; ?></td>
                                        <td><input type="text" class="form-control input-sm pnorm" id="pnorm" name="pnorm[]">
                                            <input type="hidden" class="form-control input-sm" id="idbrand" name="idbrand[]" value="76">
                                            <input type="hidden" class="form-control input-sm" id="idcategory" name="idcategory[]" >
                                        </td>
                                     </tr>
                             <?php } ?>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>TOTAL</b></td>
                            <td><b><?php echo $tot_stock ?></b></td>
                            <td><b><?php echo $t_sale ;?></b></td>
                            <td><div class="ttpqty" style="text-align: left"></div></td>
                        </tr>
                    </table>
                    <input type="hidden" name="idbranch" value="<?php echo $branch; ?>">
                    <input type="hidden" name="idproductcategory" value="<?php echo $product_category; ?>">
                    <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Stock/save_brand_placement_norms">Submit</button>
                </form>
                <script>
                    $(document).ready(function (){
                        $(document).on('change', '.pnorm', function() {
                            var  total_basic_sum = 0;
                            $('.tr_brand').each(function () {
                                $(this).find('.pnorm').each(function () {
                                    var total_basic = +$(this).val();
                                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                                        total_basic_sum += parseFloat(total_basic);
                                    }
                                });
                                $('.ttpqty').html(total_basic_sum);
                            });
                        });
                    });
                 </script>
        <?php } }
    }
    
    public function save_brand_placement_norms(){
        $pnorms = $this->input->post('pnorm');
        $idbrand = $this->input->post('idbrand');
        $idcategory = $this->input->post('idcategory');
        $idproductcategory = $this->input->post('idproductcategory');
        $idbranch = $this->input->post('idbranch');
        
        for($i=0; $i< count($pnorms); $i++){
            $data = array(
                'idbranch' => $idbranch,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory[$i],
                'idbrand' => $idbrand[$i],
                'quantity' => $pnorms[$i],
                'norm_lmb' => $_SESSION['id_users'],
                'norm_lmt' => date('Y-m-d h:i:s'),
            );
            $this->Stock_model->save_brand_placement_norms($data);
        }
         $this->session->set_flashdata('save_data', 'Placement Norms Saved Successfully !');
        redirect('Stock/brand_placement_norms');
    }
    
    public function update_brand_placement_norms(){
//        $pnorms = $this->input->post('qty');
//        $id_stock_norm = $this->input->post('id_stock_norm');
      //  die('<pre>'.print_r($_POST,1).'</pre>');
        $pnorms = $this->input->post('pnorm');
        $id_stock_norm = $this->input->post('id_stock_norm');
        $idbrand = $this->input->post('idbrand');
        $idcategory = $this->input->post('idcategory');
        $idproductcategory = $this->input->post('idproductcategory');
        $idbranch = $this->input->post('idbranch');
        
        for($i=0; $i< count($id_stock_norm); $i++){
            if($id_stock_norm[$i] != ''){
                $data = array(
                    'quantity' => $pnorms[$i],
                    'norm_lmb' => $_SESSION['id_users'],
                    'norm_lmt' => date('Y-m-d h:i:s'),
                );
                $this->Stock_model->update_brand_placement_norms($data, $id_stock_norm[$i]);
            }else{
                 $data = array(
                    'idbranch' => $idbranch,
                    'idproductcategory' => $idproductcategory,
                    'idcategory' => $idcategory[$i],
                    'idbrand' => $idbrand[$i],
                    'quantity' => $pnorms[$i],
                    'norm_lmb' => $_SESSION['id_users'],
                    'norm_lmt' => date('Y-m-d h:i:s'),
                );
                $this->Stock_model->save_brand_placement_norms($data);
            }
        }
         $this->session->set_flashdata('save_data', 'Placement Norms Saved Successfully !');
        redirect('Stock/brand_placement_norms');
    }
    public function brand_placement_norms_report() { 
        $q['tab_active'] = '';
       if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['zone_data'] = $this->General_model->get_active_zone();
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $this->load->view('stock/brand_placement_norms_report', $q);
    }
    public function ajax_get_brand_stock_norms_report(){
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        $allbranch = $this->input->post('allbranch');
        $idzone = $this->input->post('idzone');
        $allzones = $this->input->post('allzones');
//        die(print_r($allzones));
        $brand_data = $this->Stock_model->get_brand_wise_placement_norms_report($days,$product_category,$branch, $allbranch, $idzone, $allzones);
        $category_data = $this->Stock_model->get_category_wise_placement_norms_report($days,$product_category,$branch, $allbranch, $idzone, $allzones);
        $mix_data = $this->Stock_model->get_allbrand_wise_placement_norms_report($days,$product_category,$branch, $allbranch, $idzone, $allzones);
        $arr_data = json_decode(json_encode(array_merge($brand_data, $category_data, $mix_data)),TRUE);
        
        if($idzone != 'all'){ 
            if($idzone == 'allzone'){
                $col = array_column( $arr_data, "id_zone" );
                array_multisort( $col, SORT_ASC, $arr_data );    
            }else{
                $col = array_column( $arr_data, "id_branch" );
                array_multisort( $col, SORT_ASC, $arr_data );
            }
        }
        
        $arr_data = json_decode(json_encode($arr_data), FALSE);
//                die('<pre>'.print_r($arr_data,1).'</pre>');
        if($arr_data){
            if($idzone == 'all' ){ ?>
             <table class="table table-bordered table-condensed text-center" id="brand_placement_norm_report">
                <thead style="background-color: #84b8f7" class="fixedelementtop">
                    <th style="text-align: center">Sr.</th>
                    <th style="text-align: center">Promotor Count</th>
                    <th style="text-align: center">Brand</th>
                    <th style="text-align: center">Current Stock</th>
                    <th style="text-align: center">Last <?php echo $days;?> Days Sale</th>
                    <th style="text-align: center">Placement Norm</th>
                    <th style="text-align: center">Gap In Volume</th>
                    <th style="text-align: center">Ach In %</th>
                </thead>
                <tbody class="data_1">
                   <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0; $intra_qty=0; $tot_stock=0;
                   $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0; 
                   $plac_qty =0;
                    foreach($arr_data as $plac){ 
                        
                        if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                        if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                        if($plac->quantity){ $plac_qty = $plac->quantity; }else{ $plac_qty =0; }
                        if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                        $tot_stock = $stk_qty + $intra_qty;
                        
                        $brand_name = $plac->brand_name; 
                        $pcnt = $plac->promotor;

                        $gap = $tot_stock - $plac_qty;
                        if($plac->quantity){ $ach = ($tot_stock / $plac_qty)*100;}else{ $ach=0;}
                        ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pcnt;  ?></td>
                            <td><?php echo $brand_name; ?></td>
                            <td><?php echo $tot_stock; $tstk = $tstk + $tot_stock; ?></td>
                            <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                            <td><?php echo $plac_qty; $tplc = $tplc + $plac_qty;  ?></td>
                            <td><?php echo $gap;   ?></td>
                            <td><?php echo round($ach).'%';  ?></td>
                        </tr>
                   <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $tstk; ?></b></td>
                            <td><b><?php echo $tsale; ?></b></td>
                            <td><b><?php echo $tplc; ?></b></td>
                            <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                            <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100;}else{ $tach = 0; }  echo round($tach).'%'; ?></b></td>
                        </tr>
                </tbody>
            </table>
            <?php } 
            elseif($idzone == 'allzone'){ ?>
             <table class="table table-bordered table-condensed text-center" id="brand_placement_norm_report">
                <thead style="background-color: #84b8f7" class="fixedelementtop">
                    <th style="text-align: center">Sr.</th>
                    <th style="text-align: center">Zone</th>
                    <th style="text-align: center">Promotor Count</th>
                    <th style="text-align: center">Brand</th>
                    <th style="text-align: center">Current Stock</th>
                    <th style="text-align: center">Last <?php echo $days;?> Days Sale</th>
                    <th style="text-align: center">Placement Norm</th>
                    <th style="text-align: center">Gap In Volume</th>
                    <th style="text-align: center">Ach In %</th>
                </thead>
                 <tbody class="data_1">
                   <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0; $intra_qty=0; $tot_stock=0;
                   $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0; 
                   $plac_qty =0;
                   $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                   $old_name = $arr_data[0]->id_zone;
                    foreach($arr_data as $plac){ 
                        
                        if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                        if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                        if($plac->quantity){ $plac_qty = $plac->quantity; }else{ $plac_qty =0; }
                        if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                        $tot_stock = $stk_qty + $intra_qty;
                        
                        $brand_name = $plac->brand_name; 
                        $pcnt = $plac->promotor;

                        $gap = $tot_stock - $plac_qty;
                        if($plac->quantity){ $ach = ($tot_stock / $plac_qty)*100;}else{ $ach=0;}
                        
                        //Zone Wise Sum
                        if($old_name == $plac->id_zone){
                            $rcqty = $rcqty + $tot_stock;
                            $rsale = $rsale + $saleqt;
                            $rpnorm = $rpnorm + $plac_qty;
                            $rgap = $rcqty - $rpnorm;
                            if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                        }else{ ?>
                            <tr style="background-color: #ffffcc" >
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                            </tr>
                            <?php   
                            $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                             $rcqty = $rcqty + $tot_stock;
                            $rsale = $rsale + $saleqt;
                            $rpnorm = $rpnorm + $plac_qty;
                            $rgap = $rcqty - $rpnorm;
                            if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                        }
                            ?>
                            <!--End Branch Sum-->
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $plac->zone_name;  ?></td>
                            <td><?php echo $pcnt;  ?></td>
                            <td><?php echo $brand_name; ?></td>
                            <td><?php echo $tot_stock; $tstk = $tstk + $tot_stock; ?></td>
                            <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                            <td><?php echo $plac_qty; $tplc = $tplc + $plac_qty;  ?></td>
                            <td><?php echo $gap;   ?></td>
                            <td><?php echo round($ach).'%';  ?></td>
                        </tr>
                   <?php $old_name = $plac->id_zone; } ?>
                     <tr style="background-color: #ffffcc" >
                        <td style="border-left: 1px solid #cccccc;"></td>
                        <td style="border-left: 1px solid #cccccc;"></td>     
                        <td style="border-left: 1px solid #cccccc;"></td>     
                        <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                        <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                        <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                        <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                        <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                        <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tstk; ?></b></td>
                        <td><b><?php echo $tsale; ?></b></td>
                        <td><b><?php echo $tplc; ?></b></td>
                        <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                        <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100;}else{ $tach = 0; }  echo round($tach).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
            <?php } else { ?>
                <table class="table table-bordered table-condensed text-center" id="brand_placement_norm_report">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                        <th style="text-align: center">Sr.</th>
                        <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Branch</th>
                        <th style="text-align: center">Promotor Count</th>
                        <th style="text-align: center">Brand</th>
                        <th style="text-align: center">Current Stock</th>
                        <th style="text-align: center">Last <?php echo $days;?> Days Sale</th>
                        <th style="text-align: center">Placement Norm</th>
                        <th style="text-align: center">Gap In Volume</th>
                        <th style="text-align: center">Ach In %</th>
                    </thead>
                    <tbody class="data_1">
                       <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0; $intra_qty=0; $tot_stock=0;
                       $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0; 
                       $plac_qty =0;
                       
                       $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                       
                       $old_name = $arr_data[0]->id_branch;
                       
                        foreach($arr_data as $plac){ 

                            if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                            if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                            if($plac->quantity){ $plac_qty = $plac->quantity; }else{ $plac_qty =0; }
                            if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                            $tot_stock = $stk_qty + $intra_qty;

                            $brand_name = $plac->brand_name; 
                            $pcnt = $plac->promotor;

                            $gap = $tot_stock - $plac_qty;
                            if($plac->quantity){ $ach = ($tot_stock / $plac_qty)*100;}else{ $ach=0;}     ?>
                            <?php 
                            //Branch Wise Sum
                            if($old_name == $plac->id_branch){
                                $rcqty = $rcqty + $tot_stock;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }else{ ?>
                                <tr style="background-color: #ffffcc" >
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                                </tr>
                                <?php   
                                $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                                 $rcqty = $rcqty + $tot_stock;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }
                            ?>
                            <!--End Branch Sum-->
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                    <td><?php echo $plac->zone_name;  ?></td>
                                    <td><?php echo $plac->branch_name;  ?></td>
                                <td><?php echo $pcnt;  ?></td>
                                <td><?php echo $brand_name; ?></td>
                                <td><?php echo $tot_stock; $tstk = $tstk + $tot_stock; ?></td>
                                <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                                <td><?php echo $plac_qty; $tplc = $tplc + $plac_qty;  ?></td>
                                <td><?php echo $gap;   ?></td>
                                <td><?php echo round($ach).'%';  ?></td>
                            </tr>
                       <?php $old_name=$plac->id_branch; } ?>
                            <tr style="background-color: #ffffcc" >
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                            </tr>
                            <!--Overall Total-->
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Overall Total</b></td>
                                <td><b><?php echo $tstk; ?></b></td>
                                <td><b><?php echo $tsale; ?></b></td>
                                <td><b><?php echo $tplc; ?></b></td>
                                <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                                <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100;}else{ $tach = 0; }  echo round($tach).'%'; ?></b></td>
                            </tr>
                            
                    </tbody>
                </table>
            <?php } } else{ ?>
            <script>
                alert("Placement Norms Not Set");
            </script>
        <?php } 
    }
    /*
    public function brand_placement_norms_report() { 
        $q['tab_active'] = '';
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $this->load->view('stock/brand_placement_norms_report', $q);
    }
    public function ajax_get_brand_stock_norms_report(){
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        
        $placement_data = $this->Stock_model->get_stock_placement_data_byid($product_category, $branch);
//        die('<pre>'.print_r($placement_data,1).'</pre>');
        if($placement_data){ ?> 
                <table class="table table-bordered table-condensed text-center" id="brand_placement_norm_report">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                         <th>Sr.</th>
                         <th>Promotor Count</th>
                         <th>Brand</th>
                         <th>Current Stock</th>
                         <th>Last <?php echo $days;?> Days Sale</th>
                         <th>Placement Norm</th>
                         <th>Gap In Volume</th>
                         <th>Ach In %</th>
                    </thead>
                    <tbody class="data_1">
                       <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0;
                       $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0;
                        foreach($placement_data as $plac){ 
                            $idcat = $plac->idcategory;
                            $idbrand = $plac->idbrand;
                            $branddata = $this->Stock_model->get_brand_wise_placement_norms_byidbrand($days,$product_category,$idbrand,$plac->idbranch, $idcat);
                            if($branddata->curr_stock){ $stk_qty = $branddata->curr_stock; }else{ $stk_qty =0; }
                            if($branddata->sale_qty){ $saleqt = $branddata->sale_qty; }else{ $saleqt =0; }
                           
                            if($plac->idbrand != 0 && $plac->idbrand != 76){ 
                               $brand_name = $plac->brand_name; 
                               $pcnt = $branddata->promotor;
                            }
                            if($plac->idcategory > 0){ 
                               $brand_name = $plac->category_name; 
                               $pcnt = '';
                            }
                            if($plac->idbrand == 76){ 
                               $brand_name = $plac->brand_name; 
                               $pcnt = $branddata->promotor;
                            }
                            
                            $gap = $stk_qty - $plac->quantity;
                            if($plac->quantity){ $ach = ($stk_qty / $plac->quantity)*100;}else{ $ach=0;}
                            ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo $pcnt;  ?></td>
                                <td><?php echo $brand_name; ?></td>
                                <td><?php echo $stk_qty; $tstk = $tstk + $stk_qty; ?></td>
                                <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                                <td><?php echo $plac->quantity; $tplc = $tplc + $plac->quantity;  ?></td>
                                <td><?php echo $gap; $tgap = $tgap + $gap;  ?></td>
                                <td><?php echo round($ach).'%';  $tach = $tach + $ach; ?></td>
                            </tr>
                       <?php } ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $tstk; ?></b></td>
                                <td><b><?php echo $tsale; ?></b></td>
                                <td><b><?php echo $tplc; ?></b></td>
                                <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                                <td><b><?php $tach = ($tstk/$tplc)*100;  echo round($tach).'%'; ?></b></td>
                            </tr>
                    </tbody>
                </table>
        <?php } else{ ?>
            <script>
                alert("Placement Norms Not Set");
            </script>
        <?php } 
        
    }
    
    */
     //price category placement norms
    public function price_category_placement_norms() { 
        $q['tab_active'] = '';
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_zone_branch_data();
        }
        $id_users=$this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $this->load->view('stock/price_category_placement_norms', $q);
    }
    
    public function ajax_get_price_category_stock_norms(){
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        $brand_data = $this->Stock_model->get_price_category_wise_stock($days,$product_category,$branch);
        $placement_data = $this->Stock_model->get_price_cat_placement_data($product_category, $branch, $days);
        
        $check_norm_data = $this->Stock_model->get_check_price_cat_data($product_category, $branch);
            
//        die('<pre>'.print_r($check_norm_data,1).'</pre>');
        if($check_norm_data){ ?> 
            <!--EDIT FORM-->
            <form>
                <table class="table table-bordered table-condensed text-center">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                        <th style="text-align: center">Sr.</th>
                        <th style="text-align: center">Price Category</th>
                        <th style="text-align: center">Current Stock</th>
                        <th style="text-align: center">Last <?php echo $days;?> Days Sale</th>
                        <th style="text-align: center">Placement Norm</th>
                    </thead>
                    <tbody class="data_1">
                       <?php $sr=1; $saleqt=0; $stk_qty=0; $tqty = 0;$t_cstk=0;$t_sale=0;$intra_qty=0;$tot_stock=0;
                       
                        foreach($placement_data as $plac){ 
                            if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                            if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                            if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                            $tot_stock = $stk_qty + $intra_qty;
                           ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $plac->lab_name; ?></td>
                            <td><?php echo $tot_stock; $t_cstk = $t_cstk + $tot_stock; ?></td>
                            <td><?php echo $saleqt; $t_sale = $t_sale + $saleqt;  ?></td>
                            <td><input type="text" class="form-control input-sm pqty text-center" id="pqty" name="qty[]" value="<?php if($plac->norm_qty){ echo $plac->norm_qty;}else{ echo '0'; }  $tqty = $tqty + $plac->norm_qty; ?>">
                                <input type="hidden" name="id_stock_norm[]" value="<?php echo $plac->id_price_category_norms; ?>">
                                <input type="hidden" class="form-control input-sm" id="idpricecat" name="idpricecat[]" value="<?php echo $plac->id_price_category_lab; ?>">
                            </td>
                        </tr>
                       <?php } ?>
                    </tbody>
                    <tr>
                        <td></td>
                        <td><b>TOTAL</b></td>
                        <td><b><?php echo $t_cstk; ?></b></td>
                        <td><b><?php echo $t_sale; ?></b></td>
                        <td><b><div class="tpqty" style="text-align: center;"><?php echo $tqty; ?></div></b></td>
                    </tr>
                </table>
                <input type="hidden" name="idbranch" value="<?php echo $branch; ?>">
                <input type="hidden" name="idproductcategory" value="<?php echo $product_category; ?>">
                <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Stock/update_price_category_norms">Update</button>
           </form>
           <script>
               $(document).on('change', 'input[id=pqty]', function() {
                       var  total_basic_sum = 0;
                        $('tr').each(function () {
                            $(this).find('.pqty').each(function () {
                                var total_basic = $(this).val();
                                if (!isNaN(total_basic) && total_basic.length !== 0) {
                                    total_basic_sum += parseFloat(total_basic);
                                }
                            });
                            $('.tpqty').html(total_basic_sum);
                       });
                    });
           </script>
        <?php }else{ 
            if($brand_data){ ?>
                <form>
                    <table class="table table-bordered table-condensed text-center">
                        <thead style="background-color: #84b8f7" class="fixedelementtop">
                            <th style="text-align: center">Sr.</th>
                            <th style="text-align: center">Price Category</th>
                            <th style="text-align: center">Current Stock</th>
                            <th style="text-align: center">Last <?php echo $days;?> Days Sale</th>
                            <th style="text-align: center">Placement Norm</th>
                         </thead>
                         <tbody class="data_1">
                             <?php $sr=1; $curr_stock = 0; $sale_qty =0; $intra_qty=0;$tot_stock=0;
                              $cstk=0;$sqty=0;
                              
                             foreach ($brand_data as $bdata){ 
                                if($bdata->curr_stock){ $curr_stock = $bdata->curr_stock; }else{ $curr_stock =0; }
                                if($bdata->sale_qty){ $sale_qty = $bdata->sale_qty; }else{ $sale_qty =0; }
                                if($bdata->intra_stock){ $intra_qty = $bdata->intra_stock; }else{ $intra_qty =0; }
                                $tot_stock = $curr_stock + $intra_qty;
                                 ?>
                                  <tr class="tr_brand">
                                      <td><?php echo $sr++; ?></td>
                                      <td><?php echo $bdata->lab_name; ?></td>
                                      <td><?php echo $tot_stock; $cstk = $cstk + $tot_stock;   ?></td>
                                      <td><?php echo $sale_qty; $sqty = $sqty + $sale_qty;   ?></td>
                                      <td><input type="text" class="form-control input-sm pnorm text-center" id="pnorm" name="pnorm[]" >
                                          <input type="hidden" class="form-control input-sm" id="idpricecat" name="idpricecat[]" value="<?php echo $bdata->id_price_category_lab; ?>">
                                      </td>
                                  </tr>
                             <?php } ?>
                         </tbody>
                         <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $cstk; ?></b></td>
                            <td><b><?php echo $sqty; ?></b></td>
                            <td><div class="ttpqty" style="text-align:center"></div></td>
                        </tr>
                     </table>
                     <input type="hidden" name="idbranch" value="<?php echo $branch; ?>">
                     <input type="hidden" name="idproductcategory" value="<?php echo $product_category; ?>">
                     <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Stock/save_price_category_norms">Submit</button>
                </form>
            <script>
                $(document).ready(function (){
                    $(document).on('change', '.pnorm', function() {
                        var  total_basic_sum = 0;
                         $('.tr_brand').each(function () {
                             $(this).find('.pnorm').each(function () {
                                 var total_basic = +$(this).val();
                                 if (!isNaN(total_basic) && total_basic.length !== 0) {
                                     total_basic_sum += parseFloat(total_basic);
                                 }
                             });
                             $('.ttpqty').html(total_basic_sum);
                        });
                    });
                });
            </script>
        <?php }  }
    }
    
    public function save_price_category_norms(){
        // die('<pre>'.print_r($_POST,1).'</pre>');
        $pnorms = $this->input->post('pnorm');
        $idpricecat = $this->input->post('idpricecat');
        $idbranch = $this->input->post('idbranch');
        $idproductcategory = $this->input->post('idproductcategory');
        
        for($i=0; $i< count($pnorms); $i++){
            $data = array(
                'idbranch' => $idbranch,
                'idpricecategory' => $idpricecat[$i],
                'idproductcategory' => $idproductcategory,
                'norm_qty' => $pnorms[$i],
                'date' => date('Y-m-d'),
                'created_by' => $_SESSION['id_users'],
            );
            $this->Stock_model->save_price_category_norms($data);
        }
        $this->session->set_flashdata('save_data', 'Placement Norms Saved Successfully !');
        redirect('Stock/price_category_placement_norms');
    }
    public function update_price_category_norms(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $qty = $this->input->post('qty');
        $id_stock_norm = $this->input->post('id_stock_norm');
        $idbranch = $this->input->post('idbranch');
        $idproductcategory = $this->input->post('idproductcategory');
        $idpricecat = $this->input->post('idpricecat');
        
        for($i=0; $i< count($qty); $i++){
            if($id_stock_norm[$i] != ''){
                $data = array(
                    'norm_qty' => $qty[$i],
                    'date' => date('Y-m-d'),
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Stock_model->update_price_category_norms($data, $id_stock_norm[$i]);
            }else{
                $data = array(
                    'idbranch' => $idbranch,
                    'idpricecategory' => $idpricecat[$i],
                    'idproductcategory' => $idproductcategory,
                    'norm_qty' => $qty[$i],
                    'date' => date('Y-m-d'),
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Stock_model->save_price_category_norms($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Placement Norms Updated Successfully !');
        redirect('Stock/price_category_placement_norms');
    }
    
    public function price_category_placement_norms_report() { 
        $q['tab_active'] = '';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['zone_data'] = $this->General_model->get_active_zone();
        $id_users = $this->session->userdata('id_users');
        $q['product_category'] = $this->General_model->get_product_category_by_user($id_users);
        if(count($q['product_category'])==0){
            $q['product_category'] = $this->General_model->get_product_category_data();
        }
        $this->load->view('stock/price_category_placement_norms_report', $q);
    }
       public function ajax_get_price_category_norms_report(){
//        die(print_r($_POST));
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        $allbranch = $this->input->post('allbranch');
        $idzone = $this->input->post('idzone');
        $zones = $this->input->post('zones');
//        die(print_r($_POST));
        $placement_data = $this->Stock_model->get_price_cat_placement_data_report($product_category, $branch, $days, $idzone, $allbranch, $zones);
//        die('<pre>'.print_r($placement_data,1).'</pre>');
        if($placement_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="price_category_placement_norm_report">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                         <th>Sr.</th>
                         <th>Price Category</th>
                         <th>Current Stock</th>
                         <th>Last <?php echo $days;?> Days Sale</th>
                         <th>Placement Norm</th>
                         <th>Gap In Volume</th>
                         <th>Ach In %</th>
                    </thead>
                    <tbody class="data_1">
                       <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0; $intra_qty =0; $tot_qty=0;
                        $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0;
                        foreach($placement_data as $plac){ 
                            if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                            if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                            if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                            $tot_qty = $stk_qty + $intra_qty;
                            $gap = $tot_qty - $plac->norm_qty;
                            if($plac->norm_qty){ $ach = ($tot_qty / $plac->norm_qty)*100;}else{ $ach=0;}
                            ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo $plac->lab_name;  ?></td>
                                <td><?php echo $tot_qty; $tstk = $tstk + $tot_qty; ?></td>
                                <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                                <td><?php if($plac->norm_qty){ echo $plac->norm_qty;}else{ echo '0'; } $tplc = $tplc + $plac->norm_qty;  ?></td>
                                <td><?php echo $gap;   ?></td>
                                <td><?php echo round($ach).'%';  ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $tstk; ?></b></td>
                            <td><b><?php echo $tsale; ?></b></td>
                            <td><b><?php echo $tplc; ?></b></td>
                            <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                            <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100; } else{ $tach = 0;}  echo round($tach).'%'; ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <?php }elseif($idzone == 'allzone'){?>
                <table class="table table-bordered table-condensed text-center" id="price_category_placement_norm_report">
                   <thead style="background-color: #84b8f7" class="fixedelementtop">
                        <th>Sr.</th>
                        <th>Zone</th>
                        <th>Price Category</th>
                        <th>Current Stock</th>
                        <th>Last <?php echo $days;?> Days Sale</th>
                        <th>Placement Norm</th>
                        <th>Gap In Volume</th>
                        <th>Ach In %</th>
                   </thead>
                   <tbody class="data_1">
                      <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0;
                       $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0;$plac_qty=0;
                       $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                       $intra_qty =0; $tot_qty=0;
                       $old_name = $placement_data[0]->id_zone;
                       foreach($placement_data as $plac){ 
                           if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                           if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                           if($plac->norm_qty){ $plac_qty = $plac->norm_qty; }else{ $plac_qty = 0; }
                           if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                           $tot_qty = $stk_qty + $intra_qty;
                           $gap = $tot_qty - $plac->norm_qty;
                           if($plac->norm_qty){ $ach = ($tot_qty / $plac_qty)*100;}else{ $ach=0;}
                         
                            //Branch Wise Sum
                            if($old_name == $plac->id_zone){
                                $rcqty = $rcqty + $tot_qty;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }else{ ?>
                                <tr style="background-color: #ffffcc" >
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                                </tr>
                                <?php   
                                $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                                $rcqty = $rcqty + $tot_qty;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }
                            ?>
                            <!--End Branch Sum-->
                           <tr>
                               <td><?php echo $sr++; ?></td>
                               <td><?php echo $plac->zone_name;  ?></td>
                               <td><?php echo $plac->lab_name;  ?></td>
                               <td><?php echo $tot_qty; $tstk = $tstk + $tot_qty; ?></td>
                               <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                               <td><?php echo $plac_qty; $tplc = $tplc + $plac->norm_qty;  ?></td>
                               <td><?php echo $gap;   ?></td>
                               <td><?php echo round($ach).'%';  ?></td>
                           </tr>
                       <?php  $old_name = $plac->id_zone; } ?>
                       <tr style="background-color: #ffffcc" >
                            <td style="border-left: 1px solid #cccccc;"></td>     
                            <td style="border-left: 1px solid #cccccc;"></td>     
                            <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                        </tr>
                       <tr>
                           <td></td>
                            <td></td>
                           <td><b>Total</b></td>
                           <td><b><?php echo $tstk; ?></b></td>
                           <td><b><?php echo $tsale; ?></b></td>
                           <td><b><?php echo $tplc; ?></b></td>
                           <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                           <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100; } else{ $tach = 0;}  echo round($tach).'%'; ?></b></td>
                       </tr>
                   </tbody>
               </table>
            <?php } else { ?>
                <table class="table table-bordered table-condensed text-center" id="price_category_placement_norm_report">
                    <thead style="background-color: #84b8f7" class="fixedelementtop">
                        <th>Sr.</th>
                        <th>Zone</th>
                        <th>Branch</th>
                        <th>Price Category</th>
                        <th>Current Stock</th>
                        <th>Last <?php echo $days;?> Days Sale</th>
                        <th>Placement Norm</th>
                        <th>Gap In Volume</th>
                        <th>Ach In %</th>
                    </thead>
                    <tbody class="data_1">
                       <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0;
                        $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0;$plac_qty=0;
                        $intra_qty =0;$tot_qty=0;
                        $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                        $old_name = $placement_data[0]->id_branch;
                        
                        foreach($placement_data as $plac){ 
                            if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                            if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                            if($plac->norm_qty){ $plac_qty = $plac->norm_qty; }else{ $plac_qty = 0; }
                            if($plac->intra_stock){ $intra_qty = $plac->intra_stock; }else{ $intra_qty =0; }
                           $tot_qty = $stk_qty + $intra_qty;
                           
                            $gap = $tot_qty - $plac->norm_qty;
                            if($plac->norm_qty){ $ach = ($tot_qty / $plac_qty)*100;}else{ $ach=0;}
                            
                            //Branch Wise Sum
                            if($old_name == $plac->id_branch){
                                $rcqty = $rcqty + $tot_qty;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }else{ ?>
                                <tr style="background-color: #ffffcc" >
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                                </tr>
                                <?php   
                                $rcqty =0; $rsale =0;$rpnorm =0; $rgap =0; $rach=0;
                                 $rcqty = $rcqty + $tot_qty;
                                $rsale = $rsale + $saleqt;
                                $rpnorm = $rpnorm + $plac_qty;
                                $rgap = $rcqty - $rpnorm;
                                if($rpnorm > 0){ $rach = (($rcqty/$rpnorm)*100); }else{ $rach = 0; }
                            }
                            ?>
                            <!--End Branch Sum-->
                            
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo $plac->zone_name;  ?></td>
                                <td><?php echo $plac->branch_name;  ?></td>
                                <td><?php echo $plac->lab_name;  ?></td>
                                <td><?php echo $tot_qty; $tstk = $tstk + $tot_qty; ?></td>
                                <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                                <td><?php echo $plac_qty; $tplc = $tplc + $plac_qty;  ?></td>
                                <td><?php echo $gap;   ?></td>
                                <td><?php echo round($ach).'%';  ?></td>
                            </tr>
                        <?php  $old_name = $plac->id_branch; } ?>
                        <tr style="background-color: #ffffcc" >
                            <td style="border-left: 1px solid #cccccc;"></td>     
                            <td style="border-left: 1px solid #cccccc;"></td>     
                            <td style="border-left: 1px solid #cccccc;"></td>     
                            <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rcqty;  ?></b></td>                                    
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rsale; ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rpnorm; ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo $rgap ?></b></td>
                            <td style="border-left: 1px solid #cccccc;"><b><?php echo round($rach).'%' ?></b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $tstk; ?></b></td>
                            <td><b><?php echo $tsale; ?></b></td>
                            <td><b><?php echo $tplc; ?></b></td>
                            <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                            <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100; } else{ $tach = 0;}  echo round($tach).'%'; ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <?php }
        } else{ ?>
            <script>
                alert("Placement Norms Not Set");
            </script>
        <?php } 
    }
    
    public function daily_stock_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->General_model->get_product_category_data();    
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('stock/daily_stock_report',$q);
    }
    public function ajax_daily_stock_report_byidbranch(){
        
        
        $from = $this->input->post('from');
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $allzone = $this->input->post('allzone'); 
        $q['tab_active'] = '';        
            
               
            if($monthyear){
             if($idbranch=='all'){ 
             $data = $this->Stock_model->get_stock_summary_accessories_API($monthyear,$idbranch);  
//             die('<pre>'.print_r($data,1).'</pre>');
             $daily_stock_data = $this->Stock_model->get_monthly_stock_data_manual($monthyear,$idpcat,$idbranch);  
             $backup_days = $this->Stock_model->get_stock_backup_days($monthyear,0);    
             $days=$backup_days->days;
                   if($idpcat>0){ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Zone</th>                    
                    <th>Branch</th>                    
                    <th>Branch Category</th>       
                    <th>Product Category</th>                    
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th> 
                    </thead>
                    <?php $qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){ 
                       
                        $q1=0;$q2=0;
                        $m1=0;$m2=0;
                        $l1=0;$l2=0;
                        if($stock->qty>0){
                            $q1=($stock->qty/$stock->days);
                        }
                        if($stock->intra_qty>0){
                            $q2=($stock->intra_qty/$stock->intra_days);
                        }
                        $q=round($q1+$q2);
                        if($stock->mop>0){
                            $m1=($stock->mop/$stock->days);
                        }
                        if($stock->intra_mop>0){
                            $m2=($stock->intra_mop/$stock->intra_days);
                        }
                        $m=round($m1+$m2);
                        if($stock->landing>0){
                            $l1=($stock->landing/$stock->days);
                        }
                        if($stock->intra_landing>0){
                            $l2=($stock->intra_landing/$stock->intra_days);
                        }
                        $l=round($l1+$l2); 
                        
                        ?>
                    <tr>
                        <td><?php echo $stock->zone_name ?></td>                                  
                        <td><?php echo $stock->branch_name ?></td>                                  
                        <td><?php echo $stock->branch_category_name ?></td>  
                        <td><?php echo $stock->product_category_name ?></td>  
                         <td><?php echo $q; ?></td>
                        <td><?php echo $m; ?></td>
                        <td><?php echo $l; ?></td>                        
                        <?php   $qty +=$q;
                                $mop +=$m;
                                $landing +=$l;
                        ?>
                    </tr>
                <?php } ?>
                     <tr>
                         <th colspan="4">Total</th>                        
                       <th><?php echo round($qty) ?></th>
                        <th><?php echo round($mop) ?></th>
                        <th><?php echo round($landing) ?></th>                         
                    </tr>
                <?php }else{ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Zone</th>                    
                    <th>Branch</th>                    
                    <th>Branch Category</th>       
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th>
                    <th>Acc-Qty</th>
                    <th>Acc-Offer Price</th>
                    </thead>
                    <?php $accqty=0;$accmop=0;$qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){
                        
                        $acc_stock=array();
                        $key= multiarraysearch($data, "idbranch", $stock->acc_branch_id);
                            if($key){
                                $acc_stock=$data[$key];                        
                            }
                        $q1=0;$q2=0;
                        $m1=0;$m2=0;
                        $l1=0;$l2=0;
                        if($stock->qty>0){
                            $q1=($stock->qty/$stock->days);
                        }
                        if($stock->intra_qty>0){
                            $q2=($stock->intra_qty/$stock->intra_days);
                        }
                        $q=round($q1+$q2);
                        if($stock->mop>0){
                            $m1=($stock->mop/$stock->days);
                        }
                        if($stock->intra_mop>0){
                            $m2=($stock->intra_mop/$stock->intra_days);
                        }
                        $m=round($m1+$m2);
                        if($stock->landing>0){
                            $l1=($stock->landing/$stock->days);
                        }
                        if($stock->intra_landing>0){
                            $l2=($stock->intra_landing/$stock->intra_days);
                        }
                        $l=round($l1+$l2); 
                        
                        ?>
                    <tr>
                        <td><?php echo $stock->zone_name ?></td>                                  
                        <td><?php echo $stock->branch_name ?></td>                                  
                        <td><?php echo $stock->branch_category_name ?></td>               
                        <td><?php echo $q; ?></td>
                        <td><?php echo $m; ?></td>
                        <td><?php echo $l; ?></td>      
                        <?php if(count($acc_stock)>0){?>
                        <td><?php echo $acc_stock['qty']; ?></td>
                        <td><?php echo $acc_stock['mop']; ?></td>      
                        <?php }else{ ?>
                        <td></td>      
                        <td></td>      
                        <?php } ?>
                        <?php   $qty +=$q;
                                $mop +=$m;
                                $landing +=$l;
                                if(count($acc_stock)>0){
                                    $accqty +=$acc_stock['qty'];
                                    $accmop +=$acc_stock['mop'];                                
                                }
                        ?>
                    </tr>
                <?php } ?>
                    <tr>
                        <th colspan="3">Total</th>                        
                        <th><?php echo round($qty) ?></th>
                        <th><?php echo round($mop) ?></th>
                        <th><?php echo round($landing) ?></th>    
                        <th><?php echo round($accqty) ?></th>
                        <th><?php echo round($accmop) ?></th>    
                    </tr>
           <?php     }
             }elseif($idbranch>0){ 
                $daily_stock_data = $this->Stock_model->get_monthly_stock_data_manual($monthyear,$idpcat,$idbranch);             
                $backup_days = $this->Stock_model->get_stock_backup_days($monthyear,$idbranch);    
                $days=$backup_days->days;
                ?>
            <thead style="background-color: #84b8f7">
                <th><b> Branch Name :- <?php echo $daily_stock_data[0]->branch_name; ?></b></th>
                <th colspan="2"><b> Branch Category :- <?php echo $daily_stock_data[0]->branch_category_name; ?></b></th>
                <th colspan="2"<b> Zone Name :- <?php echo $daily_stock_data[0]->zone_name; ?></b></th> 
            </thead>
               <?php if($idpcat>0){ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Product Category</th>
                    <th>Brand</th>
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th> 
                    </thead>
                    <?php $qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){ ?>
                    <tr>
                        <td><?php echo $stock->product_category_name ?></td>
                        <td><?php echo $stock->brand_name ?></td>                        
                        <td><?php echo round($stock->qty/$days) ?></td>
                        <td><?php echo round($stock->mop/$days) ?></td>
                        <td><?php echo round($stock->landing/$days) ?></td>
                        <?php   $qty +=($stock->qty/$days);
                                $mop +=($stock->mop/$days);
                                $landing +=($stock->landing/$days);
                        ?>
                    </tr>
                <?php } ?>
                     <tr>
                         <th colspan="2">Total</th>                        
                       <th><?php echo round($qty) ?></th>
                        <th><?php echo round($mop) ?></th>
                        <th><?php echo round($landing) ?></th>                         
                    </tr>
                <?php }else{ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Product Category</th>
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th> 
                    </thead>
                    <?php $qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){ ?>
                    <tr>
                        <td><?php echo $stock->product_category_name ?></td>                        
                        <td><?php echo round($stock->qty/$days) ?></td>
                        <td><?php echo round($stock->mop/$days) ?></td>
                        <td><?php echo round($stock->landing/$days) ?></td>
                        <?php   $qty +=($stock->qty/$days);
                                $mop +=($stock->mop/$days);
                                $landing +=($stock->landing/$days);
                        ?>
                    </tr>
                <?php } ?>
                    <tr>
                        <th>Total</th>                        
                        <th><?php echo round($qty) ?></th>
                        <th><?php echo round($mop) ?></th>
                        <th><?php echo round($landing) ?></th>                        
                    </tr>
           <?php     }
            }else{ ?>
            <div class="col-md-10"> <h5> Data not found</h5> </div>
        <?php }
            
            
            }else if($from){
            $daily_stock_data = $this->Stock_model->get_daily_stock_data_manual($from,$idpcat,$idbranch);             
//            die('<pre>'.print_r($daily_stock_data,1).'</pre>');
            if(count($daily_stock_data)>0){
            ?>
            
            
            <?php if($idbranch>0){ ?>
            <thead style="background-color: #84b8f7">
                <th><b> Branch Name :- <?php echo $daily_stock_data[0]->branch_name; ?></b></th>
                <th colspan="2"><b> Branch Category :- <?php echo $daily_stock_data[0]->branch_category_name; ?></b></th>
                <th colspan="2"<b> Zone Name :- <?php echo $daily_stock_data[0]->zone_name; ?></b></th> 
            </thead>
               <?php if($idpcat>0){ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Product Category</th>
                    <th>Brand</th>
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th> 
                    </thead>
                    <?php $qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){ ?>
                    <tr>
                        <td><?php echo $stock->product_category_name ?></td>
                        <td><?php echo $stock->brand_name ?></td>                        
                        <td><?php echo $stock->qty ?></td>
                        <td><?php echo $stock->mop ?></td>
                        <td><?php echo $stock->landing ?></td>
                        <?php   $qty +=$stock->qty;
                                $mop +=$stock->mop;
                                $landing +=$stock->landing;
                        ?>
                    </tr>
                <?php } ?>
                     <tr>
                         <th colspan="2">Total</th>                        
                        <th><?php echo $qty ?></th>
                        <th><?php echo $mop ?></th>
                        <th><?php echo $landing ?></th>                        
                    </tr>
                <?php }else{ ?>
                    <thead style="background-color: #84b8f7">
                    <th>Product Category</th>
                    <th>Qty</th>
                    <th>MOP</th>
                    <th>Landing</th> 
                    </thead>
                    <?php $qty=0;$mop=0;$landing=0; foreach ($daily_stock_data as $stock){ ?>
                    <tr>
                        <td><?php echo $stock->product_category_name ?></td>                        
                        <td><?php echo $stock->qty ?></td>
                        <td><?php echo $stock->mop ?></td>
                        <td><?php echo $stock->landing ?></td>
                        <?php   $qty +=$stock->qty;
                                $mop +=$stock->mop;
                                $landing +=$stock->landing;
                        ?>
                    </tr>
                <?php } ?>
                    <tr>
                        <th>Total</th>                        
                        <th><?php echo $qty ?></th>
                        <th><?php echo $mop ?></th>
                        <th><?php echo $landing ?></th>                        
                    </tr>
           <?php     }
            }
            ?>
             
           
        <?php }else{ ?>
            <div class="col-md-10"> <h5> Data not found</h5> </div>
        <?php }
    }
        
    }
    
    /*
    public function ajax_get_price_category_norms_report(){
//        die(print_r($_POST));
        $days = $this->input->post('days');
        $product_category = $this->input->post('product_category');
        $branch = $this->input->post('branch');
        $allbranch = $this->input->post('allbranch');
        $idzone = $this->input->post('idzone');
//        die(print_r($_POST));
        $placement_data = $this->Stock_model->get_price_cat_placement_data_report($product_category, $branch, $days, $idzone, $allbranch);
//        die('<pre>'.print_r($placement_data,1).'</pre>');
        if($placement_data){ ?> 
            <table class="table table-bordered table-condensed text-center" id="price_category_placement_norm_report">
                <thead style="background-color: #84b8f7" class="fixedelementtop">
                     <th>Sr.</th>
                     <?php if($idzone != 'all'){ ?>
                        <th>Zone</th>
                        <th>Branch</th>
                     <?php } ?>
                     <th>Price Category</th>
                     <th>Current Stock</th>
                     <th>Last <?php echo $days;?> Days Sale</th>
                     <th>Placement Norm</th>
                     <th>Gap In Volume</th>
                     <th>Ach In %</th>
                </thead>
                <tbody class="data_1">
                   <?php $sr=1; $pcnt=0; $saleqt=0; $stk_qty=0;$gap=0; $ach=0;
                    $tstk=0;$tsale=0;$tgap=0;$tach=0;$tplc=0;
                    foreach($placement_data as $plac){ 
                        if($plac->curr_stock){ $stk_qty = $plac->curr_stock; }else{ $stk_qty =0; }
                        if($plac->sale_qty){ $saleqt = $plac->sale_qty; }else{ $saleqt =0; }
                        $gap = $stk_qty - $plac->norm_qty;
                        if($plac->norm_qty){ $ach = ($stk_qty / $plac->norm_qty)*100;}else{ $ach=0;}
                        ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <?php if($idzone != 'all'){ ?>
                                <td><?php echo $plac->zone_name;  ?></td>
                                <td><?php echo $plac->branch_name;  ?></td>
                            <?php } ?>
                            <td><?php echo $plac->lab_name;  ?></td>
                            <td><?php echo $stk_qty; $tstk = $tstk + $stk_qty; ?></td>
                            <td><?php echo $saleqt; $tsale = $tsale + $saleqt;  ?></td>
                            <td><?php if($plac->norm_qty){ echo $plac->norm_qty;}else{ echo '0'; } $tplc = $tplc + $plac->norm_qty;  ?></td>
                            <td><?php echo $gap;   ?></td>
                            <td><?php echo round($ach).'%';  ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <?php if($idzone != 'all'){ ?>
                            <td></td>
                            <td></td>
                        <?php } ?>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tstk; ?></b></td>
                        <td><b><?php echo $tsale; ?></b></td>
                        <td><b><?php echo $tplc; ?></b></td>
                        <td><b><?php $tgap = $tstk - $tplc; echo $tgap; ?></b></td>
                        <td><b><?php if($tplc > 0){ $tach = ($tstk/$tplc)*100; } else{ $tach = 0;}  echo round($tach).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
        <?php } else{ ?>
            <script>
                alert("Placement Norms Not Set");
            </script>
        <?php } 
    } */
}
    