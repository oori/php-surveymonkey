<?php
/**
 * Class for SurveyMonkey API v2
 * @package php-surveymonkey
 */
class SurveyMonkey{
  /**
   * @var string API key
   * @access protected
   */
  protected $_apiKey;

  /**
   * @var string API access token
   * @access protected
   */
  protected $_accessToken;

  /**
   * @var string API protocol
   * @access protected
   */
  protected $_protocol;

  /**
   * @var string API hostname
   * @access protected
   */
  protected $_hostname;

  /**
   * @var string API version
   * @access protected
   */
  protected $_version;

  /**
   * @var resource $conn The client connection instance to use.
   * @access private
   */
  private $conn = null;

  /**
   * @var array (optional) cURL connection options
   * @access protected
   */
  protected $_connectionOptions;

  /**
   * @const SurveyMonkey Status code:  Success
   */
  const SM_STATUS_SUCCESS = 0;

  public static function successfulHttpResponse($code){
    if ($code >= 200 and $code < 300){
      return true;
    }
    return false;
  }
  /**
   * SurveyMonkey API Status code definitions
   */
  public static $SM_STATUS_CODES = array(
    0 => "Success",
    1 => "Not Authenticated",
    2 => "Invalid User Credentials",
    3 => "Invalid Request",
    4 => "Unknown User",
    5 => "System Error",
    6 => "Plan Limit Exceeded"
  );

  /**
   * Explain Survey Monkey status code
   * @param integer $code Status code
   * @return string Definition
   */
  public static function explainStatusCode($code){
    return self::$SM_STATUS_CODES[$code];
  }

  /**
   * The SurveyMonkey Constructor.
   *
   * This method is used to create a new SurveyMonkey object with a connection to a
   * specific api key and access token
   *
   * @param string $apiKey A valid api key
   * @param string $accessToken A valid access token
   * @param array $options (optional) An array of options
   * @param array $connectionOptions (optional) cURL connection options
   * @throws SurveyMonkey_Exception If an error occurs creating the instance.
   * @return SurveyMonkey A unique SurveyMonkey instance.
   */
  public function __construct($apiKey, $accessToken, $options = array(), $connectionOptions = array()){

    if (empty($apiKey))     throw new SurveyMonkey_Exception('Missing apiKey');
    if (empty($accessToken))  throw new SurveyMonkey_Exception('Missing accessToken');
    $this->_apiKey = $apiKey;
    $this->_accessToken = $accessToken;

    $this->_protocol =  (!empty($options['protocol']))? $options['protocol']  : 'https';
    $this->_hostname =  (!empty($options['hostname']))? $options['hostname']  : 'api.surveymonkey.net';
    $this->_version =   (!empty($options['version']))?  $options['version']   : 'v2';

    $this->_connectionOptions = $connectionOptions;
  }

  /**
   * Build the request URI
   * @param string $endpoint API endpoint to call in the form: resource/method
   * @return string Constructed URI
   */
  protected function buildUri($endpoint){
    return $this->_protocol . '://' . $this->_hostname . '/' . $this->_version . '/' . $endpoint . '?api_key=' . $this->_apiKey;
  }

  /**
   * Get the connection
   * @return boolean
   */
  protected function getConnection(){
    $this->conn = curl_init();
    return is_resource($this->conn);
  }

  /**
   * Close the connection
   */
  protected function closeConnection(){
        curl_close($this->conn);
  }

  /**
   * Run the
   * @param string $method API method to run
   * @param array $params Parameters array
   * @return array Results
   */
  protected function run($endpoint, $params = array()){
    if (!is_resource($this->conn)) {
        if (!$this->getConnection()) return $this->failure('Can not initialize connection');
    }

    $request_url = $this->buildUri($endpoint);
    curl_setopt($this->conn, CURLOPT_URL, $request_url);  // URL to post to
    curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, 1 );   // return into a variable
    $headers = array('Content-type: application/json', 'Authorization: Bearer ' . $this->_accessToken);
    curl_setopt($this->conn, CURLOPT_HTTPHEADER, $headers ); // custom headers
    curl_setopt($this->conn, CURLOPT_HEADER, false );     // return into a variable
    curl_setopt($this->conn, CURLOPT_POST, true);     // POST
    $postBody = (!empty($params))? json_encode($params) : "{}";
    curl_setopt($this->conn, CURLOPT_POSTFIELDS,  $postBody);
    curl_setopt_array($this->conn, $this->_connectionOptions);  // (optional) additional options

    $result = curl_exec( $this->conn );
    if ($result === false) return $this->failure('Curl Error: ' . curl_error($this->conn));
    $responseCode = curl_getinfo($this->conn, CURLINFO_HTTP_CODE);
    if (!self::successfulHttpResponse($responseCode)){
      return $this->failure('Error ['.$responseCode.']: ' . $result);
    }

    $this->closeConnection();

    $parsedResult = json_decode($result,true);
    $jsonErr = json_last_error();
    if ($parsedResult === null  &&  $jsonErr !== JSON_ERROR_NONE) return $this->failure("Error [$jsonErr] parsing result JSON");

    $status = $parsedResult['status'];
    if ($status != self::SM_STATUS_SUCCESS) return $this->failure("API Error: Status [$status:" . self::explainStatusCode($status) . '].  Message [' . $parsedResult["errmsg"] . ']');
    else return $this->success($parsedResult["data"]);
  }



  /**
   * Return an error
   * @param string $msg Error message
   * @return array Result
   */
  protected function failure($msg){
    return array(
      'success' => false,
      'message' => $msg
    );
  }

    /**
     * Return a success with data
     * @param string $data Payload
     * @return array Result
     */
  protected function success($data){
    return array(
      'success' => true,
      'data' => $data
    );
  }


  /***************************
   * SurveyMonkey API methods
   ***************************/

  //survey methods

  /**
   * Retrieves a paged list of surveys in a user's account.
   * @see https://developer.surveymonkey.com/mashery/get_survey_list
   * @param array $params optional request array
   * @return array Result
   */
  public function getSurveyList($params = array()){
    return $this->run('surveys/get_survey_list', $params);
  }

  /**
   * Retrieve a given survey's metadata.
   * @see https://developer.surveymonkey.com/mashery/get_survey_details
   * @param string $surveyId Survey ID
   * @return array Results
   */
  public function getSurveyDetails($surveyId){
    $params = array('survey_id'=>$surveyId);
    return $this->run('surveys/get_survey_details', $params);
  }

  /**
   * Retrieves a paged list of collectors for a survey in a user's account.
   * @see https://developer.surveymonkey.com/mashery/get_collector_list
   * @param string $surveyId Survey ID
   * @param array $params optional request array
   * @return array Results
   */
  public function getCollectorList($surveyId, $params = array()){
    $params['survey_id'] = $surveyId;
    return $this->run('surveys/get_collector_list', $params);
  }

  /**
   * Retrieves a paged list of respondents for a given survey and optionally collector
   * @see https://developer.surveymonkey.com/mashery/get_respondent_list
   * @param string $surveyId Survey ID
   * @param array $params optional request array
   * @return array Results
   */
  public function getRespondentList($surveyId, $params = array()){
    $params['survey_id'] = $surveyId;
    return $this->run('surveys/get_respondent_list', $params);
  }

  /**
   * Takes a list of respondent ids and returns the responses that correlate to them.
   * @see https://developer.surveymonkey.com/mashery/get_responses
   * @param string $surveyId Survey ID
   * @param array $respondentIds Array of respondents IDs to retrieve
   * @param integer $chunkSize optional number of respondants to fetch in each chunk. We split it to multiple requests to conform with SurveyMonkey's API limits.  If successful, the returned array is a joined array of all chunks.
   * @return array Results
   */
  public function getResponses($surveyId, $respondentIds, $chunkSize = 100){
    // Split requests to multiple chunks, if larger then $chunkSize
    if (count($respondentIds) > $chunkSize){
      $data = array();
      foreach (array_chunk($respondentIds, $chunkSize) as $r){
        $result = $this->getResponses($surveyId, $r, $chunkSize);
        if (!$result["success"]) return $result;
        $data = array_merge($data, $result["data"]);
      }
      return $this->success($data);
    }

    $params = array(
      'survey_id' => $surveyId,
      'respondent_ids' => $respondentIds
    );
    return $this->run('surveys/get_responses', $params);
  }

  /**
   * Returns how many respondents have started and/or completed the survey for the given collector
   * @see https://developer.surveymonkey.com/mashery/get_response_counts
   * @param string $collectorId Collector ID
   * @return array Results
   */
  public function getResponseCounts($collectorId){
    $params = array('collector_id' => $collectorId);
    return $this->run('surveys/get_response_counts', $params);
  }

  //user methods

  /**
   * Returns basic information about the logged-in user
   * @see https://developer.surveymonkey.com/mashery/get_user_details
   * @return array Results
   */
  public function getUserDetails(){
    return $this->run('user/get_user_details');
  }

  //template methods

  /**
   * Retrieves a paged list of templates provided by survey monkey.
   * @see https://developer.surveymonkey.com/mashery/get_template_list
   * @param array $params optional request array
   * @return array Results
   */
  public function getTemplateList($params = array()){
    return $this->run('templates/get_template_list', $params);
  }

  //collector methods

  /**
   * Retrieves a paged list of templates provided by survey monkey.
   * @see https://developer.surveymonkey.com/mashery/create_collector
   * @param string $surveyId Survey ID
   * @param string $collectorName optional Collector Name - defaults to 'New Link'
   * @param string $collectorType required Collector Type - only 'weblink' currently supported
   * @param array $params optional request array
   * @return array Results
   */
  public function createCollector($surveyId, $collectorName = null, $collectorType = 'weblink'){
    $params = array(
      'survey_id'=>$surveyId,
      'collector'=>array(
        'type'=>$collectorType,
        'name'=>$collectorName
      )
    );
    return $this->run('collectors/create_collector', $params);
  }

  //batch methods

  /**
   * Create a survey, email collector and email message based on a template or existing survey.
   * @see https://developer.surveymonkey.com/mashery/create_flow
   * @param string $surveyTitle Survey Title
   * @param array $params optional request array
   * @return array Results
   */
  public function createFlow($surveyTitle, $params = array()){
    if (isset($params['survey'])){
      $params['survey']['survey_title'] = $surveyTitle;
    }
    else{
      $params['survey'] = array('survey_title'=>$surveyTitle);
    }
    return $this->run('batch/create_flow', $params);
  }

  /**
   * Create an email collector and email message attaching them to an existing survey.
   * @see https://developer.surveymonkey.com/mashery/send_flow
   * @param string $surveyId Survey ID
   * @param array $params optional request array
   * @return array Results
   */
  public function sendFlow($surveyId, $params = array()){
    $params['survey_id'] = $surveyId;
    return $this->run('batch/send_flow', $params);
  }
}

/**
 * A basic class for SurveyMonkey Exceptions.
 * @package php-surveymonkey
 * @subpackage exception
 */
class SurveyMonkey_Exception extends Exception {}