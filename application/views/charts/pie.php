<?php include 'chart_header.php'; ?>
<style>
#chartdiv {
  width: 100%;
  height: 450px;
  padding: 0;
}												
</style>
<script>
var chart = AmCharts.makeChart( "chartdiv", {
    "type": "pie",
    "radius": "50%",
    "hideCredits":true,
    "valueField": "Amount",
    "titleField": "Material",
    "outlineAlpha": 0.2,
    "startEffect": "easeInSine",
    "hideBalloonTime": 1500,
    "innerRadius": 130,
    "depth3D": 30,
    "prefixesOfBigNumbers": [],
    "prefixesOfSmallNumbers": [],
    "thousandsSeparator": ",",
//    "prefixesOfBigNumbers": [],
    "usePrefixes": true,
    "export": {
        "enabled": true
    },
    "legend": {
        "enabled": true,
        "align": "center",
        "markerType": "circle",
        "position": "right",
        "valueWidth": 150,
    },
    "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
    "angle": 30,
    "dataProvider": [ 
        <?php foreach ($amount_by_cust_material as $amt_cust_materialraw) { if($amt_cust_materialraw->idcustomer == $cust->customer_id){?>
            { "Material": "<?php echo $amt_cust_materialraw->material_name ?>", "Amount": "<?php echo $amt_cust_materialraw->sum_amount ?>" }, 
        <?php }} ?>
        <?php foreach ($site_emp_total_salary_data as $sitesalary){ if($sitesalary->idcustomer == $cust->customer_id){?>
            { "Material": "Labour Charges", "Amount": "<?php echo $sitesalary->sum_salary ?>" }, 
        <?php }} ?>
      ],
} );
</script>
<!--<article role="login">-->
    <div id="chartdiv"></div>
<!--</article>-->