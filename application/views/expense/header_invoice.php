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
    <nav class="navbar navbar-default navbar-expand-l header_new" role="navigation" style="">
        <div class="navbar-header" role="navigation" style=" margin-left: 0px; margin-top: 0px; margin-bottom: 0; padding-bottom: 0">
            <a class="waves-effect waves-block waves-light" href="<?php echo base_url();?>" style="font-size: 34px; line-height: 25px; letter-spacing: 1px; color: #000; font-family: Kurale; height: auto;margin-top: 5px; margin-bottom: 0; padding-bottom: 0; margin-left: 10px;">
                <img height="65" src="<?php echo base_url()?>assets/images/logo.jpg" />
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
<?php include __DIR__.'../../extras.php'; ?>