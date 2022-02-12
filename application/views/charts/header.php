<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Construction Software</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <!-- Bootstrap core CSS -->
        <?= link_tag("assets_ecom/css/bootstrap.css") ?>
        <?= link_tag("assets_ecom/css/signinstyle.css") ?>
        <!-- Fontawesome core CSS -->
        <?= link_tag("assets_ecom/css/font-awesome.min.css") ?>
        <?= link_tag("assets_ecom/waves/btnwave.css") ?>
        <!-- Material core CSS -->
        <?= link_tag("assets_ecom/material_font/css/materialdesignicons.css") ?>
        <!-- Creative core CSS -->
        <?= link_tag("assets_ecom/creative_font/css/pe-icon-7-stroke.css") ?>
        <!--Slide Show Css -->
        <?= link_tag("assets_ecom/ItemSlider/css/main-style.css") ?>
        <!-- custom CSS here -->
        <?= link_tag("assets_ecom/css/style.css") ?>
        <?= link_tag("assets_ecom/css/tipped.css") ?>
        <?= link_tag("assets/css/datepicker.css") ?>
        <?= link_tag("assets/css/sidebar.css") ?>
        <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
        <?= link_tag("assets/css/new-datetimepicker.css") ?>
        <script src="<?php echo site_url() ?>assets_ecom/js/jquery-3.1.1.min.js"></script>
        <script src="<?php echo site_url() ?>assets_ecom/js/jquery.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo site_url();?>assets/js/datepicker.js" type="text/javascript"></script>
        <script src="<?php echo site_url();?>assets/js/new-datetimepicker.js" type="text/javascript"></script>
        <style>
        .n1{ 
            /*background: rgba(0,0,0, .7);  Old browsers */
background: -moz-linear-gradient(45deg, rgba(76,76,76,1) 0%, rgba(0,0,0,1) 1%, rgba(0,0,0,1) 1%, rgba(71,71,71,1) 15%, rgba(71,71,71,1) 18%, rgba(71,71,71,1) 18%, rgba(89,89,89,1) 38%, rgba(89,89,89,1) 38%, rgba(102,102,102,1) 50%, rgba(102,102,102,1) 50%, rgba(17,17,17,1) 73%, rgba(28,28,28,1) 91%, rgba(19,19,19,1) 100%);   
background: -webkit-linear-gradient(45deg, rgba(76,76,76,1) 0%,rgba(0,0,0,1) 1%,rgba(0,0,0,1) 1%,rgba(71,71,71,1) 15%,rgba(71,71,71,1) 18%,rgba(71,71,71,1) 18%,rgba(89,89,89,1) 38%,rgba(89,89,89,1) 38%,rgba(102,102,102,1) 50%,rgba(102,102,102,1) 50%,rgba(17,17,17,1) 73%,rgba(28,28,28,1) 91%,rgba(19,19,19,1) 100%);
background: linear-gradient(45deg, rgba(76,76,76,1) 0%,rgba(0,0,0,1) 1%,rgba(0,0,0,1) 1%,rgba(71,71,71,1) 15%,rgba(71,71,71,1) 18%,rgba(71,71,71,1) 18%,rgba(89,89,89,1) 38%,rgba(89,89,89,1) 38%,rgba(102,102,102,1) 50%,rgba(102,102,102,1) 50%,rgba(17,17,17,1) 73%,rgba(28,28,28,1) 91%,rgba(19,19,19,1) 100%);  
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4c4c4c', endColorstr='#131313',GradientType=1 ); 
}
.sidebar1{
    background: rgba(255,140,0,1);
background: -moz-linear-gradient(45deg, rgba(255,140,0,1) 0%, rgba(245,241,157,1) 51%, rgba(234,255,166,1) 59%, rgba(255,140,0,1) 100%);
background: -webkit-gradient(left bottom, right top, color-stop(0%, rgba(255,140,0,1)), color-stop(51%, rgba(245,241,157,1)), color-stop(59%, rgba(234,255,166,1)), color-stop(100%, rgba(255,140,0,1)));
background: -webkit-linear-gradient(45deg, rgba(255,140,0,1) 0%, rgba(245,241,157,1) 51%, rgba(234,255,166,1) 59%, rgba(255,140,0,1) 100%);
background: -o-linear-gradient(45deg, rgba(255,140,0,1) 0%, rgba(245,241,157,1) 51%, rgba(234,255,166,1) 59%, rgba(255,140,0,1) 100%);
background: -ms-linear-gradient(45deg, rgba(255,140,0,1) 0%, rgba(245,241,157,1) 51%, rgba(234,255,166,1) 59%, rgba(255,140,0,1) 100%);
background: linear-gradient(45deg, rgba(255,140,0,1) 0%, rgba(245,241,157,1) 51%, rgba(234,255,166,1) 59%, rgba(255,140,0,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff8c00', endColorstr='#ff8c00', GradientType=1 );
}
/* Material Switch*/
.material-switch > input[type="checkbox"] {
    display: none;   
}
.material-switch > label {
    cursor: pointer;
    height: 0px;
    position: relative; 
    width: 40px;  
}
.material-switch > label::before {
    background: rgba(232, 100, 100,1);
    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    content: '';
    height: 12px;
    /*margin-top: -9px;*/
    position:absolute;
    opacity: 0.3;
    transition: all 0.4s ease-in-out;
    width: 28px;
}
.material-switch > label::after {
    background: rgba(232, 100, 100,1);
    border-radius: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
    content: '';
    height: 18px;
    left: -4px;
    margin-top: 1px;
    position: absolute;
    top: -4px;
    transition: all 0.3s ease-in-out;
    width: 18px;
}
.material-switch > input[type="checkbox"]:checked + label::before {
    background: inherit;
    opacity: 0.5;
}
.material-switch > input[type="checkbox"]:checked + label::after {
    background: inherit;
    left: 18px;
}
</style>
</head>
<body style="background-color: #f9f6f9">
    <nav class="navbar navbar-default n1 navbar-expand-lg" role="navigation" style="margin: 0; padding: 10px; height: auto; border: none;">
        <div class="navbar-header" role="navigation">
            <img height="60" style="position: absolute;" src="<?php echo base_url()?>assets/images/color.png" />
            <a class="navbar-brand waves-effect waves-light" href="<?php echo base_url();?>">Construction Software</a>
        </div>
        <a class="navbar-toggler pull-right btn btn-sm waves-effect waves-purple" data-toggle="collapse" data-target="#header1" aria-controls="header1" aria-expanded="false" aria-label="Toggle navigation" style="top: 5px;right: 10px; position: absolute; opacity: 0.8">
            <span class="fa fa-bars fa-2x" style="opacity: 0.4"></span>
        </a>
        <div class="collapse navbar-collapse" id="header1">
            <ul class="nav navbar-nav navbar-right">
                <li style="margin-top: -20px"><a><img height="120" style="position: absolute; z-index: 9999" src="<?php echo base_url()?>assets/images/house-158939_960_720.png" /></a></li>    
                <li><a></a></li><li><a></a></li><li><a></a></li><li><a></a></li><li><a></a></li>
                <?php if($this->session->userdata('userid')){ ?>
                <li><a class="">Welcome <?php echo $this->session->userdata('userid'); ?></a></li>    
                <li><a class="waves-effect waves-light" href="<?php echo base_url('MY_Controller/user_dashboard');?>">Home</a></li>
                <li><a class="waves-effect waves-light" href="<?php echo base_url('Login/logout');?>">Logout <span class="fa fa-sign-out"></span></a></li>
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
    </script> 
<?php 
    $now = date('Y-m-d');
    $year = date("Y",strtotime($now));
    $mont = date("M",strtotime($now));
    $month = date("m",strtotime($now));
    $timestamp = mktime(0, 0, 0, date('n'));
    $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
    $y = date('y', mktime(0, 0, 0, 9+date('m'))); 
    $y1 = $y - 1;
    $unix_time = new DateTime();
    $idsale = $unix_time->getTimestamp(); ?>

<script>
    $(document).ready(function(){
    <?php $i=1; for($i=1; $i<=14; $i++){ ?>
    $("#filter_<?php echo $i ?>").keyup(function(){
        var filter = $(this).val(), count = 0;
        $(".data_<?php echo $i ?> tr").each(function(){
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).fadeOut();
            } else {
                $(this).show();
                count++;
            }
        });
        $("#count_<?php echo $i ?>").text("Searched Result Found "+count+" Rows");
    });
   <?php } ?>
});
</script>
<?php 
function number_to_word( $num = '' )
{
    $num    = ( string ) ( ( int ) $num );
    if( ( int ) ( $num ) && ctype_digit( $num ) )
    {
        $words  = array( );
        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
        $list1  = array('','one','two','three','four','five','six','seven',
            'eight','nine','ten','eleven','twelve','thirteen','fourteen',
            'fifteen','sixteen','seventeen','eighteen','nineteen');
        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
            'seventy','eighty','ninety','hundred');
        $list3  = array('','thousand','million','billion','trillion',
            'quadrillion','quintillion','sextillion','septillion',
            'octillion','nonillion','decillion','undecillion',
            'duodecillion','tredecillion','quattuordecillion',
            'quindecillion','sexdecillion','septendecillion',
            'octodecillion','novemdecillion','vigintillion');
        $num_length = strlen( $num );
        $levels = ( int ) ( ( $num_length + 2 ) / 3 );
        $max_length = $levels * 3;
        $num    = substr( '00'.$num , -$max_length );
        $num_levels = str_split( $num , 3 );
       
        foreach( $num_levels as $num_part )
        {
            $levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';
            if( $tens < 20 )
            {
                $tens   = ( $tens ? ' ' . $list1[$tens] . ' ' : '' );
            }
            else
            {
                $tens   = ( int ) ( $tens / 10 );
                $tens   = ' ' . $list2[$tens] . ' ';
                $singles    = ( int ) ( $num_part % 10 );
                $singles    = ' ' . $list1[$singles] . ' ';
            }
            $words[]    = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        }
        $commas = count( $words );
        if( $commas > 1 )
        {
            $commas = $commas - 1;
        }
        $words  = implode( ', ' , $words );
        $words  = trim( str_replace( ' ,' , ',' , trim( ucwords( $words ) ) ) , ', ' );
        if( $commas )
        {
            $words  = str_replace( ',' , ' and' , $words );
        }
        return $words;
    }
    else if( ! ( ( int ) $num ) )
    {
        return 'Zero';
    }
    return '';
}?>