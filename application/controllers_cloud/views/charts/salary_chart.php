<?php include 'chart_header.php'; ?>
<style>
#chartdiv {
  width: 100%;
  height: 430px;
}										
</style>
<script>
var chart = AmCharts.makeChart("chartdiv", {
    "theme": "none",
    "hideCredits":true,
    "type": "serial",
    "dataProvider": [
        <?php foreach ($monthly_salary_total as $salary_total) {  ?>
        {
        "month": "<?php echo date('F', mktime(0, 0, 0, $salary_total->month, 10)) ?>",
        "Amount": <?php echo $salary_total->sum_salary ?>,
        }, <?php } ?>
    ],
    "valueAxes": [{
        "stackType": "3d",
        "unit": "₹",
        "position": "left",
        "title": "Employee Salary Amount",
    }],
    "startDuration": 1,
    "graphs": [{
        "balloonText": "Amount in [[category]] <?php echo $year ?>: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "2004",
        "type": "column",
        "valueField": "Amount"
    }],
    "plotAreaFillAlphas": 0.1,
    "depth3D": 60,
    "angle": 30,
    "columnWidth": 0.09,
    "categoryField": "month",
    "categoryAxis": {
        "gridPosition": "start"
    },
    "export": {
    	"enabled": true
     }
});
jQuery('.chart-input').off().on('input change',function() {
    var property = jQuery(this).data('property');
    var target = chart;
    chart.startDuration = 0;
    if ( property == 'topRadius') {
            target = chart.graphs[0];
    if ( this.value == 0 ) {
        this.value = undefined;
        }
    }
    target[property] = this.value;
    chart.validateNow();
});
</script>

<!-- HTML -->
<article role="login" id="chartdiv"></article>