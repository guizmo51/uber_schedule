'use strict';

/**
 * @ngdoc function
 * @name mapsApp.controller:FormCtrl
 * @description
 * # FormCtrl
 * Controller of the mapsApp
 */


angular.module('frontUberApp').controller('mapCtrl', function($scope,$http) {
    
   $scope.formData.where = {start:{}, end:{}};
    console.log("load map");
    // function to process the form
     var mapOptions = {
        zoom: 4,
        center: new google.maps.LatLng(40.0000, -98.0000),
        mapTypeId: google.maps.MapTypeId.TERRAIN
    };
    
    $scope.map = new google.maps.Map(document.getElementById('map'), mapOptions);
    var input_origin = /** @type {HTMLInputElement} */(document.getElementById('pac-input-origin'));
    var input_destination = /** @type {HTMLInputElement} */(document.getElementById('pac-input-destination'));
    var infowindow = new google.maps.InfoWindow({ content: 'Content goes here..'});
    var autocomplete_origin = new google.maps.places.Autocomplete(input_origin);
  var autocomplete_destination = new google.maps.places.Autocomplete(input_destination);
  var autocomplete = [autocomplete_origin, autocomplete_destination];
  var marker_origin = new google.maps.Marker({
      map: $scope.map,
      anchorPoint: new google.maps.Point(0, -29),
      draggable:true,
    });
  var marker_destination  = new google.maps.Marker({
      map: $scope.map,
      anchorPoint: new google.maps.Point(0, -29),
      draggable:true,
    });
  
  var marker =  [marker_origin,marker_destination ];
    $scope.map.controls[google.maps.ControlPosition.LEFT].push(input_origin);
    $scope.map.controls[google.maps.ControlPosition.LEFT].push(input_destination);

    for (var x in autocomplete){
      createListener(x);
    }

    for (var y in marker){
      markerListener(y);
    }

    function markerListener(x){
       google.maps.event.addListener(marker[x], 'dragend', function() {
    
   
  });
    }
    function createListener(x){
      google.maps.event.addListener(autocomplete[x], 'place_changed', function() {
          infowindow.close();
          marker[x].setVisible(false);
          var place = autocomplete[x].getPlace();
   
          if (!place.geometry) {
            return;
          }

          // Center the map
          if (place.geometry.viewport) {
            $scope.map.fitBounds(place.geometry.viewport);
        } else {
          $scope.map.setCenter(place.geometry.location);
          $scope.map.setZoom(17);  // Why 17? Because it looks good.
        }

        // Set the icon
        marker[x].setIcon(/** @type {google.maps.Icon} */({
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17, 34),
          scaledSize: new google.maps.Size(35, 35)
        }));
        marker[x].setPosition(place.geometry.location);
        marker[x].setVisible(true);

        var address = '';
        if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }
         
         if(x==0){
          $scope.formData.where.start.lon = place.geometry.location.D;
          $scope.formData.where.start.lat = place.geometry.location.k;
           $http.get('https://localhost/uber_schedule/app_dev.php/api/request/products/'+$scope.formData.where.start.lat+'/'+$scope.formData.where.start.lon+'').
        success(function(data) {
            $scope.products = data;
        });

        if($scope.formData.when=="later") {
          $http.get('https://maps.googleapis.com/maps/api/timezone/json?location='+$scope.formData.where.start.lat+','+$scope.formData.where.start.lon+'&timestamp='+(new Date().getTime())/1000+'&key=AIzaSyAUmV8u6gXdyFenVX6M4FqAieGah5MI7lE').
                
        success(function(data) {
          
          if(data.status=="OK"){
            $scope.formData.timezoneId = data.status.timeZoneId;
          }
        });
        }


          } else {
         $scope.formData.where.end.lat = place.geometry.location.D;
         $scope.formData.where.end.lon = place.geometry.location.k;
       }
         
        infowindow.setContent('<div style="width:150px; height:150px;"class="infoWindow"><strong>Origin</strong><p>' + place.geometry.location.D + ' ' + place.geometry.location.k + '</p><p>'+place.formatted_address+'</p></div>');
        infowindow.open($scope.map, marker[x]);

    

        });
    
}
    
});
