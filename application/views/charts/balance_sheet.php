<?php include 'chart_header.php'; ?>
<style>
#chartdiv {
  width: 100%;
  height: 480px;
}										
</style>
<script>
var chart = AmCharts.makeChart("chartdiv", {
    "theme": "none",
    "type": "serial",
    "hideCredits":true,
    "dataProvider": [
        <?php // foreach ($months as $m=>$k){
            foreach($monthly_income as $income){ foreach ($monthly_salary_total as $salary_sum){ foreach ($purchase_amount as $puramt) {  
            if($puramt->month == $income->month && $salary_sum->month == $puramt->month && $puramt->year == $year && $income->year == $year && $year == $salary_sum->year ){ ?>
            {
                "month": "<?php echo date('F', mktime(0, 0, 0, $income->month, 10)) ?>",
                "income": <?php echo $income->total_amount; ?>,
                "outcome": <?php echo $puramt->sum_total + $salary_sum->sum_salary ?>
            }, <?php }}}} ?>
    ],
    "valueAxes": [{
        "stackType": "3d",
        "unit": "₹",
        "position": "left",
        "title": "Balance Sheet",
    }],
    "startDuration": 1,
    "graphs": [{
        "balloonText": "Income in [[category]] <?php echo $year ?>: <b>[[value]] ₹</b>",
        "fillAlphas": 0.4,
        "lineAlpha": 1.2,
        "title": "2018",
        "type": "column",
        "valueField": "income"
    }, {
        "balloonText": "Purchase in [[category]] <?php echo $year ?>: <b>[[value]] ₹</b>",
        "fillAlphas": 0.4,
        "lineAlpha": 1.2,
        "title": "2018",
        "type": "column",
        "valueField": "outcome"
    }],
    "plotAreaFillAlphas": 0.1,
    "depth3D": 100,
    "angle": 30,
    "columnWidth": 0.12,
    "categoryField": "month",
    "categoryAxis": {
        "gridPosition": "start"
    },
    "export": {
    	"enabled": false
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
<div id="chartdiv"></div>