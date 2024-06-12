<!DOCTYPE html>
<html>
<head>
<title>Start Page </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="styles.css">

<style>
    .gallery >a{
    --g: -150px; /* the gap */
    --s: 500px; /* the size */
    display: grid;
    border-radius: 50%;
  }

  .gallery > a {
    display: inline-block;
    border-radius: 50%;
    overflow: hidden;
  }

  .gallery > a > img {
    grid-area: 1/1;
    width: var(--s);
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 50%;
    transform: translate(var(--_x, 0), var(--_y, 0));
    cursor: pointer;
    font-size: 0;
    z-index: 0;
    transition: .3s, z-index 0s .3s;
  }


  .gallery > a:hover img {
    font-size: var(--s);
    z-index: 1;
    transition: transform .2s, font-size .3s .2s, z-index 0s;
  }

  .gallery > a:hover img {
    transform: translate(0, 0);
  }

  .gallery > a:nth-child(1) > img {
    clip-path: polygon(15% 95%, .5em 1.2em, 0 1em, 1 1, 100% 0, 100% 1em, calc(100% - .5em) 1.2em);
    --_y: calc(-1*var(--g))
  }

  .gallery > a:nth-child(2) > img {
    clip-path: polygon(15% 95%, .5em 1.2em, 0 1em, 1 1, 100% 0, 100% 1em, calc(100% - .5em) 1.2em);
    --_y: calc(-1*var(--g))
  }

  body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-content: center;
    background: #005EB8;
  }

  /* Style the navigation bar */
.w3-top {
  position: relative;
  z-index: 999; /* Ensure the navigation bar stays on top */
}

.w3-bar {
  display: flex;
  justify-content: center; /* Center the content horizontally */
}

.w3-bar-item {
  flex: 1; /* Distribute the available space equally among items */
  text-align: center; /* Center the text */
}

.w3-bar-item:not(:last-child) {
  margin-right: 10px; /* Add spacing between items */
}



</style>
<body>
  <br>
  <div class="w3-top">
    <div class="w3-bar w3-red w3-card w3-left-align w3-large">
      <div class="w3-bar-item w3-padding-large w3-red">FIRE DEPARTMENT</div>
      <!-- "POLICE" text centered -->
      <div class="w3-bar-item w3-padding-large w3-indigo"> POLICE DEPARTMENT</div>
      <!-- "Database Page" link on the top-right -->
      
    </div>
  </div>
  
  <div class="bgimg w3-display-container w3-animate-opacity w3-text-black">
    <div class="w3-display- w3-padding-large w3-xlarge" style="text-align: center;">
      Choose department
    </div>
  </div>

<div class="gallery">
  <a href="firedep/indexfire.php" class="page-link">
  <img src="https://cdn-icons-png.flaticon.com/512/7242/7242610.png" alt="a hot air balloon">
  </a>
  <a href="1policedep/index.php" class="page-link">
  <img src="https://cdn-icons-png.flaticon.com/512/1085/1085455.png">
  </a>
</div>



</body>
</html>