<x-filament::page>

<div id="googleMap" style="width:100%;height:400px;"></div>
<?php
$packages = \App\Models\Package::all();

?>
<label>Select a Package</label><br/>
<select id="package">
@foreach($packages as $package)
    <option value="{{$package->id}}">{{$package->id}}</option>
@endforeach
</select>

<script src="https://js.pusher.com/3.0/pusher.min.js"></script>
<script src="/build/assets/app-558cdc57.js"></script>

<script>
//var e = document.getElementById("package");
//var value = e.value;
//var text = e.options[e.selectedIndex].text;

//document.getElementById("package").addEventListener('change',function(){

//});
function initMap(){

  const myLatLng = { lat: 40.7648, lng: -73.9808 };
  const map = new google.maps.Map(document.getElementById("googleMap"), {
    zoom: 10,
    center: myLatLng,
  });

  const marker = new google.maps.Marker({
    position: myLatLng,
    map,
    title: "Order Detail",
  });


    window.Echo.channel('location')
      .listen(`SendLocation`, (e)=>{
         data = e.location;
            console.log(e)
            let lat = parseFloat(data.lat);
            let long = parseFloat(data.long);
            map.setCenter({lat:lat, lng:long, alt:0});
            marker.setPosition({lat:lat, lng:long, alt:0});

         // this.updateMap(this.data);
      });
  }
</script>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdNEvj_7OPXx7Lo52ue_yTsL9c-U_Saos&callback=initMap"></script>


</x-filament::page>
