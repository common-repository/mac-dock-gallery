<?php
global $wpdb;
//Pagination
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

function mpg_pageList($curpage, $pages) {
    //Pagination
    $page_list = "";
    if ($search != '') {

        $self = '?page=' . macAlbum;
    } else {
        $self = '?page=' . macAlbum;
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
<br /><br />
<table class="wp-list-table widefat fixed media" cellspacing="0">
    <thead>
        <th class="checkall" style="width: 5%;"><input type="checkbox"  name="checkAll" id="checkAll" class="checkall" onclick="javascript:check_all('all_action', this)"></th>
        <th class="image" style="width:12%;">Image</th>
        <th class="name" style="width:14%;">Album Name</th>
        <th class="desc">Description</th>
        <th class="on" style="width:8%;">Status</th>
        <th class="albumid" style="width:9%;">Album Id</th>
        <th class="gallery" style="width:14%;">Imported from</th>
        <th class="gallery" style="width:10%;">Actions</th>
    </thead>
    <tbody id="the-list">
    <?php

    $i = 0;
    $viewSetting = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "macsettings WHERE macSet_id=1");
    $count_result = mysql_query("SELECT * FROM " . $wpdb->prefix . "macalbum");
    $site_url = get_bloginfo('url');
    $limit = 20;
    $start = mpg_findStart($limit);
    if ($_REQUEST['pages'] == 'viewAll') {
        $w = '';
    } else if (!isset($_REQUEST['pages'])) {
        $w = '';
    } else {
        $w = "LIMIT " . $start . "," . $limit;
    }

    $count = mysql_num_rows($count_result);
    /* Find the number of pages based on $count and $limit */
    $pages = mpg_findPages($count, $limit);
    /* Now we use the LIMIT clause to grab a range of rows */

    $res = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "macalbum ORDER BY macAlbum_id DESC $w");
    $album = '';
    $uploadDir = wp_upload_dir();
    $path = $uploadDir['baseurl'] . '/mac-dock-gallery';
    $addPhotosImg = plugins_url( 'images/addimges.gif' , __FILE__ );
    $viewPhotosAlb = $imgFoulder = plugins_url( 'images/viewimges.png' , __FILE__ );
    $addImgSrc = "<img src = '$addPhotosImg' title='Add Images'  /> ";
    $vewmgSrc = "<img src = '$viewPhotosAlb' title='View Images'  /> ";
    $albTab = $wpdb->prefix . 'macphotos';
    foreach ($res as $results) {
        global $wpdb;
        $improtTable = $wpdb->prefix . 'macimportalbums';
        $importFrom = intval($results->importid);
        $sql = $wpdb->prepare("SELECT importsite from  $improtTable WHERE  importid = %d",$importFrom);
        $importFrom = ucfirst($wpdb->get_var($sql));
        $macalbid = intval($results->macAlbum_id);
        $sql =  $wpdb->prepare("SELECT COUNT(*) FROM $albTab  WHERE  macAlbum_id  = %d",$macalbid);
        $numOfImgs = $wpdb->get_var($sql);


        if (!strlen($importFrom)) {
            $importFrom = 'User Upload';
        }

        $file_image = $uploadDir['basedir'] . '/mac-dock-gallery/' . $results->macAlbum_image;
        $site_url = get_bloginfo('url');
        $wpadmin_path = admin_url();
        $album .= "<tr>
        <th class='checkall'>";

        $album .= "<input type='checkbox' class='checkSing' name='checkList[]' class='others' value='$results->macAlbum_id' ></th>";

        if (file_exists($file_image) && $results->macAlbum_image != '') {
            $album .="<td><a href='javascript:void(0)' id='$path/$results->macAlbum_image'  >
                  <img src='$path/$results->macAlbum_image' width='80' height='60' /></a></td>";
        } else if (!file_exists($file_image) && $numOfImgs) {
            $path = plugins_url( 'uploads/star.jpg' , __FILE__ );
            $album .="<td><a href='javascript:void(0)' id='$path' >
             <img src='$path' width='60' height='60' /></a></td>";
        } else {
             $path = plugins_url( 'images/default_star.gif' , __FILE__ );
            $album .="<td><a href='javascript:void(0)' id='$path' >
             <img src='$path' width='60' height='60' /></a></td>";
        }
        $album .="<td class='macName'>
                    <div id='albName_" . $results->macAlbum_id . "'>" . $results->macAlbum_name . "</div>
                    <div class='delView'><a onClick=albumNameform($results->macAlbum_id) title='Edit' style='cursor:pointer;'>Quick Edit</a></div></td>";
        $album .="<td style='width:30%'><div id='displayAlbum_" . $results->macAlbum_id . "' style='text-align:justify' >" . $results->macAlbum_description . "</div> <span id='showAlbumedit_$results->macAlbum_id'></span>";
        $album .="</div>
 </td>";

        if ($results->macAlbum_status == 'ON') {
            $album .= "<td><div name='status_bind' id='status_bind_$results->macAlbum_id'  style='text-align:left'><img src=".plugins_url('images/tick.png', __FILE__)." width='16' height='16' onclick=macAlbum_status('OFF',$results->macAlbum_id) style='cursor:pointer'  /></div></td>";
        } else {
            $album .= "<td><div name='status_bind' id='status_bind_$results->macAlbum_id'  style='text-align:left'><img src=".plugins_url('images/publish_x.png', __FILE__)." width='16' height='16' onclick=macAlbum_status('ON',$results->macAlbum_id) style='cursor:pointer' /></div></td>";
        }

        $album .="<td style='text-align:left'>$results->macAlbum_id</td> ";
        $album .="<td>$importFrom</td>";
        $album .="<td>
                    <a style='text-decoration: none;padding-left:3%;' href='$wpadmin_path/admin.php?page=macPhotos&action=viewPhotos&albid=$results->macAlbum_id'>$vewmgSrc</a>
                    <a  style='float:right;padding-right:14%;' href='$wpadmin_path/admin.php?page=macPhotos&albid=$results->macAlbum_id'>$addImgSrc</a>
                    </td></tr>";

        $i++;
    }
    
    $album .='</tbody></table>';
    $pagelist = mpg_pageList($_REQUEST['pages'], $pages);
    if ($count > $limit) {
        $album .='<div align="right">' . $pagelist . '<span><a href="'.$wpadmin_path.'/upload.php?page=macAlbum&pages=viewAll">View All</a></span></div>';
    }
    echo $album;
    wp_die();
?>