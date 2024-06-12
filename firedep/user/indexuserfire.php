<?php
require '../../connection.php';

function getCoordinates($address, $apiKey) {
    $address = urlencode($address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null;
    }

    $json = json_decode($response, true);

    if (isset($json['status']) && $json['status'] == 'OK') {
        $latitude = $json['results'][0]['geometry']['location']['lat'];
        $longitude = $json['results'][0]['geometry']['location']['lng'];
        return array('latitude' => $latitude, 'longitude' => $longitude);
    } else {
        return null;
    }
}

function extractLocationFromText($description) {
    // Escape the input to make it safe for shell command
    $description = escapeshellarg($description);
    // Convert the command to handle UTF-8
    $command = "python extract_location_spacy.py " . $description;
    // Print the command for debugging
    echo "Command: $command\n";
    // Execute the command and capture the output
    $output = shell_exec($command . " 2>&1");
    // Print the output for debugging
    echo "Output: $output\n";
    // Trim whitespace from the output
    $location = trim($output);
    return $location;
}

if (isset($_POST["submit"])) {
    // Get the current maximum id
    $max_id_query = "SELECT MAX(id) AS max_id FROM userfire_reports";
    $max_id_result = mysqli_query($conn, $max_id_query);
    $max_id_row = mysqli_fetch_assoc($max_id_result);
    $new_id = $max_id_row['max_id'] + 1;

    $category = $_POST["category"];
    $description = $_POST["description"];
    $location = isset($_POST["posizione"]) ? $_POST["posizione"] : null; // Get the value from the location input field
    $created_at = $_POST["created_at"];
    
    // Check if the location input field is empty
    if (empty($location)) {
        // Extract location from text input
        $location = extractLocationFromText($description);
    }

    $latitude = null;
    $longitude = null;

    if ($location) {
        $apiKey = 'AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM';
        $coords = getCoordinates($location, $apiKey);
        if ($coords) {
            $latitude = $coords['latitude'];
            $longitude = $coords['longitude'];
        }
    }

    $attachments = '';
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
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

    $query = "INSERT INTO userfire_reports (id, created_at, description, category, location, latitude, longitude, attachments)
              VALUES('$new_id', '$created_at', '$description', '$category', '$location', '$latitude', '$longitude', '$attachments')";
    
    if (mysqli_query($conn, $query)) {
        echo "Data submitted successfully.";
        echo "Latitude and longitude values updated successfully.";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Start Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        body,h1 {font-family: "Lato", sans-serif}
        .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
        .fa-anchor,.fa-coffee {font-size:200px}
        .link-button {
            display: inline-block;
            padding: 10px 25px;
            font-size: 16px;
            font-weight: bold;
            align: center;
            text-decoration: none;
            color: white;
            background-color: #00308F;
            border: 2px solid white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .link-button:hover {
            color: #0056b3;
            background-color: rgba(0, 123, 255, 0.1);
            border-color: #0056b3;
        }

        .link-button:active {
            color: #004080;
            background-color: rgba(0, 123, 255, 0.2);
            border-color: #004080;
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

        .report-event {
            padding: 35px 16px;
            color: darkblue;
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
        .input-text {
            width: 160px;
            padding: 2px 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            text-align: center;
            
        }
        
        .header-title {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blinking-dot {
            height: 20px;
            width: 20px;
            background-color: white;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1s infinite;
            margin-right: 10px;
            position: relative;
            top: -5px; /* Adjust this value as needed */
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        
    </style>
</head>
<body>

<div class="w3-top">
    <div class="w3-bar w3-black w3-card navbar">
        <a href="../indexfire.php" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="fa fa-arrow-left"> Home Page</i></a>
        <div class="w3-bar-item w3-padding-large w3-black navbar-item" style="margin-left: 50px;">NATIONAL FIRE DEPARTMENT</div>
        <a href="datauserfire.php" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="fa fa-database"></i> Database Page</a>
    </div>
</div>
<br>
<header class="w3-container w3-red w3-center report-event">
<br>
<div class="header-title">
        <div class="blinking-dot"></div>
        <h1>REPORT LIVE EVENT</h1>
        
    </div>
    
    <form class="myForm" action="" method="post" autocomplete="on" style="margin-right: 100px;"enctype="multipart/form-data">
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" class="form-control" style="display: inline-block; width: 20%;" id="category" name="category" required value=""><br>

            <label for="description">description</label>
            <input type="text" id="description" class="form-control" style="display: inline-block; width: 20%;" name="description" required value="" autocomplete="off"><br>

            <label for="posizione">Location</label>
            <input type="text" id="posizione" class="form-control" style="display: inline-block; width: 20%;" name="posizione" autocomplete="off"><br>

            <label for="created_at" style="margin-left: 39px;">Date</label>
            <input type="text" id="created_at" name="created_at" required autocomplete="off" class="form-control" style="display: inline-block; width: 20%;">
            <button class="btn btn-outline-secondary" type="button" id="datePickerBtn" style="display: inline-block;">
                <i class="fa fa-calendar"></i>
            </button><br>

        <label for="attachments">Attachments (images or videos):</label>
        <input type="file" id="attachments" name="attachments[]" multiple required><br><br>

        </div>
        <button type="submit" name="submit" class="link-button" style="margin-left: 90px;">Submit</button>
    </form><br>    

</header>

<!-- First Grid -->
<div class="w3-row-padding w3-padding-64 w3-container">
    <div class="w3-content custom-bg-red">
        <div class="w3-twothird">
            <h1>Please submit the event's data carefully</h1>
            <h5 class="w3-padding-32"></h5>

            <p class="w3-text-grey">
            Reporting a live event to the fire department is crucial for ensuring a swift and effective response to emergencies. 
            This process requires providing thorough and precise details about the incident. 
            Make sure to specify the event's category, such as a fire, smoke detection, gas leak, chemical spill, or any other hazardous situation.
            Provide a detailed description of what occurred, including any notable actions or behaviors. 
            Accurately note the location where the event took place and provide the exact date and time of the incident.
            By carefully filling out this information, individuals help the fire department respond promptly and appropriately to the situation.
            Whether it's a minor incident or a major emergency, accurate reporting plays a vital role in maintaining public safety and minimizing damage.
    </div>
        <div class="w3-third w3-center">
            <img src="../../tweetmapping/firehat.png" alt="Your Image" style="width: 300px;">
        </div>
    </div>
</div>

<div class="w3-container w3-black w3-center w3-opacity w3-padding-64">
    <h1 class="w3-margin w3-xlarge">For emergency: Dial 199 to report emergencies</h1>
</div>

<!-- Footer -->
<footer class="w3-container w3-padding-64 w3-center w3-opacity">
    <div class="w3-xlarge w3-padding-32">
        <a href="https://www.facebook.com/vaggelis.xatziantoniou/"><i class="fa fa-facebook-official w3-hover-opacity"></i></a>
        <a href="https://www.instagram.com/vangelis_chznt/"><i class="fa fa-instagram w3-hover-opacity"></i></a>
        <a href="https://twitter.com/greenpain13"><i class="fa fa-twitter w3-hover-opacity"></i></a>
        <a href="https://www.linkedin.com/in/vangelis-chatziantoniou-6a1792155/"><i class="fa fa-linkedin w3-hover-opacity"></i></a>
    </div>
    <p>Powered by Greenpain</p>
</footer>

<script>
    // Used to toggle the menu on small screens when clicking on the menu button
    function myFunction() {
        var x = document.getElementById("navDemo");
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
        } else {
            x.className = x.className.replace(" w3-show", "");
        }
    }
</script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- Include Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    // Initialize Bootstrap Datepicker
    $('#created_at').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    // Trigger Bootstrap Datepicker when the button is clicked
    $('#datePickerBtn').click(function () {
        $('#created_at').datepicker('show');
    });
</script>
</body>
</html>