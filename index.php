<?php
include("config.php");
include("steps.php");

$mysqli = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB); 

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
$firstSteps = getFirstSteps();
$goalPercent = getGoalPercent();

echo '<html>
  <head>
  <title>Steps: '.$cursteps[0]['steps'].'</title>
  <script src="Chart.js/Chart.js"></script>
  </head>
  ';
$format = "I've taken <b>%s</b> steps today, %s%% of my goal and <b>%s</b> overall steps since %s. Which is about %s miles.<br>";
echo sprintf($format, $cursteps[0]['steps'], $goalPercent, $totalSteps, $firstSteps[0]['time'], $totalMiles);


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
