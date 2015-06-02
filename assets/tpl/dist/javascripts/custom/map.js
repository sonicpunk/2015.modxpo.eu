// MAP.JS
//--------------------------------------------------------------------------------------------------------------------------------
//This is  JS file that creates the map
// -------------------------------------------------------------------------------------------------------------------------------

(function(){

    getAdresses=function(){
        var addressData ={"locations":
            [ {"name":"Kochen und Partner - MODXpo 2015","street":"Hirschgartenallee","city":"Munich","zip":"80639","housenumber":"27","longitude":"11.507510","latitude":"48.153150"}]
        };

        for (var i=0; i < addressData.locations.length; i++ ){

            var locationObject=addressData.locations[i];
            var land='Deutschland';
            var street=locationObject.street;
            var city=locationObject.city;
            var zip=locationObject.zip;
            var housenumber=locationObject.housenumber;
            var address=street+' '+housenumber;
            var lng=locationObject.longitude;
            var lat=locationObject.latitude;

            var link='http://maps.google.com/maps?q='+address+'+'+city+'+'+zip+'+'+land;
            //pick out pin

            var pinlink='assets/tpl/dist/images/assets/googlemap-pin.png';

            // recenter map
            var marker,infobox;
            var stylesArray=
                [
                    {
                        featureType: "all",
                        elementType: "labels",
                        stylers: [
                            { visibility: "on" }
                        ]
                    }
                ];

            var mapDiv=document.getElementById('map');
            var latlng=new google.maps.LatLng(lat,lng);
            var options={
                center:latlng,
                zoom:16,
                mapTypeId:google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: false,
                streetViewControl:true,
                styles: stylesArray,
                mapTypeControl: false,
                navigationControl: false,
                scrollwheel: false,
                draggable:true

            };
            var map= new google.maps.Map(mapDiv,options);
            google.maps.event.addDomListener(window, "resize", function() {
                var center = map.getCenter();
                google.maps.event.trigger(map, "resize");
                map.setCenter(center);
            });
            if (lat && lng !=''){
                var marker=new google.maps.Marker({
                    map:map,
                    title:city,
                    icon:pinlink,
                    position: new google.maps.LatLng(lat,lng)
                });

                //wrapping listener inside an anymous function that we immeditely invoke and pass the variables to

                (function(i,marker,link,city,address,zip){

                    google.maps.event.addListener(marker,'click',function(){

                        boxContent='<div class="infowindow"><div class="top_label">'+address+', '+zip+' '+city+'</div> <div class="city_link"><a href="'+link+'" target="_blank">Route Berechnen<\/a></div></div>';

                        if(!infobox){

                            infobox = new InfoBox({
                                disableAutoPan: false,
                                maxWidth: 300,
                                pixelOffset: new google.maps.Size(-140, 0),
                                zIndex: null,
                                boxStyle: {
                                    width: "250px"
                                },
                                closeBoxMargin: "0px 0px 5px 5px",
                                closeBoxURL: "assets/tpl/dist/images/assets/btn-google-close.gif",
                                infoBoxClearance: new google.maps.Size(1, 1)
                            });
                        };
                        infobox.setContent(boxContent);
                        infobox.open(map,marker);
                    });

                })(i,marker,link,city,address,zip);
            }//end of lat lng check

        }// end of loop through addresses
    };//end of getAdresses
    getAdresses();
})();