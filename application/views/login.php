<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SS ERP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--===============================================================================================-->	
        <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/vendor/bootstrap/css/bootstrap.min.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/fonts/font-awesome-4.7.0/css/font-awesome.min.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/fonts/Linearicons-Free-v1.0.0/icon-font.min.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/vendor/animate/animate.css") ?>
        <!--===============================================================================================-->	
        <?= link_tag("assets/login2/vendor/css-hamburgers/hamburgers.min.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/vendor/animsition/css/animsition.min.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/vendor/select2/select2.min.css") ?>
        <!--===============================================================================================-->	
        <?= link_tag("assets/login2/vendor/daterangepicker/daterangepicker.css") ?>
        <!--===============================================================================================-->
        <?= link_tag("assets/login2/css/util.css") ?>
        <?= link_tag("assets/login2/css/main.css") ?>
        <!--===============================================================================================-->
    </head>
    <body style="background-color: #666666;">

        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <form class="login100-form validate-form">
                        <?php if($save = $this->session->flashdata('reject_data')): ?>
                            <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
                                <?= $save ?>
                            </div>
                        <?php endif; ?>
                        <img class="pull-left" src="<?php echo site_url('assets/images/logo_new.jpeg') ?>" style="width: 80px" /><br>
                        <span class="login100-form-title p-b-43">
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i class="fa fa-sign-in"></i> Login
                        </span>
                        <div class="wrap-input100 validate-input" data-validate = "Userid is required">
                            <input class="input100" type="text" name="email">
                            <span class="focus-input100"></span>
                            <span class="label-input100">User Name</span>
                        </div><br>
                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <input class="input100" type="password" name="password">
                            <span class="focus-input100"></span>
                            <span class="label-input100">Password</span>
                        </div>
                        <div class="flex-sb-m w-full p-t-3 p-b-32">
                            <div class="contact100-form-checkbox">
                                <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
                                <label class="label-checkbox100" for="ckb1">
                                    Remember me
                                </label>
                            </div>

                            <div>
                                <a href="#" class="txt1">
                                    Forgot Password?
                                </a>
                            </div>
                        </div>
                        <div class="container-login100-form-btn">
                            <button type="submit" class="login100-form-btn" formmethod="POST" formaction="<?php echo base_url('Login/verify_login') ?>">
                                Login
                            </button>
                        </div>
<!--                        <div class="text-center p-t-46 p-b-20">
                            <span class="txt2">
                                or sign up using
                            </span>
                        </div>-->
<!--                        <div class="login100-form-social flex-c-m">
                            <a href="#" class="login100-form-social-item flex-c-m bg1 m-r-5">
                                <i class="fa fa-facebook-f" aria-hidden="true"></i>
                            </a>
                            <a href="#" class="login100-form-social-item flex-c-m bg2 m-r-5">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                            </a>
                        </div>-->
                    </form>
                    <div class="login100-more" style="background-image: url('<?php echo site_url('assets/login2/images/bg-01.jpg') ?>');">
                    <!--<div class="login100-more" style="background-image: url('https://www.spec-india.com/wp-content/uploads/2020/05/Banner-Custom_ERP.svg');">-->
                    </div>
                </div>
            </div>
        </div>

        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/jquery/jquery-3.2.1.min.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/animsition/js/animsition.min.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/bootstrap/js/popper.js') ?>"></script>
        <script src="<?php echo site_url('assets/login2/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/select2/select2.min.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/daterangepicker/moment.min.js') ?>"></script>
        <script src="<?php echo site_url('assets/login2/vendor/daterangepicker/daterangepicker.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/vendor/countdowntime/countdowntime.js') ?>"></script>
        <!--===============================================================================================-->
        <script src="<?php echo site_url('assets/login2/js/main.js') ?>"></script>
    </body>
</html>