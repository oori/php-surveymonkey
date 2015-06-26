<?php
/**
 * Test initialization
 * @package php-surveymonkey
 * @subpackage tests
 */

require_once('config.inc.php');

require_once('../SurveyMonkey.class.php');

/** @var SurveyMonkey Initialize SurveyMonkey class */
$survey_monkey = new SurveyMonkey($api_key, $access_token);

// Try to get first survey_id,  if not manually defined above
if (empty($survey_id)) {
  $results = $survey_monkey->getSurveyList();
  if ($results["success"]) {
    $surveys = $results["data"]["surveys"];
    if (count($surveys) === 0) { echo "ERROR: You must create a survey on surveymonkey!"; die(); }
    $survey_id = $surveys[0]["survey_id"]; // get first survey's id
    echo "Auto selecting first survey: {$survey_id} \n\r";
  }
  else {
    echo "ERROR: " . $results["message"];
    die();
  }
}




/**
 * Handle returned results
 * @param  string  $name  Title of test
 * @param  array  $array Result array
 */
function handleResults($name, $array){

  $msg = array();
  $die = false;

  // Header
  $msg[] = "================== {$name} ==================";
  $msg[] = str_repeat('•', strlen( end($msg) ));
  $msg[] = "";

  if (!is_array($array)) {
    $msg[] = "ERROR: Something is wrong, I expect an array";
    $die = true;
  }
  else {
    if (empty($array["success"])) {
      $msg[] = "* FAILURE *";
      $msg[] = "Message = " . print_r($array["message"], true);
      $die = true;
    }
    else {
      $msg[] = "Data = " . print_r($array["data"], true);
    }
  }


  $sep = "\n\r";
  echo $sep . implode($sep, $msg) . $sep;
  if ($die) die();
}



?>