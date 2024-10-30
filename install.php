<?php
function macGallery_install() {
    global $wpdb;
    // set tablename settings, albums, photos
    $table_settings = $wpdb->prefix . 'macsettings';
    $table_macAlbum = $wpdb->prefix . 'macalbum';
    $table_macPhotos = $wpdb->prefix . 'macphotos';
    $talbe_imploadfound = $wpdb->prefix . 'macimportalbums';
    $sfound = false;
    $afound = false;
    $pfound = false;
    $imploadfound = false;
    $found = true;
    foreach ($wpdb->get_results("SHOW TABLES;", ARRAY_N) as $row) {
        if ($row[0] == $table_settings)
            $sfound = true;
        if ($row[0] == $table_macAlbum)
            $afound = true;
        if ($row[0] == $table_macPhotos)
            $pfound = true;
        if ($row[0] == $talbe_imploadfound)
            $imploadfound = true;
    }
    // add charset & collate like wp core
    $charset_collate = '';

    if (version_compare(mysql_get_server_info(), '4.1.0', '>=')) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";
    }
    if (!$sfound) {
        $sql = "CREATE TABLE " . $table_settings . " (
          `macSet_id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `macrow` int(10) NOT NULL,
          `macimg_page` int(10) NOT NULL,
          `summary_macrow` int(5) NOT NULL,
          `summary_page` int(5) NOT NULL,
          `mouseHei` bigint(10) NOT NULL,
          `mouseWid` bigint(10) NOT NULL,
          `resizeHei` bigint(3) NOT NULL,
          `resizeWid` bigint(3) NOT NULL,
          `macProximity` double NOT NULL,
          `macDir` int(10) NOT NULL,
          `macImg_dis` varchar(10) NOT NULL,
          `macAlbum_limit` int(11) NOT NULL DEFAULT 8,
          `mac_albumdisplay` varchar(5) NOT NULL,
          `mac_imgdispstyle` int(11) NOT NULL,
          `mac_facebook_api` varchar(50) NOT NULL,
          `mac_facebook_comment` int(5) NOT NULL,
          `show_share` varchar(10) NOT NULL,
          `show_download` varchar(10) NOT NULL
              ) $charset_collate;";
        $res = $wpdb->get_results($sql);
    } else {
//        $sql = "ALTER TABLE " . $table_settings . " ADD `show_share` VARCHAR(10), ADD `show_download` VARCHAR(10) ";
//        $res = $wpdb->get_results($sql);
    }
    if (!$afound) {
        $sql = "CREATE TABLE " . $table_macAlbum . "  (
          `macAlbum_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `macAlbum_name` varchar(100) NOT NULL,
          `macAlbum_description` text NOT NULL,
          `macAlbum_image` varchar(50) NOT NULL,
          `macAlbum_status` varchar(100) NOT NULL DEFAULT 'ON',
          `macAlbum_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
          `importid` tinyint(2) NOT NULL
          ) $charset_collate;";
        $res = $wpdb->get_results($sql);
    } else {
//        $wpdb->query("ALTER TABLE $table_macAlbum ADD COLUMN `importid` tinyint(2) NOT NULL DEFAULT 0");
    }
    if (!$pfound) {
        $sql = "CREATE TABLE " . $table_macPhotos . " (
          `macPhoto_id` int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `macAlbum_id` int(5) NOT NULL,
          `macAlbum_cover` varchar(10) NOT NULL,
          `macPhoto_name` varchar(50) NOT NULL,
          `macPhoto_desc` text NOT NULL,
          `macPhoto_image` varchar(50) NOT NULL,
          `macPhoto_status` varchar(10) NOT NULL DEFAULT 'ON',
          `macPhoto_sorting` int(4) NOT NULL,
          `macPhoto_date` date NOT NULL
           ) $charset_collate;";
        $res = $wpdb->get_results($sql);
    }
    if (!$imploadfound) {
        $sql = "  CREATE TABLE IF NOT EXISTS $talbe_imploadfound (
		  `importid` tinyint(2) NOT NULL AUTO_INCREMENT,
		  `accountids` varchar(100) NOT NULL,
		  `importsite` varchar(15) NOT NULL,
		   PRIMARY KEY (`importid`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		        ";
        $res = $wpdb->get_results($sql);
    }
    $site_url = get_option('siteurl');  //Getting the site domain path
    $page_found = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts where post_content='[macGallery]'");
    if (empty($page_found)) {
        $mac_gallery_page = "INSERT INTO " . $wpdb->prefix . "posts(`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
        VALUES
                    (1, NOW(), NOW(), '[macGallery]', 'Photos', '', 'publish', 'open', 'open', '', 'mac-gallery', '', '', '2011-01-10 10:42:43',
                    'NOW()', '','', '$site_url/?page_id=',0, 'page', '', 0)";
        $res_macpage = $wpdb->get_results($mac_gallery_page);
        $res_macpage_id = $wpdb->get_var("select ID from " . $wpdb->prefix . "posts ORDER BY ID DESC LIMIT 0,1");
        $res_macpage_id = intval($res_macpage_id);
        $upd_macPage = "UPDATE " . $wpdb->prefix . "posts SET post_parent='$videoId',guid='$site_url/?page_id=$res_macpage_id' WHERE ID='$res_macpage_id'";
        $rst_updated = $wpdb->get_results($upd_macPage);
    }
    update_option('showmacPicasaAlbums', 1);
    update_option('showmacFlickrAlbums', 1);
    update_option('showmacFacebookAlbums', 1);
    update_option('macinstallSuccess', 1);
}

function macGallery_deinstall_tables() {
    global $wpdb, $wp_version;
    update_option('macinstallSuccess', 0);
    $table_settings = $wpdb->prefix . 'macsettings';
    $table_macAlbum = $wpdb->prefix . 'macphotos';
    $table_macPhotos = $wpdb->prefix . 'macalbum';
    $talbe_imploadfound = $wpdb->prefix . 'macimportalbums';
    $wpdb->query("DROP TABLE IF EXISTS `" . $table_settings . "`");
    $wpdb->query("DROP TABLE IF EXISTS `" . $table_macAlbum . "`");
    $wpdb->query("DROP TABLE IF EXISTS `" . $table_macPhotos . "`");
    $wpdb->query("DROP TABLE IF EXISTS `" . $talbe_imploadfound . "`");
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "posts WHERE post_content='[macGallery]'");
    delete_option('macalbumPhotosList');
    delete_option('macwidget_picasa_data');
    delete_option('macwidget_picasa_albums');
    delete_option('currentPicasaAlbum');
    delete_option('macFacebookPhotos');
    delete_option('macFacebookAlbums');
    delete_option('facebookUserLoginSuccess');
    delete_option('macflickrAlbShowCount');
    delete_option('macFacebookPhotos');
    delete_option('facebookAccessToken');
    delete_option('facebookUserId');
    delete_option('macflickrAlbDetailList');
    delete_option('macPicasaUserNames');
    delete_option('picasaalubmstimedate');
    delete_option('flickralubmstimedate');
    delete_option('isGetPicasaAlbums');
    delete_option('macalbumPhotosList');
    delete_option('macwidget_picasa_albums');
    delete_option('picasaAlbumProfilePhotos');
    delete_option('macwidget_picasa_data');
    delete_option('isGetPicasaAlbums');
    delete_option('picasaAlbumProfilePhotos');
    delete_option('isGetFlickrAlbums');
    delete_option('macflickrNumOfAlbs');
    delete_option('macflickrAlbDetailList');
    delete_option('filckrCurAlbPhoCount');
    delete_option('macFlickrCurrentAlbPhotos');
    delete_option('facebookalubmstimedate');
    delete_option('facebookAccessToken');
    delete_option('macFacebookPhotos');
    delete_option('macFacebookAlbums');
    delete_option('facebookUserLoginSuccess');
    delete_option('isGetFacebookAlbums');
    delete_option('macFacebookAlbums');
    delete_option('macFacebookAlbums');
    delete_option('facebookUserLoginSuccess');
    delete_option('macFacebookAlbums');
    delete_option('facebookalubmstimedate');
    delete_option('isGetFacebookAlbums');
    delete_option('showmacPicasaAlbums');
    delete_option('showmacFlickrAlbums');
    delete_option('showmacFacebookAlbums');
    delete_option('currentTimeZone');
    delete_option('macFlickrApiId');
    delete_option('picasaAlbumProfilePhotos');
    delete_option('macPicasaUserNames');
    delete_option('importedTalbleId');
    delete_option('facebookuserid');
    delete_option('macFacebookAlbums');
    delete_option('facebookUserLoginSuccess');
    delete_option('facebookalubmstimedate');
    delete_option('isGetFacebookAlbums');
    delete_option('isGetFlickrAlbums');
    delete_option('flickralubmstimedate');
    delete_option('picasaalubmstimedate');
    delete_option('isGetPicasaAlbums');
    delete_option('macPicasaUserNames');
    delete_option('filckrCurAlbPhoCount');
    delete_option('macFlickrUserId');
    delete_option('macPicasaUserNames');
    delete_option('macFacebookApi');
    delete_option('macFacebookSecKey');
    $uploadDir = wp_upload_dir();
    $path = $uploadDir['basedir'] . '/mac-dock-gallery/';
    if (is_dir($path)) {
        chmod($path, 0777);
        $photos = opendir($path);

        while ($content = readdir($photos)) {
            if ($content != '.' && $content != '..') {

                $deleteis = $path . $content;
                unlink($deleteis);
            }
        }
    }
}

//deactive is end
function create_mac_folder() {
    $content_url = content_url();
    $org_path = $content_url . 'uploads/mac-dock-gallery';
    // to mkdir() must be specified.
    if (is_dir($org_path)){} else {
        mkdir($org_path, 0777, TRUE);
    }
    $src = $content_url . '/plugins/mac-dock-gallery/mac-dock-gallery';
    rename($src, $org_path);
}

?>