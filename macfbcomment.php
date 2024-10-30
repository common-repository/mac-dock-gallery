<?php
global $wpdb;
$pid        = intval($_REQUEST['pid']);
$phtName    = stripslashes($_REQUEST['phtName']);
$site_url   = $_REQUEST['site_url'];
$dirPage    = $_REQUEST['folder'];
$returnfbid =$wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content= '[fbmaccomments]' AND post_status='publish'");
$mac_facebook_comment = $wpdb->get_var("SELECT mac_facebook_comment FROM " . $wpdb->prefix . "macsettings WHERE macSet_id=1");
if($pid != '')
{
        $site_url = $_REQUEST['site_url'];
        $div .= '<div id="fbcomments">
                 <fb:comments canpost="true" candelete="false" numposts="'.$mac_facebook_comment.'"  xid="'.$pid.'"
                     href="'.$site_url.'/?page_id='.$returnfbid.'&macphid='.$pid.'"  title="'.$phtName.'"  publish_feed="true">
                 </fb:comments></div>';
        echo  $div;
}
?>