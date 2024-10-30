<?php
global $wpdb;
$site_url = get_bloginfo('url');

// Album Status Change
if($_REQUEST['albid'] != '')
{
    $mac_albId   = $_REQUEST['albid'];
    $mac_albStat = $_REQUEST['status'];
    $mac_albId   = intval($mac_albId);
    if($_REQUEST['status'] == 'ON')
    {
       $alumImg = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macalbum SET macAlbum_status='ON' WHERE macAlbum_id='%d'",$mac_albId));
       echo "<img src=".plugins_url('images/tick.png', __FILE__)." style='cursor:pointer' width='16' height='16' onclick=macAlbum_status('OFF',$mac_albId)  />";
    }
    else
    {
        $alumImg = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macalbum SET macAlbum_status='OFF' WHERE macAlbum_id='%d'",$mac_albId));
        echo "<img src=".plugins_url('images/publish_x.png', __FILE__)." style='cursor:pointer' width='16' height='16' onclick=macAlbum_status('ON',$mac_albId)  />";
    }
   
exit;
}
// Photos status change respect to album
else if($_REQUEST['macPhoto_id'] != '')
{
    $macPhoto_id   = $_REQUEST['macPhoto_id'];
    $mac_photoStat = $_REQUEST['status'];
    $macPhoto_id   = intval($macPhoto_id);
    if($_REQUEST['status'] == 'ON')
    {
      $photoImg = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macphotos SET macPhoto_status='ON' WHERE macPhoto_id='%d'",$macPhoto_id));
      echo "<img src=".plugins_url('images/tick.png', __FILE__)." style='cursor:pointer' width='16' height='16' onclick=macPhoto_status('OFF',$macPhoto_id)  />";
    }
    else
    {
        $photoImg = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macphotos SET macPhoto_status='OFF' WHERE macPhoto_id='%d'",$macPhoto_id));
        echo "<img src=".plugins_url('images/publish_x.png', __FILE__)." style='cursor:pointer' width='16' height='16' onclick=macPhoto_status('ON',$macPhoto_id)  />";
    }

}
else if($_REQUEST['macDelid'] != '')
{
    $macPhoto_id = $_REQUEST['macDelid'];
    $macPhoto_id = intval($macPhoto_id);
    $photoImg    = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d' ",$macPhoto_id));
    $uploadDir   = wp_upload_dir();
    $path        = $uploadDir['baseurl'];
    $path        = "$path/";
  unlink($path . $photoImg);
            $extense = explode('.', $photoImg);
            unlink($path . $macPhoto_id . '.' .$extense[1]);
             $deletePhoto = $wpdb->get_results($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d'",$macPhoto_id));
            echo '';

}
//   For photo edit form
else if($_REQUEST['macPhotoname_id'] != '')
{
    $macPhoto_id = $_REQUEST['macPhotoname_id'];
    $div = '<form name="macPhotoform" method="POST"><td style="margin:0 10px;border:none"><input type="text" name="macPhoto_name_'.$macPhoto_id.'" id="macPhoto_name_'.$macPhoto_id.'" ></td>';
    $div .= '<td colspan="2" style="padding-top:10px;text-align:center;border:none"><input type="button" name="updatePhoto_name" value="Update" onclick="updPhotoname('.$macPhoto_id.')"></td></form/>' ;
    echo $div;exit;
}

// Add as album cover from the photos
else if ($_REQUEST['macCovered_id'] != '')
{
$macPhotoid     = $_REQUEST['macCovered_id'];
$macPhotoid     = intval($macPhotoid);
$albumId        = intval($albumId);
$albumCover     = $_REQUEST['albumCover'];
$albumId        = $_REQUEST['albumId'];
if($albumCover == 'ON')
{
     $albumCover    = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macphotos SET macAlbum_cover='ON' WHERE macPhoto_id='%d' and macAlbum_id='%d'",$macPhotoid,$albumId));
     $albumCoveroff = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macphotos SET macAlbum_cover='OFF' WHERE macPhoto_id !='%d' and macAlbum_id='%d'",$macPhotoid,$albumId));
     $photoImg      = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macPhoto_id='%d' ",$macPhotoid));
     $addtoAlbum    = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macalbum SET macAlbum_image='$photoImg' WHERE macAlbum_id='%d'",$albumId));
     echo "<img src=".plugins_url('images/tick.png', __FILE__)."  style='cursor:pointer' width='16' height='16' onclick=macPhoto_status('OFF',$macPhoto_id)  />";
}
}

// update photo name
else if($_REQUEST['macPhoto_name'] != '')
{
     $macPhoto_id    =$_REQUEST['macPhotos_id'];
     $macPhoto_id    = intval($macPhoto_id);
     $macPhoto_name  =  strip_tags($_REQUEST['macPhoto_name']);
     $macPhoto_name  = preg_replace("/[^a-zA-Z0-9\/_-\s]/", ' ', $macPhoto_name);
     $sql            = $wpdb->get_results($wpdb->prepare("UPDATE " . $wpdb->prefix . "macphotos SET `macPhoto_name` = '$macPhoto_name' WHERE `macPhoto_id` = '%d'",$macPhoto_id));
     echo $macPhoto_name;exit;
}

//Album name edit form
else if($_REQUEST['macAlbumname_id'] != '')
{
    $macAlbum_id = $_REQUEST['macAlbumname_id'];
    $macAlbum_id = intval($macAlbum_id);
    $fet_res     = $wpdb->get_row($wpdb->prepare("SELECT * FROM  " . $wpdb->prefix . "macalbum WHERE macAlbum_id= %d",$macAlbum_id));
    $div         = '<form name="macUptform" method="POST">
                    <div style="margin:0;padding:0;border:none"><input type="text"
                    name="macedit_name_'.$macAlbum_id.'" id="macedit_name_'.$macAlbum_id.'" size="15" value="'.$fet_res->macAlbum_name.'" ></div>';

    $div        .= '<div><textarea name="macAlbum_desc_'.$macAlbum_id.'"  id="macAlbum_desc_'.$macAlbum_id.'" rows="6" cols="22" >'.$fet_res->macAlbum_description.'</textarea></div>';
    $div        .= '<input type="button"  name="updateMac_name" value="Update" onclick="updAlbname('.$macAlbum_id.')";>
                    <input type="button" onclick="CancelAlbum('.$macAlbum_id.')"   value="Cancel">
                    </div>';
    $div        .= '</form/>';
    echo $div;exit;
}


 else if($_REQUEST['macAlbum_id'] != '' )
{
      $macAlbum_id   =   intval(filter_input(INPUT_GET, 'macAlbum_id'));
      $macAlbum_name = strip_tags($_GET['macAlbum_name']);
      $macAlbum_name = preg_replace("/[^a-zA-Z0-9\/_-\s]/", ' ', $macAlbum_name);
      $macAlbum_desc = strip_tags($_GET['macAlbum_desc']);
          $sql = $wpdb->get_results($wpdb->prepare("UPDATE " . $wpdb->prefix . "macalbum SET `macAlbum_name`='%s',`macAlbum_description` ='%s'
    WHERE `macAlbum_id` = '%d'",$macAlbum_name,$macAlbum_desc,$macAlbum_id));
            

}
//  Album description update
 else
{
     $macAlbum_desc  =  strip_tags($_REQUEST['macAlbum_desc']) ;
     $macAlbum_id    = $_REQUEST['macAlbum_id'];
     $macAlbum_id    =  intval($macAlbum_id);
     $sql            = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "macalbum SET `macAlbum_description` = '%s' WHERE `macAlbum_id` = '%d'",$macAlbum_desc,$macAlbum_id));
     echo $macAlbum_desc;exit;
}
?>