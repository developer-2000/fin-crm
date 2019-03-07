<style>
    /* Always set the map height explicitly to define the size of the div
     * element that contains the map. */
    #map {
        height: 500px;
    }

    #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
    }

    #infowindow-content .title {
        font-weight: bold;
    }

    #infowindow-content {
        display: none;
    }

    #map #infowindow-content {
        display: inline;
    }

    .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
    }

    #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
    }

    .pac-controls {
        display: inline-block;
        padding: 5px 11px;
    }

    .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }

    #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
    }
</style>
<div class="form-group">
    <label for="approve[postal_code]" class="col-lg-3 control-label required">Postal code</label>
    <div class="col-lg-8">
        <input placeholder="Postal code" class="form-control postal_code"
               id="postal_code"
               name="approve[postal_code]"
               type="number" min="1"
               value="{{!empty($postal_code)? $postal_code : ''}}">
    </div>
</div>
<div class="form-group ">
    <div class="timezone_block">
        <label class="col-lg-3 control-label required" for="approve[timezone]">Timezone</label>
        <div class="col-lg-8">
            <select required id="approve[timezone]" name="approve[timezone]" class="form-control">
                <option value="">Choose timezone</option>
                @if(!empty(config('integrations.ninjaxpress_timezones')))
                    @foreach (config('integrations.ninjaxpress_timezones') as $key =>$value)
                        <option value="{{$key}}"
                                @if(isset($timezone) && $key == $timezone ) selected @endif>{{ $value }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="province" class="col-lg-3 control-label required ">Province</label>
    <div class="col-lg-8">
        <input placeholder="Province" class="form-control administrative_area_level_1"
               id="province"
               name="approve[warehouse]"
               type="text"
               value="@if (!empty($warehouse)){{$warehouse}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="district" class="col-lg-3 control-label required ">Kelurahan</label>
    <div class="col-lg-8">
        <input placeholder="Kelurahan" class="form-control administrative_area_level_4"
               id="district"
               name="approve[district]"
               type="text"
               value="@if (!empty($district)){{$district}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="region" class="col-lg-3 control-label required ">Kecamatan</label>
    <div class="col-lg-8">
        <input placeholder="Kecamatan" class="form-control administrative_area_level_3"
               id="region"
               name="approve[region]"
               type="text"
               value="@if (!empty($region)){{$region}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="city" class="col-lg-3 control-label required ">City</label>
    <div class="col-lg-8">
        <input placeholder="City" class="form-control administrative_area_level_2"
               id="city"
               name="approve[city]"
               type="text"
               value="@if (!empty($city)){{$city}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="street" class="col-lg-3 control-label required ">Receiver address</label>
    <div class="col-lg-8">
        <textarea class="form-control street" placeholder="Receiver address" id="street" rows="2"
                  name="approve[street]" cols="50">@if (!empty($street)){{$street}} @endif</textarea>
    </div>
</div>
{{--<div class="form-group">--}}
    {{--<label for="longitude" class="col-lg-3 control-label required">Longitude</label>--}}
    {{--<div class="col-lg-8">--}}
        {{--<input placeholder="Longitude" class="form-control longitude"--}}
               {{--id="longitude"--}}
               {{--name="approve[longitude]"--}}
               {{--type="text"--}}
               {{--value="@if (!empty($longitude)){{$longitude}} @endif">--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="form-group">--}}
    {{--<label for="latitude" class="col-lg-3 control-label required">Latitude</label>--}}
    {{--<div class="col-lg-8">--}}
        {{--<input placeholder="Latitude" class="form-control latitude"--}}
               {{--id="latitude"--}}
               {{--name="approve[latitude]"--}}
               {{--type="text"--}}
               {{--value="@if (!empty($latitude)){{$latitude}} @endif">--}}
    {{--</div>--}}
{{--</div>--}}
<div class="form-group">
    <label for="note" class="col-lg-3 control-label">Note(Заметка)</label>
    <div class="col-lg-8">
        <input type="text" id="note" class="form-control" name="approve[note]" value="{{!empty($note) ? $note : NULL}}">
    </div>
</div>
<hr>

{{--<div id="locationField">--}}
{{--<input id="autocomplete" placeholder="Enter your address" class="form-control"--}}
{{--onFocus="geolocate()" type="text"/>--}}
{{--</div>--}}
{{--<div class="pac-card" id="pac-card">--}}
    {{--<div>--}}
        {{--<div id="title">--}}
            {{--Autocomplete search--}}
        {{--</div>--}}
        {{--<div id="type-selector" class="pac-controls">--}}
            {{--<input type="radio" name="type" id="changetype-all" checked="checked">--}}
            {{--<label for="changetype-all">All</label>--}}

            {{--<input type="radio" name="type" id="changetype-establishment">--}}
            {{--<label for="changetype-establishment">Establishments</label>--}}

            {{--<input type="radio" name="type" id="changetype-address">--}}
            {{--<label for="changetype-address">Addresses</label>--}}

            {{--<input type="radio" name="type" id="changetype-geocode">--}}
            {{--<label for="changetype-geocode">Geocodes</label>--}}
        {{--</div>--}}
        {{--<div id="strict-bounds-selector" class="pac-controls">--}}
            {{--<input type="checkbox" id="use-strict-bounds" value="">--}}
            {{--<label for="use-strict-bounds">Strict Bounds</label>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div id="pac-container">--}}
        {{--<input id="pac-input" type="text"--}}
               {{--placeholder="Enter a location">--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div id="map"></div>--}}
{{--<div id="infowindow-content">--}}
    {{--<img src="" width="16" height="16" id="place-icon">--}}
    {{--<span id="place-name" class="title"></span><br>--}}
    {{--<span id="place-address"></span>--}}
{{--</div>--}}
{{--<script>--}}
    {{--// This example requires the Places library. Include the libraries=places--}}
    {{--// parameter when you first load the API. For example:--}}
    {{--// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">--}}
    {{--var map;--}}
    {{--var currentLatitude = $('#latitude').val() ? Number($('#latitude').val()) : -6.21462;--}}
    {{--var currentLongitude = $('#longitude').val() ? Number($('#longitude').val()) : 106.84513;--}}
    {{--console.log(currentLatitude);--}}
    {{--console.log(currentLongitude);--}}
    {{--var marker, place;--}}

    {{--function initMap() {--}}
        {{--var myLatLng = {lat: currentLatitude, lng: currentLongitude};--}}

        {{--map = new google.maps.Map(document.getElementById('map'), {--}}
            {{--center: {lat: currentLatitude, lng: currentLongitude},--}}
            {{--scrollwheel: false,--}}
            {{--zoom: 13--}}
        {{--});--}}

        {{--var card = document.getElementById('pac-card');--}}
        {{--var input = document.getElementById('pac-input');--}}
        {{--var types = document.getElementById('type-selector');--}}
        {{--var strictBounds = document.getElementById('strict-bounds-selector');--}}

        {{--map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);--}}

        {{--var autocomplete = new google.maps.places.Autocomplete(input);--}}

        {{--// Bind the map's bounds (viewport) property to the autocomplete object,--}}
        {{--// so that the autocomplete requests use the current map bounds for the--}}
        {{--// bounds option in the request.--}}
        {{--autocomplete.bindTo('bounds', map);--}}

        {{--// Set the data fields to return when the user selects a place.--}}
        {{--autocomplete.setFields(--}}
            {{--['address_components', 'geometry', 'icon', 'name']);--}}

        {{--var infowindow = new google.maps.InfoWindow();--}}
        {{--var infowindowContent = document.getElementById('infowindow-content');--}}
        {{--infowindow.setContent(infowindowContent);--}}

        {{--marker = new google.maps.Marker({--}}
            {{--position: myLatLng,--}}
            {{--map: map,--}}
            {{--title: 'Client address!'--}}
        {{--});--}}

        {{--google.maps.event.addListener(map, 'click', function (event) {--}}
            {{--$('#latitude').val(event.latLng.lat());--}}
            {{--$('#longitude').val(event.latLng.lat());--}}
        {{--});--}}

        {{--autocomplete.addListener('place_changed', function () {--}}
            {{--infowindow.close();--}}
            {{--marker.setVisible(false);--}}
            {{--var place = autocomplete.getPlace();--}}
            {{--if (!place.geometry) {--}}
                {{--// User entered the name of a Place that was not suggested and--}}
                {{--// pressed the Enter key, or the Place Details request failed.--}}
                {{--window.alert("No details available for input: '" + place.name + "'");--}}
                {{--return;--}}
            {{--}--}}

            {{--// If the place has a geometry, then present it on a map.--}}
            {{--if (place.geometry.viewport) {--}}
                {{--map.fitBounds(place.geometry.viewport);--}}
            {{--} else {--}}
                {{--map.setCenter(place.geometry.location);--}}
                {{--map.setZoom(17);  // Why 17? Because it looks good.--}}
            {{--}--}}
            {{--marker.setPosition(place.geometry.location);--}}
            {{--marker.setVisible(true);--}}

            {{--var address = '';--}}
            {{--if (place.address_components) {--}}
                {{--address = [--}}
                    {{--(place.address_components[0] && place.address_components[0].short_name || ''),--}}
                    {{--(place.address_components[1] && place.address_components[1].short_name || ''),--}}
                    {{--(place.address_components[2] && place.address_components[2].short_name || '')--}}
                {{--].join(' ');--}}
            {{--}--}}

            {{--infowindowContent.children['place-icon'].src = place.icon;--}}
            {{--infowindowContent.children['place-name'].textContent = place.name;--}}
            {{--infowindowContent.children['place-address'].textContent = address;--}}
            {{--infowindow.open(map, marker);--}}

            {{--place.geometry.location.lat() ? $('#latitude').val(place.geometry.location.lat()) : $('#latitude').val('');--}}
            {{--place.geometry.location.lng() ? $('#longitude').val(place.geometry.location.lng()) : $('#longitude').val('');--}}
            {{--console.log(place.geometry.location.lat());--}}
            {{--console.log(place.geometry.location.lng());--}}
            {{--if (place.geometry.location.lng() && place.geometry.location.lat()) {--}}
                {{--$.get('https://maps.googleapis.com/maps/api/timezone/json?location=' + place.geometry.location.lat()--}}
                    {{--+ ',' + place.geometry.location.lng() + '&timestamp='--}}
                    {{--+ Math.round(Date.now() / 1000) + '&key=AIzaSyCYVisnqrWsfOqELY9D6OXELpWqyEP1bs4', function (data) {--}}
                    {{--if (data.timeZoneId) {--}}
                        {{--var timezone = data.timeZoneId;--}}
                        {{--$.post('/ajax/integrations/ninjaxpress/set-timezone/html', {timezone: timezone}, function (json) {--}}
                            {{--if (json.html) {--}}
                                {{--$('.timezone_block').empty();--}}
                                {{--$('.timezone_block').append(json.html);--}}
                            {{--}--}}
                        {{--});--}}
                    {{--}--}}
                {{--});--}}
            {{--}--}}

            {{--//define indonesia administrative address components--}}
            {{--var componentForm = {--}}
                {{--subpremise: 'short_name',--}}
                {{--street_number: 'short_name',--}}
                {{--route: 'long_name',--}}
                {{--administrative_area_level_4: 'short_name',--}}
                {{--administrative_area_level_3: 'short_name',--}}
                {{--administrative_area_level_2: 'short_name',--}}
                {{--administrative_area_level_1: 'short_name',--}}
                {{--postal_code: 'short_name'--}}
            {{--};--}}

            {{--// Get each component of the address from the place details--}}
            {{--// and fill the corresponding field on the form.--}}
        {{--//     for (var i = 0; i < place.address_components.length; i++) {--}}
        {{--//--}}
        {{--//         var addressType = place.address_components[i].types[0];--}}
        {{--//--}}
        {{--//         if (componentForm[addressType]) {--}}
        {{--//             var val = place.address_components[i][componentForm[addressType]];--}}
        {{--//             if ($('.' + addressType).length) {--}}
        {{--//                 $('.' + addressType).val(val);--}}
        {{--//             }--}}
        {{--//             if (addressType == 'subpremise' || addressType == 'street_number' || addressType == 'route' || addressType == 'administrative_area_level_5') {--}}
        {{--//                 var streetVal = $('.street').val() ? $('.street').val() : '';--}}
        {{--//                 $('.street').val(streetVal + '' + val);--}}
        {{--//             }--}}
        {{--//         }--}}
        {{--//     }--}}
        {{--//--}}
        {{--// });--}}
        {{--//--}}
        {{--// // Sets a listener on a radio button to change the filter type on Places--}}
        {{--// // Autocomplete.--}}
        {{--// function setupClickListener(id, types) {--}}
        {{--//     var radioButton = document.getElementById(id);--}}
        {{--//     radioButton.addEventListener('click', function () {--}}
        {{--//         autocomplete.setTypes(types);--}}
        {{--//     });--}}
        {{--// }--}}
        {{--//--}}
        {{--// setupClickListener('changetype-all', []);--}}
        {{--// setupClickListener('changetype-address', ['address']);--}}
        {{--// setupClickListener('changetype-establishment', ['establishment']);--}}
        {{--// setupClickListener('changetype-geocode', ['geocode']);--}}
        {{--//--}}
        {{--// document.getElementById('use-strict-bounds')--}}
        {{--//     .addEventListener('click', function () {--}}
        {{--//         console.log('Checkbox clicked! New state=' + this.checked);--}}
        {{--//         autocomplete.setOptions({strictBounds: this.checked});--}}
        {{--//     });--}}


        {{--//     var card = document.getElementById('pac-card');--}}
        {{--//     var input = document.getElementById('pac-input');--}}
        {{--//     var types = document.getElementById('type-selector');--}}
        {{--//     var strictBounds = document.getElementById('strict-bounds-selector');--}}
        {{--//--}}
        {{--//     map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);--}}
        {{--//--}}
        {{--//     marker =  new google.maps.Marker({--}}
        {{--//         position: myLatLng,--}}
        {{--//         map: map,--}}
        {{--//         title: 'Client address!'--}}
        {{--//     });--}}
        {{--//--}}
        {{--//     google.maps.event.addListener(map, 'click', function (event) {--}}
        {{--//         $('#latitude').val(event.latLng.lat());--}}
        {{--//         $('#longitude').val(event.latLng.lat());--}}
        {{--//     });--}}
        {{--//--}}
        {{--//     initAutocomplete();--}}
        {{--// }--}}
        {{--//--}}
        {{--// var placeSearch, autocomplete;--}}
        {{--//--}}
        {{--// //define indonesia administrative address components--}}
        {{--// var componentForm = {--}}
        {{--//     subpremise: 'short_name',--}}
        {{--//     street_number: 'short_name',--}}
        {{--//     route: 'long_name',--}}
        {{--//     administrative_area_level_4: 'short_name',--}}
        {{--//     administrative_area_level_3: 'short_name',--}}
        {{--//     administrative_area_level_2: 'short_name',--}}
        {{--//     administrative_area_level_1: 'short_name',--}}
        {{--//     postal_code: 'short_name'--}}
        {{--// };--}}
        {{--//--}}
        {{--// function initAutocomplete() {--}}
        {{--//     // Create the autocomplete object, restricting the search to geographical--}}
        {{--//     // location types.--}}
        {{--//     autocomplete = new google.maps.places.Autocomplete(--}}
        {{--//         /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),--}}
        {{--//         {types: ['geocode']});--}}
        {{--//--}}
        {{--//     // When the user selects an address from the dropdown, populate the address--}}
        {{--//     // fields in the form.--}}
        {{--//     autocomplete.addListener('place_changed', fillInAddress);--}}
        {{--// }--}}
        {{--//--}}
        {{--// function fillInAddress() {--}}
        {{--//     // Get the place details from the autocomplete object.--}}
        {{--//--}}
        {{--//     var place = autocomplete.getPlace();--}}
        {{--//--}}
        {{--//--}}
        {{--//     place.geometry.location.lat() ? $('.latitude').val(place.geometry.location.lat()) : $('.latitude').val('');--}}
        {{--//     place.geometry.location.lng() ? $('.longitude').val(place.geometry.location.lng()) : $('.longitude').val('');--}}
        {{--//     if (place.geometry.location.lng() && place.geometry.location.lat()) {--}}
        {{--//         $.get('https://maps.googleapis.com/maps/api/timezone/json?location=' + place.geometry.location.lat()--}}
        {{--//             + ',' + place.geometry.location.lng() + '&timestamp='--}}
        {{--//             + Math.round(Date.now() / 1000) + '&key=AIzaSyCYVisnqrWsfOqELY9D6OXELpWqyEP1bs4', function (data) {--}}
        {{--//             if (data.timeZoneId) {--}}
        {{--//                 var timezone = data.timeZoneId;--}}
        {{--//                 $.post('/ajax/integrations/ninjaxpress/set-timezone/html', {timezone:timezone}, function (json) {--}}
        {{--//                     if(json.html){--}}
        {{--//                         $('.timezone_block').empty();--}}
        {{--//                         $('.timezone_block').append(json.html);--}}
        {{--//                     }--}}
        {{--//                 });--}}
        {{--//             }--}}
        {{--//         });--}}
        {{--//     }--}}
        {{--//--}}
        {{--//     var infowindow = new google.maps.InfoWindow();--}}
        {{--//     var infowindowContent = document.getElementById('infowindow-content');--}}
        {{--//     infowindow.setContent(infowindowContent);--}}
        {{--//--}}
        {{--//     if (place.geometry.viewport) {--}}
        {{--//         map.fitBounds(place.geometry.viewport);--}}
        {{--//     } else {--}}
        {{--//         map.setCenter(place.geometry.location);--}}
        {{--//         map.setZoom(17);  // Why 17? Because it looks good.--}}
        {{--//     }--}}
        {{--//     marker.setPosition(place.geometry.location);--}}
        {{--//     marker.setVisible(true);--}}
        {{--//--}}
        {{--//--}}
        {{--//     // for (var component in componentForm) {--}}
        {{--//     //     $('.target_block .'+ component).length ? $('.result .'+ component).val('') : '';--}}
        {{--//     //    // document.getElementById(component).disabled = false;--}}
        {{--//     // }--}}
        {{--//--}}
        {{--//     // Get each component of the address from the place details--}}
        {{--//     // and fill the corresponding field on the form.--}}
        {{--//     for (var i = 0; i < place.address_components.length; i++) {--}}
        {{--//--}}
        {{--//         var addressType = place.address_components[i].types[0];--}}
        {{--//--}}
        {{--//         if (componentForm[addressType]) {--}}
        {{--//             var val = place.address_components[i][componentForm[addressType]];--}}
        {{--//             if ($('.' + addressType).length) {--}}
        {{--//                 $('.' + addressType).val(val);--}}
        {{--//             }--}}
        {{--//             if (addressType == 'subpremise' || addressType == 'street_number' || addressType == 'route' || addressType == 'administrative_area_level_5') {--}}
        {{--//                 var streetVal = $('.street').val() ? $('.street').val() : '';--}}
        {{--//                 $('.street').val(streetVal + '' + val);--}}
        {{--//             }--}}
        {{--//         }--}}
        {{--//     }--}}
  {{--//  }--}}

    {{--// Bias the autocomplete object to the user's geographical location,--}}
    {{--// as supplied by the browser's 'navigator.geolocation' object.--}}
    {{--function geolocate() {--}}
        {{--if (navigator.geolocation) {--}}
            {{--navigator.geolocation.getCurrentPosition(function (position) {--}}
                {{--console.log(position);--}}
                {{--var geolocation = {--}}
                    {{--lat: position.coords.latitude,--}}
                    {{--lng: position.coords.longitude--}}
                {{--};--}}
                {{--var circle = new google.maps.Circle({--}}
                    {{--center: geolocation,--}}
                    {{--radius: position.coords.accuracy--}}
                {{--});--}}
                {{--autocomplete.setBounds(circle.getBounds());--}}
            {{--});--}}
        {{--}--}}
    {{--}--}}

{{--</script>--}}
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCYVisnqrWsfOqELY9D6OXELpWqyEP1bs4&libraries=places&callback=initMap"--}}
        {{--async defer></script>--}}
<script>
    $.getScript("/js/post_js/ninjaxpress.js");
</script>

{{--<script src="{{ URL::asset('js/post_js/ninjaxpress.js')}}"></script>--}}