<?php
class macImportAlbums {		
			private $msg;
			public  $pluginDirPaht;
			public  $wpdb;
			private $albumTable;
			private $photoTable;
			private $importTable;   
			function __construct(){					
				global $wpdb;
				$this->wpdb = $wpdb; 
				$this->albumTable = trim($this->wpdb->prefix.'macalbum');
				$this->photoTable = trim($this->wpdb->prefix.'macphotos');
				$this->importTable = trim($this->wpdb->prefix.'macimportalbums');
                                
				
				 if(isset($_REQUEST['macImportDateStore']) || isset($_REQUEST['macFacebookDateStore']) )
							{
										$this->mpg_storFormSubmitValues(); //update form values in DB
							}
					
				 else if(isset($_REQUEST['macGetPicasaPhotos']) || isset($_REQUEST['formSubmitOk']) == 'ok' )  //getting picasa site photos
				 {

				 	                $userNames  =  $_REQUEST['macPicasaUserNames'];
				 						 			
								    $this->mpg_macpicasaPhotosImport($userNames); //it import images from picasa site
				 					update_option('macPicasaUserNames',trim($_REQUEST['macPicasaUserNames']));
				 					$timeDate = $this->mpg_getCurrentTimeDate(); //get imported date and time value
				 					update_option('picasaalubmstimedate',$timeDate );
				 					 
				 }			
				 else if(isset($_REQUEST['macGetFlickrPhotos'])  || isset($_REQUEST['flickrformSubmitOk']) == 'ok')  //get Flickr site photos
				 {  
			  					    $userApiId   =  $_REQUEST['macFlickrApiId'];
									$userUserId   =  $_REQUEST['macFlickrUserId'];
									$this->msg = $this->mpg_macFlickrAlbumsAndPhotosGet($userApiId ,$userUserId ); //import flickr albums and photos
									update_option('macFlickrApiId',trim($_REQUEST['macFlickrApiId']));
									update_option('macFlickrUserId',trim($_REQUEST['macFlickrUserId']));
									$timeDate = $this->mpg_getCurrentTimeDate();   //get imported date and time value
				 					update_option('flickralubmstimedate',$timeDate );
				 }	
	 
				$this->mpg_getMenuList(); //show menu tabls of macdocgallery
				$this->showTable(); //show table of import tab
				
	}//construct()end hear
    private function  mpg_getImageWidthFromSettingsTB(){ //get imagewidth form macsettings table
    	
    	ini_set('max_execution_time',120); //set value in php.ini file
    	$macSetting    = $this->wpdb->get_var("SELECT mouseWid FROM ". $this->wpdb->prefix."macsettings");
    	$imgSiz        = $macSetting+50;
    	
    	return $imgSiz;
    }
    private  function mpg_getLastImgIdFromDB(){  //get Last Id from macPhototable
    	
								    	$sql = "SELECT macPhoto_id FROM $this->photoTable ORDER BY 1 DESC ";
								    	$lastId = $this->wpdb->get_var($sql);
								    	return $lastId;
    }
	private function mpg_getCurrentTimeDate(){
										$timeZone = get_option('currentTimeZone');
										date_default_timezone_set($timeZone);
					 				    $dateTime =  date('l jS \of F Y h:i:s A');
										return $dateTime;
	}
	private function  mpg_getPluginPath(){
										
										$siteUrl = get_bloginfo('url');	
										return $this->pluginDirPaht = plugin_dir_url(__FILE__); ;
	  
	 }
	private function mpg_getMenuList(){
		
?>
			<link rel="stylesheet" href="<?php echo plugins_url('css/style.css', __FILE__);?>">
			
<?php 
			echo '<div class="wrap">
		    <div id="icon-upload" class="icon32"><br /></div>
		        <h2 class="nav-tab-wrapper">
		        <a href="?page=macAlbum" class="nav-tab">Albums</a>
		        <a href="?page=macPhotos&albid=0" class="nav-tab">Upload Images</a>
		        <a href="?page=macSettings" class="nav-tab ">Settings</a>
		        <a href="?page=ImportAlbums" class="nav-tab nav-tab-active">Import Albums</a>
		        </h2>
	          	<div style="background-color: #ECECEC;padding: 10px;margin-top:10px;border: #ccc 1px solid">
	          		<strong> Note : </strong>Mac Photo Gallery can be easily inserted to the Post / Page by adding the following code :<br><br>
	                 (i)  [macGallery] - This will show the entire gallery [Only for Page]<br>
	                 (ii) [macGallery albid=1 row=3 cols=3] - This will show the particular album images with the album id 1
	         	</div>
			</div>';
	} //mpg_getMenuList() end hear
	private function mpg_storFormSubmitValues(){
		
		
		update_option('macFacebookApi',trim($_REQUEST['macFacebookApi']));
		update_option('macFacebookSecKey',trim($_REQUEST['macFacebookSecKey']));
		update_option('showmacFacebookAlbums',trim($_REQUEST['showmacFacebookAlbums']));
		update_option('macFacebookAlbumsStatus',trim($_REQUEST['macFacebookAlbumsStatus']));
		update_option('showmacFlickrAlbumsStatus',trim($_REQUEST['showmacFlickrAlbumsStatus']));
		update_option('showmacPicasaAlbumsStatus',trim($_REQUEST['showmacPicasaAlbumsStatus']));
		
		update_option('facebookSecKey',trim($_REQUEST['facebookSecKey']));
    	update_option('macFlickrApiId',trim($_REQUEST['macFlickrApiId']));   
    	update_option('macFlickrUserId',trim($_REQUEST['macFlickrUserId']));
    	update_option('macPicasaUserNames',trim($_REQUEST['macPicasaUserNames']));
    	update_option('showmacPicasaAlbums',trim($_REQUEST['showmacPicasaAlbums']));
    	update_option('showmacFlickrAlbums',trim($_REQUEST['showmacFlickrAlbums']));
    	update_option('showmacFacebookAlbums',trim($_REQUEST['showmacFacebookAlbums']));
        update_option('currentTimeZone', trim($_REQUEST['currentTimeZone']));  
		
		$this->msg = 'updated successfully'; 
	}//function end
	
	private  function mpg_macpicasaPhotosImport($userName){   //GET ALBUMS AND PHOTOS FORM GOOGLE PICASA SITE
		
		 $imageHW = $this->mpg_getImageWidthFromSettingsTB();//set max-exe-time = 120 and get imge size
				
		$index           = 0;
		$users           =split(';',$userName);
		$albums          = array('All');
		$photos          = array();
		$flag            = 0;
		$albphotos       = array();
		$picasaUserIds   = array();
		foreach($users as $user) {
			// get albums
			if(strlen($user) <=  0)
			{
				continue;
			}
			$rss = fetch_feed("http://picasaweb.google.com/data/feed/base/user/{$user}?alt=rss&kind=album&hl=en_US&access=public");
				
			if (!method_exists($rss,"get_items")) {
				$options['error'] = 'Invalid picasa username';
				update_option('isGetPicasaAlbums',0);
				$this->msg = 'Invalid picasa username '.$user;
				
				return 0;
			}

 			$items = $rss->get_items();
  			
			if (is_array($items)) {
				$k = 1;
				update_option('isGetPicasaAlbums',1);	
				
				foreach($items as $k => $item) {
					
							$picasaUserIds[] = $user;
							$guid = $item->get_id();
							$album = $item->get_title();
							preg_match('/.*src="(.*?)".*/',$item->get_description(),$albsub);
						//FOR STORING ALBUM PHOTOS		 
					  	   $totAlb[$flag][] =	array("photo" => $albsub[1],"link" => $item->get_link(),"title" => $item->get_title(),"description" =>$item->get_description(),'date' => $item->get_date('j-n-Y'),"desc" => "", "status" =>'ON' , 'index' => $index );
							if($album == 'Profile Photos')
							{
								$profilePhotoIs[] = array('photo' => $albsub[1]);
							}
							$albums[] = $album; // add album to the list
					   
							// get the photos RSS feed
							$rss2 = fetch_feed(str_replace("entry","feed",$guid)."&kind=photo");
							if (!method_exists($rss2,"get_items")) // download error
							continue;
							//FOR STORING PHOTOS IN ALL ALB 
							
							$items2 = $rss2->get_items();
							foreach($items2 as $item2) { 
								preg_match('/.*src="(.*?)".*/',$item2->get_description(),$sub);
						//FOR STORING PHOTOS IN ALBUM		 
								$totPhos[$flag][$album][]  = array("photo" => $sub[1],"link" => $item2->get_link(),"title" => $item2->get_title(),"description" =>$item2->get_description(),"date" =>$item2->get_date('F j, Y'));					
							}
								$k++; $index++;
						
					} //for end hear
								
			}//if condition end
			else{
				update_option('isGetPicasaAlbums',0);
				$this->msg = '(!) Error while getting Albums form picasa';
			}
			$flag++;		
		}//for user loop end hear loop end her
		
				update_option('picasaAlbumProfilePhotos',$profilePhotoIs);
				
				if($index){
				 $this->msg = ' Success got '.$k.' Albums from Picasa';
				
				}
		  		else 
		  		$this->msg = ' Failed to get Albums from Picasa';
		  			
  		      $this->mpg_storingPicasaAlbumsInDB($picasaUserIds , $totAlb , $totPhos ,$albums , $imageHW);
  		
	}//function end for get picasa albums
    private	function mpg_storingPicasaAlbumsInDB($users , $albums , $photos , $albumsNames , $imageHW){  //used to store all picasa Data In DB Tables
		
		 $accountIDs   = $users;    //store users ids
		 $picasaAlbums = $albums; //store users alubms list
		 $picasaPhotos = $photos; //store photos of all albums
		 $accountIDs   = array_unique($accountIDs); //delete duplicates
		
		 $photoTableLastId = $this->mpg_getLastImgIdFromDB(); //for firsttime insert image in photo talble
		 $lastid = $photoTableLastId+1;  
		 $imgUploadPath = $this->getUploadDirPath();
		 
    	if(is_array($accountIDs))
    	{
    		
    		$flag = 0; 
    		$incr = 1;
    		foreach($accountIDs as $k =>  $user) //ACCOUNT IDS ARRAY 0 , 6 ,8 ...
    		{
					$this->wpdb->insert($this->importTable , array('accountids' => $user , 'importsite' => 'picasa' )  ); //insert into importtable
		    		$lastUpdatedRow =  $this->wpdb->insert_id; //last row id
		    		$photoIndx = 0;
		    		
		    		foreach($picasaAlbums[$flag] as $m => $album)  //ALBUM TALBE 
		    		{
		    			$albumPhotoName      =  $album['title'];
		    			$albumsRow           = array('macAlbum_name' => $album['title'] , 'macAlbum_description' =>$album['desc'] , 'macAlbum_image' => $album['photo'] , "macAlbum_status" =>'ON' , 'importid' => $lastUpdatedRow); 
		    			$this->wpdb->insert($this->albumTable  , $albumsRow ); //insetr data into alubmtable
		    			$lastUpdatedAlbRow   =  $this->wpdb->insert_id; //get last alubm row id
		    			
		    			$albNameArray = $albumsNames[$incr];
		    				    			
		    			
		    			foreach($photos[$flag][$albNameArray] as $key => $newPhotos){
		    			
		    				       $photo =  $newPhotos['photo'];  
		    				         
		    				        $converStatus = 'OFF';      	//album cover status						    						
					    			$randVal  = rand(0000,9999);    //get random values for photo name 
					    			$imgThumb = $lastid.'_thumb_'.$randVal.'.jpg'; //name of the thumb size photo
					    			if(!strcmp($photo , $albumPhotoName ) )
		    				        {
		    				        	$this->wpdb->insert($this->albumTable  , array('macAlbum_image' => $imgThumb )  ); //album img name
		    				        	$converStatus = 'ON'; //set album cover status on
		    				        }   
					     $getBigSizePhoto = explode('/',$photo);
						 $size			  =  count($getBigSizePhoto);
						 $getBigSizePhoto[$size-2] = 's700';
						 $getBigSizePhoto = implode('/', $getBigSizePhoto); //get Big Img for picasa	
					    			$imgBig   = $lastid.'.jpg'; //Big image name
					    	
					    			$imgUploadPathIs = $imgUploadPath.$imgThumb;  //thumb photo path to store
					    		
						$photoTalbeData = array('macAlbum_id' =>$lastUpdatedAlbRow , 'macAlbum_cover' => $converStatus , 'macPhoto_name' => $newPhotos['title'] , 'macPhoto_image' => $imgThumb , 'macPhoto_status' => 'ON' , 'macPhoto_sorting' => $lastid    );
						$this->wpdb->insert($this->photoTable  , $photoTalbeData );		    						    			
							    	$lastid = $this->wpdb->insert_id; //get last inserted row id
					    			$lastid++;
					    	
					    		if(strlen($photo) && strlen($imgUploadPathIs) && strlen($imageHW) )
					    		{			
					    			$this->mpg_makeThumbImage($photo ,$imgUploadPathIs,$imageHW); //create thumb size photo and store in mac-dock-gallery DIR
					    			file_put_contents($imgUploadPath.$imgBig , file_get_contents($getBigSizePhoto));
					   			}
					   					    				
		    			} //inner foreach end hear	
		    		$photoIndx++;	$incr++;
		         }//forloop for macalbum talbe
		     		$flag++;//for albumtalbe 
		    }//forloop end for macimprotalbums talble
    	  		
     	}//if end hear
   	}//function storingPicasaAlbumsInDB END
   	
   
	private function mpg_macFlickrAlbumsAndPhotosGet($userApiId ,$userUserId ){  //GET ALBUMS AND PHOTOS FORM FLICKR SITE
		
		$imageHW = $this->mpg_getImageWidthFromSettingsTB();	
		$display='latest';
		$flickrAlbList = array(  
		    'api_key'   => $userApiId,  
		    'method'    => 'flickr.photosets.getList',  
			'display' => $display ,  
			
		    'user_id'   => $userUserId,  
		    'extras'    => 'original_format',  
		    'format'    => 'php_serial'  
		);
		$encoded_params = array();  
		foreach ($flickrAlbList as $k => $v){ $encoded_params[] = urlencode($k).'='.urlencode($v); }
		
		$ch = curl_init();  
		$timeout = 0; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, 'http://api.flickr.com/services/rest/?'.implode('&', $encoded_params));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);

		$rsp_obj = unserialize($file_contents);
		$s = 0; //for index 
		
		if ($rsp_obj['stat'] == 'ok') {
			    update_option('isGetFlickrAlbums',1);
				$photos = $rsp_obj["photoset"]["photo"];   //for photos in albm
				$photos = $rsp_obj["photosets"]["photoset"];  //for albums
				update_option('macflickrNumOfAlbs',$rsp_obj["photosets"]['total'] );
				 
				if($flickr_gallery_display == 1)
				{
					shuffle($photos);
				}
				if($flickr_gallery_display == 2 || $flickr_gallery_display == 1)
				{
					array_reverse($photos);
				}
			 
				$src1 = array();
			
				$i = 1;
				global $wp_rewrite;
				global $wpdb;
				
		$imgUploadPath = $this->getUploadDirPath();
		$photoTableLastId = $this->mpg_getLastImgIdFromDB(); //for firsttime insert image in photo talble
		$lastid = $photoTableLastId+1;

				
			$flickrUserDetails = $userApiId.','.$userUserId; //store userid and apiid	
			$this->wpdb->insert($this->importTable , array('accountids' => $flickrUserDetails , 'importsite' => 'flickr' )  ); //insert into importtable
		 
			$lastUpdatedRow =  $this->wpdb->insert_id;	//for importTable id
				
			//echo "<pre>";print_r($photos) ; echo "</pre>";	
				foreach($photos as $photo)   //storing flickr albums in albums table 
				{
					
					$farm              = $photo['farm'];
					$server            = $photo['server'];
					$fphotoId          = $photo['id'];  //for photos in alb
					$fAphotoId  	   = $photo['primary'];	//for alb pho
					$secret1           = $photo['secret'];
					$fphotoTitle       = $photo['title']['_content'];
					$description       = $photo['description']['_content'];
					$albPhoSrc = "http://farm$farm.static.flickr.com/$server/$fAphotoId"._."$secret1"._."m.jpg";
					$flickrAlbDetailList[]  = array('numOfphos' => $photo['photos'] , 'title' => $$fphotoTitle , 'date_created' => $photo['date_create'] , 'albPhoto' => $albPhoSrc , 'photoset_id' => $fphotoId , 'status' => 'ON' ,'desc' => '' , 'index' =>$s); //use it at picafrontend.php for showing alb list

						$albumsRow = array('macAlbum_name' => $fphotoTitle , 'macAlbum_description' =>$description , 'macAlbum_image' => $albPhoSrc , "macAlbum_status" =>'ON' , 'importid' => $lastUpdatedRow); 
		    			$this->wpdb->insert($this->albumTable  , $albumsRow  );
		    			$lastUpdatedAlbRow =  $this->wpdb->insert_id; //for storing photos in album at macphoto talbe
		    		
		    			$allPhotosInSingleAlbum = $this->mpg_storingFlickrAlbumsPhotsInDB($fphotoId , $userApiId );
		    			$photoTableLastId = $this->mpg_getLastImgIdFromDB(); //for firsttime insert image in photo talble
						$lastid = $photoTableLastId+1;	
		    			foreach($allPhotosInSingleAlbum as $k => $SinglePhoto)	
		    			{
		    						$name = $SinglePhoto['title'];
		    						$photoSrc = $SinglePhoto['src'];
		    						$photoBigSrc = $SinglePhoto['bigSrc'];
		    						$secret =  $SinglePhoto['secret'];
		    						$cover = '';
		    						
		    						$randVal  = rand(0000,9999);
					    			$imgThumb = $lastid.'_thumb_'.$randVal.'.jpg';
			    	    			$imgBig   = $lastid.'.jpg';
					    			$imgUploadPathIs = $imgUploadPath.$imgThumb;
		    			if(!strcmp($secret,$secret1))
		    				{
		    					$cover = 'ON';
		    					$this->wpdb->update($this->albumTable , array('macAlbum_image' => $imgThumb) , array('macAlbum_id' => $lastUpdatedAlbRow )  );
		    				}
		    			$photoTalbeData = array('macAlbum_id' =>$lastUpdatedAlbRow , 'macAlbum_cover' => $cover , 'macPhoto_name' => $name , 'macPhoto_image' => $imgThumb , 'macPhoto_status' => 'ON' , 'macPhoto_sorting' => $lastid    );
						$this->wpdb->insert($this->photoTable  , $photoTalbeData );		    						    			
					    $lastid = $this->wpdb->insert_id;
				  	    $lastid++;
				  	    	   
					    		if(strlen($photoSrc) && strlen($imgUploadPathIs) && strlen($imageHW) )
					    		{			
					    			$this->mpg_makeThumbImage($photoSrc ,$imgUploadPathIs,$imageHW);
					    			file_put_contents($imgUploadPath.$imgBig , file_get_contents($photoBigSrc));
					   			}
					   			
		    			}	
			    	$i++;$s++;
			   }//foreach end hear  
					
		}//if condition is end hear
		else
    	{  
    		update_option('isGetFlickrAlbums',0);
    	    return  "(!)Error:Albums not get form Flickr Accout";  
    	}  
		 
   	  // $this->storingFlickrAlbumsPhotsInDB($flickrAlbDetailList , $userApiId ,$userUserId);
    	return "Success imported ".($i-1) .' Albums from Flickr Account '; 
		
	}//function end hear for showing flickr albums
	private function mpg_storingFlickrAlbumsPhotsInDB($flickrPhotoSetId , $userApiId ){
	{
		 	   $photosetId = $flickrPhotoSetId;
		   
		 		$flickrPhotosInAlb  = array(  
									    'api_key'   => $userApiId ,  
									    'method'    => 'flickr.photosets.getPhotos',  
									    'photoset_id'   => $photosetId,  
									    'extras'    => 'original_format',  
									    'format'    => 'php_serial'
									   	  								
										);
									
							
						$encoded_params = array();  
						foreach ($flickrPhotosInAlb as $k => $v){ $encoded_params[] = urlencode($k).'='.urlencode($v); }  
						$ch = curl_init();  
						$timeout = 0; // set to zero for no timeout  
						
						curl_setopt ($ch, CURLOPT_URL, 'http://api.flickr.com/services/rest/?'.implode('&', $encoded_params));  
						curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);  
						curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
						$file_contents = curl_exec($ch);  
						curl_close($ch);  
						$rsp_obj = unserialize($file_contents);
						
				 if ($rsp_obj['stat'] == 'ok') {  
						  
						        $photos = $rsp_obj["photoset"]["photo"];   //for photos in albm
						    
						        $picaAlbumName = $flickrPhotosInAlb[$photosetId]["title"];   //for album name 
						        $i = 1;
						        update_option('filckrCurAlbPhoCount', array('numOfPhots' => count($photos) , 'albName' => $picaAlbumName));	
  				
				 }//if is end		
				 $imgSigeIs = 'm.jpg';
				 if(is_array($photos))
				 foreach($photos as $photo) { //display photos in album  
  
					               $farm              = $photo['farm'];  
					               $server            = $photo['server'];  
					               $fphotoId          = $photo['id'];  //for photos in alb
					               $fAphotoId  		  =	$photo['primary'];	//for alb pho
					               $secret            = $photo['secret'];  
					               $fphotoTitle       = $photo['title'];
					               
					               	$src  = "http://farm$farm.static.flickr.com/$server/$fphotoId"._."$secret"._."$imgSigeIs" ;	
					               	$src1 = "http://farm$farm.static.flickr.com/$server/$fphotoId"._."$secret"._."b.jpg" ;			               
					               $flickrBigPhotos[] = array('id' => $i , 'title' => $fphotoTitle ,'src' => $src , 'bigSrc' => $src1 ,'secret' => $secret );
					        	   $i++;  
  
    							} //foreach loop end  
	return $flickrBigPhotos; //it contain all photos in single album;
		}// main for loop end hear 
		update_option('macFlickrCurrentAlbPhotos', $flickrBigPhotos);
		$AllList = array( $flickrBigPhotos ,$albDetails	);
		return $AllList;
	}//storeflickralbsPhotos in DB function is end hear 
	
	
	private function mpg_storeFacebookAlbumsAndPhotosInDB($appSecret , $appID , $fAlbPhotosList, $fAlbIds)  //facebook Albums and Photos are storing hear
	{
		$photosInAlb   = array();
		$imgUploadPath = $this->getUploadDirPath();  //get upload dir name
		$photoTableLastId = $this->mpg_getLastImgIdFromDB(); //for firsttime insert image in photo talble
		$lastid  = $photoTableLastId+1;
		$imageHW = $this->mpg_getImageWidthFromSettingsTB();	
		$facebookAlbumsData = $fAlbIds;
		$facebookAlbPhotosData = $fAlbPhotosList;
		$importFacebook = $appSecret.','.$appID; //store userid and userSecrData
		$this->wpdb->insert($this->importTable , array('accountids' => $importFacebook , 'importsite' => 'facebook' )  ); //insert into importtable
		$lastUpdatedRow =  $this->wpdb->insert_id;	//for importTable id	
			
				foreach($facebookAlbumsData as $albKey => $album)
				{
					
					$name     = $album['albName'];  //img name
					$photoSrc = $album['src'];  //img src
					$desc     = $album['desc'];  //img name
					$albPhoId =  $album['albPhotoId'];
					$albumsRow = array('macAlbum_name' => $name , 'macAlbum_description' =>$desc , 'macAlbum_image' => $photoSrc , "macAlbum_status" =>'ON' , 'importid' => $lastUpdatedRow); 
		    		$this->wpdb->insert($this->albumTable  , $albumsRow  );
		    		$lastUpdatedAlbRow =  $this->wpdb->insert_id; //for storing album_id value in macphotos talbe
		    		$photosInAlb = $facebookAlbPhotosData[$albKey];
					
		    		foreach($photosInAlb as $phkey => $photo){ //for storing photos in talbe
						
							$name     = $photo['name'];
							$photoSrc = $photo['picture'];
							$photoBigSrc = $photo['bigPicture'];
							$photoBigSrc = str_replace('https', 'http',$photoBigSrc );
									$cover = '';
		    						$randVal  = rand(0000,9999);
					    			$imgThumb = $lastid.'_thumb_'.$randVal.'.jpg';
			    	    			$imgBig   = $lastid.'.jpg';
					    			$imgUploadPathIs = $imgUploadPath.$imgThumb;
									$pos = strrpos($photoBigSrc , $albPhoId);//for finding album cover.
					if ($pos) { // album cover image updateing
					   
								$cover = 'ON';
		    					$this->wpdb->update($this->albumTable , array('macAlbum_image' => $imgThumb) );
						
					}			
		    			
		    			$photoTalbeData = array('macAlbum_id' =>$lastUpdatedAlbRow , 'macAlbum_cover' => $cover , 'macPhoto_name' => $name , 'macPhoto_image' => $imgThumb , 'macPhoto_status' => 'ON' , 'macPhoto_sorting' => $lastid    );
						$this->wpdb->insert($this->photoTable  , $photoTalbeData );		    						    			
					    $lastid = $this->wpdb->insert_id;
				  	    $lastid++;
				  	    	   
					    		if(strlen($photoSrc) && strlen($imgUploadPathIs) && strlen($imageHW) )
					    		{			
					    			$this->mpg_makeThumbImage($photoSrc ,$imgUploadPathIs,$imageHW);  //crating thumb size image
					    			file_put_contents($imgUploadPath.$imgBig , file_get_contents($photoBigSrc));  //storing big size of image in foluder
					    			
					   			}
					   				
						
					} //inner foreach
				} //album foreach
	
	} //function end hear
	
	private function getUploadDirPath(){
								   		
								   		$uploadDir = wp_upload_dir();
										$path = $uploadDir['basedir'].'/mac-dock-gallery/';
										return $path;
   	}
	 private function mpg_makeThumbImage($src,$uploadDirPath,$desiredWidth)  //for create imageThumbnile 
	{
		  /* read the source image */
		  $src = str_replace('https', 'http', $src);  //it is for facebook imgs it have https so we chage to http
		
		  $sourceImage = imagecreatefromjpeg($src);
		  $width = imagesx($sourceImage);
		  $height = imagesy($sourceImage);
		  
		  /* find the "desired height" of this thumbnail, relative to the desired width  */
		  $desired_height = $desiredWidth; //floor($height*($desiredWidth/$width));
		  
		  /* create a new, "virtual" image */
		  $virtualImage = imagecreatetruecolor($desiredWidth,$desired_height);
		  
		  /* copy source image at a resized size */
		  imagecopyresized($virtualImage,$sourceImage,0,0,0,0,$desiredWidth,$desired_height,$width,$height);
		  
		  /* create the physical thumbnail image to its destination */
		  imagejpeg($virtualImage,$uploadDirPath);
		
	} 
	private function showTable(){
					
				if($this->msg)
				echo "<div class='mac-error_msg' id='mac-error_msg'>$this->msg</div>";
				$siteUrl = get_option('siteurl');
	            $pluginDir = plugin_dir_url(__FILE__);  
		  ?>
		 <br/><p class="description" style="padding-top: 2%;">By using below options you can import your <strong>PUBLIC</strong> photos and albums from Google picasa, Flickr and Facebook. The imported albums & album photos will be displayed on the site with mac doc effects. The download and share options are not applicable for imported photos.</p>
		  <div class='macSettings' style="padding-top: 2px;clear: both;">
		 
		  <form action="<?php echo $_SERVER['PHP_SELF'].'?page=ImportAlbums' ?>" name='importAlbumsForm' method="post">
					       <input type="hidden" name='formSubmitOk' id='formSubmitOk' value="0"> 
					      <input type="hidden" name='flickrformSubmitOk' id='flickrformSubmitOk' value="0"> 
							<input type="hidden" name="pluginName" id="pluginName" value="<?php echo plugin_dir_url(__FILE__); ?>" />	     
					      <input type='hidden' name='siteurlPluingPath' id='siteurlPluingPath' value=" <?php echo  $this->mpg_getPluginPath(); ?>"/>
					      	<input  type="hidden" name="picasaGotAlbums" id = "picasaGotAlbums"  value = '<?php echo get_option('isGetPicasaAlbums'); ?>'  /> 
					        <input  type="hidden" name="facebookGotAlbums" id = "facebookGotAlbums"  value = '<?php echo get_option('isGetFacebookAlbums'); ?>'  /> 
					        <input  type="hidden" name="flickrGotAlbums" id = "flickrGotAlbums"  value = '<?php echo get_option('isGetFlickrAlbums'); ?>'  /> 
	   
	       <table style="margin-right: 10px;">
                   <caption class="importalubmssettings" style="background-color: #ECECEC;">Picasa Albums Settings</caption>
                   
                   <?php
                  $usrProfileImg = get_option('picasaAlbumProfilePhotos');
                  $isarray = (int)is_array($usrProfileImg);
                  
						 if($isarray)
						 {
						 	echo "<tr><td>&nbsp User</td><td>";
						 	foreach($usrProfileImg as $k => $photo)
						 	{
						 		echo "<img style='margin-right:2px;float:left;' width='50' height='50'src=$photo[photo] title='Profile Photo'  />";
						 	}
						 	echo "</td>";
						 }
                   ?>
                    <tr>
                        <td><span style="margin-bottom: 80px;">Gmail Account IDs</span></td>
                        <td><input type="text" name="macPicasaUserNames" id="macPicasaUserNames" value="<?php echo get_option('macPicasaUserNames'); ?>">
                        		  <br/>
                        		   <p id='macPicasaUserNameErrorMsg' style="display: none;color: red;">Please Enter User Name</p><p class="description">user1;user2;user3</p>
			                       <p style='margin: 0px;' class="description" >
					                        You can give multiple accounts of gmail separated using semicolon. For example
											your gmail accounts are user1@gmail.com, user2@gmail.com and user3@gmail.com
											then give user1;user2;user3; as Gmail Account IDs.
			                       </p>
                        </td>
                    </tr>
                     <tr>
                        <td><span>Albums </span></td>
                        <td>
		                        <?php  $dateTime = get_option('picasaalubmstimedate'); 
		                        
		                        		if(get_option('isGetPicasaAlbums')){
		                        			echo "Import on <br/>".$dateTime;
		                        		}
		                        		else{
		                        			
		                        			if($dateTime){
		                        				echo "import failed on <br/>".$dateTime;
		                        			}
		                        			else
		                        			echo "Not imported";
		                        		}
		                        ?>
                       
                       </td>
                   </tr>

			<tr>
				<td></td>
				<td><input style="float: right;" class="button-primary"
					type="submit" name='macGetPicasaPhotos' id='macGetPicasaPhotos'
					onclick="return valideatePicasaImportAlbums()" value='Import Albums' />
				</td>
			</tr>

		</table>
		
		<table>
		     
                   <caption class="importalubmssettings" style="background-color: #ECECEC;">Flickr Albums Settings</caption>
                   
                 <tr>
                        <td><span style="margin-top: -16%;"> Flickr API ID/API Key</span></td>
                        <td><input type="text" name="macFlickrApiId" id="macFlickrApiId" value="<?php echo get_option('macFlickrApiId'); ?>">
                       <a href="http://www.flickr.com/services/api/misc.api_keys.html" target="new" > API ID</a>
                        <br/><span id='macFlickrApiErrorMsg'  class="description" style="display:none;color: red;">Please Enter API ID</span><br/>
                        <p class="description" >After click on API ID click on <strong>Create an App</strong> click on <strong>Request an API Key</strong> link then login your account click on <strong>APPLY FOR A NON-COMMERCIAL KEY</strong> </p>
                        </td>
                 </tr>
                 <tr>
                        <td><span>Flickr User ID </span></td>
                        <td><input type="text" name="macFlickrUserId" id="macFlickrUserId" value="<?php echo get_option('macFlickrUserId'); ?>">
                         <a href="http://idgettr.com/" target="new" >User Id</a>
                          <br/><span id='macFlickrUIdErrorMsg' class="description" style="display:none;color: red;">Please Enter User Id</span>
                        </td>
                 </tr>
   				 <tr>
                        <td><span>Albums </span></td>
                        <td>
                        <?php  $dateTime = get_option('flickralubmstimedate'); 
                        
                        		if(get_option('isGetFlickrAlbums')){
                        			echo "Import on <br/>".$dateTime;
                        		}
                        		else{
                        			
                        			if($dateTime){
                        				echo "import failed on <br/>".$dateTime;
                        			}
                        			else
                        			echo "Not imported";
                        		}
                        ?>
                           </td>
                   </tr>
                   <tr>
				<td></td>
				<td><input style="float: right;" class="button-primary"
					type="submit" name='macGetFlickrPhotos' id='macGetFlickrPhotos'
					onclick="return valideateFlickrImportAlbums()" value='Import Albums' />
				</td>
			</tr>
            </table>
		
           <table style="clear:both; margin-right:10px;" >
					              
          		 <caption class="importalubmssettings">Facebook Albums Settings</caption>
             	 <tr>
                    <td>
                    <?php   
                    $app_secret =  get_option('macFacebookSecKey');
                    $app_id     = get_option('macFacebookApi');						//'199313036828495';254991121235149';	//'fdb2654f9a64ed3ca4c8a69477314a0b';//1ceb54c2fa0d15100b7ec870aeab6425';
                    $facebook   = new Facebook(array(
                    	
											 			'appId' => $app_id,
									                    'secret' =>$app_secret,
									                    'cookie' => false,
                    ));
                    	
                    $user = $facebook->getUser();
                    $admin_url = admin_url();
                    	
                    if($user)
                    {
                    	$timeDate = $this->mpg_getCurrentTimeDate();
                    	update_option('facebookalubmstimedate',$timeDate );
                    		
                    	get_option('facebookalubmstimedate');
                    	if ($user) {
                    		try{
                    			// Proceed knowing you have a logged in user who's authenticated.
                    			$user_profile = $facebook->api('/me');
                    		}
                    		catch (FacebookApiException $e) {
                    			update_option('isGetFacebookAlbums',0);
                    			error_log($e);
                    			$user = null;
                    		}
                    		$s = 0;
                    		ini_set('max_execution_time',120);
                    		$accToken =  $facebook->getAccessToken();
                    			
                    		update_option('facebookUserId',$user_profile['id']);
                    		$facebook->setAccessToken($accToken);
                    		$fAlbIds           = array();
                    		$facebookAlbPhotos = $facebook->api('/me/albums?access_token='.$accToken); // gets all user albums photos
                    		for ($i = 0 ; $i < count($facebookAlbPhotos['data']); $i++)
                    		{
                    			$numOfPhotos = trim($facebookAlbPhotos['data'][$i]['count']);
                    			if($numOfPhotos){
                    				$albId      = trim($facebookAlbPhotos['data'][$i]['id']);
                    				$albName    = trim($facebookAlbPhotos['data'][$i]['name']);
                    				$albPhotoId = trim($facebookAlbPhotos['data'][$i]['cover_photo']);

                    				$createdTime = trim($facebookAlbPhotos['data'][$i]['updated_time']);
                    				$fAlbIds[] = array('albId' => $albId , 'albName' => $albName , 'albPhotoId' => $albPhotoId , 'numOfPhotos' => $numOfPhotos , 'createdTime' => $createdTime,'status' => 'ON' , 'desc' => '' , 'index' => $s);
                    			}

                    		}

                    		$facebookAlbumPhotosList = array();
                    		$fAlbPhotosList = array();
                    		for($j = 0 ; $j < count($fAlbIds) ; $j++ )
                    		{
                    			$id      = $fAlbIds[$j]['albId'];
                    			$photoId = $fAlbIds[$j]['albPhotoId'];
                    			$photos  = $facebook->api($id.'/photos/');
                    				
                    			for($i = 0; $i < count($photos['data']) ; $i++)
                    			{
                    				$pid = $photos['data'][$i]['id'];
                    				if($pid == $photoId)
                    				{
                    					$photoSrc = trim($photos['data'][$i]['images'][2]['source']) ;
                    					$fAlbIds[$j]['src'] = $photoSrc;
                    					$fAlbIds[$j]['status'] = 'ON';
                    					$fAlbIds[$j]['desc'] = '';
                    					$fAlbIds[$j]['index'] = $s;
                    					$s++;
                    				}
                    				$name    = trim($photos['data'][$i]['name']);
                    				$picture = trim($photos['data'][$i]['picture']);
                    				$bigPicture = trim($photos['data'][$i]['images'][0]['source']);
                    				$createdTime = trim(  date('d-m-Y',strtotime($photos['data'][$i]['updated_time']) ) );
                    				$fAlbPhotosList[$j][] = array('name' => $name , 'picture' => $picture , 'bigPicture' => $bigPicture , 'createdTime' => $createdTime);
                    					
                    			}
                    				
                    		}
                    		$this->mpg_storeFacebookAlbumsAndPhotosInDB($app_secret , $app_id , $fAlbPhotosList, $fAlbIds);
                    		update_option('macFacebookAlbums',count($fAlbIds) );
                    		update_option('isGetFacebookAlbums',1);


                    	}  //if($user) is end hear
                    		
                    } //main If($user) end hear
                    	
                    if ($user) {  //for user logout
                    	session_destroy();
                    	$currPage =  $admin_url.'admin.php?page=ImportAlbums';
                    	$logoutUrl  =$facebook->getLogoutUrl(array('next' => $currPage)); //added a slash

                    } else {   //for user login
                    	$currPage =  $admin_url.'admin.php?page=ImportAlbums';
                    	$loginUrl   = $facebook->getLoginUrl(
                    	array(
									                'scope'         => 'email,offline_access,publish_stream,user_photos,offline_access,user_location',
									                'redirect_uri'  => $currPage
                    	 
                    	));
                    }
                    	
                    if ($user):
                    	
                    ?> <a href=" <?php echo $logoutUrl; ?>"><img
						alt="facebook logout"
						src="<?php echo $pluginDir.'/images/flogout.jpg'; ?>"></a>
					 <?php else: ?>
						<div>
							<input type="hidden" name="facebookImportUrl"
								id="facebookImportUrl" value=" <?php  echo $loginUrl; ?> " /> <img
								style="cursor: pointer;"
								onclick="facebookImportAlbumsConfirm();" alt="facebook login"
								src="<?php echo $pluginDir.'/images/facebook_login_button.png'; ?>">
						</div>
				</td>

    								<?php endif ?>
 	<td>
 					<?php if ($user): ?> <img style="float: left; margin-right: 2%;"
					src="https://graph.facebook.com/<?php echo $user; ?>/picture"> <?php 
					$count = get_option('macFacebookAlbums');  //albums
					echo "<p> imported $count Albums from your Account </p>";
						
						
					?> <?php else:

					$count = get_option('macFacebookAlbums');  //albums
					 
					if($count)
					{
						$userPhId = get_option('facebookuserid');
						if(strlen($userPhId > 10))
						{
							echo '<img style="float:left;margin-right:2%;"src="https://graph.facebook.com/'.$userPhId.'/picture">';
						}

						echo "<p> Imported $count Albums from your Account </p>";
					}
					 
					echo "<p>If you want import Albums and Photos Click on facebook login Button.</p>";

					endif ?>
   </td>
   </tr>    
   <tr>
                        <td><span> App ID/API Key</span></td>
                        <td><input type="text" name="macFacebookApi" id="macFacebookApi" value="<?php echo get_option('macFacebookApi'); ?>">
                        <br/><span id='macFaceApiErrorMsg' style="display:none;color: red;">Please Enter App ID</span>
                        </td>
                        
   </tr>
   <tr>
                        <td><span> App Secret</span></td>
                        <td><input type="text" name="macFacebookSecKey" id="macFacebookSecKey" value="<?php echo get_option('macFacebookSecKey'); ?>"><a href='https://developers.facebook.com/apps' target="_new">Help</a>
                        <br/><span id='macFaceApiSecErrorMsg' style="display:none;color: red;">Please Enter App Secret key </span>
                        
                        </td>
   </tr>
                    
                    
   <tr>
                        <td><span>Albums </span></td>
                        <td>
                        <?php $timeDate = get_option('facebookalubmstimedate'); 
                        	  	if(!$timeDate)
                        	  	{
                        	  		echo "Not Imported ";
                        	  	}
                        	  	else{
                        	  		$ff = get_option('isGetFacebookAlbums');
                        	  		if(!$ff){
                        	  			echo "Not Imported ";
                        	  		}
                        	  		else{
                        	  			echo " Imported on <br/>".$timeDate;	
                        	  		}
                        	  		
                        	  		
                        	  	}
                        
                        ?>
                         
                        </td>
                        
   </tr>
   <tr>
                    		<td></td>
                    		<td>
                    		
                    		<div style="float: right;margin-right: 31px;">
           					<input class="button-primary" type="submit" name='macFacebookDateStore' id ='macFacebookDateStore' onclick="return valideatefacebookAlbums()" value='Save'  />
           					</div>
                    		</td>
    </tr>
</table>

		<table style="float: left;">

			<caption class="importalubmssettings">General Settings</caption>

			<tr>
				<td><span>Show Picasa Albums</span></td>
				<td><input type="radio" name="showmacPicasaAlbums"
				<?php if (get_option('showmacPicasaAlbums')) { echo 'checked'; } ?>
					value="1">Yes <input type="radio" name="showmacPicasaAlbums"
					<?php if (!get_option('showmacPicasaAlbums')) { echo 'checked'; } ?>
					value="0">No</td>
			</tr>
			<tr>
				<td><span>Show Flickr Albums</span></td>
				<td><input type="radio" name="showmacFlickrAlbums"
				<?php if (get_option('showmacFlickrAlbums')) { echo 'checked'; } ?>
					value="1">Yes <input type="radio" name="showmacFlickrAlbums"
					<?php if (!get_option('showmacFlickrAlbums')) { echo 'checked'; } ?>
					value="0">No</td>
			</tr>
			<tr>
				<td><span>Show Facebook Albums</span></td>
				<td><input type="radio" name="showmacFacebookAlbums"
				<?php if (get_option('showmacFacebookAlbums'))  { echo 'checked'; } ?>
					value="1">Yes <input type="radio" name="showmacFacebookAlbums"
					<?php if (!get_option('showmacFacebookAlbums')) { echo 'checked'; } ?>
					value="0">No</td>
			</tr>
			<tr>
				<td><span>Timezone</span></td>
				<td><?php
				$timeZone = get_option('currentTimeZone');
				if(!$timeZone)
				$timeZone =  ini_get('date.timezone');

				?> <input type="text" name="currentTimeZone" id="currentTimeZone"
					value=<?php echo $timeZone; ?>> <a
					href="http://www.php.net/manual/en/timezones.php" target="new">Help</a>
					<span id='timezoneErrorMsg' class='description' style="color: red; display: none;">Please Enter
						timezone </span> <br>
					<p style="clear: both;" class="description">for get local time and
						date</p>
				</td>
			</tr>

		</table>


		<div style="float: right;margin-right: 31px;">
           		<input class="button-primary" type="submit" name='macImportDateStore' id ='macImportDateStore' onclick="return valideateImportAlbums()" value='Update'  />
           		<br/><span id='macsiteErrorMsg' style="clear:both;color: red;display: none;"></span>
       </div>
</form>
</div>
       <?php 
	}	//showTalbe() end
}//end class
?>