<?php
include("config.php");

$mysqli = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB); 

/**
* Function to reterive the current steps
*
* @var N/A
* @return associative array - time, steps
*/
function getCurrentSteps(){
  $rows = array();
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT time,steps from steps ORDER BY ID DESC LIMIT 1");
  while($r = $result->fetch_assoc()) {
        $rows[] = $r;
  }
  return $rows;
}

/**
* Function to reterive max steps in all days
*
* @var N/A
* @return multiple arrays - time, steps
*/
function getMaxDailySteps(){
  $time = array();
  $steps = array();
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT DATE(time) AS time, MAX(steps) AS steps FROM steps GROUP BY DAY(time)");
  $rows = array();
  while($row = $result->fetch_assoc()) {
        $time[] = $row['time'];
        $steps[] = $row['steps'];
  }
  return array($time, $steps);
}

/**
* Function to reterive max steps in all days
*
* @var N/A
* @return multiple arrays - diff between previous steps, steps
*/
function getDailySteps(){
  $time = array();
  $steps = array();
  $prevNum = 0;
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT DATE_FORMAT(time, '%H:%i') AS time,steps FROM steps WHERE DATE(`time`) = CURDATE()");
  while($row = $result->fetch_assoc()) {
        $diff = $row['steps'] - $prevNum;
        $prevNum = $row['steps'];
        $time[] = $row['time'];
        $steps[] = $diff;
  }
  return array($time, $steps);
}

/**
* Function to reterive the total steps walked.
*
* TODO: Should probably create a query to find the total steps.
*
* @var N/A
* @return intiger - total of all steps walked
*/
function getTotalSetps(){
  $sum = 0;
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT DATE(time) AS time, MAX(steps) AS steps FROM steps GROUP BY DAY(time)");
  while($row = $result->fetch_assoc()) {
        $sum = $sum + $row['steps'];
  }
  return $sum;
}

/**
* Function to calculates the total miles walked.
*
* @var N/A
* @return intiger - total miles walked
*/
function getTotalMiles(){
  return getTotalSetps()/2125;
}

$GETSTEPS = isset($_GET['steps']) ? $_GET['steps'] : '';
$GETPASS  = isset($_GET['pass']) ? $_GET['pass'] : '';
$GETJSON  = isset($_GET['json']) ? $_GET['json'] : '';

if(!empty($GETSTEPS) and $GETPASS === $ACCESS_PASS){
  $steps = $mysqli->real_escape_string($GETSTEPS); 
  $query = sprintf("INSERT INTO steps (steps) value ('%s')", $steps);
  $mysqli->real_query($query);
  exit();
}
elseif ($GETJSON === 'true') {
  print json_encode(getCurrentSteps());
  exit();
}

$cursteps = getCurrentSteps();
$totalSteps = getTotalSetps();
$totalMiles = getTotalMiles();

echo '<html>
  <head>
  <title>Steps: '.$cursteps[0]['steps'].'</title>
  <script src="Chart.js/Chart.js"></script>
  </head>
  ';
$format = "I've taken <b>%s</b> steps today and <b>%s</b> overall steps. Which is about %s miles.<br>";
echo sprintf($format, $cursteps[0]['steps'], $totalSteps, $totalMiles);


list ($dailyTime, $dailySteps) = getDailySteps();
list ($dailyMaxTime, $dailyMaxSteps) = getMaxDailySteps();
?>
<canvas id="daily" width="1200" height="400"></canvas>
<canvas id="dailyMax" width="1200" height="400"></canvas>

<script type="text/javascript">
var dailyTime = '<?php echo json_encode($dailyTime); ?>';
var dailySteps = '<?php echo json_encode($dailySteps); ?>';
var dailyData = {
    labels:  eval(dailyTime),
    datasets: [
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: eval(dailySteps)
        }
    ]
};
var daily = document.getElementById("daily").getContext("2d");
var myDaily = new Chart(daily).Line(dailyData, {
  scaleShowGridLines : false,
  bezierCurve : false,
  pointDot : false,
});

var dailyMaxTime = '<?php echo json_encode($dailyMaxTime); ?>';
var dailyMaxSteps = '<?php echo json_encode($dailyMaxSteps); ?>';
var dailyMaxData = {
    labels:  eval(dailyMaxTime),
    datasets: [
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: eval(dailyMaxSteps)
        }
    ]
};
var dailyMax = document.getElementById("dailyMax").getContext("2d");
var mydailyMax = new Chart(dailyMax).Line(dailyMaxData, {
  scaleShowGridLines : false,
  bezierCurve : false,
  pointDot : false,
});


</script>
