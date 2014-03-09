<?php
	include('includes/config.php');
	include('library/general-function.php');
	include('classes/Membership.php');
	$oMembership = new Membership();
	$aMemberList = $oMembership->getAllMembers();
	$sCurrentDateTime = date('d-m-Y H:i:s');
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
<script type="text/javascript" src="js/membership-script.js"></script>
<script type="text/javascript" src="js/coin-slider.min.js"></script>
</head>
<body>
<div class="main">
  <div class="header">
    <div class="header_resize">
      <div class="menu_nav">
        <ul>
          <li><a href="index.php"><span>Home</span></a></li>
          <li class="active"><a href="add.php"><span>Add</span></a></li>
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
          <h2><span>Sign </span>in</h2>
          <div class="clr"></div>
          <form name="membersignin" action="membership.php?action=register" method="post">
	          <table border="1" width="80%">
	          	<tr class="rows"><td>Name</td><td>:</td><td><input type="text" name="username" value="" maxlength="22"/></td></tr>
	          	<tr class="rows"><td>Email Id</td><td>:</td><td><input type="text" name="emailid" value="" maxlength="45"/></td></tr>
	          	<tr class="rows"><td>Country Name</td><td>:</td><td><input type="text" name="countryname" value="" maxlength="45"/></td></tr>
	          	<tr class="rows"><td>Registered Date Time</td><td>:</td><td><input type="text" name="registereddatetime" id="registereddatetime" value="<?php echo $sCurrentDateTime;?>" readonly="readonly" maxlength="22" class="cw90"/></td></tr>
	          	<tr class="rows"><td>Country TimeZoneOffset</td><td>:</td><td><input type="hidden" name="timezoneOffset" id="timezoneOffset" value="" maxlength="6"/><span id="timezoneOffsetId">--</span></td></tr>
	          	<tr class="rows"><td>Country Current Date Time & Zone</td><td>:</td><td><input type="hidden" name="currentdatetimezone" id="currentDateTimeZone" value="" maxlength="6"/><span id="currentDateTimeZoneId"></span></td></tr>
	          	<tr class="rows"><td colspan="3" align="center"><input type="submit" name="signIn" value="Register"/></td></tr>
	          </table>
          </form>
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
