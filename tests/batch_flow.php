<?php
/**
 * Flow tests
 * @package php-surveymonkey
 * @subpackage tests
 */

require_once('init.inc.php');


//////////////////////////////////////////////////////////
//CREATE FLOW
//////////////////////////////////////////////////////////
$results = $survey_monkey->createFlow('Movie Survey', array(
  'survey'=>array(
    //'from_survey_id'=>$survey_id,
    'survey_title'=>'__TEST__'
  ),
  'collector'=>array(
    'type'=>'email',
    //'send'=>true,
    'name'=>'__TEST__',
    'recipients'=>array(
      array(
        'email'=>'recipient@domain.com'
      )
    )
  ),
  'email_message'=>array(
    'reply_email'=>'replyto@domain.com',
    'subject'=>'__TEST__'
  )
));

handleResults("createFlow", $results);


//////////////////////////////////////////////////////////
//SEND FLOW
//////////////////////////////////////////////////////////
$params = array(
  'collector'=>array(
    'type'=>'email',
    //'send'=>true,
    'name'=>'__TEST__',
    'recipients'=>array(
      array(
        'email'=>'recipient@domain.com'
      )
    )
  ),
  'email_message'=>array(
    'reply_email'=>'replyto@domain.com',
    'subject'=>'__TEST__'
  )
);
$results = $survey_monkey->sendFlow($survey_id, $params);

handleResults("sendFlow", $results);

?>