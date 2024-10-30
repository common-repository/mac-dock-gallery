<?php
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

function mpg_pageList($curpage, $pages, $albid) {
    //Pagination
    $site_url = get_bloginfo('url');
    $page_list = "";
    if ($search != '') {

        $self = '?page_id=' . get_query_var('page_id');
    } else {
        $self = '?page_id=' . get_query_var('page_id');
    }

    if (($curpage - 1) > 0) {
        $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage - 1) . "\" title=\"Previous Page\" class='macpag_left'>
                                            <img src=" . plugins_url('images/circle.GIF', __FILE__)."></a> ";
    }
    /* Print the Next and Last page links if necessary */
    if (($curpage + 1) <= $pages) {
        $page_list .= "<a href=\"" . $self . "&pages=" . ($curpage + 1) . "\" title=\"Next Page\" class='macpag_right'>
                                            <img src=" .  plugins_url('images/circle.GIF', __FILE__) . "></a> ";
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
?>