<?php
require 'connection.php';
// Fetch all events
$rowsResult = mysqli_query($conn, "SELECT * FROM policetweets");
$rows = mysqli_fetch_all($rowsResult, MYSQLI_ASSOC);

// Prepare marker data
$markerData = [];
foreach ($rows as $row) {
    // Add marker data for each event
    $markerData[] = [
        'id' => $row['id'],
        'category' => $row['category'],
        'plain_text' => $row['plain_text'],
        'created_at' => $row['created_at'],
        'latitude' => $row['latitude'],
        'longitude' => $row['longitude'],
        'spacy_woi' => $row['spacy_woi'] // Assuming this is the correct field name for location
    ];
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Database Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="cssloading.css"> <!-- Link to your loading animation CSS file -->
    <style>
        body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif}
        .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
        .fa-anchor,.fa-coffee {font-size:200px}
        .map-container {
            border: 3px solid #000; /* Add a border with 2px width and black color */
            width: 900px; /* Set the width of the container */
            height:804px; /* Set the height of the container */
            margin: 0 auto; /* Center the container horizontally */
        }
        /* CSS for link button styling */
        .link-button {
            display: inline-block;
            padding: 10px 25px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            color: white; /* Button text color */
            background-color: transparent; /* Transparent background */
            border: 2px solid white; /* Button border color */
            border-radius: 8px; /* Rounded corners */
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Smooth transition */
          }

          .link-button:hover {
            color: #0056b3; /* Darker text color on hover */
            background-color: rgba(0, 123, 255, 0.1); /* Light background color on hover */
            border-color: #0056b3; /* Darker border color on hover */
          }

          .link-button:active {
            color: #004080; /* Even darker text color when button is active */
            background-color: rgba(0, 123, 255, 0.2); /* Even darker background color when button is active */
            border-color: #004080; /* Even darker border color when button is active */
          }
        .navbar {
            display: flex;
            justify-content: space-between;
          }

        .navbar-item {
          margin: 0 20px;
        }
        body {
            background-color: black; /* Set background color of the page */
            color:black; /* Set default text color */
          }
        .table-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150;
        }
        table {
            border: 1px solid black; /* Set border color */
            border-collapse: collapse; /* Collapse table borders */
            width: 65%; /* Set width of the table */
            margin: 0 auto; /* Center the table horizontally */
            background-color: white; /* Set background color of the table */
            color:black;
        }
        th, td {
            border: 1px solid black; /* Set border color for table cells */
            padding: 10px; /* Add padding to table cells */
            text-align: left; /* Align text to the left within table cells */
        }

        <style>
      .marker-label {
        color: white;
        background-color: black;
        font-size: 12px;
        padding: 2px;
        border-radius: 3px;
    }
</style>
    </style>
</head>
<body>
<div class="w3-top">
  <div class="w3-bar w3-black w3-card navbar">
    <a href="data.php" class="w3-bar-item w3-button w3-padding-large w3-black" style="margin-right: 20px;" class="page-link"><i class="fa fa-arrow-left" ></i> Database Page</a>
    <div class="w3-bar-item w3-padding-large w3-black navbar-item"style="margin-right: 530px;">NATIONAL POLICE DEPARTMENT</div>
  </div>
</div>

  <!-- Navbar on small screens -->
  <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
    <a href="data.php" class="w3-bar-item w3-button w3-padding-large">Database Page</a>
  </div>
  <header class="w3-container w3-blue w3-center" style="padding:30 16px">
    <h1 class="w3-margin w3-xlarge">
<br>
<br>
<!-- Collect latitude and longitude values for all events -->
<?php
$allLocations = [];
foreach ($rows as $row) {
    if (!empty($row["latitude"]) && !empty($row["longitude"])) {
        $allLocations[] = [
            'latitude' => $row["latitude"],
            'longitude' => $row["longitude"],
            'plain_text' => $row["plain_text"],
            'created_at' => $row["created_at"],
            'category' => $row["category"],
            'spacy_woi' => $row["spacy_woi"] // Assuming this is the correct field name for location
        ];
    }
}
?>

<!-- Display a single map with all events -->
<div class="map-container">
    <div id="map" style="width: 100%; height: 800px;" style="border:1;" allowfullscreen="" loading="lazy"></div>
</div>

<!-- Script to initialize the map and display all event markers -->
<script>
    // Initialize the map
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10, // Adjust the initial zoom level as needed
            center: {lat: <?php echo $allLocations[0]['latitude']; ?>, lng: <?php echo $allLocations[0]['longitude']; ?>}, // Set the initial center of the map
        });

        // Display markers for all events
        var bounds = new google.maps.LatLngBounds();
        var markers = <?php echo json_encode($allLocations); ?>;
        var markerArray = [];

        // Create OverlappingMarkerSpiderfier instance
        var oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true});

        markers.forEach(function(markerData) {
            var position = {lat: parseFloat(markerData.latitude), lng: parseFloat(markerData.longitude)};
            var marker = new google.maps.Marker({
                position: position,
                map: map
            });

            var infoWindowContent = `
                <div style="color: black; font-family: Arial, sans-serif; width: 250px;">
                    <h3 style="margin: 0; padding: 5px; background-color: #1E90FF; color: white;">${markerData.spacy_woi}</h3>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Event Date:</strong> ${markerData.created_at}</p>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Category:</strong> ${markerData.category}</p>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Details:</strong> ${markerData.plain_text}</p>
                </div>`;
            var infoWindow = new google.maps.InfoWindow({
                content: infoWindowContent
            });

            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });

            bounds.extend(marker.position);
            markerArray.push(marker);

            // Add marker to OverlappingMarkerSpiderfier
            oms.addMarker(marker);
        });

        // Automatically fit the map to contain all markers
        map.fitBounds(bounds);

        // Initialize MarkerClusterer
        var markerCluster = new MarkerClusterer(map, markerArray, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            styles: [{
                textColor: 'white',
                url: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m1.png',
                height: 53,
                width: 53
            }]
        });

        // Use a dictionary to keep track of overlapping markers
        var markerDict = {};

        oms.addListener('spiderfy', function(markers) {
            markers.forEach(function(marker) {
                var position = marker.getPosition().toString();
                if (markerDict[position]) {
                    markerDict[position].count++;
                } else {
                    markerDict[position] = { marker: marker, count: 1 };
                }
            });

            for (var key in markerDict) {
                var data = markerDict[key];
                var markerLabel = new MarkerWithLabel({
                    position: data.marker.getPosition(),
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 0
                    },
                    labelContent: data.count,
                    labelClass: "marker-label", // Class for the label
                    labelAnchor: new google.maps.Point(10, 10),
                    labelStyle: {opacity: 0.75}
                });
                data.marker.label = markerLabel;
            }
        });

        oms.addListener('unspiderfy', function(markers) {
            markers.forEach(function(marker) {
                if (marker.label) {
                    marker.label.setMap(null);
                    delete marker.label;
                }
            });
            markerDict = {};
        });
    }
</script>

<!-- Load the Google Maps JavaScript API with the callback to initialize the map -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM&callback=initMap" async defer></script>
<!-- Load the MarkerClusterer library -->

<!-- Load the OverlappingMarkerSpiderfier library -->
<script src="https://jawj.github.io/OverlappingMarkerSpiderfier/bin/oms.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/markerwithlabel/1.1.10/markerwithlabel.js"></script>


</body> 
</html>
