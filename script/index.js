function initMap() {
    var uluru = {lat: 59.92, lng: 30.31};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: uluru
    });
    var pointsCollection = [];
    var coordinates = [];
    var data = [];
    var i = 0;

    initAutocomplete();
    document.querySelector("#count").addEventListener("click", fillInAddress);
    document.querySelector("#reset").addEventListener("click", ClearPoints);
    document.querySelector("#solve").addEventListener("click", function () {
        CR(pointsCollection);
    });

    function initAutocomplete() {
        var input = document.getElementById('searchAddress');

        var options = {
            /* types: ['(address)'],*/
            language: 'ru',
            componentRestrictions: {country: 'ru'}
        };

        autocomplete = new google.maps.places.Autocomplete(input, options);


    }

    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();
        var position = {lat: lat, lng: lng};
        var address = place.formatted_address;

        var marker = new google.maps.Marker({
            position: position,
            map: map
        });

        pointsCollection.push(address);
        coordinates.push(position);

        i++;
        $('#collection').append("<p>" + address + ' - Пункт ' + i + "</p>");
    }

    function ClearPoints() {
        var pointsCollection = [];
        var coordinates = [];
        $("#collection").empty();
        var result = {};
        i = 0;
        document.querySelector(".hidden").classList.add("hidden");
    }

    function CR(c) {

        var res = {};
        if (c.length > 2) {
            document.querySelector(".hidden").classList.remove("hidden");
            for (var i = 0; i < c.length - 1; i++) {


                for (j = i + 1; j < c.length; j++) {
                    var start = c[i];
                    var end = c[j];
                    $.ajax({
                        url: "./proxy.php",
                        type: "POST",
                        dataType: 'json',
                        begin: i,
                        stop: j,

                        data: {"start": start, "end": end},
                        success: function (result) {
                            data = JSON.parse(result);
                            distance = data.routes[0].legs[0].distance.value;
                            console.log(distance);
                            var key = this.begin.toString() + this.stop.toString();
                            res[key] = distance;
                        }
                    });


                }
            }

            document.querySelector("#redirect").addEventListener("click", function () {
                passData(res, c.length, pointsCollection);
            });
        }
        else {
            alert('Введите не менее трех точек! ');
        }

    }

    function passData(data, num, points) {
        var myURL = "http://localhost:55415/gmaps/commis.php";
        window.open(myURL + "/?data=" + JSON.stringify(data) + "&num=" + JSON.stringify(num) + "&addr=" + JSON.stringify(points));
    }
}
initMap();

