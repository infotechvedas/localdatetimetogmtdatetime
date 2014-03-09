<?php
#ini_set('log_errors', 1);
#ini_set('error_log', '/var/www/more/logs/php-error.log');
#error_reporting(E_ALL);
#ini_set('display_errors',1);

/*This model is used for reporting CSV report for campaign details
 * 
 * 
 * */ 
class Membership { 
    private $_db = null; 

    public function __construct() { 
		global $aDatabase;
		$aDb1 = $aDatabase['db1'];
		$this->_db = new mysqli($aDb1['host'], $aDb1['user'], $aDb1['password'], $aDb1['database']);
    }
	
	/**
	* getAllMembers() - This function is used for getting all members list
	* @param - none
	* @pre - none 
	* @post -none 
	* @return array
	*/
	public function getAllMembers(){
		$sql = "SELECT * FROM membership";
		try { 
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $aResultData;
	}

	/**
	* add() - This function is used for adding new members
	* @param - $sUserName, $sEmaiId, $sCountryName, $sTimezoneOffset, $sCurrentDateTimeZone, $sGmtDateTime, $sRegisteredDateTime
	* @pre - none 
	* @post -none 
	* @return array
	*/
	public function add($sUserName, $sEmaiId, $sCountryName, $sTimezoneOffset, $sCurrentDateTimeZone, $sGmtDateTime, $sRegisteredDateTime){
		$sql = "insert into membership (name, emaild, registered_date, country_name, country_timezone, gmt_date_time, local_date_time) value ('$sUserName', '$sEmaiId', '$sRegisteredDateTime', '$sCountryName', '$sTimezoneOffset', '$sGmtDateTime', '$sCurrentDateTimeZone')";
		try {
			error_log($sql); 
			$stat = $this->_db->query($sql); 
			$iAdded = $this->_db->affected_rows; 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $iAdded;
	}
	
}
?>
