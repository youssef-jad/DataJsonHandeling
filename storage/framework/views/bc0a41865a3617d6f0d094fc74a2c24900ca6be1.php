<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <a href="<?php echo e(url('/api/map')); ?>" target="_blank"><button class="btn btn-danger">Access Json</button></a>
                </div>
                <div class="col-sm-6"></div>
                <hr>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Message</th>
                                <th scope="col">Sentiment</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $parser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $step_1 = stristr($value['content']['$t'],"message:");
                                    $step_2 = stristr($step_1,", sentiment:","sentiment:");
                                ?>
                                <tr>
                                    <th scope="row"><?php echo e(stristr($value['content']['$t'],', message:',", ")); ?></th>
                                    <th scope="row"><?php echo e($step_2); ?></th>
                                    <th scope="row"><?php echo e(stristr($value['content']['$t'],'sentiment')); ?></th>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <hr>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="my-address" placeholder="Please enter your address">
                    <br>
                    <div class="col-md-4" id="flow2">
                        <button id="getCords" class="btn btn-success" onClick="codeAddress();">Find Location</button>
                    </div>

                </div>


                <div class="page_load">
                    <img title="Loading..." alt="Loading..." src="">
                </div>
                <div id="map" style="height:600px;width:100%;margin-top:30px;"></div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">


            </div>
        </div>
    </div>

    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC558kEoz2bc-lVlkpddzdlnJ8vVfcHFBQ&libraries=places"></script>
    <script type="text/javascript">

        var popup_pin = "";
        var markersArray = [];
        var newmarkersArray = [];
        var customIcons = {
            restaura3t: {
                icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            },
            bar: {
                icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            },
            driver_free: {
                icon: 'driver_available.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            },
            driver_not_approved: {
                icon: 'driver_not_approved.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            },
            driver_on_trip: {
                icon: 'driver_on_trip.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            },
            driver: {
                icon: 'driver-70.png',
                shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
            }
        };

        function load(lat, lng) {
            var latitude = '';
            var longitude = '';
            if (lat != '') {
                latitude = lat;
                longitude = lng;
            } else {
                var mapOptions = {
                    zoom: 10
                };
                map = new google.maps.Map(document.getElementById('map'),
                    mapOptions);
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {

                            <?php
                            $latitude = 0;
                            $longitude = 0;
                            if (isset($admin)) {
                                $latitude = $admin->latitude;
                                $longitude = $admin->longitude;
                            }
                            ?>
                            <?php if ($latitude != 0 && $longitude != 0) { ?>
                        var pos = new google.maps.LatLng("<?php echo $latitude; ?>",
                            "<?php echo $longitude; ?>");
                        console.log("admin location");
                            <?php } else { ?>
                        var pos = new google.maps.LatLng(position.coords.latitude,
                            position.coords.longitude);
                        console.log("geo locating");
                            <?php } ?>

                        var infowindow = new google.maps.InfoWindow({
                                map: map,
                                position: pos,
                                content: 'You are here'
                            });

                        map.setCenter(pos);
                    }, function () {
                        handleNoGeolocation(true);
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleNoGeolocation(false);
                }
            }
            var address = (document.getElementById('my-address'));
            var autocomplete = new google.maps.places.Autocomplete(address);
            autocomplete.setTypes(['geocode']);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }
            });
            var map = new google.maps.Map(document.getElementById("map"),
                {
                    center: new google.maps.LatLng(latitude, longitude),
                    zoom: 6,
                    mapTypeId: 'roadmap',
                    scrollwheel: false,
                });
            var infoWindow = new
            google.maps.InfoWindow;
            (function () {
                var BaseUrl = 'https://serinc.tech/Task/public'
                var f = function () {
                    var marker = new google.maps.Marker();
                    downloadUrl(BaseUrl + "/api/map",
                        function (data) {
                            var json = data.json();
                            var markers = "<?php echo e(asset('')); ?>driver_available.png";
                            popup_pin = "";
                            for (var i = 0; i < markers.length; i++) {
                                var name = markers[i].getAttribute("name");
                                var client_name = markers[i].getAttribute("client_name");
                                var contact = markers[i].getAttribute("contact");
                                var amount = markers[i].getAttribute("amount");
                                var type = markers[i].getAttribute("type");
                                var id = markers[i].getAttribute("id");
                                var angl = markers[i].getAttribute("angl");
                                var link = markers[i].getAttribute("link");
                                var point = new google.maps.LatLng(
                                    parseFloat(markers[i].getAttribute("lat")),
                                    parseFloat(markers[i].getAttribute("lng")));

                                html = "<b>" + client_name + "</b><p style='font-size:16px;'><b><span class ='fa fa-mobile-phone icon-phone' style=''></span><span style='margin-left:5px;'>" + contact + "<br><a target='_blank' href='https://www.google.com.eg/maps/dir/29.99,31.27/Nasr+City,+%D8%A7%D9%84%D9%85%D9%86%D8%B7%D9%82%D8%A9+%D8%A7%D9%84%D8%A3%D9%88%D9%84%D9%89%D8%8C+%D9%85%D8%AF%D9%8A%D9%86%D8%A9+%D9%86%D8%B5%D8%B1%E2%80%AD/@30.020959,31.3403329,13z/data=!3m1!4b1!4m8!4m7!1m0!1m5!1m1!1s0x14583e5d94c66301:0xddddf100de42206c!2m2!1d31.3301076!2d30.0566104'>Open Direction</a>";

                                var icon = customIcons[type] || {};
                                marker = new google.maps.Marker({
                                    map: map,
                                    position: point,
                                    icon: icon.icon,
                                    shadow: icon.shadow
                                });
                                newmarkersArray.push(marker);
                                bindInfoWindow(marker, map,
                                    infoWindow, html, type, name, popup_pin,);
                            }
                            clearOverlays(markersArray);
                            markersArray = newmarkersArray;
                            newmarkersArray = [];
                        });
                };
                window.setInterval(f, 15000);
                f();

                var legendDiv = document.createElement('DIV');
                var legend = new Legend(legendDiv, map);
                legendDiv.index = 1;
                map.controls[google.maps.ControlPosition.RIGHT_TOP].push(legendDiv);

            })();
        }


        function clearOverlays(arr) {
            for (var i = 0; i < arr.length; i++) {
                arr[i].setMap(null);
            }
        }

        function bindInfoWindow(marker, map, infoWindow, html, type, name, popup_pin) {
            if (name == popup_pin) {
                infoWindow.setContent(html);
                infoWindow.open(map, marker);
                popup_pin = "";
            }
            google.maps.event.addListener(marker, 'click', function () {

                if (type == 'driver_free') {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                } else if (type == 'driver_on_trip') {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                } else {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                }
            });
        }

        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ?
                new ActiveXObject('Microsoft.XMLHTTP') :
                new XMLHttpRequest;
            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    request.onreadystatechange = doNothing;
                    callback(request, request.status);
                }
            };
            request.open('GET', url, true);
            request.send(null);
        }


        function initialize() {

        }

        function codeAddress() {
            geocoder = new google.maps.Geocoder();
            var address = document.getElementById("my-address").value;
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    // initialize_map(results[0].geometry.location.lat(),results[0].geometry.location.lng());
                    load(latitude, longitude);
                }

                else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            });
        }

        function doNothing() {
        }

        function Legend(controlDiv, map) {
            controlDiv.style.padding = '5px';

            // Set CSS for the control border
            var controlUI = document.createElement('DIV');
            controlUI.style.backgroundColor = 'white';
            controlUI.style.borderStyle = 'solid';
            controlUI.style.borderWidth = '1px';
            controlUI.title = 'Legend';
            controlDiv.appendChild(controlUI);

            // Set CSS for the control text
            var controlText = document.createElement('DIV');
            controlText.style.fontFamily = 'Arial,sans-serif';
            controlText.style.fontSize = '12px';
            controlText.style.paddingLeft = '4px';
            controlText.style.paddingRight = '4px';

        }

        google.maps.event.addDomListener(window, 'load', load('', ''));

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>