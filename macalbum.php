<?php
$dbtoken = md5(DB_NAME);

class macManage {

    function macManage() {

        $_REQUEST['action'] = NULL;
        if ($_REQUEST['action'] == 'editAlbum') {
            updateAlbum();
        } else {
            controller();
        }
    }

}

function controller() {
    global $wpdb, $site_url, $folder, $admin_url;
    
    $site_url = get_bloginfo('url');
    $admin_url = admin_url();
    $pageURL = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $split_title = $wpdb->get_var("SELECT option_value FROM " . $wpdb->prefix . "options WHERE option_name='get_title_key'");
    $get_title = unserialize($split_title);
    $strDomainName = $site_url;
    preg_match("/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
    preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder[2], $matches);
    $customerurl = $matches['domain'];
    $customerurl = str_replace("www.", "", $customerurl);
    $customerurl = str_replace(".", "D", $customerurl);
    $customerurl = strtoupper($customerurl);
    $get_key = macgal_generate($customerurl);
    $macSet = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "macsettings");
    $mac_album_count = $wpdb->get_var("SELECT count(*) FROM " . $wpdb->prefix . "macalbum");

    if (isset($_REQUEST['doaction_album'])) {
        if (isset($_REQUEST['action_album']) == 'delete') {
            for ($i = 0; $i < count($_POST['checkList']); $i++) {
                $albIdVal = is_numeric($_POST['checkList'][$i]);

                if ($albIdVal) {
                    $macAlbum_id = $_POST['checkList'][$i];
                    $alumImg = $wpdb->get_var($wpdb->prepare("SELECT macAlbum_image FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id='%d' ",$macAlbum_id));
                    $delete = $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_id='%d'",$macAlbum_id));

                    $phtImg = $wpdb->get_results($wpdb->prepare("SELECT macPhoto_id , macPhoto_image FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d'",$macAlbum_id));
                    $uploadDir = wp_upload_dir();
                    $path = $uploadDir['basedir'] . '/'.mac-dock-gallery;

                    foreach ($phtImg as $phtImgs) {
                        $photois = $path . '/' . $phtImgs->macPhoto_image;
                        if (file_exists($photois)) {
                            unlink($photois);
                        }
                        $imgName = $phtImgs->macPhoto_image;
                        $bigImgName = explode('.', $imgName);
                        $bigImg = $path . '/' . $phtImgs->macPhoto_id . '.' . $bigImgName[1];

                        if (file_exists($filename)) {
                            unlink($bigImg);
                        }
                    }//for loop end hear

                    $deletePht = $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "macphotos WHERE macAlbum_id='%d'",$macAlbum_id));
                }//if end hear
                else {
                    echo $albIdVal = (string) $_POST['checkList'][$i];

                    $albTypeAndId = explode('-', $albIdVal);

                    $albType = $albTypeAndId[0];
                    $albId = $albTypeAndId[1];
                    switch ($albType) {
                        case 333 : //facebook
                            $macFacebookAlbums = get_option('macFacebookAlbums');
                            unset($macFacebookAlbums[$albId]);
                            update_option('macFacebookAlbums', $macFacebookAlbums);

                            break;

                        case 222 :  //picasa
                            $picaalbphotos = get_option('macalbumPhotosList');
                            unset($picaalbphotos[$albId]);
                            update_option('macalbumPhotosList', $picaalbphotos);
                            //echo "<pre>";print_r($picaalbphotos);echo "</pre>";exit;


                            break;
                        case 111 :   //flickr  
                            $macflickrAlbDetailList = get_option('macflickrAlbDetailList');
                            unset($macflickrAlbDetailList[$albId]);
                            update_option('macflickrAlbDetailList', $macflickrAlbDetailList);
                            break;
                    }
                }
            }
            $msg = 'Album/s Deleted Successfully';
        }
    }
    ?>
    <link rel='stylesheet' href='<?php echo plugins_url('css/style.css', __FILE__)?>' type='text/css' />


    <script type="text/javascript">

        var site_url = '<?php echo $site_url; ?>';
        var url = '<?php echo $site_url; ?>';
        var admin_url = '<?php echo $admin_url; ?>';
        var pages = '<?php echo $_REQUEST['pages']; ?>';
        var get_title = '<?php echo $get_title['title']; ?>';
        var title_value = '<?php echo $get_key ?>';
        var dragdr = jQuery.noConflict();
        dragdr(document).ready(function(dragdr) {
            macAlbum(pages)
        });
    </script>

    <script type="text/javascript">
       var test = jQuery.noConflict();
        dragdr(document).ready(function($) {
          dragdr('a[rel*=facebox]').facebox();
        });
    </script>
    <script type="text/javascript">

        function check_all(frm, chAll)
        {
            var i = 0;
            comfList = document.forms[frm].elements['checkList[]'];
            checkAll = (chAll.checked) ? true : false; // what to do? Check all or uncheck all.
            // Is it an array
            if (comfList.length) {
                if (checkAll) {
                    for (i = 0; i < comfList.length; i++) {
                        comfList[i].checked = true;
                    }
                }
                else {
                    for (i = 0; i < comfList.length; i++) {
                        comfList[i].checked = false;
                    }
                }
            }
            else {
                /* This will take care of the situation when your
                 checkbox/dropdown list (checkList[] element here) is dependent on
                 a condition and only a single check box came in a list.
                 */
                if (checkAll) {
                    comfList.checked = true;
                }
                else {
                    comfList.checked = false;
                }
            }

            return;
        }
        var dragdr = jQuery.noConflict();
        jQuery(function() {
            dragdr("#macAlbum_submit").click(function() {
                // Made it a local variable by using "var"
                var macAlbum_name = document.getElementById("macAlbum_name").value;
                if (macAlbum_name == "") {
                    document.getElementById("error_alb").innerHTML = 'Please enter the album name';
                    return false;
                }
                else if (get_title != title_value && <?php echo $mac_album_count ?> != 0)
                {
                    dragdr(document).ready(function($) {
                        dragdr('a[rel*=oops]').facebox();
                    });
                }

            });
        });
    </script>

    <div class="wrap nosubsub"><div id="icon-upload" class="icon32"><br /></div>
        <h2 class="nav-tab-wrapper">
            <a href="?page=macAlbum" class="nav-tab nav-tab-active">Albums</a>
            <a href="?page=macPhotos&albid=0" class="nav-tab">Upload Images</a>
            <a href="?page=macSettings" class="nav-tab">Settings</a>
            <a href="?page=ImportAlbums" class="nav-tab">Import Albums</a></h2>
        <div style="background-color:#ECECEC;padding: 10px;margin-top:10px;border: #ccc 1px solid">
            <strong> Note : </strong>Mac Photo Gallery can be easily inserted to the Post / Page by adding the following code :<br><br>
            (i)  [macGallery] - This will show the entire gallery [Only for Page]<br>
            (ii) [macGallery albid=1 row=3 cols=3] - This will show the particular album images with the album id 1
        </div>
        <h3 style="float:left;width:200px;padding:10px 0 0 12px">Add New Album</h3>
    <?php
    if (isset($_REQUEST['macAlbum_submit'])) {
        $uploadDir = wp_upload_dir();
        $path = $uploadDir['basedir'] . '/'.mac-dock-gallery;
        
        if ($get_title['title'] == $get_key || $mac_album_count <= 1) {
            $macAlbum_name = strip_tags(filter_input(INPUT_POST, 'macAlbum_name'));
            $macAlbum_description = strip_tags(filter_input(INPUT_POST, 'macAlbum_description'));
            $macAlbum_name = preg_replace("/[^a-zA-Z0-9\/_-\s]/", '', $macAlbum_name);
            $current_image = $_FILES['macAlbum_image']['name'];

            $get_albname = $wpdb->get_var($wpdb->prepare("SELECT macAlbum_name FROM " . $wpdb->prefix . "macalbum WHERE macAlbum_name like '%s'",$macAlbum_name));
            if (!$get_albname) {

                $sql = $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "macalbum
                    (`macAlbum_name`, `macAlbum_description`,`macAlbum_image`,`macAlbum_status`,`macAlbum_date`) VALUES
                    ('%s', '%s', '','ON',NOW())",$macAlbum_name,$macAlbum_description));
                
            } else {
                echo "<script> alert('Album name already exist');</script>";
            }
        } else {
            echo '<div class="mac-error_msg">Album Created successfully</div>';
        }
    }

    $options = get_option('get_title_key');
    if (!is_array($options)) {
        $options = array('title' => '', 'show' => '', 'excerpt' => '', 'exclude' => '');
    }
    if (isset($_POST['submit_license'])) {
        $options['title'] = strip_tags(stripslashes($_POST['get_license']));

        update_option('get_title_key', $options);
    }

    if ($get_title['title'] != $get_key) {
        ?>
            <p><a href="#mydiv" rel="facebox"><img src="<?php echo plugins_url('images/licence.png', __FILE__) ?>" align="right"></a>
                <a href="http://www.apptha.com/shop/checkout/cart/add/product/23/" target="_new"><img src="<?php echo plugins_url('images/buynow.png', __FILE__) ?>" align="right" style="padding-right:5px;"></a>
            </p>

            <div id="mydiv" style="display:none;width:465px;padding:5px 0 30px;background:#fff;">
                
                <form method="POST" action=""  onSubmit="return validateKey()">
                    <h2 align="center">License Key</h2>
                    <div align="center"><input type="text" name="get_license" id="get_license" size="48" value="" />
                        <input type="submit" name="submit_license" id="submit_license" value="Save"/></div>
                </form>
            </div>

            <script>
                function validateKey()
                {
                    var Licencevalue = document.getElementById("get_license").value;
                    if (Licencevalue == "" || Licencevalue != "<?php echo $get_key ?>") {
                        alert('Please enter valid license key');
                        return false;
                    }
                    else
                    {
                        alert('Valid License key is entered successfully');
                        return true;
                    }
                }
            </script>
            <div id="oops" style="display:none">
                <p><strong>Oops! you will not be able to create more than one album with the free version.</strong></p>
                <p>However you can play with the default album</p>
                <ul>
                    <li> - You can add n number of photos to the default album</li>
                    <li> - You can rename the default photo album</li>
                    <li> - You can use widgets to show the photos from the default album</li>
                </ul>
                <p>Please purchase the <a href="http://www.apptha.com/category/extension/Wordpress/Mac-Photo-Gallery" target="_blank">license key</a> to use complete features of this plugin.</p>
            </div>
    <?php } //else {  ?>
        <div class="clear"></div>
    <?php if (!empty($msg)) {
        ?>
            <div  class="updated below-h2">
                <p><?php echo $msg; ?></p>
            </div>
    <?php } ?>
        <div name="form_album" name="left_content" class="left_column widefat" style="margin:0 10px;padding:10px;border-width: 1px;border-color: #dfdfdf;background-color: #f9f9f9;">
            <form name="macAlbum" method="POST" id="macAlbum" enctype="multipart/form-data"><div class="form-wrap">
                    <div class="form-macAlbum">
                        <label for="macAlbum_name">Album Name*</label>                      
                        <input name="macAlbum_name" id="macAlbum_name" type="text" value="" size="32" aria-required="true" />
                        <div id="error_alb" style="color:red"></div>
                        <p style="color:#666;"><?php _e('The album name is how it appears on your site.'); ?></p>
                    </div>
                    <br />
                    <div class="form-macAlbum">
                        <label for="macAlbum_description">Album Description</label>
                        <textarea name="macAlbum_description" id="macAlbum_description" rows="5" cols="32"></textarea>
                        <p><?php _e('The description is for the album.'); ?></p>
                    </div>
                    <p class="submit"><a href="#oops" rel="oops">
                            <input type="submit" class="button button-primary button-large" name="macAlbum_submit" id="macAlbum_submit" value="<?php echo 'Add New Album'; ?>" /></a></p>
                </div>
            </form>
        </div>

        <div name="right_content" class="right_column">
            <form name="all_action"  action="" method="POST" onSubmit="return deleteAlbums();" >
                <div class="alignleft actions">
    <?php // if($get_title['title'] == $get_key) { ?>
                    <select name="action_album" id="action_album">
                        <option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
                        <option value="delete"><?php _e('Delete'); ?></option>
                    </select>
                    <input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction_album" id="doaction_album" class="button-secondary action" />
    <?php //} wp_nonce_field('bulk-tags');  ?>
                </div>

                <div id="bind_macAlbum"></div>
                <script type="text/javascript">
    function deleteAlbums() {
    if (document.getElementById('action_album').selectedIndex == 1)
    {
    var album_delete = confirm('Are you sure to delete album/s ?');
    if (album_delete) {
    return true;
    }
    else {
    return false;
    }
    }
    else if (document.getElementById('action_album').selectedIndex == 0)
    {
    return false;
    }

    }
                </script>
            </form>
        </div>

    </div>

    <?php
}
?>
<input type="hidden" name="token" id="token" value="<?php echo $dbtoken; ?>"/>