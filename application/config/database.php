<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'kconstruction';
$query_builder = TRUE;

$db['kconstruction'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => 'erp#server',
	'database' => 'erp_new',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
   