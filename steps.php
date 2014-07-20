<?php
/**
* Function to reterive the current steps
*
* @var N/A
* @return associative array - time, steps
*/
function getCurrentSteps(){
  $rows = array();
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT time,steps from steps ORDER BY id DESC LIMIT 1");
  while($r = $result->fetch_assoc()) {
        $rows[] = $r;
  }
  return $rows;
}

/**
* Function to reterive first steps.
*
* @var N/A
* @return associative array - time, steps
*/
function getFirstSteps(){
  $rows = array();
  $mysqli = $GLOBALS['mysqli'];
  $result = $mysqli->query("SELECT DATE(time) AS time,steps from steps ORDER BY id LIMIT 1");
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
  $steps_per_mile = $GLOBALS['STEPS_PER_MILE'];
  return round(getTotalSetps() / $steps_per_mile, 2);
}

/**
* Function to calculates goal percentage.
*
* @var N/A
* @return intiger - total goal percentage
*/
function getGoalPercent(){
  $goal = $GLOBALS['GOAL_STEPS'];
  $cursteps = getCurrentSteps();
  $percent = ( $cursteps[0]['steps'] / $goal ) * 100;
  return round($percent);
}

?>
