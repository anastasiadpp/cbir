<?php 
  require 'functions.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>IMAGE RETRIEVAL BASKET</title>
  <link rel="stylesheet" href="style.css" media="screen" title="no title" charset="utf-8">
  <style>
  header{
    position: fixed;
  }
  body{
    background-image: url(symphony.png);
  }
.kolompencarian {  
    text-align: center;
    padding: 50px 200px 0px 200px;
}
input[type=text]:focus {
    width: 500px;  
}
</style>
<title>IMAGE RETRIEVAL BASKET</title>
</head>
<body>
  <div><nav>
  </nav></div>
  <section class="mainpart">
</section>
<div class="cls"></div>
<div class="kolompencarian">
<img src="logo.png"><br><br>
<?php
foreach (new DirectoryIterator('query') as $fileInfo) {
  if($fileInfo->isDot()) continue;
  if (isset($_GET["file"]) && $_GET["file"] == $fileInfo->getFilename()) $class = "selected";
  else $class = "sample";
    echo "<a href='index.php?file=".$fileInfo->getFilename()."&page=1'><img class='".$class."' src='query/".$fileInfo->getFilename()."'></a>\n";
}
?> 
<br>
<?php
if (isset($_GET["file"]) && strlen($_GET["file"]) > 0)
{
  //start timer
  $start = microtime(true);
    
  // Parse image to get y,i,q values
  list($y_values, $i_values, $q_values) = ParseImage("query/".$_GET["file"]);

  // Dempose and trunctate
  DecomposeImage($y_values);
  $y_trunc = TruncateCoeffs($y_values, $COEFFNUM);
  DecomposeImage($i_values);
  $i_trunc = TruncateCoeffs($i_values, $COEFFNUM);
  DecomposeImage($q_values);
  $q_trunc = TruncateCoeffs($q_values, $COEFFNUM);
  $hostname="localhost";
  $username="root";
  $password="cumlaude2018";
  $dbname="cbir_tasia";
  $connection = mysqli_connect($hostname, $username, $password, $dbname );
  // Initialize scores and filenames
  $result = mysqli_query($connection,"SELECT * FROM images");
  while($image = mysqli_fetch_array($result)){
    $scores[$image['image_id']] = $w['Y'][0]*ABS($y_values[0][0] - $image['Y_average'])
                  + $w['I'][0]*ABS($i_values[0][0] - $image['I_average']) 
                  + $w['Q'][0]*ABS($q_values[0][0] - $image['Q_average']);
    $filenames[$image['image_id']] = $image['filename'];
  }
  // compare query coefficients with database
  for ($i = 0; $i < $COEFFNUM; $i++) {

    $query = "SELECT * FROM coeffs_y WHERE X = ".$y_trunc['x'][$i]." AND Y = ".$y_trunc['y'][$i]." AND SIGN = '".$y_trunc['sign'][$i]."'";
    $result = mysqli_query($connection,$query);  
    while($coeff_y = mysqli_fetch_array($result)){
      $scores[$coeff_y['image']] -= $w['Y'][bin($coeff_y['X'],$coeff_y['Y'])];
    }
  
    $query = "SELECT * FROM coeffs_i WHERE X = ".$i_trunc['x'][$i]." AND Y = ".$i_trunc['y'][$i]." AND SIGN = '".$i_trunc['sign'][$i]."'";
     $result = mysqli_query($connection,$query);  
    while($coeff_i = mysqli_fetch_array($result)){
      $scores[$coeff_i['image']] -= $w['I'][bin($coeff_i['X'],$coeff_i['Y'])];
    }
  
    $query = "SELECT * FROM coeffs_q WHERE X = ".$q_trunc['x'][$i]." AND Y = ".$q_trunc['y'][$i]." AND SIGN = '".$q_trunc['sign'][$i]."'";
     $result = mysqli_query($connection,$query);  
    while($coeff_q = mysqli_fetch_array($result)){
      $scores[$coeff_q['image']] -= $w['Q'][bin($coeff_q['X'],$coeff_q['Y'])];
    }
  }
  mysqli_close($connection);
  asort($scores,SORT_NUMERIC);
  ?>
  <br>
  <h1><marquee style="padding-top: 15px; color: black; font-size: 55;"><text><b>HASIL TERKAIT</text></marquee></h1>
  <br>
  <?php
  // paging
  if ($_GET["page"] == 1)
  $prev_page = 1;
  else
    $prev_page = $_GET["page"] - 1;
    $next_page = $_GET["page"] + 1;
  // show results
  $i = 0;
  foreach($scores as $key => $value){
    if ($i >= 9*($_GET["page"]-1) && $i <= (9*$_GET["page"])-1){
    echo "<img src='images/".$filenames[$key]."'>\n";
  }
    $i++;
  }
  echo " </td></tr>\n";
}
?>
  </div>
</body>
</html>