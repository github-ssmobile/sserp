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
    <!--<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">--> 
    <?= link_tag("assets/css/sidebar.css") ?>
    <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
    <?= link_tag("assets/css/new-datetimepicker.css") ?>   
    <?= link_tag("assets/css/datepicker.css") ?>   
    <?= link_tag("assets/css/choosen.css") ?>
    <?= link_tag("assets/css/sweet-alert.css") ?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/choosen.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    <script src="<?php echo site_url();?>assets/js/datepicker.js" type="text/javascript"></script>
    <script src="<?php echo site_url();?>assets/js/new-datetimepicker.js" type="text/javascript"></script>
    <script src="<?php echo site_url();?>assets/ckeditor/ckeditor.js" type="text/javascript" ></script>
     <script src="<?php echo base_url();?>assets/js/jquery.validate.min.js" type="text/javascript" ></script>
    <script src="<?php echo base_url();?>assets/js/common_functions.js"></script> 
  
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <style>.btn{ margin: 0; }</style>
<script>
       base_url='<?php echo base_url();?>';
        $(document).ready(function(){
            
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.onkeydown = function(e) {
            if(e.keyCode == 123) {
               return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
               return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
               return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
               return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
               return false;
            }
          }

        });
    </script>
</head>
<body>
    <!--<nav class="navbar hovereffect1 navbar-default navbar-expand-l" role="navigation" style="margin: 0; padding-bottom: 5px; height: auto; border: none; border-radius: 0;background-image: url(<?php echo base_url()?>assets/images/header.jpg)">-->
    <nav class="navbar navbar-default navbar-expand-l header_new" role="navigation" style="">
        <div class="navbar-header" role="navigation" style=" margin-left: 0px; margin-top: 0px; margin-bottom: 0; padding-bottom: 0">
            <a class="waves-effect waves-block waves-light" href="<?php echo base_url();?>" style="font-size: 34px; line-height: 25px; letter-spacing: 1px; color: #000; font-family: Kurale; height: auto;margin-top: 5px; margin-bottom: 0; padding-bottom: 0; margin-left: 10px;">
                <img height="65" src="<?php echo base_url()?>assets/images/sslogo.jpg" />
                <!--<strong>ERP</strong>-->
            </a>
        </div>
        <a class="navbar-toggler pull-right btn btn-sm waves-effect waves-purple" data-toggle="collapse" data-target="#header1" aria-controls="header1" aria-expanded="false" aria-label="Toggle navigation" style="top: 5px; right: 5px; position: absolute; color: wheat; opacity: .5 ">
            <span class="fa fa-bars fa-2x"></span>
        </a>
        <div class="collapse navbar-collapse" id="header1">
            <ul class="nav navbar-nav navbar-right" style="padding-right: 10px; margin: 10px;">
                <?php if($this->session->userdata('userid')){ ?>
                <li><a class="black-text">Welcome <?php echo $this->session->userdata('userid'); ?></a></li>
                <li><a class="waves-effect waves-ripple black-text" href="<?php echo base_url($this->session->userdata('dashboard'));?>">Home</a></li>
                <li><a class="waves-effect waves-ripple black-text" href="<?php echo base_url('Login/logout');?>">Logout <span class="fa fa-sign-out"></span></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <div style="position: absolute; z-index: 999; right: 0"><div id="google_translate_element"></div></div>
<?php include_once 'extras.php'; ?>
<div class="container-fluid">
<div class="row">
<div class="wrapper">
    <nav id="sidebar" class="nav">
        <!--<div class="sidebar-header">-->
            <!--<h4><img src="<?php // echo site_url('assets/images/sslogo.jpg')?>" style="width: 100%" /></h4>-->
        <!--</div>-->
        <ul class="list-unstyled components">
            <!--<li class="<?php// if($tab_active == ''){ ?>active<?php// } ?>"><a class="waves-effect waves-teal" href="<?php //echo base_url($_SESSION['dashboard']) ?>"><i class="fa fa-home fa-lg"></i>Home</a></li>-->
            <?php   
            if(!isset($_SESSION['menus'])){
                return redirect('Login');
            }
            if(count($_SESSION['menus'])>0){ 
                foreach ($_SESSION['menus'] as $menu) {                         
                    if(count($menu['submenu'])>0){ ?>
                    <li class="nav-item">
                        <a class="nav-link collapsed waves-block waves-effect" href="javascript:void(0)" data-toggle="collapse" data-target="#submenu1sub<?php echo $menu['id_menu'] ?>"><i class="<?php echo $menu['font'] ?> fa-lg"></i> <?php echo $menu['menu'] ?> <span class="pe pe-7s-angle-down fa-lg" style="position: absolute; right: 7px"></span></a>
                        <div class="collapse" id="submenu1sub<?php echo $menu['id_menu'] ?>">
                            <ul class="nav" style="margin-left: 10px;">
                                <?php foreach ($menu['submenu'] as $submenu){ ?>
                                <li class="nav-item <?php if($tab_active == $submenu->submenu){ ?>active<?php } ?>">
                                    <a class="nav-link waves-effect waves-block" href="<?php echo base_url($submenu->url) ?>">
                                        <i class="<?php echo $submenu->font ?> fa-lg"></i> <?php echo $submenu->submenu ?> 
                                    </a>
                                </li>
                                <?php }?>                                           
                            </ul>
                        </div>
                    </li>
                    <?php  }else{
                         if($menu['url']!=null){ ?>
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-block" href="<?php echo base_url($menu['url']) ?>">
                                <i class="<?php echo $menu['font'] ?> fa-lg"></i> <?php echo $menu['menu'] ?> </a>
                            </li>
                    <?php } ?>                            
                <?php } ?>
            <?php } ?>
        <?php } ?>              
<!--            <li class="nav-item" style="padding: 5px;margin-top: 50px">
                <img class="thumbnail" src="<?php echo site_url('assets/images/Nr9k.gif')?>" style="width: 100%;" />
            </li>-->
            <!--<li class="nav-item" style="padding: 5px;margin-top: 50px">-->
                <?php // include 'charts/clock.php'; ?>
            <!--</li>-->
        </ul>
    </nav>
    <div class="tab-content" id="content">
        <?php if($save = $this->session->flashdata('save_data')): ?>
            <div class="alert alert-dismissible alert-info" id="alert-dismiss">
                <?= $save ?>
            </div>
        <?php endif; ?>
        <?php if( $save = $this->session->flashdata('reject_data')): ?>
            <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
                <?= $save ?>
            </div>
        <?php endif; ?>
        <div class="col-md-1 pull-left">
            <a id="sidebarCollapse" class="waves-effect"><i class="fa fa-bars fa-lg"></i></a>
        </div>