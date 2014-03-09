Purpose of this example to solve the problem for GMT DATE TIME and LOCAL DATE TIME

1. Create one database
	set database/user/password into "includes/config.php"
	
2. Extract the folder put into folder where it can be accessible to the browser

3. hit url in browser see some existing example on home page

4. to add new row click on the add link

I am passing timezoneoffset from add form and current date time

Processing timezoneoffset as in array format: 
	because we need conversion from number i.e. (+5.50) to time as 05:30, sign will be + 
	if timezone offset number is in negative i.e. (-5.50) as 05:30 sign will be -

I have written this function in "library/general-function.php"
	convertTimezoneOffsetToTime(timezoneoffset);
	
	getGMTtoLocalTime($aTimeZoneOffset, $gmtDateTime);
	
	getLocalTimeToGMT($aTimeZoneOffset, $currentDateTime);
	
	
For this example its required timezoneoffset, otherwise it won't work.


timezoneoffset will get on browser from JavaScript, its varies from country to country.

You must read these files to understand the code:
	
	1. js/membership-script.js --- getting timezoneoffset
	2. library/general-function.php ---- calculating timezoneoffset, getting local date time to GMT date Time, GMT to local date time, php date to MySQL date, MySQL to local date time
	3. membership.php --- adding processing timezoneoffset, local date time to GMT date time
	4. index.php --- showing added data processing timezoneoffset, GMT date time to local date time 
	5. add.php --- showing add form 

My contact details are as follows:

	Email: dhananjayksharma@gmail.com or infotechvedas.yahoo.co.in
	Blog: http://readlamp.blogspot.com/
	WebSite: www.infotechvedas.com -- coming soon
	Skype id: yashdhananjay


