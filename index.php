<?php
	include('includes/config.php');
	include('library/general-function.php');
	include('classes/Membership.php');
	$oMembership = new Membership();
	$aMemberList = $oMembership->getAllMembers();
?>
<!DOCTYPE HTML>
<head>
<title>Converting GMT to LocalTime and LocalTime to GMT</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/coin-slider.css" />
<script type="text/javascript" src="js/cufon-yui.js"></script>
<script type="text/javascript" src="js/cufon-georgia.js"></script>
<script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/coin-slider.min.js"></script>
</head>
<body>
<div class="main">
  <div class="header">
    <div class="header_resize">
      <div class="menu_nav">
        <ul>
          <li class="active"><a href="index.php"><span>Home</span></a></li>
          <li><a href="add.php"><span>Add</span></a></li>
        </ul>
      </div>
      <div class="logoNew">
        <h1><a href="index.php"><span>Converting GMT to LocalTime and LocalTime to GMT</span></a></h1>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  <div class="content">
    <div class="content_resize">
      <div class="mainbar">
        <div class="article">
          <h2><span>List of </span>members</h2>
          <div class="clr"></div>
          <table border="1" width="100%">
          <tr class="rows"><td>Id</td><td>Name</td><td>Email</td><td>Country</td><td>Registered DateTime (local)</td><td>TimeZone Offset</td><td>DateTime (GMT) </td><td>Calculated Local Time from GMT and TimeZone Offset </td><td>Broweser Date Time</td></tr>
          <?php
          	foreach($aMemberList as $row){
				$iId = $row['id'];
				$sName = $row['name'];
				$sEmailId = $row['emaild'];
				$sCountry_name = $row['country_name'];
				$dRegisteredDate = mysqlToPhpDate($row['registered_date'], '-');
				$iCountryTimezone = $row['country_timezone'];
				$sGmt_date_time = $row['gmt_date_time'];
				
				/*Preparing localDateTime to GMT dateTime - start */
				$aTimeZoneOffset = convertTimezoneOffsetToTime($iCountryTimezone);
				$sLocalDateTime = getGMTtoLocalTime($aTimeZoneOffset, $sGmt_date_time);
				$dtSepa = '-';
				$sLocalDateTimePhpFormat =  mysqlToPhpDate($sLocalDateTime, $dtSepa);
				/*Preparing localDateTime to GMT dateTime - start */
				
				$sLocal_date_time = $row['local_date_time'];
				echo "<tr class='rows'><td>$iId</td><td>$sName</td><td>$sEmailId</td><td>$sCountry_name</td><td>$dRegisteredDate (local)</td><td>$iCountryTimezone</td><td>$sGmt_date_time</td><td>$sLocalDateTimePhpFormat</td><td>$sLocal_date_time</td></tr>";          		
          	}
          ?>
          </table>
        </div> 
      </div>
      <div class="clr"></div>
    </div>
  </div> 
  <div class="footer">
    <div class="footer_resize">
      <p class="lf">Example by Info Tech Vedas </p>
      <div style="clear:both;"></div>
    </div>
  </div>
</div>
</html>
