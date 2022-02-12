<!DOCTYPE html>
<html>
  <head>
    <title>Simple Polylines</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCGIMNp5mXHPmrhjGyMqswPwcDmpF-YmIM&callback=initMap&libraries=&v=weekly"
      defer ></script>
    <style type="text/css">
      #map {
        height: 400px;
      }
    </style>
    <script>
      "use strict";

      // This example creates a 2-pixel-wide red polyline showing the path of
      // the first trans-Pacific flight between Oakland, CA, and Brisbane,
      // Australia which was made by Charles Kingsford Smith.
      function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 8,
          center: {
            lat: 17.0948333,
            lng: 74.4419002
          },
          mapTypeId: "roadmap"
        });
        const flightPlanCoordinates = [
          {
            lat: 17.0948333,
            lng: 74.4419002
          },
          {
            lat: 16.8543615,
            lng: 74.5490806
          },
          {
            lat: 16.8156333,
            lng: 74.2984337
          },
        ];
        var iconsetngs = {
            path: google.maps.SymbolPath.FORWARD_OPEN_ARROW
        };
        const flightPath = new google.maps.Polyline({
          path: flightPlanCoordinates,
          geodesic: true,
          strokeColor: "#FF0000",
          strokeOpacity: 1.0,
          strokeWeight: 2,
          icons: [{
            icon: iconsetngs,
            repeat:'35px',
            offset: '100%'}]
        });
        flightPath.setMap(map);
      }
    </script>
  </head>
  <body>
    <div id="map"></div>
  </body>
</html>