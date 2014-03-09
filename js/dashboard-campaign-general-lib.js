/* general section - start */
 var viewportwidth;
 var viewportheight;
 
 // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
 
 if (typeof window.innerWidth != 'undefined') {
      viewportwidth = window.innerWidth;
      viewportheight = window.innerHeight;
 }
 
// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document) 
 else if (typeof document.documentElement != 'undefined'  && typeof document.documentElement.clientWidth !=
     'undefined' && document.documentElement.clientWidth != 0) {
       viewportwidth = document.documentElement.clientWidth;
       viewportheight = document.documentElement.clientHeight;
 }
  // older versions of IE
 else {
   viewportwidth = document.getElementsByTagName('body').clientWidth;
   viewportheight = document.getElementsByTagName('body').clientHeight;
 }
 
function getPosition(obj){
    var topValue= 0,leftValue= 0;
    while(obj){
		leftValue+= obj.offsetLeft;
		topValue+= obj.offsetTop;
		obj= obj.offsetParent;
    }
    finalvalue = leftValue + "," + topValue;
    return finalvalue;
}

function scrollWindow(id){
	var objId = document.getElementById(id); 
	var iObjIdHeight = objId.offsetHeight; 
	var iPositionAt = getPosition(objId);
	var aPosition = iPositionAt.split(',');
	var iIdAtLeft = aPosition[0];
	//var iIdHeight = parseInt(aPosition[1])+parseInt(iObjIdHeight)+20; 
	var iIdHeight = parseInt(iObjIdHeight)+20; 
	//var sWindowScroll = getWindowScroll();	var aWindowScroll = sWindowScroll.split(','); 
	var iScrollTop = getWindowScroll();
	var aIdPosition = findPositionOfElement(id);
	var iIdPositionTop = aIdPosition[1];
	var iScreenToMove = parseInt(iIdPositionTop)-parseInt(iScrollTop)+parseInt(iIdHeight) - viewportheight + 10;// extra 10
	
	//alert('idTop:'+ iIdPositionTop + ', iScrollTop: '+ iScrollTop + ', iIdHeight:' + iIdHeight + ', iScreenToMove: '+ iScreenToMove +', viewportheight: ' +viewportheight); 
		
	if (iScreenToMove >0){
		window.scrollBy(0,iIdHeight);
	}
}

function getWindowScroll(){ 
	var ScrollTop = document.body.scrollTop;
	if (ScrollTop == 0){
		if (window.pageYOffset)
			ScrollTop = window.pageYOffset;
		else
			ScrollTop = (document.body.parentElement) ? document.body.parentElement.scrollTop : 0;
	}
	
	return ScrollTop;
}

function findPositionOfElement(id) {
	 var obj = document.getElementById(id);
	  var curleft = 0;
	  var curtop = 0;
	if (obj.offsetParent) {
	do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent); 
		return [curleft,curtop];
	}
	
	return [curleft,curtop];
}


/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Url = { 
	// public method for url encoding
	encode : function (string) {
		return escape(this._utf8_encode(string));
	}, 
	// public method for url decoding
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	}, 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			} 
		} 
		return utftext;
	}, 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0; 
		while ( i < utftext.length ) { 
			c = utftext.charCodeAt(i); 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			} 
		} 
		return string;
	} 
}

/* general section - end */

/* general - 2  section - start */
function limitText(limitField, limitCount, limitNum) {
	var limitFieldValue = $('#'+limitField).val();
	if (limitFieldValue.length > limitNum) {
		var currentText = limitFieldValue.substring(0, limitNum);
		$('#'+limitField).val(currentText);
	} else {
		document.getElementById(limitCount).innerHTML = limitNum - limitFieldValue.length;
	}
} 
  
function redirectPage(pageUrl){
	var redirectUrl = webURL+pageUrl;
	window.location = redirectUrl;
}

function confirmDelete(pageUrl){
	var deleteConfirm = confirm('Are you sure to delete this record ?');
	
	if (deleteConfirm==true){
		redirectPage(pageUrl);
	}
}


function checkParent(parentId,childId) {
	var iChildCnt = jQuery(".child_"+parentId).length;
	var iSelectedChildCnt2 = $(".child_"+parentId+":checked").length;

    if(iSelectedChildCnt2 > 0) {
           $('#'+parentId).attr("checked", "checked");
       } else {
         /*  $('#'+parentId).removeAttr("checked"); */
       }
 }


function toggleCampaignUntildate(){ 
	if ($('#limitResponse').is(':checked')){ 
		$('#campReplyLastDt').removeAttr('disabled',''); 
		$('#campReplyLastDt').datetimepicker({ minDate:0});
		
		var displayDate = getCurrentDateTime();
		$('#campReplyLastDt').val(displayDate);
		
	}
	else{
		$('#campReplyLastDt').attr('disabled','disabled');
		$('#campReplyLastDt').val('');
	}
}

function getCurrentDateTime(){
	var myDate = new Date();
	var displayTime = myDate.getHours()+':' + myDate.getMinutes()+':' + myDate.getSeconds();
	var displayDate = (myDate.getMonth()+1) + '/' + (myDate.getDate()) + '/' + myDate.getFullYear() + ' ' + displayTime;
	return displayDate;
}

/* used in broadcast  */
function toggleBroadcastTypedate(){
	var displayDate = getCurrentDateTime();		 
	var bcSendType = $("#bcsendType option:selected").val();  
	if (bcSendType=='schedule'){
		$('#bcScheduleDate').removeAttr('disabled', '');
		$('#bcScheduleDate').datetimepicker({
			minDate: 0,
			maxDate: iValidPaySubsDays,
			onSelect: function(dateText, inst){
					getLocalDateTimewithTimezone(dateText);
				}
			});
		$('#bcScheduleDate').val(displayDate); 
    }
	else{
		$('#bcScheduleDate').attr('disabled', true);
		$('#bcScheduleDate').val('');
		getLocalDateTimewithTimezone(displayDate);// get local datetime with zone
	} 
}

function getLocalDateTimewithTimezone(dateTime){
	//Wed Jun 27 2012 15:15:57 GMT+0845 (CWST)
	var localTimeZoneDate = new Date(dateTime);
	var localTimeZoneDate = localTimeZoneDate.toLocaleString();
	document.getElementById('localTimeZoneId').value = localTimeZoneDate; 
}

/* used in add/edit user  */
function onRoleChange(role){
	if (role=='admins'){ 
		$('#allowedAccessKeyId :input').attr('disabled', 'disabled'); 
	}
	else{ 
		$('#allowedAccessKeyId :input').removeAttr('disabled', ''); 
	}
}

/* general - 2  section - end */

function IsValidPhoneNumber(sText){
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;
	for(i=0;i<sText.length&&IsNumber==true;i++){
		Char=sText.charAt(i);
		if(ValidChars.indexOf(Char)==-1){IsNumber=false;}
	}
	return IsNumber;
}

function IsValidUserName(sText){
	var regExpChars = /^[a-zA-Z0-9]+$/;
	IsTagCode = regExpChars.test(sText); 
	return IsTagCode;
}


function IsValidTagCode(sText){
	var regExpChars = /^[a-zA-Z0-9]+$/;
	IsTagCode = regExpChars.test(sText); 
	return IsTagCode;
}


function IsValidText(sText){
	var regExpChars = /^[a-zA-Z0-9\s.,]+$/;
	IsTagCode = regExpChars.test(sText); 
	return IsTagCode;
}

function validateConfirmFields(text1, text2){
	if (text1==text2){
		return true;
	}
	else{
		return false;
	}
}

function validateConfirmPassword(text1, text2){
	if (text1==text2){
		return true;
	}
	else{
		return false;
	}
}

/* add Input  */ 
var counterStoreDiv = 1;
function addMoreStore(divName){
	counterStoreDiv = counterStoreDiv + 1;
	var divStoreId = 'storeDivId_' + counterStoreDiv;
	
	var feesAmountValue = document.getElementById('feesAmount').value;	 
	/* style="border:1px red solid;height:20px;" */
 
	var newdiv = document.createElement('div');
	newdiv.innerHTML = "<span class='f14' style='float:left;display:block;width:280px;text-align:left;border:0px green solid;height:20px;'><input type='text' name='storename[]' size='16'/>  <b>$ " + feesAmountValue + "</b> <a href='javascript:;' onclick='removeStore(\"" + divStoreId +"\",\"" + divName + "\")'>Remove</a></span>";
	newdiv.id = divStoreId; 
	newdiv.className = "amountStoreRunning"; 
	document.getElementById(divName).appendChild(newdiv); 
	updateRunningTotalAmount();//update amount 
}

/* remove Input  */ 
function removeStore(divStoreId, divName){
	counterStoreDiv = counterStoreDiv - 1;
	var elementStoreDiv = document.getElementById(divStoreId) 
	elementStoreDiv.parentNode.removeChild(elementStoreDiv);  
	updateRunningTotalAmount();//update amount
}

function updateRunningTotalAmount(){ 
	var feesAmountValue = document.getElementById('feesAmount').value;	 
	var runningTotalAmt = counterStoreDiv * feesAmountValue;
	/* alert(feesAmountValue + ' --- ' + runningTotalAmt  +' ---' + counterStoreDiv); */
	var runningTotalObj = document.getElementById('runningTotal');
	
	if (runningTotalAmt>0){	
		runningTotalObj.innerHTML = runningTotalAmt ;
	}
	else{
		alert('Subscription amount error');
	}
}

/* add Input  */ 
var counterFileDiv = 1;
function addMoreFile(divName){
	counterFileDiv++;
	var divStoreId = 'fileNameDivId_' + counterFileDiv;
	var newdiv = document.createElement('div');
	newdiv.innerHTML = "<input type='file' name='fileName[]'/><a href='javascript:;' onclick='removeMoreFile(\"" + divStoreId +"\",\"" + divName + "\")'>Remove</a>";
	newdiv.id = divStoreId; 
	document.getElementById(divName).appendChild(newdiv);
}
/* remove Input  */ 
function removeMoreFile(divStoreId, divName){
	counterFileDiv--;
	var elementStoreDiv = document.getElementById(divStoreId) 
	elementStoreDiv.parentNode.removeChild(elementStoreDiv); 
}

//replace space with -
function replaceSpacesWithDash(str){
	var sStringTrimed = str.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
	var tt =  sStringTrimed.replace(/ /g,'-');
	var tt2 =  tt.replace(/--/g,''); 
	return tt2; 
}
//
function removeSpecialChar(str){
	var regExp = /[,#{}$@~\/:<>`|\^!()\[\]'\.\?;"\*&]/g;
	var tt =  str.replace(regExp,'');
	return tt;
}

//remove space
function removeSpaces(str){
	var tt =  str.replace(/^\s+|\s+$/g,'');
	return tt;
}
//

//required data
function strRequired(str){
	var str1 = removeSpaces(str);
	return str1; //return blank number
}
// is number
function isNumeric(str){
	return isNaN(str);
}

//required data
function numberRequired(str){
	var str1 = str;
	if (str1.length>0){
		var tt =  isNumeric(str1);
		if (tt==false){
			return true;
		} else {
			return false;
		}
	} else {
		return false; //return blank number
	}
}

// date converter - start
function getDateObject(dateString,dateSeperator){
	//This function return a date object after accepting 
	//a date string ans dateseparator as arguments
	var curValue=dateString;
	var sepChar=dateSeperator;
	var curPos=0;
	var cDate,cMonth,cYear;

	//extract day portion
	curPos=dateString.indexOf(sepChar);
	cDate=dateString.substring(0,curPos);
	
	//extract month portion				
	endPos=dateString.indexOf(sepChar,curPos+1);
	cMonth=dateString.substring(curPos+1,endPos);

	//extract year portion				
	curPos=endPos;
	endPos=curPos+5;			
	cYear=curValue.substring(curPos+1,endPos);
	
	//Create Date Object
	dtObject = new Date(cYear,cMonth,cDate);	
	return dtObject;
}

// date converter - end
// dateComparison - start
function dateComparison(sdt){
	var chStartDays = parseInt(document.getElementById("passsdateid").value); 
	var today = new Date();
	var cm = today.getMonth();
	var cy = today.getFullYear();
	var cdTmp = today.getDate();
	var cd = cdTmp + chStartDays; // chStartDays days added by dhannajay as required for  
	var dtc  = cd+'-'+cm+'-'+cy;
	dtcObj = getDateObject(dtc,"-");
	dttObj = getDateObject(sdt,"-");
	//alert(dttObj + '  ' + dtcObj + ' ' + cd  + ' '+cdTmp );
	if (dttObj<dtcObj){
		return false;
	}
}
// dateComparison - end

function checkSpecialChar(charvalue){
	var invalid = /[#{}$@~/:<>`|^!()\[\];]/i;
	var charFound = charvalue.search(invalid);
	if (charFound>=0){
		return false;
	}
    return true;
}

function validateEmailId(emailVal){  
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/; 
	var isError = false;  
	if(reg.test(emailVal) == true) {
		return true;
	}
	else {
		return false;
	}
}
