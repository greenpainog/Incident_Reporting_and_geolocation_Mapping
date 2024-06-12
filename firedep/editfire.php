<?php
ob_start();
require '../connection.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id === null) {
    echo "No ID provided.";
    exit;
}

$query = "SELECT * FROM firetweets WHERE id='$id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "No record found with the provided ID.";
    exit;
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST["submit"])) {
    $category = $_POST["category"];
    $plain_text = $_POST["text"];
    $spacy_woi = isset($_POST["location"]) ? $_POST["location"] : null;
    $latitude = isset($_POST["latitude"]) ? $_POST["latitude"] : null;
    $longitude = isset($_POST["longitude"]) ? $_POST["longitude"] : null;
    $created_at = isset($_POST["created_at"]) ? date('Y-m-d H:i:s', strtotime($_POST["created_at"])) : null;

    $query = "UPDATE firetweets
              SET plain_text='$plain_text', category='$category', spacy_woi='$spacy_woi', latitude='$latitude', longitude='$longitude', created_at='$created_at'
              WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "Data updated successfully.";
        header("Location: datafire.php?id=$id");
        exit;
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
ob_end_flush();
?>


<!DOCTYPE html>
<html>
<head>
<title>Edit Fire Tweet</title>
<meta charset="UTF-8">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            
            color: white; /* Set body text color to white */
            
        }
        .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
        .fa-anchor,.fa-coffee {font-size:200px}
        body {background-color: #E52B50;}
        
        #map {
            height: 400px;
            width: 100%;
        }
        
            .container {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-top: 60px; /* Add margin to avoid overlap with fixed navbar */
            }
        .text-color{color: white;}
        .map-container {
            border: 3px solid #000; /* Add a border with 2px width and black color */
            width: 900px; /* Set the width of the container */
            height:804px; /* Set the height of the container */
            margin: 0 auto; /* Center the container horizontally */
        }
        
        
        
        .myForm {
            text-align: center;
        }

        .myForm label {
            width: 90px;
            display: inline-block;
        }

        .myForm input {
            margin-bottom: 10px;
        }



        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            width: 50%;
            margin-top: 20px;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 30%;
            font-weight: bold;
        }

        .form-group input, .form-group textarea {
            width: 65%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }


        .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
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
<body >
    <br >
    <br>
    <br><br>
    <br>
    
    <div class="w3-top">
        <div class="w3-bar w3-black w3-card navbar">
            <a href="datafire.php" class="w3-bar-item w3-button w3-padding-large w3-black" class="page-link"><i class="fa fa-arrow-left"></i> Database Page</i></a>
            <div class="w3-bar-item w3-padding-large w3-black navbar-item" style="margin-left: 50px;">NATIONAL FIRE DEPARTMENT</div>
            <a href="mapfire.php" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="fa fa-map-marker"></i> Event map</a>
        </div>
    </div>
    
    





<div class="container">
        <h1>Edit Fire Tweet</h1>
        <form method="post" class="form-container">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

    <div class="form-group">
        <label for="category">Category:</label>
        <input type="text" id="category" name="category" value="<?php echo $row['category']; ?>">
    </div>

    <div class="form-group">
        <label for="text">Text:</label>
        <textarea id="text" name="text"><?php echo $row['plain_text']; ?></textarea>
    </div>

    <div class="form-group">
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo $row['spacy_woi']; ?>">
    </div>

    <div class="form-group">
                <label for="created_at">Date:</label>
                <input type="datetime-local" id="created_at" name="created_at" value="<?php echo date('Y-m-d\TH:i', strtotime($row['created_at'])); ?>">
    </div>

    <div class="form-group">
        <input type="hidden" id="latitude" name="latitude" value="<?php echo $row['latitude']; ?>" readonly>
    </div>

    <div class="form-group">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $row['longitude']; ?>" readonly>
    </div>

    <input type="submit" name="submit" value="Update" class="submit-btn">
</form>

<br>
        <div class="map-container">
    <div id="map" style="width: 100%; height: 800px;" style="border:1;" allowfullscreen="" loading="lazy"></div>
</div>
<br>



<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var map = L.map('map').setView([0, 0], 2); // Set initial view to a global view

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            <?php if (!empty($row['latitude']) && !empty($row['longitude'])): ?>
                var existingMarker = L.marker([<?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>]).addTo(drawnItems);
                map.setView([<?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>], 8); // Set view to the marker location
            <?php endif; ?>

            var drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems
                },
                draw: {
                    marker: true,
                    polyline: false,
                    polygon: false,
                    rectangle: false,
                    circle: false,
                    circlemarker: false
                }
            });
            map.addControl(drawControl);

            map.on(L.Draw.Event.CREATED, function (event) {
                var layer = event.layer;

                drawnItems.clearLayers();
                drawnItems.addLayer(layer);

                var latLng = layer.getLatLng();
                document.getElementById('latitude').value = latLng.lat;
                document.getElementById('longitude').value = latLng.lng;
            });
        });
    </script>





</body>
</html>
