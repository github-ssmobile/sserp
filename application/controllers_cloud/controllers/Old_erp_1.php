<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Old_erp_1 extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Old_erp_model');
    }
    function index() {
        $q['tab_active'] = '';
        $q['daily_stock_data'] = $this->Old_erp_model->get_daily_stock_data();
        $q['daily_transit_stock_data'] = $this->Old_erp_model->get_daily_transit_stock_data();
        $this->load->view('old_erp/daily_stock_backup', $q);
    }
}