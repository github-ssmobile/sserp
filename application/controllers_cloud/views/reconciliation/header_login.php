<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Ipalace</title>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <?= link_tag("assets_ecom/css/bootstrap.css") ?>
    <?= link_tag("assets_ecom/css/signinstyle.css") ?>
    <?= link_tag("assets_ecom/css/font-awesome.min.css") ?>
     <!--link_tag("assets_ecom/waves/btnwave.css") ?>-->
    <?= link_tag("assets_ecom/material_font/css/materialdesignicons.css") ?>
    <?= link_tag("assets/css/kurale-font.css") ?>
     <?= link_tag("assets_ecom/css/k2d.css") ?>
    <?= link_tag("assets_ecom/css/style.css") ?>
    <?= link_tag("assets_ecom/css/tipped.css") ?>
    <?= link_tag("assets/css/datepicker.css") ?>
    <?= link_tag("assets/css/sidebar.css") ?>
    <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
    <?= link_tag("assets/css/new-datetimepicker.css") ?>
    <script src="<?php echo site_url() ?>assets_ecom/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets_ecom/js/choosen.js"></script>
    <script src="<?php echo site_url() ?>assets_ecom/js/jquery.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo site_url();?>assets/js/datepicker.js" type="text/javascript"></script>
    <script src="<?php echo site_url();?>assets/js/new-datetimepicker.js" type="text/javascript"></script>
</head>
<style>
    .hovereffect1 {
      background: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
      -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 10px rgba(0, 0, 0, 0.24);
      webkit-transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
      transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
<body style="background-color: #f8f9fc;" >
    <!--onbeforeunload=" return 'Are you really want to perform the action?'"-->
    <nav class="navbar hovereffect1 navbar-default navbar-expand-lg" role="navigation" style="margin: 0; padding-bottom: 5px; height: auto; border: none; border-radius: 0; background-image: linear-gradient(to right, #15ccd3, #0ccebc, #3dce9f, #3dce9f, #8ac85b);">
        <div class="navbar-header" role="navigation" style=" margin-left: 20px; margin-top: 0px; margin-bottom: 0; padding-bottom: 0">
            <img height="70" src="<?php echo base_url()?>assets/images/alogo.png" />
            <a class="waves-effect waves-ripple" href="<?php echo base_url();?>" style="font-size: 32px; line-height: 25px; letter-spacing: 1px; font-family: Kurale; color: #000; height: auto; margin-bottom: 0; padding-bottom: 0; margin-left: 10px;"><strong> I</strong>Palace</a>
        </div>
        <a class="navbar-toggler pull-right btn btn-sm waves-effect waves-purple" data-toggle="collapse" data-target="#header1" aria-controls="header1" aria-expanded="false" aria-label="Toggle navigation" style="top: 5px; right: 5px; position: absolute; color: wheat; opacity: .5 ">
            <span class="fa fa-bars fa-2x"></span>
        </a>
        <div class="collapse navbar-collapse" id="header1">
            <ul class="nav navbar-nav navbar-right" style="padding-right: 10px; margin: 10px;">
                <?php if($this->session->userdata('userid')){ ?>
                <li><a class="black-text">Welcome <?php echo $this->session->userdata('userid'); ?></a></li>    
                <li><a class="waves-effect waves-ripple black-text" href="<?php echo base_url('Master/user_dashboard');?>">Home</a></li>
                <li><a class="waves-effect waves-ripple black-text" href="<?php echo base_url('Login/logout');?>">Logout <span class="fa fa-sign-out"></span></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <script>
        window.setTimeout(function() {
            $("#alert-dismiss").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
        $(document).ajaxStart(function() {
            $('.img').show(); // show the gif image when ajax starts
        }).ajaxStop(function() {
            $('.img').hide(); // hide the gif image when ajax completes
        });
    </script> 
<div class="">
<div class="">
<div class="">
<style>
.pinkBg {
    background-color: #ed184f!important;
    background-image: linear-gradient(90deg, #fd5581, #fd8b55);
    z-index: 999999;
}
.intro-banner-vdo-play-btn{
    height:60px;
    width:60px;
    position:fixed;
    top:50%;
    left:50%;
    text-align:center;
    margin:-30px 0 0 -30px;
    border-radius:100px;
    z-index:1
}
.intro-banner-vdo-play-btn i{
    line-height:56px;
    font-size:30px
}
.intro-banner-vdo-play-btn .ripple{
    position:absolute;
    width:160px;
    height:160px;
    z-index:-1;
    left:50%;
    top:50%;
    opacity:0;
    margin:-80px 0 0 -80px;
    border-radius:100px;
    -webkit-animation:ripple 1.8s infinite;
    animation:ripple 1.8s infinite
}

@-webkit-keyframes ripple{
    0%{
        opacity:1;
        -webkit-transform:scale(0);
        transform:scale(0)
    }
    100%{
        opacity:0;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
@keyframes ripple{
    0%{
        opacity:1;
        -webkit-transform:scale(0);
        transform:scale(0)
    }
    100%{
        opacity:0;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
.intro-banner-vdo-play-btn .ripple:nth-child(2){
    animation-delay:.3s;
    -webkit-animation-delay:.3s
}
.intro-banner-vdo-play-btn .ripple:nth-child(3){
    animation-delay:.6s;
    -webkit-animation-delay:.6s
}
</style>
    <article>
        <a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
            <span class="ripple pinkBg"></span>
            <span class="ripple pinkBg"></span>
            <span class="ripple pinkBg"></span>
        </a>