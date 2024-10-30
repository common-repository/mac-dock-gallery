<?php
class macfb
{
   function mpg_fbmacreturn()
   {
    
     global $wpdb;
        $site_url     = get_bloginfo('url');
        $returnfbid   = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content= '[fbmaccomments]' AND post_status='publish'"); //Return page id
        $macphid      = $_REQUEST['macphid']; // photo id
        $macphid      = intval($macphid);
        $macDis       = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macphotos WHERE  macPhoto_id='%d' and macPhoto_status='ON'",$macphid)); //select photo
        $mafex        = explode('.',$macDis->macPhoto_image);  //getting original image  extension from the thumb image
        $macorgimg    = explode('_',$mafex[0]); //getting original image from the thumb image
          
        $div           = '<div id="fb-root"></div>';
        $div         .= "<h3>$macDis->macPhoto_name</h3>";
        $div          .=  '<img src="'.plugin_dir_url(__FILE__).'/uploads/'.$macorgimg[0].'.'.$mafex[1].'" />';
        $div          .= '<div id="facebook" align="center">';
        $div          .= '<div id="fbcomments">
                      <fb:comments canpost="true" candelete="false" numposts="10" width="750" xid="photo'.'.'.$macphid.'"
                       href="'.$site_url.'/?page_id='.$returnfbid.'&macphid='.$macphid.'" url="'.$site_url.'/?page_id='.$returnfbid.'&macphid='.$macphid.'"
                       title="'.$macDis->macPhoto_name.'"  publish_feed="true">
                      </fb:comments></div>';
        $div          .= '</div>';
        echo $div;
      }
}
?>