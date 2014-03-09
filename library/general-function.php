<?php 
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	
	function getDateOnly($sDateTime){
		return @date('Y-m-d', strtotime($sDateTime)); 
	}
	
		
	function getLongDateFormat($sDateTime){
		return date('D d M Y H:i:s', strtotime($sDateTime)); 
	}
	
	function getLongDateFormatGMTTxt($sDateTime){
		return date('D d M Y H:i:s', strtotime($sDateTime)) . ' GMT'; 
	}
	
	function getCurrentDateTime(){
		return @date('Y-m-d H:i:s');
	}
  
	function getAllData($oResult){
		$aResultData = array();
		if ($oResult){
			while ($row = $oResult->fetch_assoc()) {
			 $aResultData[] = $row;
			} 
		}
		return $aResultData;
	}
	
	function phpDateToMysql($dateTime){
		return date('Y-m-d H:i:s', strtotime($dateTime));//'2013-01-08 18:30:00'
	}
		
	//function for date formatting
	function mysqlToPhpDate($dateTime, $dtSepa){
		if ($dtSepa==''){
			$dtSepa='/';
		} 
		$aDateTime = explode(' ', $dateTime); 
		$aDate = explode('-', $aDateTime[0]); 

		$sDateTime = $aDate[1].$dtSepa.$aDate[2].$dtSepa.$aDate[0].' '.$aDateTime[1]; 
		return $sDateTime;
	}

	/* this function is written for converting GMT to localtime zone time
	 * written by Dhananjay
	 * date @ 22-Jun-2012 
	 * 
	 **/
	 function getGMTtoLocalTime($aTimeZoneOffset, $currentDateTime){
		$sSign = $aTimeZoneOffset['sign'];//@ smybol + or -
		$iHours = $aTimeZoneOffset['hrs'];//@ hours 
		$iMinutes = $aTimeZoneOffset['min'];//@ minutes
		
		error_log("$currentDateTime : $sSign, $iHours, $iMinutes"); 
				 
		if ($sSign=='+'){ 
			$sStrTimeToProcess = $currentDateTime .' - '. $iHours .' hours - '. $iMinutes . ' minutes ';
			$sDateInMysqlFormat =  date('Y-m-d H:i:s', strtotime($sStrTimeToProcess));
			error_log('in minus : org '.$currentDateTime . ' final ' .$sDateInMysqlFormat);			
		}
		else if ($sSign=='-'){
			$sStrTimeToProcess = $currentDateTime .' + '. $iHours .' hours +'. $iMinutes . ' minutes ';
			$sDateInMysqlFormat =  date('Y-m-d H:i:s', strtotime($sStrTimeToProcess));
			error_log('in plus  : org '.$currentDateTime . ' final ' .$sDateInMysqlFormat);
						
		}
		else{
			$sDateInMysqlFormat = $currentDateTime; 
		}
		return $sDateInMysqlFormat;
	}
 
	/* this function is written for converting localtime zone time to GMT 
	 * written by Dhananjay
	 * date @ 22-Jun-2012 
	 * 
	 **/
	function getLocalTimeToGMT($aTimeZoneOffset, $currentDateTime){
		$sSign = $aTimeZoneOffset['sign'];//@ smybol + or -
		$iHours = $aTimeZoneOffset['hrs'];//@ hours 
		$iMinutes = $aTimeZoneOffset['min'];//@ minutes
		
		error_log("$currentDateTime : $sSign, $iHours, $iMinutes"); 
		
		if ($sSign=='+'){ 
			$sStrTimeToProcess = $currentDateTime .' + '. $iHours .' hours + '. $iMinutes . ' minutes ';
			$sDateInMysqlFormat =  date('Y-m-d H:i:s', strtotime($sStrTimeToProcess));
			error_log('in minus : org '.$currentDateTime . ' final ' .$sDateInMysqlFormat);
		}
		else if ($sSign=='-'){
			$sStrTimeToProcess = $currentDateTime .' - '. $iHours .' hours - '. $iMinutes . ' minutes ';
			$sDateInMysqlFormat =  date('Y-m-d H:i:s', strtotime($sStrTimeToProcess));
			error_log('in plus  : org '.$currentDateTime . ' final ' .$sDateInMysqlFormat);			
		}
		else{
			$sDateInMysqlFormat = $currentDateTime; 
		}
		return $sDateInMysqlFormat;
	}
	
	function convertTimezoneOffsetToTime($timezoneoffset){
		$aTimeZoneOffset['sign'] = '';
		$aTimeZoneOffset['hrs'] = 0; 
		$aTimeZoneOffset['min'] = 0; 
		$aTimeZoneOffset['timezoneoffset'] = $timezoneoffset;
		
		if (strlen($timezoneoffset)>0){
			if ($timezoneoffset < 0){
				$sSmybol = '-';
			}
			else{
				$sSmybol = '+';
			}
			$timezoneoffset_HrsInt = (int) $timezoneoffset; // getting hours 
			$timezoneoffset_MinRemainder = round(($timezoneoffset - $timezoneoffset_HrsInt) * 60);// getting minutes
			
			$aTimeZoneOffset['sign'] = $sSmybol;
			$aTimeZoneOffset['hrs'] = abs($timezoneoffset_HrsInt); 
			$aTimeZoneOffset['min'] = abs($timezoneoffset_MinRemainder); 
		}
		
		return $aTimeZoneOffset;
	}
 
?>
