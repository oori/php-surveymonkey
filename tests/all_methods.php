<?php

require_once('../SurveyMonkey.class.php');

/**
 * Call all methods using PHP SDK for SurveyMonkey API v2
 * @package default
 */

//////////////////////////////////////////////////////////
//AUTH
//////////////////////////////////////////////////////////
$api_key = 'YOURAPIKEY';
$access_token = 'YOURACCESSTOKEN';
$survey_monkey = new SurveyMonkey($api_key, $access_token);

//////////////////////////////////////////////////////////
//GET SURVEY LIST
//////////////////////////////////////////////////////////
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

$list = $survey_monkey->getSurveyList($params);
print_r($list);

//////////////////////////////////////////////////////////
//GET SURVEY DETAILS
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$details = $survey_monkey->getSurveyDetails($survey_id);
print_r($details);

//////////////////////////////////////////////////////////
//GET COLLECTOR LIST
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$params = array(
  // 'page'=>2,
  'page_size'=>10,
  // 'start_date'=>'2013-02-02 00:00:00',
  // 'end_date'=>'2013-04-12 22:43:01',
  // 'title'=>'My Survey',
  // 'recipient_email'=>'test@gmail.com',
  'order_asc'=>false,
  'fields'=>array('title','preview_url','date_created','date_modified','question_count')
);

$list = $survey_monkey->getCollectorList($survey_id, $params);
print_r($list);

//////////////////////////////////////////////////////////
//GET RESPONDENT LIST
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
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

$list = $survey_monkey->getRespondentList($survey_id, $params);
print_r($list);

//////////////////////////////////////////////////////////
//GET RESPONSES
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$respondent_ids = array('RID1', 'RID2');

$responses = $survey_monkey->getResponses($survey_id, $respondent_ids);
print_r($responses);

//////////////////////////////////////////////////////////
//GET RESPONSE COUNTS
//////////////////////////////////////////////////////////
$collector_id = 'COLLECTORID';
$response_counts = $survey_monkey->getResponseCounts($collector_id);
print_r($response_counts);

//////////////////////////////////////////////////////////
//GET USER DETAILS
//////////////////////////////////////////////////////////
$user_details = $survey_monkey->getUserDetails();
print_r($user_details);

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

$list = $survey_monkey->getTemplateList($params);
print_r($list);

//////////////////////////////////////////////////////////
//CREATE COLLECTOR
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$collector_name = 'Test Link 123';
$collector = $survey_monkey->createCollector($survey_id, $collector_name);
print_r($collector);


//////////////////////////////////////////////////////////
//CREATE FLOW
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$res = $survey_monkey->createFlow('Movie Survey', array(
  'survey'=>array(
    'from_survey_id'=>$survey_id,
    'survey_title'=>'What movie would YOU like to watch?'
  ),
  'collector'=>array(
    'type'=>'email',
    'send'=>true,
    'name'=>'Test Email Collector',
    'recipients'=>array(
      array(
        'first_name'=>'John',
        'last_name'=>'Smith',
        'email'=>'tester@gmail.com'
      ),
      array(
        'first_name'=>'Jane',
        'last_name'=>'Smith',
        'email'=>'tester2@gmail.com'
      )
    )
  ),
  'email_message'=>array(
    'reply_email'=>'me@gmail.com',
    'subject'=>'Choose our next Movie!',
    'body_text'=>"You can help decide our next movie:[SurveyLink] If you don't want to receive any more emails from us, click here: [RemoveLink]."
  )
));
print_R($res);


//////////////////////////////////////////////////////////
//SEND FLOW
//////////////////////////////////////////////////////////
$survey_id = 'SURVEYID';
$params = array(
  'collector'=>array(
    'type'=>'email',
    'send'=>true,
    'name'=>'Another Test Email Collector',
    'recipients'=>array(
      array(
        'first_name'=>'John',
        'last_name'=>'Smith',
        'email'=>'tester@gmail.com'
      ),
      array(
        'first_name'=>'Jane',
        'last_name'=>'Smith',
        'email'=>'tester2@gmail.com'
      )
    ),
  ),
  'email_message'=>array(
    'reply_email'=>'me@gmail.com',
    'subject'=>'Choose our next Movie!',
    'body_text'=>"You can help decide our next movie:[SurveyLink] If you don't want to receive any more emails from us, click here: [RemoveLink]."
  )
);
$res = $survey_monkey->sendFlow($survey_id, $params);

print_r($res);




?>