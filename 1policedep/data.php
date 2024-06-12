<?php
session_start(); // Start the session
require '../connection.php';


// Number of entries per page
$entriesPerPage = 10;

// Initialize variables
$currentPage = 1;
$totalEntries = 0;
$totalPages = 1; // Initialize totalPages

// Check if a search query is submitted
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    // Sanitize the input to prevent SQL injection
    $eventId = mysqli_real_escape_string($conn, $_GET['event_id']);

    // Fetch event details based on the provided ID
    $searchResult = mysqli_query($conn, "SELECT * FROM policetweets WHERE id LIKE '$eventId%'");

    // Count the total number of entries for the searched ID
    $totalEntries = mysqli_num_rows($searchResult);

    // Calculate total number of pages for the searched ID
    $totalPages = ceil($totalEntries / $entriesPerPage);

    // Get the current page number
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = intval($_GET['page']);
    }

    // Set session variable to indicate that search result message has been displayed
    $_SESSION['search_result_displayed'] = true;
    
} else {
    // If no search query is submitted, fetch all events
    $totalEntries = mysqli_query($conn, "SELECT COUNT(*) FROM policetweets")->fetch_row()[0];
    $totalPages = ceil($totalEntries / $entriesPerPage);
}

// Get the current page number
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
}

// Calculate the offset for the SQL query
$offset = ($currentPage - 1) * $entriesPerPage;

// Determine the SQL query based on search and sort criteria
$sqlQuery = "SELECT * FROM policetweets";

// Fetch entries for the current page
$rowsResult = null;
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    // Fetch entries for the searched ID
    if (isset($_GET['sort']) && $_GET['sort'] === 'category') {
        $rowsResult = mysqli_query($conn, "SELECT * FROM policetweets WHERE id LIKE '$eventId%' ORDER BY category DESC LIMIT $offset, $entriesPerPage");
    } else {
        // Default sort order is by created_at
        $rowsResult = mysqli_query($conn, "SELECT * FROM policetweets WHERE id LIKE '$eventId%' ORDER BY created_at DESC LIMIT $offset, $entriesPerPage");
    }
} else {
    // Fetch all events
    if (isset($_GET['sort']) && $_GET['sort'] === 'category') {
        $rowsResult = mysqli_query($conn, "SELECT * FROM policetweets ORDER BY category DESC LIMIT $offset, $entriesPerPage");
    } else {
        // Default sort order is by created_at
        $rowsResult = mysqli_query($conn, "SELECT * FROM policetweets ORDER BY created_at DESC LIMIT $offset, $entriesPerPage");
    }
}

// Ensure $rowsResult is valid before using it
if ($rowsResult && mysqli_num_rows($rowsResult) > 0) {
    $rows = mysqli_fetch_all($rowsResult, MYSQLI_ASSOC);
} else {
    $rows = [];
}



if(isset($_POST["submit"])){
  // Process form data
  
  // Your existing code to insert data into the database
  
  // Execute filllocation.php
  exec('php filllocation.php', $output, $return);
  
  


}


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Database Page</title>
    <meta charset="UTF-8">
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
</style>
<style> 
        /* The alert message box */
        .alert {
            opacity: 2;
            transition: opacity 0.5s; /* 600ms to fade out */
            padding: 3px;
            background-color: #f44336;
            padding: 5px;
            color: white;
            margin-top: 2px;
            width: 270px; /* Adjust the width as needed */
            justify-content: space-between; /* Space between content and close button */
            align-items: center; /* Center items vertically */
            margin-left: auto; /* Align it to the center horizontally */
            margin-right: auto; /* Align it to the center horizontally */
        }

        .alert.success {background-color: #2460bf;}
        .alert.info {background-color: #2196F3;}
        .alert.warning {background-color: #ff9800;}

        .closebtn {
          margin-left: 15px;
          color: white;
          font-weight: bold;
          float: right;
          font-size: 22px;
          line-height: 20px;
          cursor: pointer;
          transition: 0.3s;
        }

        .closebtn:hover {
          color: grey;
        }
        .search-message {
        margin-bottom: 10px;
        font-size: 16px;
        color: #white;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px; /* Adjust the margin as needed */
        }

        .button-container button {
            margin-left: 10px; /* Adjust the spacing between the input and button */
        }

        .button-12 {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 6px 14px;
            font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            border-radius: 6px;
            border: none;

            background: #000000;
            box-shadow: 0px 0.5px 1px rgba(0, 0, 0, 0.1), inset 0px 0.5px 0.5px rgba(255, 255, 255, 0.5), 0px 0px 0px 0.5px rgba(0, 0, 0, 0.12);
            color: #DFDEDF;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
          }

          .button-12:focus {
            box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1px rgba(0, 0, 0, 0.1), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
            outline: 0;
          }
        .pagination-container a {
            margin-right: 10px; /* Adjust the value to increase/decrease the space between pages */
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
            background-color: #f0f0f0; /* Set background color of the page */
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
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: center; /* Center the text */
        }

        th {
            background-color: #f2f2f2;
        }

        .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0; /* Optional: adds some space above and below the pagination */
    }

    .pagination-container a.btn {
        margin: 0 5px;
        padding: 5px 10px;
        text-decoration: none;
        background-color: white;
        color: #000;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .pagination-container a.btn:hover {
        background-color: #007bff;
    }

    .pagination-container .active {
        font-weight: bold;
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .pagination-container span {
        margin: 0 5px;
    }
         .edit-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .edit-button:hover {
            background-color: #45a049;
        }

        .edit-button .fas {
            margin-right: 5px;
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
  <body style="background-color:dodgerblue;">
  <div class="w3-top">
  <div class="w3-bar w3-black w3-card navbar">
    <a href="index.php" class="w3-bar-item w3-button w3-padding-large w3-black" class="page-link"><i class="fa fa-arrow-left"></i> Report Event</a>
    <div class="w3-bar-item w3-padding-large w3-black navbar-item" >NATIONAL POLICE DEPARTMENT</div>
    <a href="map.php" class="link-button"  class="page-link"><i class="fa fa-map-marker"></i> View event map</a>
  </div>
</div>

  <!-- Navbar on small screens -->
    <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
      <a href="data.php" class="w3-bar-item w3-button w3-padding-large">Database Page</a>
      </div>
    </div>
    <header class="w3-container w3-blue w3-center" style="padding:30 16px">
  <h1 class="w3-margin w3-xlarge">

  <br>
  
 
  
  <p class="w3-xlarge">Listed Events</p>
  </h1>




  





  <?php
// Display search result message only if it hasn't been displayed yet in this session
if (isset($_SESSION['search_result_displayed']) && $_SESSION['search_result_displayed']) {
    echo '<div id="searchResultMsg" class="alert success">';
    echo 'Found ' . $totalEntries . ' entries for the search.';
    echo '<span class="closebtn" onclick="closeSearchResult()">×</span>'; // Close button
    echo '</div>';

    // Reset the session variable to avoid displaying the message on subsequent pages
    $_SESSION['search_result_displayed'] = false;
}
?>

<!-- Search Section -->
<div class="w3-container w3-center" style="margin-top:50px">
    <h2>Search Event</h2>
    <form action="data.php" method="GET">
        <input type="text" name="event_id" placeholder="Enter Event ID">
        <div class="button-container">
            <button class="button-12" type="submit">Search</button>
        </div>
    </form>
</div>

<?php if (isset($_SESSION['search_result_displayed']) && $_SESSION['search_result_displayed']): ?>
    <div class="w3-container w3-center">
        <div class="alert success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Search result found.
        </div>
    </div>
    <?php unset($_SESSION['search_result_displayed']); ?>
<?php endif; ?>

<div class="w3-container w3-center">
    <form action="data.php" method="GET">
        <input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : ''; ?>">
        <select name="sort" onchange="this.form.submit()">
            <option value="created_at" <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'created_at') ? 'selected' : ''; ?>>Sort by Date</option>
            <option value="category" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'category') ? 'selected' : ''; ?>>Sort by Category</option>
        </select>
    </form>
</div>





<div class="w3-container w3-center pagination-container">
    <h4>Page</h4>
    <?php
    // Calculate the range of page numbers to display
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $startPage + 4);

    // Adjust start page if end page is less than 5
    if ($endPage - $startPage < 4) {
        $startPage = max(1, $endPage - 4);
    }

    // Previous page button
    if ($currentPage > 1) {
        echo '<a class="btn" href="?page=' . ($currentPage - 1) . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">&laquo; Prev</a>';
    }

    // First page link
    if ($startPage > 1) {
        echo '<a class="btn" href="?page=1' . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">1</a>';
        if ($startPage > 2) {
            echo '<span>...</span>';
        }
    }

    // Middle pages links
    for ($i = $startPage; $i <= $endPage; $i++) {
        echo '<a class="btn' . ($i === $currentPage ? ' active' : '') . '" href="?page=' . $i . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">' . $i . '</a>';
    }

    // Last page link
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            echo '<span>...</span>';
        }
        echo '<a class="btn" href="?page=' . $totalPages . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">' . $totalPages . '</a>';
    }

    // Next page button
    if ($currentPage < $totalPages) {
        echo '<a class="btn" href="?page=' . ($currentPage + 1) . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">Next &raquo;</a>';
    }
    ?>
</div>



    

    





  <!-- Display the search results or all events in the table -->
<div class="w3-container w3-center table-container">
    
    <table class="w3-table w3-bordered w3-hoverable">
        <tr >
            <th>ID</th>
            <th>Category</th>
            <th>Text</th>
            <th>Location</th>
            <th>Created At</th>
            <th>Maps</th>
        </tr>

        <?php if (isset($row)): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["category"]; ?></td>
                <td><?php echo $row["plain_text"]; ?></td>
                <td><?php echo !empty($row["spacy_woi"]) ? $row["spacy_woi"] : "No Location"; ?></td>
                <td><?php echo $row["created_at"]; ?></td>
                <td>
                    <?php if (!empty($row["latitude"]) && !empty($row["longitude"])): ?>
                        <iframe style="width: 100%; height: 100%;" src="https://www.google.com/maps?q=<?php echo $row["latitude"]; ?>,<?php echo $row[""]; ?>&hl=es;z=14&output=embed"></iframe>
                    <?php else: ?>
                        <p>No map available</p>
                    <?php endif; ?>
                </td>
                <td><a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a></td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["category"]; ?></td>
                    <td><?php echo $row["plain_text"]; ?></td>
                    <td><?php echo !empty($row["spacy_woi"]) ? $row["spacy_woi"] : "No Location"; ?></td>

                    <td><?php echo $row["created_at"]; ?></td>
                    <td>
                        <?php if (!empty($row["latitude"]) && !empty($row["longitude"])): ?>
                            <iframe style="width: 100%; height: 100%;" src="https://www.google.com/maps?q=<?php echo $row["latitude"]; ?>,<?php echo $row["longitude"]; ?>&hl=es;z=14&output=embed"></iframe>
                        <?php else: ?>
                            <p>No map available</p>
                        <?php endif; ?>
                    </td>
                    <td><a href="edit.php?id=<?php echo $row['id']; ?>" class='edit-button'><i class='fas fa-edit'></i> Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
  
<!-- Pagination container -->

<div class="w3-container w3-center pagination-container">
    <h4>Page</h4>
    <?php
    // Calculate the range of page numbers to display
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $startPage + 4);

    // Adjust start page if end page is less than 5
    if ($endPage - $startPage < 4) {
        $startPage = max(1, $endPage - 4);
    }

    // Previous page button
    if ($currentPage > 1) {
        echo '<a class="btn" href="?page=' . ($currentPage - 1) . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">&laquo; Prev</a>';
    }

    // First page link
    if ($startPage > 1) {
        echo '<a class="btn" href="?page=1' . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">1</a>';
        if ($startPage > 2) {
            echo '<span>...</span>';
        }
    }

    // Middle pages links
    for ($i = $startPage; $i <= $endPage; $i++) {
        echo '<a class="btn' . ($i === $currentPage ? ' active' : '') . '" href="?page=' . $i . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">' . $i . '</a>';
    }

    // Last page link
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            echo '<span>...</span>';
        }
        echo '<a class="btn" href="?page=' . $totalPages . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">' . $totalPages . '</a>';
    }

    // Next page button
    if ($currentPage < $totalPages) {
        echo '<a class="btn" href="?page=' . ($currentPage + 1) . (isset($_GET['event_id']) ? '&event_id=' . $_GET['event_id'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : 'category') . '">Next &raquo;</a>';
    }
    ?>
</div>


  


    
  </header>


<!-- Load the Google Maps JavaScript API with the callback to initialize the map -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM&callback=initMap" async defer></script>
 

<script>
        function closeSearchResult() {
            var searchResultMsg = document.getElementById('searchResultMsg');
            searchResultMsg.style.opacity = '0';
            setTimeout(function() {
                searchResultMsg.style.display = 'none';
            }, 500);
        }
</script>

<script>
    window.onload = function() {
        // Populate the search input field with the value from the hidden input field
        var hiddenInput = document.getElementById('hidden_event_id');
        var searchIdInput = document.getElementById('event_id');
        searchIdInput.value = hiddenInput.value;
    }

    // Function to update the hidden input field with the current value of the search ID input field
    function updateHiddenInput() {
        var searchIdInput = document.getElementById('event_id');
        var hiddenInput = document.getElementById('hidden_event_id');
        hiddenInput.value = searchIdInput.value;
    }
</script>

<script>
function delayRedirect(event) {
  event.preventDefault(); // Prevent the default link behavior
  
  // Add a delay of 1000 milliseconds (1 second) before redirecting
  setTimeout(function() {
    window.location.href = event.target.href;

  }, 500);

  </script>
}
<script>
    function clearSearch() {
        document.getElementById('event_id').value = ''; // Clear the search input
        window.location.href = 'data.php'; // Redirect to the original page
    }
</script>

<script src="script.js"></script>
<script>
        // Show loading animation when navigating to a new page
        const loadingOverlay = document.querySelector('.loading-overlay');

        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const href = this.getAttribute('href');
                loadingOverlay.style.display = 'flex'; // Show loading overlay
                setTimeout(() => {
                    window.location.href = href; // Navigate to the new page after a delay (you can adjust this)
                }, 1000); // Example delay of 1 second (1000 milliseconds)
            });
        });
    </script>
    <script>
  window.onload = function() {
    // Create a new XMLHttpRequest object
    var xhttp = new XMLHttpRequest();
    
    // Define the PHP file to call
    var url = "filllocation.php";
    
    // Send the AJAX request
    xhttp.open("GET", url, true);
    xhttp.send();
  };
</script>




  </body> 
</html>


