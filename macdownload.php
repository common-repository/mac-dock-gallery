<?php
$upload_dir = wp_upload_dir();
$albumid = filter_input( INPUT_GET, 'albid' );
$filepart    = explode(".",$albumid);
echo $file        = $upload_dir['basedir']."/mac-dock-gallery/".$albumid;

$fileExt = '';
$allowedExtensions = array("jpg", "jpeg", "png", "gif");
 if(preg_grep( "/$filepart[1]/i" , $allowedExtensions )){
 	$fileExt = true;
 }
 
if(file_exists($file)  && (is_int( (int)$filepart[0]) ) && ((int)$filepart[0] > 0) && $fileExt == '1'){

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
}
else{
    die("No direct access");
}
?>