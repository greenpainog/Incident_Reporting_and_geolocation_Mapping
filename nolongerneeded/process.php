<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = $_POST['location'];
    $apiKey = 'AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM';  // Replace with your actual API key

    // Function to get coordinates from Google Maps API
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

    // Get coordinates
    $coords = getCoordinates($location, $apiKey);
    if ($coords) {
        echo "Location: $location<br>";
        echo "Lat: " . $coords['latitude'] . "<br>";
        echo "Long: " . $coords['longitude'] . "<br>";
    } else {
        echo "Could not get coordinates for the location: $location";
    }
}
?>
