<?php
class contusMacgallery {

    function mpg_macEffectgallery($arguments = array(), $wid) {

        $i = NULL;
        $alb = NULL;
        $albid = NULL;
        $current_page = NULL;
        $search = $albrow = NULL;
        $macSetting = NULL;
        $albnam = NULL;
        $albbname = NULL;

        if ($wid == '')
            $wid = 'pirobox_gall';

        global $wpdb;
        global $t;
        $site_url = get_bloginfo('url');
        $admin_url = admin_url();
        $uploadDir = wp_upload_dir();
        $path = $uploadDir['baseurl'] . '/mac-dock-gallery';
        $macSetting = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "macsettings"); // Full settings get form the admin
        // Page id to display the mac effect
        $macGallid = $wpdb->get_var(("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_content LIKE '%[macGallery]%' AND post_type = 'page' AND post_status = 'publish'"));
        ?>

        <div id="fb-root"></div>
        <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
        <link rel="stylesheet" href="<?php echo  plugins_url('css/style.css', __FILE__); ?>">
        <link rel="stylesheet" href="<?php echo  plugins_url('css/fish-eye.css', __FILE__) ?>">
        <link rel="stylesheet" href="<?php echo  plugins_url('css/images.css', __FILE__); ?>" type="text/css" media="screen" />

        <?php
        $aid = '';
        if (!empty($_REQUEST['albid'])) {
            $aid = intval($_REQUEST['albid']);        //If  request the album from the gallery display page
        } else if (!empty($arguments['albid'])) {
            $aid = intval($arguments['albid']);     //If  request in the admin page to display the mac images
        } else if (!empty($arguments['walbid'])) {  //If  request in the widgets to display the mac images
            $aid = intval($arguments['walbid']);
            $n = $arguments['cols'];
            $no_of_row = $arguments['row'];
        }
        if ($aid != '') {    // If any albid is get then the mac images respective to the albums will be displayed
            $get_albcount = $wpdb->get_var($wpdb->prepare("SELECT macAlbum_id FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id='%d' AND macAlbum_status='ON'",$aid));
            // Only the album exist
            if ($get_albcount > 0) {
                ?>
                <!-- For the mac Effect and carousel -->
                <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('css/ie7/skin.css', __FILE__) ?>" />



                <!-- single image pirobox script-->

                <?php
                $macAlbid = intval($aid);
                $macSetting = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "macsettings"); // Full settings get form the admin
                /* display randomly */
                if (($macSetting->macImg_dis == 'random')) {
                    $where = 'ORDER BY RAND()';
                } else {
                    $where = 'ORDER BY macPhoto_sorting ASC';
                }

                if (!empty($arguments['row']) && !empty($argument['cols'])) {
                    $n = $arguments['cols'];
                    $no_of_row = $arguments['row'];
                    $albid = $arguments['albid'];
                    $itemwidth = $macSetting->mouseWid;
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='%d' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='%d' $where",$macAlbid,$macAlbid));
                } else if (empty($arguments['row']) && !empty($argument['cols'])) {
                    $n = $arguments['cols'];
                    $no_of_row = $macSetting->macimg_page;
                    $albid = $arguments['albid'];
                    $itemwidth = $macSetting->mouseWid;
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='%d' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='%d' $where",$macAlbid,$macAlbid));
                } else if (!empty($arguments['row']) && empty($arguments['cols'])) {
                    $n = $macSetting->macrow;
                    $no_of_row = $arguments['row'];
                    $albid = $arguments['albid'];
                    $itemwidth = $macSetting->mouseWid;
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='%d' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='%d' $where",$macAlbid,$macAlbid));
                } else {
                    $n = $macSetting->macrow;
                    $no_of_row = $macSetting->macimg_page;
                    if (!empty($arguments['albid']))
                        $albid = intval($arguments['albid']);
                    $itemwidth = $macSetting->mouseWid;
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='%d' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='%d' $where",$albid,$albid));
                }

                if (is_home()) {
                    $n = $macSetting->summary_page;
                    $no_of_row = $macSetting->summary_macrow;
                    $itemwidth = $macSetting->mouseWid;
                    $limit = $macSetting->summary_macrow * $macSetting->summary_page;
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='$macAlbid' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='%d' $where limit 0,$limit",$macAlbid));
                }
                if (!empty($arguments['walbid'])) {
                    $walbid = $arguments['walbid'];
                    $n = $arguments['column'];
                    $no_of_row = $arguments['rows'];
                    $itemwidth = $arguments['width'];

                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT a.*,b.* FROM " . $wpdb->prefix . "macphotos as a,
                                                                      " . $wpdb->prefix . "macalbum as b WHERE a.macAlbum_id='%d' and a.macPhoto_status='ON' and b.macAlbum_status ='ON' and b.macAlbum_id='5d' $where",$walbid,$walbid));
                } else if (!empty($_REQUEST['albid'])) {
                    $itemwidth = $macSetting->mouseWid;

                    //Pagination
                    if(!function_exists(mpg_listPagesNoTitle)){
                    function mpg_listPagesNoTitle($args) { //Pagination
                        if ($args) {
                            $args .= '&echo=0';
                        } else {
                            $args = 'echo=0';
                        }
                        $pages = wp_list_pages($args);
                        echo $pages;
                    }
                    }
                    
                     if(!function_exists(mpg_findStart)){
                    function mpg_findStart($limit) { //Pagination
                        if (!(isset($_REQUEST['pages'])) || ($_REQUEST['pages'] == "1")) {
                            $start = 0;
                            $_GET['pages'] = 1;
                        } else {
                            $start = ($_GET['pages'] - 1) * $limit;
                        }
                        return $start;
                    }
                     }
                    /*
                     * int findPages (int count, int limit)
                     * Returns the number of pages needed based on a count and a limit
                     */
 if(!function_exists(mpg_findPages)){
                    function mpg_findPages($count, $limit) { //Pagination
                        $pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1;
                        if ($pages == 1) {
                            $pages = '';
                        }
                        return $pages;
                    }
 }
                    /*
                     * string pageList (int curpage, int pages)
                     * Returns a list of pages in the format of "Â« < [pages] > Â»"
                     * */
 if(!function_exists(mpg_pageList)){
                    function mpg_pageList($curpage, $pages, $albid) {
                        //Pagination

                        $site_url = get_bloginfo('url');
                        $page_list = "";
                        if (!empty($search)) {

                            $self = '?page_id=' . get_query_var('page_id') . '&albid=' . $albid;
                        } else {
                            $self = '?page_id=' . get_query_var('page_id') . '&albid=' . $albid;
                        }
                        if (($curpage - 1) > 0) {
                            $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage - 1) . "\" title=\"Previous Page\" class='macpag_left'>
                                                    <img src='" . plugins_url('images/left.png', __FILE__)."' class='mac-no-border'></a> ";
                        }
                        /* Print the Next and Last page links if necessary */
                        if (($curpage + 1) <= $pages) {
                            $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage + 1) . "\" title=\"Next Page\"  class='macpag_right'>
                                                    <img src='" . plugins_url('images/right.png', __FILE__)."' class='mac-no-border'></a> ";
                        }
                        $page_list .= "</td>\n";
                        return $page_list;
 }}

                    /*
                     * string nextPrev (int curpage, int pages)
                     * Returns "Previous | Next" string for individual pagination (it's a word!)
                     */
                if(!function_exists(mpg_nextPrev)){
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
 }
                    //End of Pagination
                    $sqlphts = mysql_query($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macphotos where macAlbum_id='%d'
                                                                         and macPhoto_status='ON'",$macAlbid));
                    $limit = $n * $no_of_row;
                    $start = mpg_findStart($limit);
                    $w = "LIMIT " . $start . ", " . $limit;
                    $count = mysql_num_rows($sqlphts);
                    /* Find the number of pages based on $count and $limit */
                    $pages = mpg_findPages($count, $limit);
                    /* Now we use the LIMIT clause to grab a range of rows */

                    /* display in order */
                    $phtDis = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macphotos where macAlbum_id='%d'
                                                                         and macPhoto_status='ON' $where $w",$macAlbid));
                }
                if (!empty($_REQUEST['pages']))
                    $current_page = $_REQUEST['pages'];
                $limitperpage = $macSetting->macrow * $macSetting->macimg_page;
                $current_limit = 0;
                for ($i = 1; $i < $current_page; $i++) {
                    $current_limit = $current_limit + $limitperpage;
                }
                if (count($phtDis) > 0) {
                    //Parameters for mac Effect

                    $maxwidth = $macSetting->mouseHei;
                    $prox = $macSetting->macProximity;
                    $largewidth = 0;
                    $largeheight = 0;
                    $theight = $macSetting->mouseHei;
                    $twidth = $macSetting->mouseHei;
                    $direction = $macSetting->macDir;
                    $imgwidth = $macSetting->mouseWid;
                    $total = count($phtDis);
                    $totalimages = count($phtDis);
                    if (($total / $n) < $no_of_row) {
                        $no_of_row = ceil($total / $n);
                    }
                    $totalh = $twidth + $imgwidth;
                    $preheight = (($totalh + 5) * $no_of_row);
                    $width_total = ($macSetting->macrow * $macSetting->mouseWid) + (50) . 'px';
                    $page_count = ($no_of_row * ($macSetting->mouseWid) / 2 - 10) . 'px';
                    //Enf of parameters
                    $div = '<style type="text/css">';
                    /* Normal image display style */
                    if ($macSetting->mac_imgdispstyle == 0) {
                        $div .= '.imgcorner{
            border-radius: 0px;
            -moz-border-radius :0px;
            -webkit-border-radius: 0px;
            }';
                    }
                    /* Rounded corner image display style */ else if ($macSetting->mac_imgdispstyle == 1) {
                        $div .= '.imgcorner{
            border-radius: 10px !important;
            -moz-border-radius :10px;
            -webkit-border-radius: 10px;
            }';
                    }
                    /* Winged display style */ else if ($macSetting->mac_imgdispstyle == 2) {
                        $div .= '.imgcorner{

            -moz-border-radius: 1em 4em 1em 4em;
            border-radius: 1em 4em 1em 4em;
             -webkit-border-top-left-radius: 2em 0.5em;
            -webkit-border-top-right-radius: 1em 3em;
            -webkit-border-bottom-right-radius: 2em 0.5em;
            -webkit-border-bottom-left-radius: 1em 3em;


            }';
                    }
                    /* Rounded  image display  */ else if ($macSetting->mac_imgdispstyle == 3) {
                        $div .= '.imgcorner{
            border-top-left-radius:4em;
            border-top-right-radius:4em;
            border-bottom-right-radius:4em;
            border-bottom-left-radius:4em;

            -moz-border-radius-topleft: 4em;
            -moz-border-radius-topright: 4em;
            -moz-border-radius-bottomright: 4em;
            -moz-border-radius-bottomleft: 4em;

            -webkit-border-top-left-radius:4em;
            -webkit-border-top-right-radius:4em;
            -webkit-border-bottom-right-radius:4em;
            -webkit-border-bottom-left-radius:4em;
            }';
                    }
                    // The black bg color for the mac effect images from the gallery
                    if (!empty($_REQUEST['albid']) && empty($arguments['walbid']) && empty($arguments['albid'])) {
                        $div .= '
                #imgmain
                {
                 width: ' . $width_total . ';
                 margin: 0px auto;
                } #imgwrapper
                {
width: ' . $width_total . ';
}';
                    }

                    /* if direction is top */
                    if ($direction == 0) {
                        $position = "top:0px";
                        $positionvalue = 0;
                    }
                    /* if direction is bottom */ else {
                        $position = "bottom:0px";
                        $positionvalue = ($itemwidth);
                    }
                    for ($l = 1; $l <= $no_of_row; $l++) {
                        $div .= '#dock' . $t . ' { width: 100%;left: 0px;position: relative;  top:' . $positionvalue . 'px; height:' . $itemwidth . 'px  }
                  
                    #imgmain a.dock-item { ' . $position . '; 
    } ';
                        /* if direction is top */
                        if ($direction != 0) {
                            $div .= ' .dock-container' . $t . ' {  position: absolute;padding-left: 20px;}';
                        }
                        /* if direction is bottom */ else {
                            $div .= ' .dock-container' . $t . ' {  position: absolute;padding-left: 20px;z-index:' . $t . ';}';
                        }

                        $t++;
                    }
                    $div .= '</style>';
                    $y = 0;




                    foreach ($phtDis as $phtDisplay) {  // Getting all the values and stored in array
                        $imgsrc[$y]['macPhoto_image'] = $phtDisplay->macPhoto_image;
                        $imgsrc[$y]['macAlbum_id'] = $phtDisplay->macAlbum_id;
                        $imgsrc[$y]['macPhoto_id'] = $phtDisplay->macPhoto_id;
                        $imgsrc[$y]['macPhoto_name'] = $phtDisplay->macPhoto_name;
                        $imgsrc[$y]['macPhoto_desc'] = $phtDisplay->macPhoto_desc;
                        $imgsrc[$y]['macPhoto_date'] = $phtDisplay->macPhoto_date;
                        $y++;
                    }


                    $mac_album = $wpdb->get_row($wpdb->prepare("SELECT macAlbum_name,macAlbum_description,macAlbum_id FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id ='%d'",$macAlbid));
                    $height = $no_of_row * $itemwidth;

                    $div .= '<div id="imgwrapper" style="height:' . $no_of_row * $itemwidth . 'px">';
                    if (!empty($arguments['walbid'])) {
                        $maclimit = 0;
                        $div .= '<div id="imgmain" style="position:absolute;">';
                    } else {
                        $maclimit = $current_limit;
                        $div .= '<div id="imgmain">';
                    }

                    $div .= '<div class="clearfix" style="width: 100%;position:relative;z-index:9999;">';
                    $m = $n - 1;
                    $e = $t - 1;
                    for ($j = $no_of_row; $j >= 1; $j--) {
                        $k = 1;
                        $s = $m;
                        if ($s >= $totalimages
                        )
                            $s = $totalimages - 1;
                        if ($direction == 0) {
                            if ($total % $n != 0) {
                                $o = $total % $n;
                                // echo 'o='.$o;
                                if ($o == 0) {
                                    $o = $n;
                                } else {
                                    $s = $o - 1;
                                }
                                $m = $s;
                            }
                            else
                                $o = $n;
                        }
                        else {
                            if ($total % $n != 0) {
                                $o = $n;
                            } else {

                                $o = $total % $n;
                                if ($o == 0) {
                                    $o = $n;
                                } else {
                                    $s = $o - 1;
                                }
                                $m = $s;
                            }
                        }
                        if ($direction != 0) {
                            //  $u = $s - $n;
                            $u = $s;
                            if ($u <= 0) {

                                $s = 0;
                            } else {
                                $s = ($m - $n) + 1;
                            }
                        }
                        $div .='<div class="dock" id="dock' . $e . '">';
                        $div .='<div class="dock-container' . $e . '">';

                        for ($i = $k; $i <= $total; $i++) {
                            $l = $totalimages - 1 - $s;
                            if ($k <= $o) {
                                $extense = explode('.', $imgsrc[$s]['macPhoto_image']);
                                $bigImg[$s] = $imgsrc[$s]['macPhoto_id'] . '.' . $extense[1];  //Getting the big image path
                                //Dock Effect Images
                                $file_image = $uploadDir['basedir'] . '/mac-dock-gallery/' . $imgsrc[$s]['macPhoto_image'];

                                if (file_exists($file_image)) {
                                    $file_image = $path . '/' . $imgsrc[$s]['macPhoto_image'];
                                } else {
                                    $file_image = plugins_url('uploads/no-photo.png', __FILE__);
                                }

                                $div .='<a class="dock-item" rel="facebox" href="' . $admin_url . 'admin-ajax.php?action=macimageview&mac_phtid=' . $imgsrc[$s]['macPhoto_id'] . '&mac_albid=' . $imgsrc[$s]['macAlbum_id'] . '&limit=' . $maclimit . '">
                   <div class="dock_img_space"><img class="imgcorner mac-no-border" title="' . $imgsrc[$s]['macPhoto_name'] . '"
                   src="' . $file_image . '"
                   alt="" width="' . $twidth . '"> </div>
                   <span></span></a>';
                                if ($direction == 0) {
                                    $s--;
                                } else {
                                    $s++;
                                }
                            } else {
                                $total = $total - $k + 1;
                                break;
                            }
                            $k++;
                            $maclimit++;
                        }
                        $div .= '</div>';
                        $div .=' </div>';
                        $m = $m + $n;
                        $e--;
                    }

                    $div .=' </div>';
                    $div .= '</div>';
                    $div .= '</div>';
                } // End of photos count
                else {
                    $div .= '<div><h4> No Images in this album</h4></div>';
                }
                if (!empty($_REQUEST['albid']) && empty($arguments['walbid']) && empty($arguments['albid'])) { // mac effect pagination
                    $pagelist = mpg_pageList($_GET['pages'], $pages, $_GET['albid']);
                    $div .= '<div class="page_list">' . $pagelist . '</div>';
                }
                if (empty($arguments['walbid']) && empty($arguments['albid'])) {

                    if ($mac_album->macAlbum_description == '') {
                        $div .= '<div id="macshow"></div>';
                    } else {
                        $div .= '<div id="macshow"><h2 class="mac_album_des">' . $mac_album->macAlbum_name . ':' . '  </h2><p>' . $mac_album->macAlbum_description . '</p></div>';
                    }

// Horizontal Carosoule
                    $macGallid = $wpdb->get_var(("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_content LIKE '%[macGallery]%' AND post_type = 'page' AND post_status = 'publish'"));
                    $div .='<div class="album_carosole"><h2 style="margin:0px">ALBUM</h2></div>';
                    $div .= '<div id="mac_slider" >';
                    $div .= '<ul id="second-carousel" class="first-and-second-carousel jcarousel-skin-ie7">';
// Default selected first album
                    $get_albid = intval($_GET['albid']);
                    $get_album_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id='%d' and macAlbum_status='ON'",$get_albid));
                    $photoCount = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON'",$get_albid));
                    $default_first = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON' ORDER BY macPhoto_id DESC LIMIT 0,1",$get_albid));
                    $uploadDir = wp_upload_dir();
                    $file_image = $uploadDir['basedir'] . '/mac-dock-gallery/' . $get_album_row->macAlbum_image;

                    if (isset($get_album_row->albumname)) {
                        $albnam = $get_album_row->albumname;
                    }

                    if ((file_exists($file_image)) && ($get_album_row->macAlbum_image != '')) {
                        $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $get_album_row->macAlbum_id . '"><img class="mac-no-border" title="' . $albnam . '" src="' . $path . '/' . $get_album_row->macAlbum_image . '"
                              alt="" style="height:112px;filter:alpha(opacity=30);  opacity:1.0;"/>
                              <span class="carousel_lefttxt">' . substr(trim($get_album_row->macAlbum_name, ' '), 0, 15) . '</span></a></li>';
                    } else if ($get_album_row->macAlbum_image == '' && $photoCount != '0') {
                        $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $get_album_row->macAlbum_id . '"><img class="mac-no-border" title="' . $albnam . '" src="' . $path . '/' . $default_first . '"
                              alt="" style="height:112px;filter:alpha(opacity=30);  opacity:1.0;"/>
                              <span class="carousel_lefttxt">' . substr(trim(trim($get_album_row->macAlbum_name, ' '), ''), 0, 15) . '</span></a></li>';
                    } else if (!file_exists($file_image)) {
                        $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $get_album_row->macAlbum_id . '"><img class="mac-no-border" title="' . $albnam . '" src="' .  plugins_url('uploads/star.jpg', __FILE__) . '"
                         alt="" style="height:112px;filter:alpha(opacity=30);  opacity:1.0;"/>
                              <span class="carousel_lefttxt">' . substr(trim($get_album_row->macAlbum_name, ' '), 0, 15) . '</span></a></li>';
                    } else {
                        $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $get_album_row->macAlbum_id . '"><img class="mac-no-border" title="' . $albnam . '" src="' . plugins_url('uploads/star.jpg', __FILE__) . '"
                              <span class="carousel_lefttxt">' . substr(trim($get_album_row->macAlbum_name, ' '), 0, 15) . '</span></a></li>';
                    }

                    // All other  albums
                    $album_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM  " . $wpdb->prefix . "macalbum WHERE macAlbum_id !='%d' and macAlbum_status='ON'",$get_albid));

                    foreach ($album_results as $dis_results) {
                        $uploadDir = wp_upload_dir();
                        $default_first = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON' ORDER BY macPhoto_id DESC LIMIT 0,1",$dis_results->macAlbum_id));
                        $file_image = $uploadDir['basedir'] . '/mac-dock-gallery/' . $dis_results->macAlbum_image;
                        $photoCount = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON'",$dis_results->macAlbum_id));


                        if ((file_exists($file_image)) && ($dis_results->macAlbum_image != '')) {
                            $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $dis_results->macAlbum_id . '">
                        <img class="mac-no-border" title="' . $dis_results->albumname . '" src="' . $path . '/' . $dis_results->macAlbum_image . '"
                         alt=""  style="height:112px;filter:alpha(opacity=30);" />
                         <span class="carousel_lefttxt">' . substr(trim($dis_results->macAlbum_name, ' '), 0, 11) . '</span></a></li>';
                        } else if ($dis_results->macAlbum_image == '' && $photoCount != '0') {
                            if (isset($dis_results->albumname)) {
                                $albbname = $dis_results->albumname;
                            }
                            $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $dis_results->macAlbum_id . '"><img class="mac-no-border" title="' . $albbname . '" src="' . $path . '/' . $default_first . '"
                              alt="" style="height:112px;filter:alpha(opacity=30);"/>
                              <span class="carousel_lefttxt">' . substr(trim($dis_results->macAlbum_name, ' '), 0, 11) . '</span></a></li>';
                        } else if (!file_exists($file_image)) {
                            $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $dis_results->macAlbum_id . '">
                        <img class="mac-no-border" title="' . $albbname . '" src="' . plugins_url('uploads/star.jpg', __FILE__) . '"
                         alt=""  style="height:112px;filter:alpha(opacity=30);" />
                         <span class="carousel_lefttxt">' . substr(trim($dis_results->macAlbum_name, ' '), 0, 11) . '</span></a></li>';
                        } else {
                            $div .='<li><a href="' . $site_url . '?page_id=' . $macGallid . '&albid=' . $dis_results->macAlbum_id . '">
                        <img class="mac-no-border" title="' . $dis_results->albumname . '" src="' . plugins_url('uploads/star.jpg', __FILE__) . '"
                         alt=""  style="height:112px;filter:alpha(opacity=30);" />
                         <span class="carousel_lefttxt">' . substr(trim($dis_results->macAlbum_name, ' '), 0, 11) . '</span></a></li>';
                        }
                    }
                    $div .= '</ul>';

                    $div .= '</div>';
                    ?>
                    <script type="text/javascript">
                        var mac = jQuery.noConflict();
                        
                        mac(document).ready(function() {
                            mac('.first-and-second-carousel').jcarousel();

                        });
                    </script>
                    <?php
                }
            }  // If only that album exixt
            else {
                $div .= '<div>Album does not exist </div>';
            }
            if (count($phtDis) > 0) {
                // else end for no images in album
                //End of carosoule
                $albRows = 1;
                $alignment = 'left';
                $valign = 'top';
                $halign = 'left';
                
                global $d;
                ?>
                <script type="text/javascript">
                    var mac = jQuery.noConflict();

                    var site_url, mac_folder, numfiles,admin_url;
                    site_url = '<?php echo $site_url; ?>';
                    mac_folder = '<?php echo $folder; ?>';
                    admin_url = '<?php echo $admin_url?>';
                    appId = '<?php echo $macSetting->mac_facebook_api; ?>';

                    var docinarr<?php echo $t; ?> = <?php echo $t - 1; ?>;
                    var totalrec<?php echo $t; ?> = <?php echo $no_of_row; ?>;

                    function maceffect<?php echo $t; ?>()
                    {

                        for (k = docinarr<?php echo $t; ?>; k > (docinarr<?php echo $t; ?> - totalrec<?php echo $t; ?>); k--) {

                            mac('#dock' + k).Fisheye({
                                maxWidth: <?php echo $macSetting->mouseHei; ?>,
                                items: 'a',
                                itemsText: 'span',
                                valign: 'top',
                                container: '.dock-container' + k,
                                itemWidth: <?php echo $itemwidth; ?>,
                                proximity: <?php echo $macSetting->macProximity ?>,
                                halign: '<?php echo 'center'; ?>'
                            });

                        }

                    }

                </script>
                <script>
                    mac(document).ready(function() {
                        maceffect<?php echo $t; ?>();

                    });
                </script>
                <script type="text/javascript">

                    function getfacebook()
                    {
                        FB.init({appId: '<?php echo $macSetting->mac_facebook_api; ?>', status: true, cookie: true,
                            xfbml: true});
                    }</script>


                <?PHP
            } // Second End of photos count
        }   // Photos of the respective alubm
        else {

            //Pagination
         
           
            

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
                $site_url = get_bloginfo('url');
                $page_list = "";
                if (!empty($search)) {

                    $self = '?page_id=' . get_query_var('page_id');
                } else {
                    $self = '?page_id=' . get_query_var('page_id');
                }

                if (($curpage - 1) > 0) {
                    $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage - 1) . "\" title=\"Previous Page\" class='macpag_left'>
                                                    <img src='" . plugins_url('images/left.png', __FILE__)."' class='mac-no-border'></a> ";
                }
                /* Print the Next and Last page links if necessary */
                if (($curpage + 1) <= $pages) {
                    $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage + 1) . "\" title=\"Next Page\"  class='macpag_right'>
                                                    <img src='" . plugins_url('images/right.png', __FILE__) . "' class='mac-no-border'></a> ";
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

            //End of Pagination

            $limit = $macSetting->macAlbum_limit;
            $sql = mysql_query("SELECT * FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_status='ON'");
            $start = mpg_findStart($limit);
            $w = "LIMIT " . $start . ", " . $limit;
            $count = mysql_num_rows($sql);
            /* Find the number of pages based on $count and $limit */
            $pages = mpg_findPages($count, $limit);
            /* Now we use the LIMIT clause to grab a range of rows */
            $albDis = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_status='ON' $w");

            // Album div starts
            $div = '<div id="albwrapper" >';
            foreach ($albDis as $albDisplay) {
                $uploadDir = wp_upload_dir();
                $file_image = $uploadDir['basedir'] . '/mac-dock-gallery/' . $albDisplay->macAlbum_image;
                $path = $uploadDir['baseurl'] . '/mac-dock-gallery';
                $site_url = get_bloginfo('url');
                $macalbumid = intval($albDisplay->macAlbum_id);
                $photoCount = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON'",$macalbumid));
                $default_first = $wpdb->get_var($wpdb->prepare("SELECT macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d' and macPhoto_status='ON' ORDER BY macPhoto_id DESC LIMIT 0,1",$macalbumid));
                $div .='<div  class="albumimg lfloat">';

                if ($albDisplay->macAlbum_image == '' && $photoCount == '0') {
                    $div .='<div class="inner_albim_image"><a class="thumbnail" href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $macalbumid . '"><img title="' . $albDisplay->macAlbum_name . '" src="' . plugins_url('uploads/star.jpg', __FILE__) . '"></a></div>';
                } else if ($albDisplay->macAlbum_image == '' && $photoCount != '0') {
                    $div .='<div class="inner_albim_image"><a class="thumbnail" href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $macalbumid . '"><img title="' . $albDisplay->macAlbum_name . '" src="' . $path . '/' . $default_first . '"></a></div>';
                } else if ((file_exists($file_image))) {
                    $div .='<div class="inner_albim_image"><a class="thumbnail" href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $macalbumid . '"><img title="' . $albDisplay->macAlbum_name . '" src="' . $path . '/' . $albDisplay->macAlbum_image . '" ></a></div>';
                } else {
                    $div .='<div class="inner_albim_image"><a class="thumbnail" href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $macalbumid . '"><img title="' . $albDisplay->macAlbum_name . '" src="' . plugins_url('uploads/star.jpg', __FILE__) . '"></a></div>';
                }
                $div .='<div class="mac_title"><a class="thumbnail" href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $macalbumid . '">' . substr($albDisplay->macAlbum_name, 0, 15) . '</a></div>';

                $macDate = explode(' ', $albDisplay->macAlbum_date);
                if (isset($macSetting->albumRow)) {
                    $albrow = $macSetting->albumRow;
                }
                $exDate = explode('-', $macDate[0]);
                $div .='<div class="mac_date">' . $exDate[2] . '-' . $exDate[1] . '-' . $exDate[0] . '</div>';
                $div .='<a href="' . $site_url . '/?page_id=' . $macGallid . '&albid=' . $albDisplay->macAlbum_id . '" class="album_href">
                                        <span class="countimg">
                                        <img src="' . plugins_url('images/photo.jpg', __FILE__) . '" class="mac_count_img" />' . $photoCount . ' </span></a>';
                $div .='</div>';
                $i++;
                $album_row = $albrow;
            }
            $div .= '<div class="clear"></div>';
            $div .= '</div>';

            if (!empty($_GET['albid'])) {
                $alb = $_GET['albid'];
            }
            $pagelist = mpg_pageList($_GET['pages'], $pages, $alb);
            $div .= '<div align="center">' . $pagelist . '</div>';
        }   // End of the Album list show
//$div.='';
        return $div;
    }

// End of the function
}
?>