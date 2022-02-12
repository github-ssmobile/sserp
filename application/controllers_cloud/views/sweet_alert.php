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
    <?=  link_tag("assets/waves/btnwave.css") ?>
    <?= link_tag("assets/css/sweet-alert.css") ?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    <style>.btn{ margin: 0; }</style>
</head>
<body>
    <div class="example" style="padding: 100px">
        <button class="btn btn-primary" id="b1">A basic message</button><br><br>
        <button class="btn btn-info" id="b2">A title with a text under</button><br><br>
        <button class="btn btn-success" id="b3">A success message!</button><br><br>
        <button class="btn btn-warning" id="b4">A warning message, with a function attached to the "Confirm"-button...</button><br><br>
        <button class="btn btn-danger" id="b5">... and by passing a parameter, you can execute something else for "Cancel".</button><br><br>
        <button class="btn btn-default" id="b6">A message with a custom icon</button>
    </div>
    <script>
    document.getElementById('b1').onclick = function(){
        swal("Here's a message!");
    };

    document.getElementById('b2').onclick = function(){
        swal("Here's a message!", "It's pretty, isn't it?");
    };

    document.getElementById('b3').onclick = function(){
        swal("Good job!", "You clicked the button!", "success");
    };

    document.getElementById('b4').onclick = function(){
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, delete it!',
            closeOnConfirm: false,
            //closeOnCancel: false
        },
        function(){
            swal("Deleted!", "Your imaginary file has been deleted!", "success");
        });
    };

    document.getElementById('b5').onclick = function(){
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm){
              swal("Deleted!", "Your imaginary file has been deleted!", "success");
            } else {
              swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
        });
    };

    document.getElementById('b6').onclick = function(){
        swal({
            title: "Sweet!",
            text: "Here's a custom image.",
            imageUrl: 'https://i.imgur.com/4NZ6uLY.jpg'
        });
    };
    </script>
<script src="<?php echo site_url('assets/js/sweet-alert.min.js') ?>"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>