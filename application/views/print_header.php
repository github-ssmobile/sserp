<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>ERP</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <?= link_tag("assets/css/bootstrap.css") ?>
    <?= link_tag("assets/css/signinstyle.css") ?>
    <?= link_tag("assets/css/font-awesome.min.css") ?>
    <?= link_tag("assets/material_font/css/materialdesignicons.css") ?>
    <?= link_tag("assets/css/kurale-font.css") ?>
    <?= link_tag("assets/css/style.css") ?>
    <?=  link_tag("assets/waves/btnwave.css") ?>
    <?=  link_tag("assets/creative_font/css/pe-icon-7-stroke.css") ?>
    <?=  link_tag("assets/creative_font/css/helper.css") ?>
    <?= link_tag("assets/css/tipped.css") ?>    
    <?= link_tag("assets/css/k2d.css") ?>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet"> 
    <?= link_tag("assets/css/sidebar.css") ?>
    <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
    <?= link_tag("assets/css/new-datetimepicker.css") ?>   
    <?= link_tag("assets/css/choosen.css") ?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/choosen.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    <script src="<?php echo site_url();?>assets/js/new-datetimepicker.js" type="text/javascript"></script>
    <script src="<?php echo site_url();?>assets/ckeditor/ckeditor.js" type="text/javascript" ></script>
    <style>.btn{ margin: 0; }</style>
</head>
<body style="
      font-family: 'Nunito Sans', sans-serif;
      /*font-family: 'Roboto', sans-serif;*/
    /*font-size: 17px;*/
    font-weight: 400;">
    <!--<nav class="navbar hovereffect1 navbar-default navbar-expand-l" role="navigation" style="margin: 0; padding-bottom: 5px; height: auto; border: none; border-radius: 0;background-image: url(<?php echo base_url()?>assets/images/header.jpg)">-->
    <div style="position: absolute; z-index: 999; right: 0"><div id="google_translate_element"></div></div>
<?php include_once 'extras.php'; ?>
<div class="container-fluid">
<div class="row">
    <div class="wrapper" style="display:block;">    
    <div class="tab-content" id="content">
        <?php // die('<pre>'.print_r($menus,1).'</pre>'); 
        if( $save = $this->session->flashdata('save_data')): ?>
            <div class="alert alert-dismissible alert-info" id="alert-dismiss">
                <?= $save ?>
            </div>
        <?php endif; ?>
        <?php if( $save = $this->session->flashdata('reject_data')): ?>
            <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
                <?= $save ?>
            </div>
        <?php endif; ?>
        