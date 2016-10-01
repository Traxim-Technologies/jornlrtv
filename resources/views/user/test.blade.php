<html>
<title>Google Maps API v3 - Two maps side-by-side</title>
<head>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
<script type="text/javascript">
</script>
</head>
<body onload="initialize()">
<H1 align="center">Two Google maps side-by-side</h1>
<div id="map_canvas" style="top: 10px; left: 25px; width:210px; height:220px; float: left"></div>
<div id="map_canvas_2" style="top: 10px; left: 75px; width:210px; height:220px"></div>
</body>
</html>
<script type="text/javascript">
   
   function initialize()
{
    var latlng = new google.maps.LatLng(18.520266,73.856406);
    var latlng2 = new google.maps.LatLng(28.579943,77.330006);
    var myOptions =
    {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var myOptions2 =
    {
        zoom: 15,
        center: latlng2,
        mapTypeId: google.maps.MapTypeId.SATELLITE
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    
    var map2 = new google.maps.Map(document.getElementById("map_canvas_2"), myOptions2);

    var myMarker = new google.maps.Marker(
    {
        position: latlng,
        map: map,
        title:"Pune"
   });

    var myMarker2 = new google.maps.Marker(
    {
        position: latlng2,
        map: map2,
        title:"Noida"
    });
}
</script>