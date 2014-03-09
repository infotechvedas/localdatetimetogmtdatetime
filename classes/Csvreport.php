<?php
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/more/logs/php-error.log');
error_reporting(E_ALL);
ini_set('display_errors',1);

/*This model is used for reporting CSV report for campaign details
 * 
 * 
 * */ 
class Csvreport {
	private $_reportFilePathDir = null;
	private $_reportFilePathView = null;
	
    private $_db = null;
    private $_db_slave = null;

    public function __construct() { 
		global $aDatabase;
		$aDb1 = $aDatabase['db1'];
		$aDb2 = $aDatabase['db2'];
		$this->_db = new mysqli($aDb1['host'], $aDb1['user'], $aDb1['password'], $aDb1['database']);
    }
	
 	/**
	* saveData() - This function is used for save report on disk as csv file format 
	* @param - $aGuestListData, $sConsortiumId='', $sCampaignId='', $aReportHead=''
	* @pre - none 
	* @post -none 
	* @return string
	*/
	public function saveData($aGuestListData, $sConsortiumId='', $sCampaignId='', $aReportHead='') {
		if (strlen($sConsortiumId) > 0 && strlen($sCampaignId)>0){
			$bTargetFolderStatus = $this->addFolder($sConsortiumId);
			if ($bTargetFolderStatus==1){
				$aResultData = $this->createCsvData($aGuestListData[$sCampaignId]);
				$sCsvData = $aResultData['data'];
				$aReportHead['noofvoucher_redemed'] = $aResultData['redemed']; 
			 
				$sDataHead = $this->getReportHead($aReportHead, $sCampaignId);

				$sDataHead .= "\n\n"."Reward ID,Email ID, Redeemed \n" . $sCsvData; 
				
				$sSaveCSVDataStatus = $this->saveCSVData($sDataHead, $sConsortiumId, $sCampaignId);
				if ($sSaveCSVDataStatus==1){
					$sMessage = ' CSV data saved successfully campaign id: '. $sCampaignId;
					error_log(__FUNCTION__ .' Line no. '. __LINE__. $sMessage);
				}
				else{
					$sMessage = ' CSV data not saved';
					error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);	
				} 		
			}
			else{
				$sMessage = ' CSV data not saved at 56 new.....';
				error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);
			}
		}
		else{
			$sMessage = ' Consortium or campaignid not found ';
			error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);
		}
		return $sMessage;
	}
	
	/**
	* saveCSVData() - This function is used for save csv file on disk as csv file format 
	* @param - $sCsvData, $sConsortiumId, $sCampaignId
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	public function saveCSVData($sCsvData, $sConsortiumId, $sCampaignId) {
		$sReportFilePathDir = $this->_reportFilePathDir;
		$bFileStatus = 0;
		$sReportFileName = '/redemption-'.$sCampaignId.'.csv';
		$sTargetFileName = $sReportFilePathDir . $sConsortiumId . $sReportFileName;

		$f1 = fopen($sTargetFileName, 'w');
		
		try{
			fwrite($f1, $sCsvData);
			$bFileStatus = 1;
			fclose($f1);
		}
		catch(exception $e){
			error_log(__FUNCTION__ .' Target file creation error :'. $e->getMessage());
		}
		return $bFileStatus;
	}
			 
 	/**
	* addFolder() - This function is used for save report on disk as csv file format 
	* @param - $sListIds
	* @pre - none 
	* @post -none 
	* @return array
	*/
	public function addFolder($sConsortiumId,$folder="",$reporttype="") {
		$sReportFilePathDir = $this->_reportFilePathDir;
		error_log('check folder ' . $sReportFilePathDir);
		$bDirStatus = 0;
		if (file_exists($sReportFilePathDir))
		{
			if($reporttype!="")
				$sTargetDir = $sReportFilePathDir.$folder;
			else
				$sTargetDir = $sReportFilePathDir.$folder.$sConsortiumId;
			
			error_log('check folder ' . $sTargetDir);
			if (!file_exists($sTargetDir)){
				try{
					$bDirStatus = mkdir($sTargetDir, 0777);
					if ($bDirStatus==1){
						error_log("Folder created successfully");
					}
					else{
						error_log("Folder not created");
						throw new Exception("Folder not created");
					}
				}
				catch(exception $e){
					error_log(__FUNCTION__ .' Target folder creation error :'. $e->getMessage() . " status : $bDirStatus");
				}
			}
			else{
				$bDirStatus = 1;
				error_log("$sReportFilePathDir sub dir exist '$sConsortiumId'");
			}
		}
		else {
			error_log("$sReportFilePathDir does not exists");
		}
		return $bDirStatus;
	}
	
	/**
	* createCsvData() - This function is used for creating csv data from aGuestListData 
	* @param - $aGuestListData
	* @pre - none 
	* @post -none 
	* @return array
	*/	
	private function createCsvData($aGuestListData){
		$aResultData =  array();
		$sCsvData = '';
		$iRedemed = 0;
		foreach($aGuestListData as $rowData){// processing for voucherBatchId
			#print_r($rowData);echo "\n\n ** ";
			foreach($rowData as $row){// processing for voucherId
				$iAccountKey = $row['accountKey']; 
				$sEmailId = $row['emailId']; 
				$sRedemStatus = (strlen($row['redemedId']) > 0) ? 'Yes' : 'No'; 
				if ($sRedemStatus === 'Yes'){
					$iRedemed++;
				}
				$sCsvData .= $iAccountKey . ', ' . $sEmailId . ', ' . $sRedemStatus ."\n";
			}
		}
		
		$aResultData = array('data' => $sCsvData, 'redemed' => $iRedemed);
		return $aResultData;
	}
	
	/**
	* getPushNotificationCount() - This function is used for counting push notification for campaign
	* @param - $sCampaignId
	* @pre - none 
	* @post -none 
	* @return integer
	*/ 
	public function getPushNotificationCount($sCampaignId){
		$iPushCount = 0;
		$sql = "SELECT sum(subsCount) as pushCount FROM pushBatch WHERE fkCampaignId='$sCampaignId'";
		#error_log('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql);
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat);	
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		$iPushCount = $aResultData[0]['pushCount'];
		if ($iPushCount == null ){
			$iPushCount = 0;
		}
	
		return $iPushCount;
	}
	
		
	/**
	* getCouponsCount() - This function is used for counting push notification for campaign
	* @param - $sCampaignId
	* @pre - none 
	* @post -none 
	* @return integer
	*/ 
	public function getCouponsCount($sCampaignId){
		$iPushCount = 0;
		$sql = "SELECT sum(subsCount) as couponCount FROM voucherBatch WHERE fkCampaignId='$sCampaignId'";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql) ."\n";
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat);
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}

		$iPushCount = $aResultData[0]['couponCount'];
		if ($iPushCount == null ){
			$iPushCount = 0;
		} 
		
		return $iPushCount;
	}
	
	/**
	* getReportHead() - This function is used for counting push notification for campaign
	* @param - $aReportHead, $sCampaignId, $sType='csvfile'
	* @pre - none 
	* @post -none 
	* @return string
	*/ 
	public function getReportHead($aReportHead, $sCampaignId, $sType='csvfile'){
		$sName = $aReportHead['name'];
		$sSendtype = $aReportHead['sendtype'];
		$iNoofusers_push = $aReportHead['noofusers_push'];
		$iNoofusers_coupon = $aReportHead['noofusers_coupon'];
		
		$sCampaignOfferValidityDateTime = getLongDateFormatGMTTxt($aReportHead['campaignoffervaliditydatetime']);
			
		$sCampaignlocaldate = getLongDateFormatGMTTxt($aReportHead['campaignlocaldate']);
		if($sSendtype=='schedule'){
			$sCampaignlaunchdate = getLongDateFormatGMTTxt($aReportHead['campaignlaunchdate']);	
		}
		else{
			$sCampaignlaunchdate = $sCampaignlocaldate;	
		} 
		
		$sConsortiumId = $aReportHead['consortiumId'];
 
		$aConsortiumData = $this->getConsortiumDetails($sConsortiumId);
 
		$sConsortiumName = $aConsortiumData['description'];
		$sConsortiumType = $aConsortiumData['externalRefId'];
 
		if ($sConsortiumType=='Partner' || $sConsortiumType=='Group'){
		}
		else{
			$sConsortiumType = 'Store';
		}
		
		$sLabelValueSeparator = ', ';
		if ($sType=='view'){
			$sLabelValueSeparator = '';	
			$sLineBreaker = "<br/>";
			$sLabelStyleStart = "<b>";
			$sLabelStyleEnd = "</b>";
		}
		else{ 
			$sLineBreaker = "";
			$sLabelStyleStart = "";
			$sLabelStyleEnd = "";			
		}
		
		$iRedemptionCount = $this->getVoucherRedemptionCount($sCampaignId);//Calling @ self for getting redemption count
		if ($iRedemptionCount > 0 ){
			//Calculating redemption rate in percentage 
			$iRedemptionRate =  round(($iRedemptionCount / $iNoofusers_coupon * 100), 2) . '%';	
		}
		else{
			$iRedemptionRate = '0%';
		}

$sDataHead = <<<EOD
{$sLabelStyleStart}Name :$sLabelStyleEnd $sLabelValueSeparator $sConsortiumName $sLineBreaker
{$sLabelStyleStart}Type :$sLabelStyleEnd $sLabelValueSeparator $sConsortiumType $sLineBreaker
{$sLabelStyleStart}Campaign Name :$sLabelStyleEnd $sLabelValueSeparator $sName $sLineBreaker
{$sLabelStyleStart}Campaign Start Date :$sLabelStyleEnd $sLabelValueSeparator $sCampaignlaunchdate $sLineBreaker
{$sLabelStyleStart}Campaign Completion Date :$sLabelStyleEnd $sLabelValueSeparator $sCampaignOfferValidityDateTime $sLineBreaker
{$sLabelStyleStart}Report Date :$sLabelStyleEnd $sLabelValueSeparator $sCampaignlocaldate $sLineBreaker
{$sLabelStyleStart}Eligible Guest Count (Coupon) :$sLabelStyleEnd $sLabelValueSeparator $iNoofusers_coupon $sLineBreaker
{$sLabelStyleStart}Eligible Guest Count (Push Notification) :$sLabelStyleEnd $sLabelValueSeparator $iNoofusers_push $sLineBreaker
{$sLabelStyleStart}Redemptions :$sLabelStyleEnd $sLabelValueSeparator $iRedemptionCount $sLineBreaker
{$sLabelStyleStart}Redemption Rate :$sLabelStyleEnd $sLabelValueSeparator $iRedemptionRate $sLineBreaker

EOD;
	
		return $sDataHead;
	}
	
	
	public function getVouchersBatchesByCampaignId($sCampaignId){
		$aResultData = array();
		$sql = "SELECT voucherBatchId FROM voucherBatch WHERE fkCampaignId='$sCampaignId'";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql) ."\n";
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat);
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		
		return $aResultData;
	}
	
	public function getCouponByCampaignId($iVouchersId){
		$aResultData = $aResultData2 = array();
		$sql = "SELECT v.voucherId, a.accountKey, u.emailId, vr.voucherId as redemedId FROM vouchers as v left join voucherRedemptions as vr on v.voucherId=vr.voucherId, accounts as a, users as u WHERE u.userId=a.userId AND v.accountId=a.accountId AND v.voucherBatchId='$iVouchersId'";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql) ."\n";
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat);
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		
		return $aResultData;
	}
	
	public function isVoucherRedemed($iVouchersId){
		$iCnt = 0;
		$sql = "SELECT count(voucherId) as cnt FROM voucherRedemptions WHERE voucherId='$iVouchersId'";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql) ."\n";
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat);
			$iCnt = $aResultData[0]['cnt'];
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		
		return $iCnt;
	}
	
	public function getConsortiumDetails($sConsortiumId){
		$aResultData = array();
		$sql = "SELECT description, externalRefId FROM consortia WHERE consortiumId='$sConsortiumId'";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql) ."\n";
		try {
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat); 
			if (is_array($aResultData) && count($aResultData) > 0){
				$aResultData = $aResultData[0];
			}
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		
		return $aResultData;
	}
	
	/**
	* getVoucherRedemptionCount() - This function is used for counting redemption vouchers for campaign
	* @param - $sCampaignId
	* @pre - none 
	* @post -none 
	* @return integer
	*/ 
	public function getVoucherRedemptionCount($sCampaignId){
		$iRedemptionCount = 0;
		$sql = "SELECT count(v.accountId) as redemptionCnt  FROM voucherBatch as vb, vouchers as v  WHERE v.voucherBatchId=vb.voucherBatchId AND vb.fkCampaignId='$sCampaignId' and v.accountId in (SELECT v.accountId  FROM voucherBatch as vb, vouchers as v ,voucherRedemptions as vr WHERE v.voucherId=vr.voucherId AND v.voucherBatchId=vb.voucherBatchId AND vb.fkCampaignId='$sCampaignId');";
		#error_log('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql);
		try { 
			$stat = $this->_db->query($sql); 
			$aResultData = getAllData($stat); 
			if (is_array($aResultData) && count($aResultData) > 0){
				$iRedemptionCount = $aResultData[0]['redemptionCnt'];
			}			
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
 
		return $iRedemptionCount;
	}
	
	/**
	* saveData() - This function is used for save report on disk as csv file format 
	* @param - $aGuestListData, $sConsortiumId='', $sCampaignId='', $aReportHead=''
	* @pre - none 
	* @post -none 
	* @return string
	*/
	public function saveBaselineData($aGuestListData, $sConsortiumId='', $sCampaignId='', $aReportHead='') {
		if (strlen($sConsortiumId) > 0 && strlen($sCampaignId)>0){
			$bTargetFolderStatus = $this->addFolder($sConsortiumId);
			if ($bTargetFolderStatus==1){
				$aResultData = $this->createCsvBaselineData($aGuestListData);
				$sCsvData = $aResultData['data'];
				
				$sDataHead = $this->getReportBaselineHead($aReportHead, $sCampaignId);

				$sDataHead .= "\n\n"."Reward ID,Store ID, Transaction Date, Amount \n" . $sCsvData; 
				
				$sSaveCSVDataStatus = $this->saveCSVBaselineData($sDataHead, $sConsortiumId, $sCampaignId);
				if ($sSaveCSVDataStatus==1){
					$sMessage = ' CSV data saved successfully campaign id: '. $sCampaignId;
					error_log(__FUNCTION__ .' Line no. '. __LINE__. $sMessage);
				}
				else{
					$sMessage = ' CSV data not saved';
					error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);	
				} 		
			}
			else{
				$sMessage = ' CSV data not saved at new.....';
				error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);
			}
		}
		else{
			$sMessage = ' Consortium or campaignid not found ';
			error_log(__FUNCTION__ .' Line no. '. __LINE__. ' message ' .$sMessage);
		}
		return $sMessage;
	}

	/**
	* createCsvBaselineData() - This function is used for creating csv data from aGuestListData 
	* @param - $aGuestListData
	* @pre - none 
	* @post -none 
	* @return array
	*/	
	private function createCsvBaselineData($aGuestListData){
		#echo "createCsvBaselineData start \r\n" ;	#print_r($aGuestListData);		echo "createCsvBaselineData end\r\n" ;
		$aResultData =  array();
		$sCsvData = '';
		$iRedemed = 0;
		foreach($aGuestListData as $rowData){// processing all data 
			$iAccountKey = $rowData['accountKey']; 
			$sTxnStoreId = $rowData['txnStoreId']; 
			$sTxnDateTime = $rowData['txnDateTime'];
			$sTxnAmount = $rowData['txnValue'];
			$sCsvData .= $iAccountKey . ', ' . $sTxnStoreId . ', ' . $sTxnDateTime . ', ' . $sTxnAmount ."\n"; 
		}
		
		$aResultData = array('data' => $sCsvData);
		return $aResultData;
	}
	
	/**
	* saveCSVBaselineData() - This function is used for save csv file on disk as csv file format 
	* @param - $sCsvData, $sConsortiumId, $sCampaignId
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	public function saveCSVBaselineData($sCsvData, $sConsortiumId, $sCampaignId) {
		$sReportFilePathDir = $this->_reportFilePathDir;
		$bFileStatus = 0;
		$sReportFileName = '/baseline-'.$sCampaignId.'.csv';
		$sTargetFileName = $sReportFilePathDir . $sConsortiumId . $sReportFileName;

		$f1 = fopen($sTargetFileName, 'w');
		
		try{
			fwrite($f1, $sCsvData);
			$bFileStatus = 1;
			fclose($f1);
		}
		catch(exception $e){
			error_log(__FUNCTION__ .' Target file creation error :'. $e->getMessage());
		}
		return $bFileStatus;
	}
	
	/**
	* getReportBaselineHead() - This function is used for save csv file on disk as csv file format 
	* @param - $aReportHead, $sCampaignId, $sType='csvfile'
	* @pre - none 
	* @post -none 
	* @return boolean
	*/	
	public function getReportBaselineHead($aReportHead, $sCampaignId, $sType='csvfile'){
		$sName = $aReportHead['name'];
		$sSendtype = $aReportHead['sendtype'];
		$iBaselineVisit = $aReportHead['baselinevisit'];
		$iBaselineRevenue = $aReportHead['baselinerevenue'];
 
		$sCampaignlocaldate = getLongDateFormatGMTTxt($aReportHead['campaignlocaldate']);
		if($sSendtype=='schedule'){
			$sCampaignlaunchdate = getLongDateFormatGMTTxt($aReportHead['campaignlaunchdate']);	
		}
		else{
			$sCampaignlaunchdate = $sCampaignlocaldate;	
		} 
		
		$sConsortiumId = $aReportHead['consortiumId'];
 
		$aConsortiumData = $this->getConsortiumDetails($sConsortiumId);
 
		$sConsortiumName = $aConsortiumData['description'];
		$sConsortiumType = $aConsortiumData['externalRefId'];
 
		if ($sConsortiumType=='Partner' || $sConsortiumType=='Group'){
		}
		else{
			$sConsortiumType = 'Store';
		}
		
		$sLabelValueSeparator = ', ';
		if ($sType=='view'){
			$sLabelValueSeparator = '';	
			$sLineBreaker = "<br/>";
			$sLabelStyleStart = "<b>";
			$sLabelStyleEnd = "</b>";
		}
		else{ 
			$sLineBreaker = "";
			$sLabelStyleStart = "";
			$sLabelStyleEnd = "";			
		}
		
$sDataHead = <<<EOD
{$sLabelStyleStart}Name :$sLabelStyleEnd $sLabelValueSeparator $sConsortiumName $sLineBreaker
{$sLabelStyleStart}Type :$sLabelStyleEnd $sLabelValueSeparator $sConsortiumType $sLineBreaker
{$sLabelStyleStart}Campaign Name :$sLabelStyleEnd $sLabelValueSeparator $sName $sLineBreaker
{$sLabelStyleStart}Campaign Start Date :$sLabelStyleEnd $sLabelValueSeparator $sCampaignlaunchdate $sLineBreaker
{$sLabelStyleStart}Report Date :$sLabelStyleEnd $sLabelValueSeparator $sCampaignlocaldate $sLineBreaker
{$sLabelStyleStart}Baseline Visits :$sLabelStyleEnd $sLabelValueSeparator $iBaselineVisit $sLineBreaker
{$sLabelStyleStart}Baseline Revenue :$sLabelStyleEnd $sLabelValueSeparator $iBaselineRevenue $sLineBreaker

EOD;
	
		return $sDataHead;
	}

	/**
	* getSummaryLoyaltyReportHead() - This function is used to create header for summary loyality report of minipos
	* @param - $aReportHead, $sType='csvfile'
	* @pre - none 
	* @post -none 
	* @return string
	*/ 
	public function getSummaryLoyaltyReportHead($dt,$sReportName, $aReportHead,$ssuffixTable,$totalpointsadded,$totalreedemedcoupon,$sType='csvfile')
	{
		$sReportname="$sReportName $ssuffixTable";
		
		$currentdate=date("Y-m-d H:i:s");		$sReportDate=getLongDateFormatGMTTxt($dt);

		$sTotalPointsAdded=$totalpointsadded;
		$sTotalPointsRedeemed=$totalreedemedcoupon;
		
		$sLabelValueSeparator = '';
		if ($sType=='view')
		{
			$sLabelValueSeparator = '';	
			$sLineBreaker = "<br/>";
			$sLabelStyleStart = "<b>";
			$sLabelStyleEnd = "</b>";
		}
		else
		{ 
			$sLineBreaker = "";
			$sLabelStyleStart = "";
			$sLabelStyleEnd = "";			
		}
				
		$sDataHead = <<<EOD
{$sLabelStyleStart}Name :$sLabelStyleEnd $sLabelValueSeparator $sReportname $sLineBreaker
{$sLabelStyleStart}Date :$sLabelStyleEnd $sLabelValueSeparator $sReportDate $sLineBreaker
{$sLabelStyleStart}Total Points Added :$sLabelStyleEnd $sLabelValueSeparator $sTotalPointsAdded $sLineBreaker
{$sLabelStyleStart}Total Coupons Redeemed :$sLabelStyleEnd $sLabelValueSeparator $sTotalPointsRedeemed $sLineBreaker
EOD;
	
		return $sDataHead;
	}
	 
	 
	 
	/*
	* saveSummaryLoyaltyCSVData() - This function is used for save csv file on disk as csv file format 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	public function saveSummaryLoyaltyCSVData($dt,$ipartnerId,$aCsvData,$ssuffixTable,$totalpointsadded,$totalreedemedcoupon) {
		$this->addFolder($ipartnerId,"minipos-reports/");
		$sReportFilePathDir = $this->_reportFilePathDir."minipos-reports/{$ipartnerId}/";
		$bFileStatus = 0;
		$sReportFileName = "summaryLoyaltyReport-{$dt}.csv";
		$sTargetFileName = $sReportFilePathDir .$sReportFileName;
		$sReportName="Summary Loyalty Activity Report";
		$aReportHead="";		
		$sDataHead = $this->getSummaryLoyaltyReportHead($dt,$sReportName,$aReportHead,$ssuffixTable,$totalpointsadded,$totalreedemedcoupon);
		
		$aResultData = $this->createMiniposCsvBaselineData($aCsvData);
		$sCsvData = $aResultData['data'];
		
		$sDataHead .= "\n\n"."Store Id, Total Points Added, Total Coupons Redeemed \n".$sCsvData;
		 		
		$f1 = fopen($sTargetFileName, 'w');
		
		try
		{
			fwrite($f1, $sDataHead);			
			fclose($f1);
			
			$mailto = "swatiadawade@gmail.com,swati@vollabs.com";
			$from_mail = "support@appsymth.com";
			$from_name = "appsymth";
			$replyto = "swati@vollabs.com";
			$subject = "Summary Loyalty Activity Report for $dt";
			$message = "Please find attached daily Summary Loyalty Activity Report for $dt";
			
			$this->mail_attachment($sReportFileName, $sReportFilePathDir, $mailto, $from_mail, $from_name, $replyto, $subject, $message);
		}
		catch(exception $e)
		{
			error_log(__FUNCTION__ .' Target file creation error :'. $e->getMessage());
		}
	}
	
	/*
	* createMiniposCsvBaselineData() - This function is used to create csv format data 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	private function createMiniposCsvBaselineData($aCsvData)
	{
		$aResultData =  array();
		$sCsvData = '';
		$iRedemed = 0;
		foreach($aCsvData as $rowData)
		{// processing all data 
			$sStoreId = $rowData['txnStoreId']; 
			$sTotalPoints = $rowData['PointsAdded'];
			$sTotalCouponsRedeemed = $rowData['couponcnt'];
			if($sStoreId!="")
				$sCsvData .= $sStoreId . ', ' . $sTotalPoints . ', ' . $sTotalCouponsRedeemed."\n"; 
		}
		
		$aResultData = array('data' => $sCsvData);
		return $aResultData;
	}
	
	/*
	* saveLoyaltyRedeemptionCSVData() - This function is used for save csv file on disk as csv file format 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	public function saveLoyaltyRedeemptionCSVData($dt,$ipartnerId,$aCsvData,$ssuffixTable,$totalpointsdeducted,$totalcouponadded) {
		$this->addFolder($ipartnerId,"minipos-reports/");
		//$dt=date("Y-m-d");
		//$dt=date("2014-02-23");
		$sReportFilePathDir = $this->_reportFilePathDir."minipos-reports/{$ipartnerId}/";
		$bFileStatus = 0;		
		$sReportFileName = "LoyaltyRedeemptionReport-{$dt}.csv";
		$sTargetFileName = $sReportFilePathDir .$sReportFileName;
		
		$aReportHead="";		
		$sDataHead = $this->getLoyaltyReedemptionReportHead($aReportHead,$ssuffixTable,$totalpointsdeducted,$totalcouponadded);
		
		$aResultData = $this->createMiniposRedemptionCsvBaselineData($aCsvData);
		$sCsvData = $aResultData['data'];		
		$sDataHead .= "\n\n"."Date/Time, Guest ID, Points Deducted, Coupon Name \n".$sCsvData;
		 		
		$f1 = fopen($sTargetFileName, 'w');
		
		try
		{
			fwrite($f1, $sDataHead);			
			fclose($f1);
			
			$mailto = "swatiadawade@gmail.com,swati@vollabs.com";
			$from_mail = "support@appsymth.com";
			$from_name = "appsymth";
			$replyto = "swati@vollabs.com";
			$subject = "Loyalty Redemption Report for $dt";
			$message = "Please find attached Loyalty Redemption report for $dt";
			
			$this->mail_attachment($sReportFileName, $sReportFilePathDir, $mailto, $from_mail, $from_name, $replyto, $subject, $message);			
		}
		catch(exception $e)
		{
			error_log(__FUNCTION__ .' Target file creation error :'. $e->getMessage());
		}
	}
	
	/*
	* createMiniposRedemptionCsvBaselineData() - This function is used to create csv format data 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	private function createMiniposRedemptionCsvBaselineData($aCsvData)
	{
		$aResultData =  array();
		$sCsvData = '';
		$iRedemed = 0;
		foreach($aCsvData as $rowData)
		{
			// processing all data 
			$sDate = $rowData['transferDate']; 
			$sGuestId = $rowData['guestId'];
			$sTotalPointsDeducted = $rowData['pointsdeducted'];
			$sTotalCouponsAdded = $rowData['couponname'];
			
			$sCsvData .= $sDate . ', '.$sGuestId.', ' . $sTotalPointsDeducted . ', ' . $sTotalCouponsAdded."\n"; 
		}
		
		$aResultData = array('data' => $sCsvData);
		return $aResultData;
	}
	  
	 
	/**
	* getLoyaltyReedemptionReportHead() - This function is used to create header for summary loyality report of minipos
	* @param - $aReportHead, $sType='csvfile'
	* @pre - none 
	* @post -none 
	* @return string
	*/ 
	public function getLoyaltyReedemptionReportHead($aReportHead,$ssuffixTable,$totalpointsdeducted,$totalcouponadded,$sType='csvfile')
	{
		$sReportname="Loyalty Points Redeemption Report $ssuffixTable";
		
		$currentdate=date("Y-m-d H:i:s");
		$sReportDate=getLongDateFormatGMTTxt($currentdate);

		$sTotalpointsdeducted=$totalpointsdeducted;
		$sTotalCouponsAdded=$totalcouponadded;
		
		$sLabelValueSeparator = '';
		if ($sType=='view')
		{
			$sLabelValueSeparator = '';	
			$sLineBreaker = "<br/>";
			$sLabelStyleStart = "<b>";
			$sLabelStyleEnd = "</b>";
		}
		else
		{ 
			$sLineBreaker = "";
			$sLabelStyleStart = "";
			$sLabelStyleEnd = "";			
		}
				
		$sDataHead = <<<EOD
{$sLabelStyleStart}Name :$sLabelStyleEnd $sLabelValueSeparator $sReportname $sLineBreaker
{$sLabelStyleStart}Date :$sLabelStyleEnd $sLabelValueSeparator $sReportDate $sLineBreaker
{$sLabelStyleStart}Total Points Added :$sLabelStyleEnd $sLabelValueSeparator $sTotalpointsdeducted $sLineBreaker
{$sLabelStyleStart}Total Coupons Redeemed :$sLabelStyleEnd $sLabelValueSeparator $sTotalCouponsAdded $sLineBreaker
EOD;
	
		return $sDataHead;
	}



/*
	* saveSummaryLoyaltyCSVData() - This function is used for save csv file on disk as csv file format 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	public function saveStoreLoyaltyCSVData($dt, $ipartnerId,$consortiumId,$aCsvData,$ssuffixTable,$totalpointsadded,$totalreedemedcoupon) {
		//$dt=date("Y-m-d");	
		//$dt=date("2014-02-25");
		$reporttype="minipos";
		$this->addFolder($ipartnerId,"minipos-reports/{$ipartnerId}/{$dt}/",$reporttype);		
			
		$sReportFilePathDir = $this->_reportFilePathDir."minipos-reports/{$ipartnerId}/{$dt}/";
		$bFileStatus = 0;		
		$sReportFileName = "storeLoyaltyReport-{$consortiumId}-{$dt}.csv";
		$sTargetFileName = $sReportFilePathDir .$sReportFileName;
			
		$sReportName = "Store Loyalty Activity Report ";
		$aReportHead="";		
		$sDataHead = $this->getSummaryLoyaltyReportHead($dt,$sReportName, $aReportHead,$ssuffixTable,$totalpointsadded,$totalreedemedcoupon);
		
		$aResultData = $this->createMiniposStoreLoyaltyCsvBaselineData($aCsvData);
		$sCsvData = $aResultData['data'];
		
		$sDataHead .= "\n\n"."Date/Time, Store Id, Guest ID, Add/Redeem, Amount/Coupon Name, Staff ID \n".$sCsvData;
		 		
		$f1 = fopen($sTargetFileName, 'w');
		
		try
		{
			fwrite($f1, $sDataHead);			
			fclose($f1);
			
			$mailto = "swatiadawade@gmail.com,swati@vollabs.com";
			$from_mail = "support@appsymth.com";
			$from_name = "appsymth";
			$replyto = "swati@vollabs.com";
			$subject = "Store Loyalty Activity Report for $dt";
			$message = "Please find attached daily Store Loyalty Activity Report for $dt";
			
			$this->mail_attachment($sReportFileName, $sReportFilePathDir, $mailto, $from_mail, $from_name, $replyto, $subject, $message);
		}
		catch(exception $e)
		{
			error_log(__FUNCTION__ .' Target file creation error :'. $e->getMessage());
		}
	}
	
	/*
	* createMiniposCsvBaselineData() - This function is used to create csv format data 
	* @param - $aCsvData
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	private function createMiniposStoreLoyaltyCsvBaselineData($aCsvData)
	{
		$aResultData =  array();
		$sCsvData = '';
		$iRedemed = 0;
		foreach($aCsvData as $rowData)
		{
			// processing all data
			//print "<pre>";print_r($rowData);
			//print "</pre>";
			$sTransferDate = $rowData['transferDate'];	//date  
			$sStoreId = $rowData['txnStoreId'];		//storeid
			$sGuestId = $rowData['guestId']; 	//guestid
			$sAddRedeemStatus = $rowData['addredeemstatus'];		//Add Redeem Status
			$samtcouponname = $rowData['amtcouponname'];	//amt/coupon name
			$sStaffId = $rowData['staffid'];		//staff id		
			
			if($sStoreId!="")
				$sCsvData .= $sTransferDate.', '.$sStoreId . ', ' . $sGuestId.', '. $sAddRedeemStatus.', '.$samtcouponname . ', ' . $sStaffId."\n"; 
		}
		
		$aResultData = array('data' => $sCsvData);
		return $aResultData;
	}
	
	/**
	* mail_attachment() - This function is used to send mail with attachment 
	* @param - $filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message
	* @pre - none 
	* @post -none 
	* @return boolean
	*/
	private function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) 
	{
    	$file = $path.$filename;
    	$file_size = filesize($file);
    	$handle = fopen($file, "r");
    	$content = fread($handle, $file_size);
    	fclose($handle);
    	$content = chunk_split(base64_encode($content));
    	$uid = md5(uniqid(time()));
    	$name = basename($file);
    	$header = "From: ".$from_name." <".$from_mail.">\r\n";
    	$header .= "Reply-To: ".$replyto."\r\n";
    	$header .= "MIME-Version: 1.0\r\n";
    	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    	$header .= "This is a multi-part message in MIME format.\r\n";
	    $header .= "--".$uid."\r\n";
	    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
	    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	    $header .= $message."\r\n\r\n";
	    $header .= "--".$uid."\r\n";
	    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
	    $header .= "Content-Transfer-Encoding: base64\r\n";
	    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	    $header .= $content."\r\n\r\n";
	    $header .= "--".$uid."--";
	    if (mail($mailto, $subject, "", $header)) 
	    {
	        echo "mail send ... OK"; // or use booleans here
	    } 
	    else 
	    {
	        echo "mail send ... ERROR!";
	    }
	}	
}
?>
