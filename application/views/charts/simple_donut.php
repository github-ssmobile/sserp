<?php include 'chart_header.php'; ?>
<style>
#chartdiv {
	width		: 100%;
	height		: 500px;
	font-size	: 11px;
}							
</style>
<!-- Chart code -->
<script>
var chart = AmCharts.makeChart( "chartdiv", {
  "type": "pie",
  "theme": "light",
  "dataProvider": [ {
    "title": "New",
    "value": 4852
  }, {
    "title": "Returning",
    "value": 9899
  } ],
  "titleField": "title",
  "valueField": "value",
  "labelRadius": 5,

  "radius": "42%",
  "innerRadius": "60%",
  "labelText": "[[title]]",
  "export": {
    "enabled": true
  }
} );
</script>

<!-- HTML -->
<div id="chartdiv"></div>	