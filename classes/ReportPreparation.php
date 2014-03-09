<?php
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/more/logs/php-error.log');
error_reporting(E_ALL);
ini_set('display_errors',1);

/*This model is used for reporting CSV report for campaign details
 * 
 * 
 * */ 
class Reportpreparation {
	private $_reportFilePathDir = null;
	private $_reportFilePathView = null;
	
    private $_db_master = null;
    private $_db_slave = null;

    public function __construct() { 
		global $aDatabase;
		$aDb1 = $aDatabase['db1'];
		$aDb2 = $aDatabase['db2'];

		$this->_db_master = new mysqli($aDb1['host'], $aDb1['user'], $aDb1['password'], $aDb1['database']);
		
		$this->_db_slave = new mysqli($aDb2['host'], $aDb2['user'], $aDb2['password'], $aDb2['database']);
		
		$this->_reportFilePathDir = REPORT_PATH_DIR;
    }
	/**
	* getLastWeekStartEnd() - This function is used for getting last week date range
	* @param - $sCurrentDate
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function getLastWeekStartEnd($sCurrentDate){
		#$sCurrentDate = 'current_date()';		
		#$sCurrentDate = "'2013-01-01'";
		
		$sql = "select date_sub(date('$sCurrentDate'), INTERVAL (weekday('$sCurrentDate') + 7) DAY) as dateStartOfWeek;";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql . "\n\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
	
		$sDateStartOfWeek = $aResultData[0]['dateStartOfWeek'];	
		$sql = "select date('$sDateStartOfWeek') as WeekStartDate,  date_add(date('$sDateStartOfWeek'), INTERVAL 6 DAY) as WeekEndDate;";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql . "\n\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $aResultData;
	}
	
	/**
	* getCurrentWeekStartEnd() - This function is used for getting current week date range for campaign
	* @param - $sCurrentDate
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function getCurrentWeekStartEnd($sCurrentDate){
		#$sCurrentDate = 'current_date()';		
		#$sCurrentDate = "'2013-01-01'";
		
		$sql = "select date_sub(date('$sCurrentDate'), INTERVAL weekday('$sCurrentDate') DAY) as dateStartOfWeek;";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql . "\n\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
	
		$sDateStartOfWeek = $aResultData[0]['dateStartOfWeek'];	
		$sql = "select date('$sDateStartOfWeek') as WeekStartDate,  date_add(date('$sDateStartOfWeek'), INTERVAL 6 DAY) as WeekEndDate;";
		#echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql . "\n\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $aResultData;
	}	
	/**
	* getRedemtionAccountKeyBy() - This function is used for getting all redemption for given campaign no.
	* @param - $sCampaignId
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function getRedemtionAccountKeyBy($sCampaignId){
		$sql = "SELECT a.accountKey FROM vouchers as v, voucherRedemptions as vr, accounts as a WHERE a.accountId=v.accountId AND vr.voucherId= v.voucherId AND v.voucherBatchId IN (SELECT voucherBatchId FROM voucherBatch WHERE fkCampaignId='$sCampaignId');";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		try { 
			$stat = $this->_db_slave->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $aResultData;
	}
	
	/**
	* getVisitCountNTransactionAmt() function is used for getting accountKeys
	* @param - $sAccountKeys, $sWeekStartDate, $sWeekEndDate, $sSuffixTable
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function getVisitCountNTransactionAmt($sAccountKeys, $sWeekStartDate, $sWeekEndDate, $sSuffixTable){
		$aResultData = array(); 
		$sql = "SELECT round(sum(txnValue),2) as txnsValue, count(txnValue) as visits from guestTxns_$sSuffixTable WHERE accountKey in($sAccountKeys) AND txnValue > 0 AND txnDateTime >= '$sWeekStartDate' AND txnDateTime<= '$sWeekEndDate';";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		#error_log('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql);
		try { 
			$stat = $this->_db_slave->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		return $aResultData;
	}
	
	/**
	* getBaselineReportDetails() function is used for getting accountKeys
	* @param - $sAccountKeys, $sWeekStartDate, $sWeekEndDate, $sSuffixTable
	* @pre - none 
	* @post -none 
	* @return array
	*/
	public function getBaselineReportDetails($sAccountKeys, $sWeekStartDate, $sWeekEndDate, $sSuffixTable){
		$sql = "SELECT * from guestTxns_$sSuffixTable WHERE accountKey in($sAccountKeys) AND txnValue > 0 AND txnDateTime >= '$sWeekStartDate' AND txnDateTime<= '$sWeekEndDate';";
		echo ('New SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		try { 
			$stat = $this->_db_slave->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		return $aResultData;
	}
	
	/**
	* addCampaignReports() function is used for adding report summary for campaign
	* @param - $sCampaignId, $aBaselineData, $aCampaignWeekData, $sOfferValidDate
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function addCampaignReports($sCampaignId, $aBaselineData, $aCampaignWeekData, $sOfferValidDate){
 
		$iBaselineVisits = $aBaselineData[0]['visits'];
		$iBaselineTxnsValue = $aBaselineData[0]['txnsValue'];
				
		$iCampaignVisits = $aCampaignWeekData[0]['visits'];
		$iCampaignTxnsValue = $aCampaignWeekData[0]['txnsValue'];
		
		$iIncreasedVisit = $iCampaignVisits-$iBaselineVisits;
		$iIncreasedRevenue = $iCampaignTxnsValue-$iBaselineTxnsValue;

		$iIncreasedRevenuePerVisit = 0;

		if ($iIncreasedVisit<>0 && $iIncreasedRevenue <> 0){
			$iIncreasedRevenuePerVisit = round($iIncreasedRevenue / $iIncreasedVisit, 2);
			if ($iIncreasedVisit < 0 || $iIncreasedRevenue < 0 ){// if is negative - result will be negative
				$iIncreasedRevenuePerVisit =  '-'.$iIncreasedRevenuePerVisit;
			}
		}
		
		$iReportExist = 0;
		$sql = "SELECT count(*) as cnt from campaignReports WHERE fkCampaignId='$sCampaignId'";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
			$iReportExist = $aResultData[0]['cnt'];
		}
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		
		if ($iReportExist > 0){
			$sql = "UPDATE campaignReports set baselineVisits='$iBaselineVisits', baselineRevenue='$iBaselineTxnsValue', campaignVisits='$iCampaignVisits', campaignRevenue='$iCampaignTxnsValue', incsdVisits='$iIncreasedVisit', incsdRevenue='$iIncreasedRevenue', revnPerIncVisit='$iIncreasedRevenuePerVisit', campaignRank='', createdDateTime='$sOfferValidDate' WHERE fkCampaignId='$sCampaignId'";	
		}
		else{
			$sql = "INSERT INTO campaignReports (crId, fkCampaignId, baselineVisits, baselineRevenue, campaignVisits, campaignRevenue, incsdVisits, incsdRevenue, revnPerIncVisit, campaignRank, createdDateTime) VALUES (uuid(), '$sCampaignId', '$iBaselineVisits', '$iBaselineTxnsValue', '$iCampaignVisits', '$iCampaignTxnsValue', '$iIncreasedVisit', '$iIncreasedRevenue', '$iIncreasedRevenuePerVisit', '', '$sOfferValidDate');";
		}
		
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		try {			 
			$stat =  $this->_db_master->query($sql);
		} 
		catch (exception $e){
			echo ('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());
		} 
	}

/**
	* addCampaignRanks() function is used for adding campaign report rank
	* @param - $sReportDate
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function addCampaignRanks($sReportDate){
		$aResultData = array();
		$sql = "SELECT * from campaignReports WHERE createdDateTime ='$sReportDate' ORDER BY revnPerIncVisit DESC ";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r"); 
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		}
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
		$iCountResultData = count($aResultData);
		
		if ( $iCountResultData > 0 && is_array($aResultData)){
			for ($i=0; $i < $iCountResultData; $i++){
				$sCampaignId = $aResultData[$i]['fkCampaignId'];
				$sRank =  ($i+1) . '/'. $iCountResultData;
				$sql = "UPDATE campaignReports set campaignRank='$sRank' WHERE fkCampaignId='$sCampaignId'";
				try {	 
					$stat =  $this->_db_master->query($sql);
				} 
				catch (exception $e){
					echo ('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());
					error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());
				}				
			}
		}

		return true;
	}
	
	
	/**
	* getPrevReportPeriod() - This function is used for getting last week date range
	* @param - $sStartDate, $sEndDate
	* @pre - none 
	* @post -none 
	* @return array
	*/ 
	public function getPrevReportPeriod($sStartDate, $sEndDate, $sCampaignId){		
		$sql = "select datediff(date('$sEndDate'),  date('$sStartDate')) as noofdays;";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		}
	
		$iNoOfDays = $aResultData[0]['noofdays'];	
		if ($iNoOfDays > 7 ){
			$iNoOfDays = ceil($iNoOfDays / 7)  * 7; 
		}
		else{
			$iNoOfDays = 8;
		}
		
		$sql = "select '$sStartDate' as currStartDate, '$sEndDate' as currEndDate, date_sub(date('$sStartDate'), INTERVAL $iNoOfDays DAY) as prevStartDate,  date_sub(date('$sEndDate'), INTERVAL $iNoOfDays DAY) as prevEndDate;";
		echo ('SQL in '.__FUNCTION__.' : line '.__LINE__.' : '.$sql."\n\r");
		try { 
			$stat = $this->_db_master->query($sql); 
			$aResultData = getAllData($stat); 
		} 
		catch (exception $e){
			error_log('Exception thrown: SQL in '.__FUNCTION__.' : line '.__LINE__.' : Error : '.$e->getMessage());	
		} 
		return $aResultData;
	}
	
}
?>
