<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Current Address</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <button id="getLocation">Get Current Address</button>
    <div id="address"></div>

    <script>
        $(document).ready(function() {
            $('#getLocation').click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    $('#address').text("Geolocation is not supported by this browser.");
                }
            });

            function showPosition(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                getAddress(lat, lon);
            }

            function showError(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        $('#address').text("User denied the request for Geolocation.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        $('#address').text("Location information is unavailable.");
                        break;
                    case error.TIMEOUT:
                        $('#address').text("The request to get user location timed out.");
                        break;
                    case error.UNKNOWN_ERROR:
                        $('#address').text("An unknown error occurred.");
                        break;
                }
            }

            function getAddress(lat, lon) {
                // Use Nominatim API for reverse geocoding
                var apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(data) {
                        if (data.display_name) {
                            $('#address').text(data.display_name);
                        } else {
                            $('#address').text("No address found for the given coordinates.");
                        }
                    },
                    error: function(error) {
                        $('#address').text("Error fetching address.");
                    }
                });
            }
        });
    </script>
</body>
</html>
