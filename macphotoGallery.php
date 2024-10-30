<?php
//Adding Buy now and Apply licence button in photos page
$dbtoken = md5(DB_NAME);
?>
<?php
global $wpdb; 

$site_url = get_bloginfo('url');

class macPhotos {
	var $base_page = '?page=macPage';
	function macPhotos() {
		maccontroller();
                
	}
}

function maccontroller() {
        $action  = NULL;
	global $wpdb, $site_url;
	$site_url  = get_bloginfo('url');
        $admin_url = admin_url();
	
	$pageURL   = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
				if ($_SERVER["SERVER_PORT"] != "80")
				{
				    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} 
				else 
				{
				    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
			$copyofurl = $pageURL;	
			$pageURL =	explode('albid',$pageURL);
         
	?>


<script type="text/javascript">
        var site_url,mac_folder,numfiles,admin_url;
        site_url = '<?php echo $site_url; ?>';
        var url = '<?php echo $site_url; ?>';
        mac_folder  = '<?php echo plugin_dir_url(__FILE__); ?>';
        var admin_url = '<?php echo admin_url(); ?>';
         var includes_url = '<?php echo includes_url(); ?>';
        keyApps = '<?php echo $configXML->keyApps; ?>';
        videoPage = '<?php echo $meta; ?>';
        var dragdr = jQuery.noConflict();
                function GetSelectedItem() {
                  //  alert(document.frm1.macAlbum_name.length);
                len = document.frm1.macAlbum_name.length;
                i = 0;
                chosen = "none";
                for (i = 0; i < len; i++) {
                    if (document.frm1.macAlbum_name[i].selected) {
                        chosen = document.frm1.macAlbum_name[i].value;
                    }
                }
               window.location = admin_url+"admin.php?page=macPhotos&albid="+chosen;

            }

       window.onload = function()
       {
           if (document.getElementById('macAlbum_name').value == 0 ||document.getElementById('macAlbum_name').value == -1)
           {
        	   document.getElementById('swfupload-control').style.visibility='hidden';
           }
           else
           {
        	   document.getElementById('swfupload-control').style.visibility='visible';
           }
       }
    </script>
<script type="text/javascript">
QueueCountApptha = 0;
    dragdr(document).ready(function(){
    if(document.getElementById('mac-test-list'))
                {
                 dragdr("#mac-test-list").sortable({
                 handle : '.handle',
                 update : function () {
                    var pagestart =   parseInt(dragdr('#pagestart').val());
                    var order =dragdr('#mac-test-list').sortable('serialize');
                     dragdr.post(admin_url+"admin-ajax.php?action=process-sortable&"+order+"&pagestart="+pagestart);
                   }

                });
    }

           dragdr('#swfupload-control').swfupload({
                upload_url: admin_url+"admin-ajax.php?action=macphotoupload&albumId=<?php echo $_REQUEST['albid'] ?>",
                file_post_name: 'uploadfile',
                file_size_limit : 0,
                post_params: {"token" : "<?php echo md5(DB_NAME); ?>"},
                file_types : "*.jpg;*.png;*.jpeg;*.gif",
                file_types_description : "Image files",
                file_upload_limit : 1000,
                flash_url : includes_url+"js/swfupload/swfupload.swf",
                button_image_url : mac_folder+'/js/swfupload/wdp_buttons_upload_114x29.png',
                button_width : 114,
                button_height : 29,
                button_placeholder :dragdr('#button')[0],
                debug: false
            })
            .bind('fileQueued', function(event, file){
                var listitem='<li id="'+file.id+'" >'+
                    'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                    '<div class="progressbar" ><div class="progress" ></div></div>'+
                    '<p class="status" >Pending</p>'+
                    '<span class="cancel" >&nbsp;</span>'+
                    '</li>';

                dragdr('#log').append(listitem);

               dragdr('li#'+file.id+' .cancel').bind('click', function(){
                    var swfu =dragdr.swfupload.getInstance('#swfupload-control');
                    swfu.cancelUpload(file.id);
                    dragdr('li#'+file.id).slideUp('fast');
                });
                // start the upload since it's queued
                dragdr(this).swfupload('startUpload');
            })
            .bind('fileQueueError', function(event, file, errorCode, message){
                alert('Size of the file '+file.name+' is greater than limit');

            })
            .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
               dragdr('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);

               numfiles = numFilesQueued;
               totalQueues = numFilesSelected;
               i=1;
               j=numfiles;
               
            })
            .bind('uploadStart', function(event, file){

                dragdr('#log li#'+file.id).find('p.status').text('Uploading...');
               dragdr('#log li#'+file.id).find('span.progressvalue').text('0%');
               dragdr('#log li#'+file.id).find('span.cancel').hide();
            })
            .bind('uploadProgress', function(event, file, bytesLoaded){
                //Show Progress

                var percentage=Math.round((bytesLoaded/file.size)*100);
               dragdr('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
               dragdr('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
            })
            .bind('uploadSuccess', function(event, file, serverData){
            	
                var item=dragdr('#log li#'+file.id);
                QueueCountApptha++;
                item.find('div.progress').css('width', '100%');
                item.find('span.progressvalue').text('100%');
                item.addClass('success').find('p.status').html('Done!!!');
                jQuery('#queuestatus').text('Files Selected: '+totalQueues+' / Queued Files: '+QueueCountApptha);

            })
            .bind('uploadComplete', function(event, file){
                // upload has completed, try the next one in the queue
                dragdr(this).swfupload('startUpload');
                if(j == i)
                    {
                 macPhotos(numfiles,'<?php echo $_REQUEST['albid'] ?>');
                    }
                    i++;
            })

        });
    </script>
<script type="text/javascript">
        function checkallPhotos(frm,chkall)
        {
            var j=0;
            comfList123 = document.forms[frm].elements['checkList[]'];
            checkAll = (chkall.checked)?true:false; // what to do? Check all or uncheck all.

            // Is it an array
            if (comfList123.length) {
                if (checkAll) {
                    for (j = 0; j < comfList123.length; j++) {
                        comfList123[j].checked = true;
                    }
                }
                else {
                    for (j = 0; j < comfList123.length; j++) {
                        comfList123[j].checked = false;
                    }
                }
            }
            else {
                /* This will take care of the situation when your
    checkbox/dropdown list (checkList[] element here) is dependent on
    a condition and only a single check box came in a list.
                 */
                if (checkAll) {
                    comfList123.checked = true;
                }
                else {
                    comfList123.checked = false;
                }
            }

            return;
        }


    </script>

<script type="text/javascript">
// starting the script on page load
dragdr(document).ready(function(){

	imagePreview();
});

 dragdr(document).ready(function(dragdr) {
      dragdr('a[rel*=facebox]').facebox()
    })
 </script>


<style type="text/css">
#swfupload-control p {
	margin: 10px 5px;
	font-size: 11px;
	width: 75%;
}

#log {
	margin: 0;
	padding: 0;
	width: 75%;
}

#log li {
	list-style-position: inside;
	margin: 2px;
	border: 1px solid #ccc;
	padding: 10px;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
	color: #333;
	background: #fff;
	position: relative;
	word-wrap:break-word;
}

#log li .progressbar {
	border: 1px solid #333;
	height: 5px;
	background: #fff;
}

#log li .progress {
	background: #999;
	width: 0%;
	height: 5px;
}

#log li p {
	margin: 0;
	line-height: 18px;
}

#log li.success {
	border: 1px solid #339933;
	background: #ccf9b9;
}

#log li span.cancel {
	position: absolute;
	top: 5px;
	right: 5px;
	width: 20px;
	height: 20px;
	background: url('../cancel.png') no-repeat;
	cursor: pointer;
}
#mydiv{background:#fff;width:500px;height:100px;}
</style>
</head>
<body>
<?php
if(!empty($_REQUEST['action'])){
    
    $action = $_REQUEST['action'];
}
if ($action == 'viewPhotos')
{
	$albid = $_REQUEST['albid'];
	if ($_REQUEST['macPhotoid'] != '') {
		$macPhotoid = intval($_REQUEST['macPhotoid']);
		$photoImg = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d' ",$macPhotoid));
		$delete = $wpdb->query("DELETE FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='$macPhotoid'");

	
		$uploadDir = wp_upload_dir();
		$path = $uploadDir['basedir'].'/mac-dock-gallery';
		unlink($path .'/'.$photoImg);
		$extense = explode('.', $photoImg);
		unlink($path . $macPhotoid . '.' . $extense[1]);

}


		if (isset($_REQUEST['action_photos']) == 'Delete') {
			for ($k = 0; $k < count($_POST['checkList']); $k++) {
				$macPhoto_id = $_POST['checkList'][$k];
                                $macPhoto_id = intval($macPhoto_id);
				$photoImg = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d' ",$macPhoto_id));
				$delete = $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d'",$macPhoto_id));
				
				$uploadDir = wp_upload_dir();
				$path = $uploadDir['basedir'].'/mac-dock-gallery';
				$tumpImg = $path .'/'.$photoImg;
				if(file_exists($tumpImg))
				{
					unlink($tumpImg);
				}	
				$extense = explode('.', $photoImg);
				     $bigImageIs = $macPhoto_id.'.'.$extense[1];
				
				$oriImgDel = $path .'/'.$bigImageIs;
				if(file_exists($oriImgDel))
				{
					unlink($oriImgDel);
				}	
			}
			$msg = 'Photos Deleted Successfully';
		}


	function mpg_listPagesNoTitle($args) { //Pagination
		if ($args) {
			$args .= '&echo=0';
		} else {
			$args = 'echo=0';
		}
		$pages = wp_list_pages($args);
		echo $pages;
	}

	function mpg_findStart($limit) { //Pagination
		if (!(isset($_REQUEST['pages'])) || ($_REQUEST['pages'] == "1")) {
			$start = 0;
			$_GET['pages'] = 1;
		} else {
			$start = ($_GET['pages'] - 1) * $limit;
		}
		return $start;
	}

	/*
	 * int findPages (int count, int limit)
	 * Returns the number of pages needed based on a count and a limit
	 */

	function mpg_findPages($count, $limit) { //Pagination
		$pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1;
		if ($pages == 1) {
			$pages = '';
		}
		return $pages;
	}

	/*
	 * string pageList (int curpage, int pages)
	 * Returns a list of pages in the format of "Ã‚Â« < [pages] > Ã‚Â»"
	 * */

	function mpg_pageList($curpage, $pages, $albid) {
		//Pagination
		$page_list = "";
		if ($search != '') {

			$self = '?page=' . macPhotos . '&action=viewPhotos' . '&albid=' . $albid;
		} else {
			$self = '?page=' . macPhotos . '&action=viewPhotos' . '&albid=' . $albid;
		}

		/* Print the first and previous page links if necessary */
		if (($curpage != 1) && ($curpage)) {
			$page_list .= "  <a href=\"" . $self . "&pages=1\" title=\"First Page\"><<</a> ";
		}

		if (($curpage - 1) > 0) {
			$page_list .= "<a href=\"" . $self . "&pages=" . ($curpage - 1) . "\" title=\"Previous Page\"><</a> ";
		}

		/* Print the numeric page list; make the current page unlinked and bold */
		for ($i = 1; $i <= $pages; $i++) {
			if ($i == $curpage) {
				$page_list .= "<b>" . $i . "</b>";
			} else {
				$page_list .= "<a href=\"" . $self . "&pages=" . $i . "\" title=\"Page " . $i . "\">" . $i . "</a>";
			}
			$page_list .= " ";
		}

		/* Print the Next and Last page links if necessary */
		if (($curpage + 1) <= $pages) {
			$page_list .= "<a href=\"" . $self . "&pages=" . ($curpage + 1) . "\" title=\"Next Page\">></a> ";
		}

		if (($curpage != $pages) && ($pages != 0)) {
			$page_list .= "<a href=\"" . $self . "&pages=" . $pages . "\" title=\"Last Page\">>></a> ";
		}
		$page_list .= "</td>\n";

		return $page_list;
	}

	/*
	 * string nextPrev (int curpage, int pages)
	 * Returns "Previous | Next" string for individual pagination (it's a word!)
	 */

	function mpg_nextPrev($curpage, $pages) { //Pagination
		$next_prev = "";

		if (($curpage - 1) <= 0) {
			$next_prev .= "Previous";
		} else {
			$next_prev .= "<a href=\"" . $_SERVER['PHP_SELF'] . "&pages=" . ($curpage - 1) . "\">Previous</a>";
		}

		$next_prev .= " | ";

		if (($curpage + 1) > $pages) {
			$next_prev .= "Next";
		} else {
			$next_prev .= "<a href=\"" . $_SERVER['PHP_SELF'] . "&pages=" . ($curpage + 1) . "\">Next</a>";
		}
		return $next_prev;
	}
	?>
	<link rel='stylesheet' href='<?php echo plugins_url('css/style.css', __FILE__)?>' type='text/css' />
		<div class="wrap nosubsub"
		style="width: 98%; float: left; margin-right: 15px; align: center">
		<div id="icon-upload" class="icon32">
			<br />
		</div>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=macAlbum" class="nav-tab">Albums</a> <a
				href="?page=macPhotos&action=macPhotos"
				class="nav-tab  nav-tab-active">View Images</a> 
				<a href="?page=macSettings" class="nav-tab">Settings</a>
				 <a href="?page=ImportAlbums" class="nav-tab">Import Albums</a>
				 
		</h2>
		
		<div
			style="background-color: #ECECEC; padding: 10px; margin: 10px 0px 10px 0px; border: #ccc 1px solid">
			<strong> Note : </strong>Mac Photo Gallery can be easily inserted to
			the Post / Page by adding the following code :<br> <br> (i)
			[macGallery] - This will show the entire gallery [Only for Page]<br> (ii) [macGallery
			albid=1 row=3 cols=3] - This will show the particular album with the
			album id 1
		</div>
		<?php if ($msg) {
 ?>
            <div  class="updated below-h2">
                <p><?php echo $msg; ?></p>
            </div>
<?php } ?>
		<div class="clear"></div>
		<?php
		if($_REQUEST['albid'] != '' && $_REQUEST['albid']!='0')
		{
			$macAlbum = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id='%d'",$albid));
			?>
		<h4>
			<div class="lfloat">Album Name :</div>
			<div style="color: #448abd;">
			<?php echo $macAlbum->macAlbum_name; ?>
			</div>
		</h4>

		<?php
			 $uploadDir = wp_upload_dir();
			 $file_image =  $uploadDir['basedir'] . '/mac-dock-gallery/' .$macAlbum->macAlbum_image;
			 $path = $uploadDir['baseurl'].'/mac-dock-gallery';
			 $site_url = get_bloginfo('url');
		 if(file_exists($file_image)&& ($macAlbum->macAlbum_image != '')){

		 ?>
   		<img
			src="<?php echo $path; ?>/<?php echo $macAlbum->macAlbum_image; ?>"
			width="100" height="100" />

			<?php
		} else if(!file_exists($file_image)){?>
			<img
			src="<?php echo plugins_url('uploads/star.png', __FILE__)?>" width="100" height="100"
			/>
<?php
}
		else{
			?>
		<img
			src="<?php plugins_url('images/default_star.gif', __FILE__);?>"
			width="50px" height="50px" />
			<?php } 
			?>

		<div style="float: right; width: 80%">
			<form name="macPhotos" id="macPhotos" method="POST" onSubmit="return deleteImages();">
			<div id="showGalleryNames" style="float: left" >
		
			Select Album <select  onchange="displaySelectedAlbum(this.value,'<?php echo $pageURL[0] ; ?>')" > 
					<?php
					$picaAlbumList = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "macalbum" );
					$numOfTimes =  count($picaAlbumList);
						
						foreach($picaAlbumList as $key => $value ){
							 if( $value->macAlbum_id == $albid)
							 {
							 	 $isselect =  "selected='selected'";
							 	 $albName = $value->macAlbum_name;
							 } 
							else {$isselect = ''; }
				echo "<option  $isselect value=".$value->macAlbum_id."  >".$value->macAlbum_name."</option>" ;			
						}
					
						
				
						
					?>		 
			</select>
		</div>
			
			
			

				<select name="action_photos"  id="action_photos" style="float: left">
					<option name="bulk" value="bulk" selected="selected">
					<?php _e('Bulk Actions'); ?>
					</option>
					<option name="Delete" value="Delete">
					<?php _e('Delete'); ?>
					</option>
				</select>
				<ul class="alignright actions">
					<li><a
						href="<?php echo $admin_url ?>admin.php?page=macPhotos&albid=<?php echo $macAlbum->macAlbum_id; ?>"
						class="gallery_btn"> Add Images</a></li>

				</ul>

				<input type="submit" value="<?php esc_attr_e('Apply'); ?>"
					name="doaction_photos" id="doaction_photos"
					class="button-secondary action" />
<!--				<div id="info">Waiting for update</div>-->
				<script type="text/javascript">
				function deleteImages(){
					if(document.getElementById('action_photos').selectedIndex == 1)
					{
						var answer = confirm('Are you sure to delete photo/s ?');
						if (answer){
							return true;
						}
						else{
							return false;
						}
					}
					else if(document.getElementById('action_photos').selectedIndex == 0)
					{
					return false;
					}

				}
				</script>
				<table cellspacing="0" cellpadding="0" border="1"
					class="mac_gallery">
					<thead>
						<tr>
							<th style="width: 5%">Sort</th>
							<th class="maccheckPhotos_all"
								style="width: 5%; text-align: center;"><input type="checkbox"
								name="maccheckPhotos" id="maccheckPhotos" class="maccheckPhotos"
								onclick="checkallPhotos('macPhotos',this);" /></th>
							<th class="macname" style='max-width: 30%; text-align: left'>Name</th>
							<th class="macimage" style='width: 10%; text-align: left'>Image</th>
							<th class="macdesc" style='width: 30%; text-align: left'>Description</th>
							<th class="macon" style='width: 10%'>Album Cover</th>
							<th class="macon" style='width: 10%; text-align: center'>Sorting</th>
							<th class="macon" style='width: 10%; text-align: center'>Status</th>
						</tr>
					</thead>
					<tbody id="mac-test-list" class="list:post">
					<?php
					$site_url = get_bloginfo('url');
                                        $upload_path = wp_upload_dir();
                                        $upload_path = $upload_path['baseurl'];
					/* Pagination */

					$limit = 20;
					$sql = mysql_query($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' ORDER BY macPhoto_sorting ASC",$albid));
                                        $start = mpg_findStart($limit); ?>
                                            <input type="hidden"  value ="<?php echo ($start)<0?0:$start;?>" id="pagestart">
                                         <?php
					if($_REQUEST['pages']== 'viewAll')
					{
						$w= '';
					}
					else
					{

						$w = "LIMIT " . $start . "," . $limit;
					}

					$count = mysql_num_rows($sql);
					/* Find the number of pages based on $count and $limit */
					$pages = mpg_findPages($count, $limit);
					/* Now we use the LIMIT clause to grab a range of rows */
					$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='$albid' ORDER BY macPhoto_sorting DESC $w",$albid));
					$album = '';

					if(count($result) == '0')
					{
						echo '<tr><td colspan="8" style="text-align: center;">No photos</td></tr>';
					}
					else
					{
						foreach ($result as $results)
						{
							$album .= "<tr class='$j' id='listItem_$results->macPhoto_id'>
                               <td class='mac_sort_arrow'><img src=".plugins_url('images/arrow.png', __FILE__)." alt='move' width='16' height='16' class='handle' /></td>
                               <td class='checkPhotos_all' style='text-align: center'><input type=hidden id=macPhoto_id name=macPhoto_id value='$results->macPhoto_id' >
                               <input type='checkbox' class='checkSing' name='checkList[]' class='others' value='$results->macPhoto_id' ></td>

                               <td class='macName'style='text-align: left' ><div id='macPhotos_$results->macPhoto_id' onclick=photosNameform($results->macPhoto_id); style='cursor:pointer'>" . $results->macPhoto_name . "</div>
                               <span id='showPhotosedit_$results->macPhoto_id'></span>
                               <div class='delView'></div></td>";

							if ($results->macPhoto_image == '')
							{
								$album .="<td  style='width:10%;align=center'>
                    <a id=".plugins_url('images/default_star.gif', __FILE__)." class='preview' alt='Edit'  href='javascript:void(0)'>
                     <img src=".plugins_url('images/default_star.gif', __FILE__)." width='40' height='20' /></a></td>";
							} else
							{

								$album .="<td  style='width:10%;align=center'>
                    <a id='$upload_path/mac-dock-gallery/$results->macPhoto_image' class='preview' alt='Edit' href='javascript:void(0)'>
                    <img src='$upload_path/mac-dock-gallery/$results->macPhoto_image' width='40' height='20' /></a></td>";
							}

							$album .="<td style='width:30%'><div id='display_txt_" . $results->macPhoto_id . "'>" . $results->macPhoto_desc . "</div>
                             <a id='displayText_" . $results->macPhoto_id . "' href='javascript:phototoggle($results->macPhoto_id);'>Edit</a>
                             <div id='toggleText" . $results->macPhoto_id . "' style='display: none'>
                             <textarea name='macPhoto_desc' id='macPhoto_desc_" . $results->macPhoto_id . "' rows='6' cols='30' >$results->macPhoto_desc</textarea><br />
                             <input type='button' onclick='javascript:macdesc_updt($results->macPhoto_id);' value='Update'>
                             </div></td>";
							if ($results->macAlbum_cover == 'ON')
							{
								$album .= "<td align='center'><div id='albumCover_bind_$results->macPhoto_id' style='text-align:center'>
                            <img src=".plugins_url('images/tick.png', __FILE__)." width='16' height='16' style='cursor:pointer;text-align:center' onclick=macAlbcover_status('OFF',$albid,$results->macPhoto_id) /></div></td>";
							} else
							{
								$album .= "<td align='center'><div id='albumCove_bind_$results->macPhoto_id' style='text-align:center'>
                            <img src=".plugins_url('images/publish_x.png', __FILE__)." width='16' height='16' style='cursor:pointer;text-align:center' onclick=macAlbcover_status('ON',$albid,$results->macPhoto_id) /></div></td>";
							}
							$album .="<td style='text-align:center'>$results->macPhoto_sorting</td>";
							if ($results->macPhoto_status == 'ON')
							{
								$album .= "<td><div id='photoStatus_bind_$results->macPhoto_id' style='text-align:center'>
                            <img src=".plugins_url('images/tick.png', __FILE__)." width='16' height='16' style='cursor:pointer' onclick=macPhoto_status('OFF',$results->macPhoto_id) /></div></td>";
							} else
							{
								$album .= "<td><div id='photoStatus_bind_$results->macPhoto_id' style='text-align:center'>
                            <img src=".plugins_url('images/publish_x.png', __FILE__)." width='16' height='16' style='cursor:pointer' onclick=macPhoto_status('ON',$results->macPhoto_id) /></div></td></tr>";
							}
						} // for loop
					}  // else for record exist
					$pagelist = mpg_pageList($_GET['pages'], $pages, $_GET['albid']);

					echo $album;
					?>
					</tbody>
				</table>
			</form>
			<div align="right">
			<?php echo $pagelist; ?>
			<?php
			if($count > $limit )
			{ ?>
				<a
					href="<?php echo $admin_url?>admin.php?page=macPhotos&action=viewPhotos&albid=<?php echo $albid;?>&pages=viewAll">See
					All</a>
			</div>
			<?php
			}
			?>

			<?php   ?>
			<?php }
			else
			{
				?>
			<div style="padding-top: 20px">No albums is selected. Please Go to
				back and select the respective album to view images</div>
				<?php
			}
			?>

		</div>

	</div>
	<?php
} else {
	?>
	<div class="wrap nosubsub clearfix">
		<div id="icon-upload" class="icon32">
			<br />
		</div>
		<h2 class="nav-tab-wrapper">
			<a href="?page=macAlbum" class="nav-tab">Albums</a> <a
				href="?page=macPhotos&action=macPhotos"
				class="nav-tab  nav-tab-active">Upload Images</a> <a
				href="?page=macSettings" class="nav-tab">Settings</a>
				  <a href="?page=ImportAlbums" class="nav-tab">Import Albums</a>
		</h2>
		<div
			style="background-color: #ECECEC; padding: 10px; margin: 10px 0px 30px 0px; border: #ccc 1px solid">
			<strong> Note : </strong>Mac Photo Gallery can be easily inserted to
			the Post / Page by adding the following code :<br> <br> (i)
			[macGallery] - This will show the entire gallery<br> (ii) [macGallery
			albid=1 row=3 cols=3] - This will show the particular album images with the
			album id 1
		</div>
		<div class="clear">
			<div style="width: 30%; float: left; margin-right: 15px;">
				<h3>Select The Album To Upload Photos</h3>
				<div class="clear"></div>
				<form name="frm1">
					<div class="macLeft">
						<select name="macAlbum_name" id="macAlbum_name"
							onchange="GetSelectedItem()">
							<option value="0">-Select Album Here-</option>

							<?php
							if (($_REQUEST['albid']) != '') {
								$albid = $_REQUEST['albid'];
							}
							$albRst = $wpdb->get_results("SELECT * FROM  " . $wpdb->prefix . "macalbum");
							foreach ($albRst as $albRsts) {
								if ($albid == $albRsts->macAlbum_id) {
									$selected = 'selected = "selected"';
								} else {
									$selected = '';
								}
								?>
							<option value="<?php echo $albRsts->macAlbum_id; ?>"
							<?php echo $selected ?>>
								<?php echo $albRsts->macAlbum_name; ?>
							</option>
							<?php
							}
							?>
						</select>
					</div>
					<div class="macLeft" style="padding-left: 5px"> <a href="<?php echo $admin_url.'admin.php?page=macAlbum'?>">Create New Album</a></div>
				</form>
				<div id="swfupload-control" class="left_align">
					<p>Upload multiple image files(jpg, jpeg, png, gif)</p>
					choose files to upload <input type="button" id="button" />
					<p id="queuestatus"></p>
					<ol id="log"></ol>
				</div>
				<?php if(!empty($_REQUEST['albid']))
				if($_REQUEST['albid'] != '0' && $_REQUEST['albid'] != '' && $_REQUEST['albid'] != '-1' )
				{
					?>
					<script type="text/javascript">
						document.getElementById('swfupload-control').style.visibility='visible';
					</script>

				<?php }
				else
				{
				?>
				<script type="text/javascript">
						document.getElementById('swfupload-control').style.visibility='hidden';
					</script>
				<?php } ?>

			</div>

			<div name="bind_macPhotos" id="bind_macPhotos" class="bind_macPhotos"></div>
		</div>

		<input type="hidden" name="bind_value" id="bind_value" value="0" />
	</div>
	<?php
}
}
?>
<input type="hidden" name="token" id="token" value="<?php echo $dbtoken;?>"/>