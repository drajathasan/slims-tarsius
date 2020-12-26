<?php
/**
 * @Created by          : Drajat Hasan
 * @Date                : 2020-12-26 21:30:57
 * @File name           : index.php
 */

defined('INDEX_AUTH') OR die('Direct access not allowed!');

// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');
// start the session
require SB . 'admin/default/session.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';

// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

function httpQuery($query = [])
{
    return http_build_query(array_unique(array_merge($_GET, $query)));
}

$page_title = 'Buku Induk';

/* Action Area */

/* End Action Area */
?>
<div class="menuBox">
    <div class="menuBoxInner memberIcon">
        <div class="per_title">
            <h2><?php echo $page_title; ?></h2>
        </div>
        <div class="sub_section">
            <div class="btn-group">
                <a href="<?= $_SERVER['PHP_SELF'] . '?' . httpQuery() ?>" class="btn btn-default">Daftar</a>
            </div>
            <form name="search" action="<?= $_SERVER['PHP_SELF'] . '?' . httpQuery() ?>" id="search" method="get" class="form-inline"><?php echo __('Search'); ?>
                <input type="text" name="keywords" class="form-control col-md-3"/>
                <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>"
                        class="s-btn btn btn-default"/>
            </form>
        </div>
    </div>
</div>

<?php
/* Datagrid area */

/* End datagird */
