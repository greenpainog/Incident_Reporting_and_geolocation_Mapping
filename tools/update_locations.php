<?php
require '../connection.php';

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

$apiKey = 'AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM';  // Replace with your actual API key

// Fetch all entries that need updating
$query = "SELECT id, spacy_woi FROM firetweets WHERE latitude IS NULL OR longitude IS NULL";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching records: " . mysqli_error($conn));
}

$updated_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $location = $row['spacy_woi'];

    if ($location) {
        $coords = getCoordinates($location, $apiKey);
        if ($coords) {
            $latitude = $coords['latitude'];
            $longitude = $coords['longitude'];

            // Update the database with the new coordinates
            $update_query = "UPDATE firetweets SET latitude = '$latitude', longitude = '$longitude' WHERE id = '$id'";
            if (!mysqli_query($conn, $update_query)) {
                echo "Error updating record ID $id: " . mysqli_error($conn) . "<br>";
            } else {
                $updated_count++;
                echo "Successfully updated record ID $id with latitude: $latitude, longitude: $longitude<br>";
            }
        } else {
            echo "Coordinates not found for location: $location (ID: $id)<br>";
        }
    } else {
        echo "No location specified for record ID $id<br>";
    }
}

mysqli_close($conn);

echo "<br>Total records updated: $updated_count";
?>
