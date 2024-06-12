<?php
require 'connection.php';

// Read and decode the JSON file
$jsonData = file_get_contents('towns_in_greece_with_greek_names.json');
$locations = json_decode($jsonData, true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = $_POST['location'];

    // Process the data (e.g., save to database)

    // Return a response
    echo "Data received: " . htmlspecialchars($location);
} else {
    echo "Invalid request.";
}
// Loop through each entry in the JSON data
foreach ($locations as $location) {
    // Extract values from the JSON data
    $name = $location['Name'];
    $name_el = $location['name_el'];
    $latitude = $location['Latitude'];
    $longitude = $location['Longitude'];

    // Update latitude and longitude values in the 'policetweets' table
    updateTable('policetweets', $name, $name_el, $latitude, $longitude);

    // Update latitude and longitude values in the 'firetweets' table
    updateTable('firetweets', $name, $name_el, $latitude, $longitude);
}

echo "Latitude and longitude values updated successfully.";

// Function to update latitude and longitude values in a specified table
function updateTable($table, $city, $city_el, $latitude, $longitude) {
    global $conn;

    // Retrieve tweets where the location matches the city name
    $query = "SELECT id, geograpy_woi, spacy_woi FROM $table WHERE geograpy_woi = '$city' OR spacy_woi = '$city' OR spacy_woi = '$city_el'";
    $result = mysqli_query($conn, $query);

    // Update latitude and longitude values for matching tweets
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];

        // Determine the value to use for geograpy_woi
        $geograpy_woi = !empty($row['geograpy_woi']) ? $row['geograpy_woi'] : $city;

        // Determine the value to use for spacy_woi
        // If spacy_woi matches either city or city_el, retain the existing value
        // Otherwise, prefer city_el if empty
        if ($row['spacy_woi'] !== $city && $row['spacy_woi'] !== $city_el) {
            $spacy_woi = $city_el;
        } else {
            $spacy_woi = $row['spacy_woi'];
        }

        // Update latitude and longitude values
        $updateQuery = "UPDATE $table SET latitude='$latitude', longitude='$longitude', geograpy_woi='$geograpy_woi', spacy_woi='$spacy_woi' WHERE id='$id'";
        mysqli_query($conn, $updateQuery);
    }
}
?>
