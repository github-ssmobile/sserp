<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata'); ?>
<!--        <script>
            if(navigator.userAgent.indexOf("Chrome") != -1 )
            {
                if(confirm('Not able to use chrome browser!! Use Fireforx browser...')){
                    window.location = '';
                }else{
                    window.location = '';
                }
            }
        </script>-->
    <?php     
    }

    public function index()
    {
        if($this->session->userdata('dashboard')){
            return redirect($this->session->userdata('dashboard'));
        }
        $this->load->view('login');
    }
    public function new_login()
    {
        $this->load->view('login_animation');
    }
    
    public function sweet_alert() {
        $this->load->view('sweet_alert');
    }
    
    public function verify_login() {
        $userid = $this->input->post('email');
        $password = $this->input->post('password');
        $q['h'] = NULL;
        $q['h'] = $this->Login_model->login($userid, $password);        
        if(isset($q) && $q['h']->num_rows() > 0) {
            foreach ($q['h']->result() as $row) {
//                if(!$row->active){
//                    $this->session->set_flashdata('reject_data', 'Your account has been deactivated!');
//                    return redirect();
//                }
                $this->session->item;
                $this->session->set_userdata('userid',$row->userid);
                $this->session->set_userdata('id_users',$row->id_users);
                $this->session->set_userdata('idrole',$row->iduserrole);
                $this->session->set_userdata('idbranch',$row->idbranch);
                $this->session->set_userdata('level',$row->level);
                $this->session->set_userdata('role_type',$row->role_type);
                $this->session->set_userdata('role_name',$row->role);
                $this->session->set_userdata('branch_name',$row->branch_name);
                $this->session->set_userdata('branch_warehouse',$row->idwarehouse);
                $this->session->set_userdata('branch_code',$row->branch_code);
                $this->session->set_userdata('expense_allowed',$row->expense_allowed);
                $this->session->set_userdata('direct_inward',$row->allow_purchase_direct_inward);
                $menus = $this->General_model->get_userrole_has_menu_byrole($row->iduserrole);                  
                $this->session->set_userdata('menus',$menus);
                $this->session->set_userdata('dashboard',$row->home);
                return redirect($row->home);
            }
            $this->session->set_flashdata('reject_data', 'Please enter valid Userid & Password!');
            return redirect();
        }
        $this->session->set_flashdata('reject_data', 'Please enter valid Userid & Password!');
        return redirect();
    }
    public function logout() {
//        $this->Login_model->erase_login_time($this->session->userdata('userid'));
        $this->session->unset_userdata('userid');
        $this->session->unset_userdata('idrole');
        $this->session->unset_userdata('id_users');
        $this->session->unset_userdata('idbranch');
        $this->session->unset_userdata('branch_name');
        $this->session->unset_userdata('level');
        $this->session->sess_destroy();
        $this->session->set_flashdata('save_data', 'Log Out Successfully');
        return redirect();
    }
	public function get_daily_stock_backup(){
		$this->load->model('Old_erp_model');
        $q['tab_active'] = '';
        $q['daily_stock_data'] = $this->Old_erp_model->get_daily_stock_data();
        $q['daily_transit_stock_data'] = $this->Old_erp_model->get_daily_transit_stock_data();
        $this->load->view('old_erp/daily_stock_backup', $q);
    }
    public function forgot_password() {
        $data = array(
        'password' => $this->input->post('password'),
        );
        $change = $this->Login_model->forgot_password($this->input->post('userid'),$this->input->post('mobile'),$data);
        if($change){
            $this->session->set_flashdata('save_data', 'New Password Changed Successfully');
        }
        elseif(!$change){
            $this->session->set_flashdata('logout1', 'Enter correct userid or mobile. Password Not Changed');
        }
        return redirect('Master');
    }
    public function role_menu() {
        $this->load->view('role_menu');
    }
    public function login1() {
        $this->load->view('login_1_1');
    }
    public function pie() {
        $this->load->view('charts/pie');
    }
    public function clock() {
        $this->load->view('charts/clock');
    }
    public function donut() {
        $this->load->view('charts/donut');
    }
    public function donut_gradient() {
        $this->load->view('charts/donut_gradient');
    }
    public function pie_animated() {
        $this->load->view('charts/pie_animated');
    }
    public function donut_3d() {
        $this->load->view('charts/donut_3d');
    }
    public function bars_balance() {
        $this->load->view('charts/bars_balance');
    }
    public function bars_3d() {
        $this->load->view('charts/new_balancesheet');
    }
    public function simple_donut() {
        $this->load->view('charts/simple_donut');
    }
    public function new_pie() {
        $this->load->view('charts/new_pie');
    }
    public function month_bars() {
        $this->load->view('charts/month_bars');
    }
}