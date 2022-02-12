<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['profiler'] = "Profiler_controller"; 
$route['profiler/disable'] = "Profiler_controller/disable";
$route['email'] = 'Email_test';
$route['validation'] = 'Form';
$route['uri-(:any)/(:num)'] = "executive_controller/approve/$1/$2"; //(or) $route['uri-(:any)'] = "controller/function/$1";



$route['branch_details'] = 'Master/branch_details';
$route['branch_details/(:any)'] = 'Master/branch_details/$1';
$route['branch-info-details'] = 'Master/branch_info_details';
$route['branch-shopact-gst'] = 'Cost_center/branchShopactGst';
$route['branch-shopact-gst/(:any)'] = 'Cost_center/branchShopactGst/$1';
$route['branch-shopact-gst-store'] = 'Cost_center/branchShopactGstStore';

$route['branch-final-doc-rent-cp'] = 'Cost_center/branchFinalDocRentCp';
$route['branch-final-doc-rent-cp/(:any)'] = 'Cost_center/branchFinalDocRentCp/$1';
$route['branch-final-doc-rent-cp-store'] = 'Cost_center/branchFinalDocRentCpStore';

$route['branch_information/(:any)'] = 'Cost_center/branch_information/$1';
$route['branch_basic_details'] = 'Cost_center/branch_basic_details';
$route['branch_basic_details/(:any)'] = 'Cost_center/branch_basic_details/$1';
$route['branch_basic_details_store'] = 'Cost_center/branch_basic_details_store';
$route['branch_rent_details'] = 'Cost_center/branch_rent_details';
$route['branch_rent_details_legal'] = 'Cost_center/branch_rent_details_legal';
$route['branch_rent_details/(:any)'] = 'Cost_center/branch_rent_details/$1';
$route['branch_rent_details/(:any)/(:any)'] = 'Cost_center/branch_rent_details/$1/$1';
$route['branch_rent_details_store'] = 'Cost_center/branch_rent_details_store';

$route['branch_cp_details'] = 'Cost_center/branch_cp_details';
$route['branch_cp_details/(:any)'] = 'Cost_center/branch_cp_details/$1';
$route['branch_cp_details/(:any)/(:any)'] = 'Cost_center/branch_cp_details/$1/$1';
$route['branch_cp_details_store'] = 'Cost_center/branch_cp_details_store';

$route['branch_insurence_details'] = 'Cost_center/branch_insurence_details';
$route['branch_insurence_details/(:any)'] = 'Cost_center/branch_insurence_details/$1';
$route['branch_insurence_details_store'] = 'Cost_center/branch_insurence_details_store';

$route['branch_mbb_details'] = 'Cost_center/branch_mbb_details';
$route['branch_mbb_details/(:any)'] = 'Cost_center/branch_mbb_details/$1';
$route['branch_mbb_details_store'] = 'Cost_center/branch_mbb_details_store';
$route['branch_electricity_details/(:any)'] = 'Cost_center/branch_electricity_details/$1';

$route['branch_rent_cp_deposit'] = 'Cost_center/branch_rent_cp_deposit';
$route['get_branch_rent_cp_deposit_data'] = 'Cost_center/get_branch_rent_cp_deposit_data';
$route['save_branch_rent_cp_details'] = 'Cost_center/save_branch_rent_cp_details';
$route['approve_cofo_branch'] = 'Cost_center/approve_cofo_branch';
$route['receive_cofo_branch_deposit'] = 'Cost_center/receive_cofo_branch_deposit';
$route['pay_branch_deposit'] = 'Cost_center/pay_branch_deposit';

$route['download_expense_format'] = 'Cost_center/download_expense_format';
$route['upload_expense_format'] = 'Costing/ajax_get_branch_costing_data_uploadedxl';

$route['vendor_create_interior'] = 'Master/vendor_create_interior';
$route['vendor_create_interior/(:any)'] = 'Master/vendor_create_interior/$1';
$route['vendor_create_insurence'] = 'Master/vendor_create_insurence';
$route['vendor_create_insurence/(:any)'] = 'Master/vendor_create_insurence/$1';
$route['vendor_create_store'] = 'Master/vendor_create_store';

$route['cost_header_month_data'] = 'Cost_center/cost_header_month_data';
$route['ajax_get_costing_data_for_month'] = 'Cost_center/ajax_get_costing_data_for_month';
$route['save_cost_header_month_data'] = 'Cost_center/save_cost_header_month_data';
$route['invoice_whatspp_api_routes/(:any)'] = 'Invoice_whatsapp_api/invoice_whatsapp_api/$1';