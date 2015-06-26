<?php
/**
 * Test all getters
 * @package php-surveymonkey
 * @subpackage tests
 */

require_once('init.inc.php');

/**
 * TEST:  all getter methods
 */



//////////////////////////////////////////////////////////
//GET SURVEY LIST
//////////////////////////////////////////////////////////

/** @var array (optional) parameters */
$params = array(
  // 'page'=>2,
  // 'page_size'=>1,
  // 'start_date'=>'2013-02-02 00:00:00',
  // 'end_date'=>'2013-04-12 22:43:01',
  // 'title'=>'My Survey',
  // 'recipient_email'=>'test@gmail.com',
  'order_asc'=>false,
  'fields'=>array('title','preview_url','date_created','date_modified','question_count')
);

$results = $survey_monkey->getSurveyList($params);
handleResults("getSurveyList", $results);



//////////////////////////////////////////////////////////
//GET SURVEY DETAILS
//////////////////////////////////////////////////////////
$results = $survey_monkey->getSurveyDetails($survey_id);
handleResults("getSurveyDetails", $results);



//////////////////////////////////////////////////////////
//GET COLLECTOR LIST
//////////////////////////////////////////////////////////

/** @var array (optional) parameters */
$params = array(
  // 'page'=>2,
  // 'page_size'=>10,
  // 'start_date'=>'2013-02-02 00:00:00',
  // 'end_date'=>'2013-04-12 22:43:01',
  // 'title'=>'My Survey',
  // 'recipient_email'=>'test@gmail.com',
  // 'order_asc'=>false,
  'fields'=>array('title','preview_url','date_created','date_modified','question_count')
);

$results = $survey_monkey->getCollectorList($survey_id, $params);
handleResults("getCollectorList", $results);

if (!empty($results["data"]["collectors"])) { // Keep first found collector, if any
  $first_collector = $results["data"]["collectors"][0]["collector_id"];
}



//////////////////////////////////////////////////////////
//GET RESPONDENT LIST
//////////////////////////////////////////////////////////
$params = array(
  // 'collector_id'=>'COLLECTORID',
  // 'page'=>2,
  'page_size'=>10,
  // 'start_date'=>'2013-02-02 00:00:00',
  // 'end_date'=>'2013-04-12 22:43:01',
  'order_by'=>'date_start',
  // 'recipient_email'=>'test@gmail.com',
  'order_asc'=>false,
  'fields'=>array('title','preview_url','date_created','date_modified','question_count')
);

$results = $survey_monkey->getRespondentList($survey_id, $params);
handleResults("getRespondentList", $results);

if (!empty($results["data"]["respondents"])) { // Keep first found respondant, if any
  $first_respondent = $results["data"]["respondents"][0]["respondent_id"];
}



//////////////////////////////////////////////////////////
//GET RESPONSES
//////////////////////////////////////////////////////////

$respondent_ids = array();  // Placeholder for you to manually set respondants

if (empty($respondent_ids) && isset($first_respondent)) {
  $respondent_ids[] = $first_respondent;  // if not defined, set it to first
}

$results = $survey_monkey->getResponses($survey_id, $respondent_ids);
handleResults("getResponses", $results);



//////////////////////////////////////////////////////////
//GET RESPONSE COUNTS
//////////////////////////////////////////////////////////

$collector_id = '';  // Placeholder for you to manually set collector

if (empty($collector_id) && isset($first_collector)) {
  $collector_id = $first_collector;  // if not defined, set it to first
}

$results = $survey_monkey->getResponseCounts($collector_id);
handleResults("getResponseCounts", $results);



//////////////////////////////////////////////////////////
//GET USER DETAILS
//////////////////////////////////////////////////////////
$results = $survey_monkey->getUserDetails();
handleResults("getUserDetails", $results);



//////////////////////////////////////////////////////////
//GET TEMPLATE LIST
//////////////////////////////////////////////////////////

$params = array(
  'page'=>1,
  'page_size'=>2,
  'language_id'=>1,
  // 'category_id'=>'',
  'show_only_available_to_current_user'=>true,
  'fields'=>array('title','short_description','date_created','date_modified','question_count')
);

$results = $survey_monkey->getTemplateList($params);
handleResults("getTemplateList", $results);


?>