<?php
require '../connection.php';
// Fetch all events
$rowsResult = mysqli_query($conn, "SELECT * FROM firetweets");
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
    <script src="https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js"></script>
    <link rel="stylesheet" href="cssloading.css"> <!-- Link to your loading animation CSS file -->
    <style>
        body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif}
    .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
    .fa-anchor,.fa-coffee {font-size:200px}



        /* Your existing CSS styles */
    </style>
    <style>
        
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
        
        body {
            background-color: #DE3163; /* Set background color of the page */
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
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-item {
            flex-grow: 1;
            text-align: center;
        }
        .navbar a {
            flex-shrink: 0;
        }
        .spacer {
            flex-grow: 1;
        }
        
    </style>
</head>
<body background-color="#2196F3">
<div class="w3-top">
  <div class="w3-bar w3-black w3-card navbar">
    <a href="datafire.php" class="w3-bar-item w3-button w3-padding-large w3-black" style="margin-right: 20px;" class="page-link"><i class="fa fa-arrow-left" ></i> Database Page</a>
    <div class="w3-bar-item w3-padding-large w3-black navbar-item"style="margin-right: 230px;">NATIONAL FIRE DEPARTMENT</div>
    
  </div>
</div>


  <!-- Navbar on small screens -->
  <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
      <a href="data.php" class="w3-bar-item w3-button w3-padding-large">Database Page</a>
    
      </div>
    </div>
<br>
<br>
<br>
<!-- Your existing HTML code -->
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
            'spacy_woi' => $row["spacy_woi"]
        ];
    }
}
?>
<!-- Display a single map with all events -->
<div class="map-container">
    <div id="map" style="width: 100%; height: 800px;" style="border:1;" allowfullscreen="" loading="lazy"></div>
</div>

<!-- Include the MarkerClusterer library -->
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>

<!-- Your existing HTML and PHP code -->
<script>
    // Initialize the map
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7, // Adjust the initial zoom level as needed
            center: {lat: 38.5236, lng: 23.8585}
        });

        // Display markers for all events
        var markers = <?php echo json_encode($allLocations); ?>;

        // Create an array to hold the markers
        var markerArray = [];

        markers.forEach(function(markerData) {
            var position = {lat: parseFloat(markerData.latitude), lng: parseFloat(markerData.longitude)};
            var marker = new google.maps.Marker({
                position: position,
                markerData: markerData, // Store marker data
                map: map
            });

            // Create an info window content
            var infoWindowContent = `
                <div style="color: black; font-family: Arial, sans-serif; width: 250px;">
                    <h3 style="margin: 0; padding: 5px; background-color: #1E90FF; color: white;">${markerData.spacy_woi}</h3>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Event Date:</strong> ${markerData.created_at}</p>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Category:</strong> ${markerData.category}</p>
                    <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Details:</strong> ${markerData.plain_text}</p>
                </div>`;

            // Create an info window with the content
            var infoWindow = new google.maps.InfoWindow({
                content: infoWindowContent
            });

            // Add a click event listener to the marker to open the info window
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });

            // Push marker to the markerArray
            markerArray.push(marker);
        });

        // Create a MarkerClusterer object and pass the markerArray to it
        var markerCluster = new MarkerClusterer(map, markerArray, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });

        // Add click event listener to the marker cluster to spiderfy markers when clicked
        google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {
            var markers = cluster.getMarkers();
            if (markers.length > 1) { // If the cluster contains multiple markers
                // Create a spiderfied marker for the cluster
                var spiderMarker = new google.maps.Marker({
                    position: cluster.getCenter(),
                    map: map
                });

                var infoWindow = new google.maps.InfoWindow();

                // Build info window content for spiderfied markers
                var infoWindowContent = '';

                markers.forEach(function(marker) {
                    var markerData = marker.markerData; // Access marker data
                    infoWindowContent += `
                        <div style="color: black; font-family: Arial, sans-serif; width: 250px;">
                            <h3 style="margin: 0; padding: 5px; background-color: #1E90FF; color: white;">${markerData.spacy_woi}</h3>
                            <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Event Date:</strong> ${markerData.created_at}</p>
                            <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Category:</strong> ${markerData.category}</p>
                            <p style="margin: 0; padding: 5px;"><strong style="font-weight: bold;">Details:</strong> ${markerData.plain_text}</p>
                        </div>`;
                });

                infoWindow.setContent(infoWindowContent);
                infoWindow.open(map, spiderMarker);
            }
        });
    }
</script>
<!-- Your existing HTML and PHP code -->


<!-- Your existing HTML and PHP code -->


<!-- Load the Google Maps JavaScript API with the callback to initialize the map -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM&callback=initMap" async defer></script>
<!-- Add this before your closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/js-markerclustererplus/dist/markerclusterer.min.js"></script>

<script src="https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js"></script>

<br>
</body>
</html>
