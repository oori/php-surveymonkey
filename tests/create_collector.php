<?php
/**
 * Test collector creation
 * @package php-surveymonkey
 * @subpackage tests
 */

require_once('init.inc.php');

/**
 * TEST:  Creating collector
 */

//////////////////////////////////////////////////////////
//CREATE COLLECTOR
//////////////////////////////////////////////////////////
$collector_name = '__TEST__';
$results = $survey_monkey->createCollector($survey_id, $collector_name);
handleResults("createCollector", $results);


//////////////////////////////////////////////////////////
//GET COLLECTOR LIST
//////////////////////////////////////////////////////////

/** @var array (optional) parameters */
$params = array(
  'fields'=>array('title','preview_url','date_created','date_modified')
);

$results = $survey_monkey->getCollectorList($survey_id, $params);
handleResults("getCollectorList", $results);

?>