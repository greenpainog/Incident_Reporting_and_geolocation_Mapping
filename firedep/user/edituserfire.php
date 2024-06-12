<?php
ob_start();
require '../../connection.php';


$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id === null) {
    echo "No ID provided.";
    exit;
}

$query = "SELECT * FROM userfire_reports WHERE id='$id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "No record found with the provided ID.";
    exit;
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST["Update"])) {
    $category = $_POST["category"];
    $description = $_POST["description"];
    $location = isset($_POST["location"]) ? $_POST["location"] : null;
    $latitude = isset($_POST["latitude"]) ? $_POST["latitude"] : null;
    $longitude = isset($_POST["longitude"]) ? $_POST["longitude"] : null;
    $created_at = $_POST["created_at"];
    $attachments = $row['attachments']; // Use existing attachments by default

    // Handle new attachments
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        // Delete old attachments if new ones are provided
        if (!empty($row['attachments'])) {
            $oldAttachments = explode(',', $row['attachments']);
            foreach ($oldAttachments as $oldAttachment) {
                if (file_exists($oldAttachment)) {
                    unlink($oldAttachment);
                }
            }
        }

        $attachmentFiles = [];
        $uploadDirectory = 'uploads/';
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['attachments']['name'][$key];
            $file_tmp = $_FILES['attachments']['tmp_name'][$key];
            $filePath = $uploadDirectory . basename($file_name);
            if (move_uploaded_file($file_tmp, $filePath)) {
                $attachmentFiles[] = $filePath;
            }
        }
        $attachments = implode(',', $attachmentFiles);
    }

    $query = "UPDATE userfire_reports 
              SET created_at='$created_at', description='$description', category='$category', location='$location', latitude='$latitude', longitude='$longitude', attachments='$attachments'
              WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "Data updated successfully.";
        header("Location: datauserfire.php?id=$id");
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
<title>Edit Live Event</title>
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
        color: white;
        background-color: red;
    }
    .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
    .fa-anchor,.fa-coffee {font-size:200px}
    
    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 60px;
    }
    .text-color{color: white;}
    .map-container {
        border: 3px solid #000;
        width: 900px;
        height:804px;
        margin: 0 auto;
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
    .form-container {
        display: inline-block;
        text-align: left;
    }
    .form-container label, .form-container input {
        display: block;
        margin: 10px 0;
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
    .white-background {
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 40%;
        margin: 20px auto;
        border-radius: 8px;
        text-align: center;
        color: black;
    }
</style>
</head>
<body>
<br><br><br><br><br>

<div class="w3-top">
    <div class="w3-bar w3-black w3-card navbar">
        <a href="datauserfire.php" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="fa fa-arrow-left"></i> Database Page</a>
        <div class="w3-bar-item w3-padding-large w3-black navbar-item" >NATIONAL FIRE DEPARTMENT</div>
        <a href="" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="far fa-edit"></i></i> Edit page</a>
    </div>
</div>

<div class="white-background">
    <h1>Edit Live Event</h1>
    <form method="post" action="edituserfire.php?id=<?php echo $row['id']; ?>" class="form-container" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($row['category']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Text:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($row['location']); ?>">
        </div>
        <div class="form-group">
            <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($row['latitude']); ?>" readonly>
        </div>
        <div class="form-group">
            <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($row['longitude']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="attachments">Attachments (images or videos):</label>
            <input type="file" id="attachments" name="attachments[]" multiple>
            <?php if (!empty($row['attachments'])): ?>
                <?php $attachments = explode(',', $row['attachments']); ?>
                <ul>
                    <?php foreach ($attachments as $attachment): ?>
                        <li><a href="<?php echo htmlspecialchars($attachment); ?>" target="_blank"><?php echo htmlspecialchars(basename($attachment)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <input type="submit" name="Update" value="Update" class="submit-btn">
    </form>
</div>
<br>
<div class="map-container">
    <div id="map" style="width: 100%; height: 800px;"></div>
</div>
<br>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    var map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    <?php if (!empty($row['latitude']) && !empty($row['longitude'])): ?>
        var existingMarker = L.marker([<?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>]).addTo(drawnItems);
        map.setView([<?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>], 8);
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
