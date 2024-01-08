<?php

class MoodleRestPro {
  function make_test_user( $arrPost ){
    $user = new stdClass();
    $user->username = $arrPost['username'];
    $user->password = $arrPost['password'];
    $user->firstname = $arrPost['firstname'];
    $user->lastname = $arrPost['username'];
    $user->email = $arrPost['email'];
    $user->auth = 'manual';
    $user->lang = 'es';
    $user->calendartype = 'gregorian';
    return $user;
  }

  function make_test_course( $n ) 
  {
    $course = new stdClass();
    $course->fullname = 'testcourse' . $n;
    $course->shortname = 'testcourse' . $n;
    $course->categoryid = 1;
    return $course;
  }

  function create_user( $user, $token )
  {
    $users = array( $user );
    $params = array( 'users' => $users );

    $response = $this->call_moodle( 'core_user_create_users', $params, $token );

    if ( $this->xmlresponse_is_exception( $response ) ) {
      //throw new Exception( $response );
      return array(
        'status' => 'error',
        'message' => "Caught exception: " .  $response
      );
    } else {
      $user_id = $this->xmlresponse_to_id( $response );
      //return $user_id;
      return array(
        'status' => 'success',
        'message' => "Creado " .  $user_id
      );
    }
  }


  /* Returns a user data structure containing Moodle's
    data for $user_id. It generates this by
    parsing the XML that Moodle returns. If Moodle
    thinks there is no such user, returns NULL.
  */
  function get_user( $user_id, $token )
  {
    $userids = array( $user_id );
    $params = array( 'userids' => $userids );

    $response = $this->call_moodle( 'core_user_get_users_by_id', $params, $token );

    $user = $this->xmlresponse_to_user( $response );

    if ( array_key_exists( 'id', $user ) )
      return $user;
    else
      return NULL;
    // If there is no user with this ID, Moodle
    // returns the same enclosing XML as if there were, but 
    // with no values for ID and the other fields. My
    // XML-parsing code therefore creates an object
    // with no fields, which the conditional above
    // detects.
  }


  /* Assigns the role with $role_id to the user with $user_id
    in the specified context.

    At the moment, always returns an error. I don't know 
    whether this is a bug in Moodle, or a problem with my
    configuration or user or whatever.
  */
  function assign_role( $user_id, $role_id, $context_id, $token )
  {
    $assignment = array( 'roleid' => $role_id, 'userid' => $user_id, 'contextid' => $context_id );
    $assignments = array( $assignment );
    $params = array( 'assignments' => $assignments );

    $response = $this->call_moodle( 'core_role_assign_roles', $params, $token );
  }


  /* Creates a course from a
    structure defining a course. If the
    creation succeeds, returns the 
    ID for this course. If not, throws
    an exception whose text is the XML
    returned by Moodle.
  */
  function create_course( $course, $token ) 
  {
    $courses = array( $course );
    $params = array( 'courses' => $courses );

    $response = $this->call_moodle( 'core_course_create_courses', $params, $token );

    if ( $this->xmlresponse_is_exception( $response ) )
      throw new Exception( $response );
    else {
      $course_id = $this->xmlresponse_to_id( $response );
      return $course_id;
    }
  }


  /* Returns a course data structure containing Moodle's
    data for $course_id. It generates this by
    parsing the XML that Moodle returns. If Moodle
    thinks there is no such course, returns NULL.
  */
  function get_course( $course_id, $token )
  {
    $courseids = array( $course_id );
    $params = array( 'options' => array('ids' => $courseids ) );

    $response = $this->call_moodle( 'core_course_get_courses', $params, $token );

    $course = $this->xmlresponse_to_course( $response );

    if ( array_key_exists( 'id', $course ) )
      return $course;
    else
      return NULL;
    // If there is no user with this ID, Moodle
    // returns the same enclosing XML as if there were, but 
    // with no values for ID and the other fields. My
    // XML-parsing code therefore creates an object
    // with no fields, which the conditional above
    // detects.
  }


  /* Enrols the user into the course with the specified role.
    Does not yet check for errors.

    I haven't tested this.
  */
  function enrol( $user_id, $course_id, $role_id, $token ) 
  {
    $enrolment = array( 'roleid' => $role_id, 'userid' => $user_id, 'courseid' => $course_id );
    $enrolments = array( $enrolment );
    $params = array( 'enrolments' => $enrolments );

    $response = $this->call_moodle( 'enrol_manual_enrol_users', $params, $token );
  }

  function enrol_curso( $user_id, $course_id, $role_id, $token ) 
  {
    //funcion de finalizacion de fecha por año
    $arrFecha = localtime(time(),true);
    $iYear = (1900 + $arrFecha['tm_year']);
    $iMonth = (strlen(1 + $arrFecha['tm_mon']) > 1 ? (1 + $arrFecha['tm_mon']) : '0' . (1 + $arrFecha['tm_mon']));
    $iDay = (strlen($arrFecha['tm_mday']) > 1 ? $arrFecha['tm_mday'] : '0' . $arrFecha['tm_mday']);
    $fecha_actual = $iDay . '-' . $iMonth . '-' . $iYear;
    $timestamp = strtotime($fecha_actual."+ 365 days");//sumo 1 año

    $enrolment = array( 'roleid' => $role_id, 'userid' => $user_id, 'courseid' => $course_id, 'timeend' => $timestamp );
    $enrolments = array( $enrolment );
    $params = array( 'enrolments' => $enrolments );

    $response = $this->call_moodle( 'enrol_manual_enrol_users', $params, $token );
    return $response;
  }

  /* Returns data about users enrolled in the specified course.

    Not sure what Moodle returns yet, so exactly how
    I should parse it. Does not handle multiple users.
  */
  function get_enrolled_users( $course_id, $token ) 
  {
    $params = array( 'courseid' => $course_id );

    $response = $this->call_moodle( 'core_enrol_get_enrolled_users', $params, $token );

    $user = $this->xmlresponse_to_user( $response );
    return $user;
  }

  function get_user_field( $params, $token )
  {
    $response = $this->call_moodle( 'core_user_get_users', $params, $token );

    $user = $this->xmlresponse_to_user_all( $response );
    
    if ( array_key_exists( 'id', $user ) ) {
      return array(
        'status' => 'success',
        'message' => "Existe usuario",
        'response' => $user
      );
    } else {
      return array(
        'status' => 'error',
        'message' => "No existe usuario"
      );
    }
  }

  /* Calls the Moodle at http://ireson-paine.com, invoking the specified
    function on $params. Also takes a token. 
    Returns Moodle's response as a string containing XML.
  */ 
  function call_moodle( $function_name, $params, $token )
  {
    $domain = 'https://aulavirtualprobusiness.com';

    $serverurl = $domain . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$function_name;

    //require_once( './curl.php' );
    require_once(APPPATH.'/libraries/curl.php');
    $curl = new curl;
    $restformat = '';
    $response = $curl->post( $serverurl . $restformat, $params );

    return $response;
  }


  /* Given a string containing XML returned
    by a successful user creation or course
    creation, parses it and returns the user or course ID
    as an integer.
    Undefined if the XML does not contain such an ID,
    for example if it's an error response.
  */
  function xmlresponse_to_id( $xml_string )
  {
    $xml_tree = new SimpleXMLElement( $xml_string );          

    $value = $xml_tree->MULTIPLE->SINGLE->KEY->VALUE;
    $id = intval( sprintf( "%s", $value ) );
    // See discussion on http://php.net/manual/es/book.simplexml.php ,
    // especially the posting for "info at kevinbelanger dot com 20-Jan-2011 05:07".
    // There is a bug in the XML parser whereby it doesn't return the
    // text associated with property [0] of a node. The above
    // posting uses sprintf to force a conversion to string.

    return $id;
  }  


  /* Given a string containing XML returned
    by a successful call to core_user_get_users_by_id,
    parses it and returns the data as a user
    data structure.
    Undefined if the XML does not contain such an ID,
    for example if it's an error response.

    Does not yet handle fields with multiple values.
    I think these are customfields, preferences,
    and enrolledcourses.
  */
  function xmlresponse_to_user( $xml_string )
  {
    return $this->xmlresponse_parse_names_and_values( $xml_string );
  }


  /* Given a string containing XML returned
    by a successful call to core_course_get_courses,
    parses it and returns the data as a course
    data structure.
    Undefined if the XML does not contain such an ID,
    for example if it's an error response.

    Does not yet handle fields with multiple values.
  */
  function xmlresponse_to_course( $xml_string )
  {
    return $this->xmlresponse_parse_names_and_values( $xml_string );
  }


  function xmlresponse_to_user_all( $xml_string )
  {
    return $this->xmlresponse_parse_names_and_values_all( $xml_string );
  }

  /* This parses a string containing the XML returned by
    functions such as core_course_get_courses,
    core_user_get_users_by_id, or core_enrol_get_enrolled_users.
    These strings contain name-value pairs encoded thus:
      <RESPONSE>
      <MULTIPLE>
      <SINGLE>
      <KEY name="id"><VALUE>169</VALUE>
      </KEY>
      <KEY name="username"><VALUE>testusername32</VALUE>
      </KEY>
      </SINGLE>
      </MULTIPLE>
      </RESPONSE>
    The function returns an object with the corresponding
    keys and values.

    Does not yet convert strings to integers where they
    ought to be converted.
  */
  function xmlresponse_parse_names_and_values( $xml_string )
  {
    $xml_tree = new SimpleXMLElement( $xml_string ); 

    $struct = new StdClass();

    foreach ( $xml_tree->MULTIPLE->SINGLE->KEY as $key ) {
      $name = $key['name'];
      $value = (string)$key->VALUE;
      $struct->$name = $value;
    }

    return $struct;
  }

  function xmlresponse_parse_names_and_values_all( $xml_string )
  {
    $xml_tree = new SimpleXMLElement( $xml_string ); 
    
    $struct = new StdClass();

    if(!empty($xml_tree->SINGLE->KEY->MULTIPLE->SINGLE->KEY)) {
      foreach ( $xml_tree->SINGLE->KEY->MULTIPLE->SINGLE->KEY as $key ) {
        $name = $key['name'];
        $value = (string)$key->VALUE;
        $struct->$name = $value;
      }
    }

    return $struct;
  }

  /* True if $xml_string's top-level is
    <EXCEPTION>. I use this to check for error
    responses from Moodle.
  */
  function xmlresponse_is_exception( $xml_string )
  {
    $xml_tree = new SimpleXMLElement( $xml_string );          

    $is_exception = $xml_tree->getName() == 'EXCEPTION';
    return $is_exception;
  }  

  function createUser($arrPost){
    try {
      $token = '2a41772b01afcf26da875fc1ab59bf45';
      $user_data_1 = $this->make_test_user( $arrPost );
      $user_id_1 = $this->create_user( $user_data_1, $token );
      return $user_id_1;
    } 
    catch ( Exception $e ) {
      return array(
        'status' => 'error',
        'message' => "Caught exception: " .  $e->getMessage()
      );
      //echo "\nCaught exception:\n" .  $e->getMessage() . "\n";
    }
  }

  function getUser($arrParams){
    try {
      $token = '2a41772b01afcf26da875fc1ab59bf45';
      $user_id_1 = $this->get_user_field( $arrParams, $token );
      return $user_id_1;
    } 
    catch ( Exception $e ) {
      return array(
        'status' => 'error',
        'message' => "Caught exception: " .  $e->getMessage()
      );
      //echo "\nCaught exception:\n" .  $e->getMessage() . "\n";
    }
  }

  function crearCursoUsuario($arrParams){
    try {
      $token = '2a41772b01afcf26da875fc1ab59bf45';

      $user_id_1 = $arrParams['id_usuario'];
      $role_id = 5;//usuario invitado

      $course_id = 4;//Módulo 1: Introducción a las importaciones
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );
      
      $course_id = 5;//Módulo 2: Importación Simplificada
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );
      
      $course_id = 6;//Módulo 3: Importación de USA
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );
      
      $course_id = 7;//Módulo 4: Importación Definitiva
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );
      
      $course_id = 10;//Módulo 5: Carga Consolidada
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );
      
      $course_id = 9;//sumen de Módulos
      $response_xml = $this->enrol_curso( $user_id_1, $course_id, $role_id, $token );
      $response_xml = new SimpleXMLElement( $response_xml );

      if(!isset($response_xml->MESSAGE)){
        return array(
          'status' => 'success',
          'message' => "Usuario y curso se regisro correctamente"
        );
      } else {
        return array(
          'status' => 'error',
          'message' => "Problemas: " . $response_xml->MESSAGE
        );
      }
    } 
    catch ( Exception $e ) {
      return array(
        'status' => 'error',
        'message' => "Caught exception: " .  $e->getMessage()
      );
    }
  }
}
?>