<?php
require 'connection.php';


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

function extractLocationFromText($text) {
    // Escape the input to make it safe for shell command
    $text = escapeshellarg($text);
    // Convert the command to handle UTF-8
    $command = "python extract_location_spacy.py " . $text;
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
    $max_id_query = "SELECT MAX(id) AS max_id FROM policetweets";
    $max_id_result = mysqli_query($conn, $max_id_query);
    $max_id_row = mysqli_fetch_assoc($max_id_result);
    $new_id = $max_id_row['max_id'] + 1;

    $category = $_POST["category"];
    $plain_text = $_POST["text"];
    $location = isset($_POST["Location"]) ? $_POST["Location"] : null; // Get the value from the location input field
    $created_at = $_POST["created_at"];

    // Check if the location input field is empty
    if (empty($location)) {
        // Extract location from text input
        $spacy_woi = extractLocationFromText($plain_text);
    } else {
        // Use the location entered by the user
        $spacy_woi = $location;
    }

    $latitude = null;
    $longitude = null;

    if ($spacy_woi) {
        $apiKey = 'AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM';
        $coords = getCoordinates($spacy_woi, $apiKey);
        if ($coords) {
            $latitude = $coords['latitude'];
            $longitude = $coords['longitude'];
        }
    }

    $query = "INSERT INTO policetweets (id, created_at, plain_text, category, spacy_woi, latitude, longitude)
              VALUES('$new_id', '$created_at', '$plain_text', '$category', '$spacy_woi', '$latitude', '$longitude')";
    if (mysqli_query($conn, $query)) {
        echo "Data submitted successfully.";
        echo "Latitude and longitude values updated successfully.";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test File</title>
    <link rel="stylesheet" href="navbar.css">
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
            text-align: center;
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
        }

        .navbar-item {
            margin: 0 20px;
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
        .red-dot-icon {
            color: red;
            font-size: 10px; /* Adjust size as needed */
            vertical-align: middle; /* Align with text */
            margin-right: 5px; /* Space between the dot and text */
        }
    </style>
</head>
<body>
    <nav>
        <div class="navbar">
            <div class="container nav-container">
                <input class="checkbox" type="checkbox" name="" id="" />
                <div class="hamburger-lines">
                    <span class="line line1"></span>
                    <span class="line line2"></span>
                    <span class="line line3"></span>
                </div>  
                <div class="logo">
                <h1>NATIONAL POLICE DEPARTMENT</h1>
                </div>
                <div class="menu-items">
                    <li>        <a href="../startpage.php" ><i class="fa fa-home"> Home Page</i></a>
                    </li>
                    <li>        <a href="data.php" ><i class="fa fa-database"></i> Database Page</a>
                    </li>
                    <li><a href="user/indexuser.php" ><i class="fa fa-circle red-dot-icon"></i> Report live event</a></li>
                    <li><a href="user/map.php" ><i class="fa fa-map "></i> Event Map</a></li>
                </div>
            </div>
        </div>
    </nav>



    <br>
<header class="w3-container w3-blue w3-center report-event">
    <h1 class="w3-margin w3-xlarge">Report Event</h1>
    <form class="myForm" action="" method="post" autocomplete="on" style="margin-right: 100px;">
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" class="form-control" style="display: inline-block; width: 20%;" id="category" name="category" required value=""><br>

            <label for="text">Text</label>
            <input type="text" id="text" class="form-control" style="display: inline-block; width: 20%;" name="text" required value="" autocomplete="off"><br>

            <label for="Location">Location</label>
            <input type="text" id="Location" class="form-control" style="display: inline-block; width: 20%;" name="Location" autocomplete="off"><br>

            <label for="created_at" style="margin-left: 39px;">Date</label>
            <input type="text" id="created_at" name="created_at" required autocomplete="off" class="form-control" style="display: inline-block; width: 20%;">
            <button class="btn btn-outline-secondary" type="button" id="datePickerBtn" style="display: inline-block;">
                <i class="fa fa-calendar"></i>
            </button>
        </div>
        <button type="submit" name="submit" class="link-button" style="margin-left: 90px;">Submit</button>
    </form><br>
    <div><h3>Is there an event of violation, offense or iligality happening right in front of you?<br>
    Report it now!</h3>
    <br>
    <a href="user/indexuser.php" class="w3-bar-item w3-button w3-padding-large w3-black"><i class="fa fa-circle red-dot-icon"></i> Report live event</a></div>
</header>



<!-- First Grid -->
<div class="w3-row-padding w3-padding-64 w3-container">
    <div class="w3-content">
        <div class="w3-twothird">
            <h1>Please submit the event's data carefully</h1>
            <h5 class="w3-padding-32"></h5>

            <p class="w3-text-grey">Reporting an event to the police department requires providing thorough and precise details about the incident.
                This includes specifying the event's category, providing a detailed description of what occurred, noting the location where the event took place, and providing the date and time of the incident.
                By carefully filling out this information, individuals ensure that law enforcement can respond promptly and appropriately to the situation.
                Whether it's a crime, accident, or any other issue requiring police assistance, accurate reporting plays a crucial role in maintaining public safety and order.</p>
        </div>

        <div class="w3-third w3-center">
            <img src="tweetmapping/policehat.png" alt="Your Image" style="width: 300px;">
        </div>
    </div>
</div>

<div class="w3-container w3-black w3-center w3-opacity w3-padding-64">
    <h1 class="w3-margin w3-xlarge">For emergency: Dial 100 to report emergencies</h1>
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
</body>
</html>
