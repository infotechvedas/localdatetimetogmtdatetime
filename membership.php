<?php
	include('includes/config.php');
	include('library/general-function.php');
	include('classes/Membership.php');
	$oMembership = new Membership();

	$sUserName = addslashes($_POST['username']);
	$sEmaiId = addslashes($_POST['emailid']);
	$sCountryName = $_POST['countryname'];
	$sTimezoneOffset = $_POST['timezoneOffset'];
	$sCurrentDateTimeZone =  addslashes($_POST['registereddatetime']);
	$sRegisteredDateTime = $_POST['registereddatetime'];
	
	/*Preparing localDateTime to GMT dateTime - start */
	$aTimeZoneOffset = convertTimezoneOffsetToTime($sTimezoneOffset);
	$sGmtDateTime = getLocalTimeToGMT($aTimeZoneOffset, $sRegisteredDateTime);
	/*Preparing localDateTime to GMT dateTime - start */
	
	$sGmtDateTime = phpDateToMysql($sGmtDateTime);
	$sRegisteredDateTime = phpDateToMysql($sRegisteredDateTime);
	#echo "$sUserName, $sEmaiId, $sCountryName, $sTimezoneOffset, $sCurrentDateTimeZone, $sGmtDateTime, $sRegisteredDateTime";
	$sAction = $_POST['signIn'];
	if ($sAction == 'Register'){
		$iAdded = $oMembership->add($sUserName, $sEmaiId, $sCountryName, $sTimezoneOffset, $sCurrentDateTimeZone, $sGmtDateTime, $sRegisteredDateTime);
		if ($iAdded > 0){
			header("location: index.php?status=added");	
			exit();
		}
		else{
			header("location: index.php?status=failed");
			exit();
		}
	} 	
	die();
?>
