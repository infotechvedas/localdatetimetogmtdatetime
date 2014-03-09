$(document).ready(function(){
	var current_date = new Date();
	var gmt_offset = current_date.getTimezoneOffset( ) / 60; 
	try{
		$('#timezoneOffset').val(gmt_offset);
		
		document.getElementById('timezoneOffsetId').innerHTML = gmt_offset;
		
		var localTimeZoneDate = new Date();  
		document.getElementById('currentDateTimeZoneId').innerHTML = localTimeZoneDate;
		document.getElementById('currentDateTimeZone').value = localTimeZoneDate;
	}
	catch (err){
		
	} 
});