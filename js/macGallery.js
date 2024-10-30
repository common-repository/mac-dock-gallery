//For Showing the list of Album in adjacent
function facebookImportAlbumsConfirm(){	
	if(confirm("Do you want Import Albums and photos from facebook ? "))
		{
		     names1 = document.getElementById('macFacebookApi').value;
		     names2 = document.getElementById('macFacebookSecKey').value;
		     names1 = names1.trim();
		     names2 = names2.trim();
		     names = names1+','+names2;
		importalbumschecking(names , 'facebook');		
		}
}

function valideatefacebookAlbums(){
	
	var facebookapi = document.getElementById('macFacebookApi').value;
	var facbooksecrkey = document.getElementById('macFacebookSecKey').value;
	facbooksecrkey = facbooksecrkey.trim();
	facebookapi = facebookapi.trim();
	 var isvalideValues = 1;  
		   if(facbooksecrkey == '')
			{
			document.getElementById('macFaceApiSecErrorMsg').style.display = 'block';
			isvalideValues = 0;
			}
			else{
				document.getElementById('macFaceApiSecErrorMsg').style.display = 'none';
			}
			if(facebookapi == '')
			{
			document.getElementById('macFaceApiErrorMsg').style.display = 'block';
			isvalideValues = 0;
			}
			else{
					
				 var isnum =  isNumeric(facebookapi);
				 if(isnum)
					 {
					 document.getElementById('macFaceApiErrorMsg').style.display = 'none';
					 }
				 else{
					 document.getElementById('macFaceApiErrorMsg').style.display = 'block';
					 isvalideValues = 0;
				 }
				 				 
				
			}
			if(isvalideValues)
				return true;
			else
				return false;
}

function displaySelectedAlbum(albid,pageurl){					
					window.location = pageurl+'albid='+albid;									
				}


function valideatePicasaImportAlbums(){
	
	var userName = document.getElementById('macPicasaUserNames').value;
	
		userName = userName.trim();
		 var Error = 0;	
		 
		if(userName == '')
		{	
			document.getElementById('macPicasaUserNameErrorMsg').style.display = 'block';
			Error = 1;
		}
		else
		{
			document.getElementById('macPicasaUserNameErrorMsg').style.display = 'none';
		}
		
		 if(Error)
			 return false;
		 else{			
	 		var site = 'picasa';
	 		var importflag = 0;	 		 		
	 		 importflag = importalbumschecking(userName , site );	 	 		 
		 }		
			return false;	
}

function valideateFlickrImportAlbums(){
	
	document.getElementById('macsiteErrorMsg').style.display = 'none';
		var isEmp = document.getElementById('macFlickrApiId').value;
		var isEmp2 = document.getElementById('macFlickrUserId').value;
	
		var Error = 0;
		isEmp = isEmp.trim();
		isEmp2 = isEmp2.trim();
		if(isEmp == '' )
			{
			Error = 1;
			document.getElementById('macFlickrApiErrorMsg').style.display = 'block';
			}
		else{
			document.getElementById('macFlickrApiErrorMsg').style.display = 'none';
		}
		if(isEmp2 == '' )
		{
		Error = 1;
		document.getElementById('macFlickrUIdErrorMsg').style.display = 'block';
		}
	else{
		document.getElementById('macFlickrUIdErrorMsg').style.display = 'none';
	}
		 if(Error)
			 return false;
		 else{
			 
			 var site = 'flickr';
			 userName = isEmp+','+isEmp2;	
	 		 importalbumschecking(userName , site );
	 		
		 	}
		 return false;
}
//bad implementation
function sleep(milliSeconds){
	
	var startTime = new Date().getTime(); // get the current time
	while (new Date().getTime() < startTime + milliSeconds); // hog cpu
}


function valideateImportAlbums(){
	
	 var value = document.getElementById('currentTimeZone').value;
	 if(value == '' || value == null)
		 {
		 document.getElementById('timezoneErrorMsg').style.display = 'block';
		 return false;
		 }
	 else{
		 document.getElementById('timezoneErrorMsg').style.dispaly = 'none';
		
	 } 
}

function isNumeric(value) {
	  if (value != null && !value.toString().match(/^[-]?\d*\.?\d*$/)) return false;
	  return true;
	}


function  importalbumschecking(names , site )   //beform improt data from picasa we check he alredy used that accout or not?
{
	var pluginName = document.getElementById('pluginName').value;
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4  && xmlhttp.status==200)
    {
    	var responce = xmlhttp.responseText;
    			   
			    	if(responce == '11')
			    		{       
			    				if(confirm('This Albums are alreday improted If you want import again ? '))
			    					{
				    					 if(site == 'picasa'){   //
				    						 document.getElementById('formSubmitOk').value = 'ok';
				    						 alert('Importing Albums from picasa take few seconds please wait');
					    					 document.importAlbumsForm.submit();
				    					 }
				    					 else if(site == 'flickr'){
				    						 document.getElementById('flickrformSubmitOk').value = 'ok';
				    						 alert('Importing Albums from flickr take few seconds please wait');
					    					 document.importAlbumsForm.submit();
				    					 }
				    					 else if(site == 'facebook')
				    					 {
				    						 alert('Importing Albums from Facebook take few seconds Click Ok to Login');
				    						 //alert('Please Login to your facebook Account.');
				    						 var facebookloginSite = document.getElementById('facebookImportUrl').value;
				    							window.location = facebookloginSite;
				    					 }	 
			    					
			    					}//if end hear
			    				else {
			    					document.getElementById('formSubmitOk').value = 'no';
			    					document.getElementById('flickrformSubmitOk').value = 'no';
			    				}
			    		}
			    	else{
			    		 if(site == 'picasa'){
	   						 document.getElementById('formSubmitOk').value = 'ok';
	   						alert('Importing Albums from picasa take few seconds please wait');
				    		 document.importAlbumsForm.submit();
			    		 }
			    		 else if(site == 'flickr')
			    		 {
	   						 document.getElementById('flickrformSubmitOk').value = 'ok';
	   						 alert('Importing Albums from flickr take few seconds please wait');
				    		 document.importAlbumsForm.submit();
	   					 }
			    		 else if(site == 'facebook')
    					 {
			    			 alert('Importing Albums from Facebook take few seconds Click Ok to Login .');
    						 var facebookloginSite = document.getElementById('facebookImportUrl').value;
    						 window.location = facebookloginSite;
	    					 
    					 }	 
			    		
			    	}
		      
    }//if end hear
  }//onready state change end 
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&importalubms='+names+'&site='+site,true);
xmlhttp.send();
 }

function  importalbumsDeleting(allowimport)   //beform improt data from picasa we check he alredy used that accout or not?
{

if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
    	var responce = xmlhttp.responseText;         
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&importalubmsdelete='+allowimport,true);
xmlhttp.send();
 }


function macAlbum(pages)
{

if(pages == '')
{
    pages = 1;
}

if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('bind_macAlbum').innerHTML = xmlhttp.responseText
        imagePreview();
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalblist&pages='+pages,true);
xmlhttp.send();
 }

 //showing the name edit form
function albumNameform(macAlbum_id)
{
 if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('showAlbumedit_'+macAlbum_id).style.display="block";
        document.getElementById('showAlbumedit_'+macAlbum_id).innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macAlbumname_id='+macAlbum_id,true);
xmlhttp.send();

}

//Update the album name
function updAlbname(macAlbum_id)
{
var macAlbum_id = macAlbum_id;
var macAlbum_name   = document.getElementById('macedit_name_'+macAlbum_id).value;
var macAlbum_desc   = document.getElementById('macAlbum_desc_'+macAlbum_id).value;

var regex = /(<([^>]+)>)/ig;
macAlbum_name = macAlbum_name.replace(regex, '');
macAlbum_desc = macAlbum_desc.replace(regex, '');

  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
      document.getElementById('albName_'+macAlbum_id).innerHTML = macAlbum_name;
      document.getElementById('displayAlbum_'+macAlbum_id).innerHTML = macAlbum_desc;
      document.getElementById('showAlbumedit_'+macAlbum_id).style.display="none";
    }
  }
 
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macAlbum_id='+macAlbum_id+'&macAlbum_name='+macAlbum_name+'&macAlbum_desc='+macAlbum_desc,true);
xmlhttp.send();
}

//For Changing Mac Album Page id

function albumPageid(macAlbum_id)
{
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('showPageedit_'+macAlbum_id).innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&macAlbumpage_id='+macAlbum_id,true);
xmlhttp.send();
}

//For Updating Mac Album Page id

function updPageid(macAlbum_id)
{

var macAlbum_pageid = document.getElementById('macedit_pageid_'+macAlbum_id).value;

 if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
      document.getElementById('macPageid_'+macAlbum_id).innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&macAlbum_pageid='+macAlbum_pageid+'&macAlbum_id='+macAlbum_id,true);
xmlhttp.send();
}

//For changing the Album Status
 function macAlbum_status(status,macAlbum_id)
 {
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
    	//alert(xmlhttp.responseText);
       document.getElementById('status_bind_'+macAlbum_id).innerHTML = xmlhttp.responseText;
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&status='+status+'&albid='+macAlbum_id,true);
xmlhttp.send();
 }

// View Album description Update
function albumtoggle(macAlbum_id) {
	var albumele = document.getElementById("albumtoggleText_"+macAlbum_id);
	var albumtext = document.getElementById("albumdisplayText_"+macAlbum_id);
	if(albumele.style.display == "block") {
    		albumele.style.display = "none";
		albumtext.innerHTML = "Edit";
  	}
	else {
		albumele.style.display = "block";
		albumtext.innerHTML = "hide";
	}
}

function macdeleteAlbum(macAlbum_id)
{
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        window.location = self.location;
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macdelAlbum='+macAlbum_id,true);
xmlhttp.send();
}


//For Showing the Multiple Photo Upload in Adjacent
function macPhotos(numFilesQueued,albid)
{
    var show_pht = document.getElementById('bind_value').value;
    document.getElementById('bind_value').value = Number(show_pht)+Number(numFilesQueued);
    var rst_pht = document.getElementById('bind_value').value;
   
   if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('bind_macPhotos').innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotos&queue='+rst_pht+'&albid='+albid,true);
xmlhttp.send();
 }
//For Delete from the upload images
function macdelAjax(macPhoto_id)
{
     if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('remve_macPhotos_'+macPhoto_id).innerHTML += xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&macDelid='+macPhoto_id,true);
xmlhttp.send();
}
//For showing the name,description box adjacent to photos

 function maceditPhotos(rst)
{
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('edit_macPhotos').innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macEdit='+rst,true);
xmlhttp.send();
 }

//Update Name and Description

function upd_disphoto(queue,albid)
{
    for(i=1;i<=queue;i++)
    {
     var macedit_phtid = document.getElementById("macedit_id_"+i).value;
     var macedit_name  = document.getElementById("macedit_name_"+i).value;
     var macedit_desc  = document.getElementById("macedit_desc_"+i).value;
     var regex = /(<([^>]+)>)/ig;
     macedit_name = macedit_name.replace(regex, '');
     macedit_desc = macedit_desc.replace(regex, '');
     dragdr = jQuery.noConflict();
	 dragdr.ajax({
     method:"GET",
       url: admin_url+'admin-ajax.php?action=macphotoajax',
       data : "macedit_phtid="+macedit_phtid+"&macedit_name="+macedit_name+"&macedit_desc="+macedit_desc,
       asynchronous:false,
       error: function(html){
            },
      success: function(html){
    	  
          window.location = admin_url+'admin.php?page=macPhotos&action=viewPhotos&albid='+albid;
           }
       });
  }
alert('Updated sucessfully');
}

//Mac Individual Photo Delete
 function macdeletePhoto(macdeleteId)
 {
var agree=confirm("Are you sure you want to delete ?");
if (agree)
{
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('photo_delete_'+macdeleteId).style.visibility = 'hidden';
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macdeleteId='+macdeleteId,true);
xmlhttp.send();
 }
 }


 function macdesEdit(macPhoto_id)
 {

  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
        document.getElementById('edit_macDesc').innerHTML = xmlhttp.responseText
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macphotoDesc_id='+macPhoto_id,true);
xmlhttp.send();
 }

function photosNameform(macPhotos_id) {
 
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
    	var photoName = document.getElementById('showPhotosedit_'+macPhotos_id); 
    	photoName.innerHTML = xmlhttp.responseText;
    	photoName.style.display = '';
    	} 	
    }

xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macPhotoname_id='+macPhotos_id,true);
xmlhttp.send();
}
 //Update the Photo name
function updPhotoname(macPhotos_id)
{

var macPhoto_name = document.getElementById('macPhoto_name_'+macPhotos_id).value;
var regex = /(<([^>]+)>)/ig;
macPhoto_name = macPhoto_name.replace(regex, '');
 if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {       
      document.getElementById('macPhotos_'+macPhotos_id).innerHTML = xmlhttp.responseText
      document.getElementById('showPhotosedit_'+macPhotos_id).style.display = 'none';
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&macPhoto_name='+macPhoto_name+'&macPhotos_id='+macPhotos_id,true);
xmlhttp.send();
}
// View Photo description Update
function phototoggle(macPhoto_id) {
	var ele = document.getElementById("toggleText"+macPhoto_id);
     	var text = document.getElementById("displayText_"+macPhoto_id);
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Edit";
  	}
	else {
		ele.style.display = "block";
		
	}
}

function macdesc_updt(macPhoto_id)
{
var macPhoto_desc = document.getElementById('macPhoto_desc_'+macPhoto_id).value;
var regex = /(<([^>]+)>)/ig;
macPhoto_desc = macPhoto_desc.replace(regex, '');
var ele = document.getElementById("toggleText"+macPhoto_id);
    if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
      document.getElementById('display_txt_'+macPhoto_id).innerHTML = xmlhttp.responseText
      ele.style.display = "none";
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macalbajax&macPhoto_desc='+macPhoto_desc+'&macPhoto_id='+macPhoto_id,true);
xmlhttp.send();
}
function macAlbcover_status(addCover,albumId,macPhoto_id)
{
    if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {
      window.location = self.location;
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&albumCover='+addCover+'&albumId='+albumId+'&macCovered_id='+macPhoto_id,true);
xmlhttp.send();
}

//Photos Status Changing
function macPhoto_status(status,macPhoto_id)
{
  if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4)
    {   // alert(xmlhttp.responseText);
       document.getElementById('photoStatus_bind_'+macPhoto_id).innerHTML = xmlhttp.responseText;
    }
  }
xmlhttp.open("GET",admin_url+'admin-ajax.php?action=macphotoajax&status='+status+'&macPhoto_id='+macPhoto_id,true);
xmlhttp.send();
}
function fbcomments(pid,title,siteurl) {

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
   httpxml=new XMLHttpRequest();
  }
httpxml.onreadystatechange=function()
  {
  if (httpxml.readyState==4 && httpxml.status==200)
    {  
    var fbComments = httpxml.responseText;
    document.getElementById("facebook").innerHTML = fbComments;
     getfacebook();
     return false;
    }
  }
httpxml.open("GET",admin_url+"admin-ajax.php?action=macfbcooment&pid="+pid+'&phtName='+title+'&site_url='+siteurl+'&appId='+appId,true);
httpxml.send();
}
function CancelAlbum(macAlbum_id)
{
    document.getElementById('showAlbumedit_'+macAlbum_id).style.display="none";
}

