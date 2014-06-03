<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( http://www.xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  Goffy ( wedega.com )
 *
 *  You may not change or alter any portion of this comment or credits
 *  of supporting developers from this source code or any supporting
 *  source code which is considered copyrighted (c) material of the
 *  original comment or credit authors.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  ---------------------------------------------------------------------------
 *  @copyright  Goffy ( wedega.com )
 *  @license    GNU General Public License 2.0
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

include "admin_header.php";
xoops_cp_header();
//global $pathIcon, $indexAdmin;
// We recovered the value of the argument op in the URL$
$op = xNewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');

switch ($op) {
    case "list" :
    default :
        echo $indexAdmin->addNavigation('mailinglist.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, 'mailinglist.php?op=new_mailinglist', 'add');
        echo $indexAdmin->renderButton();
        $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
        $criteria = new CriteriaCompo();
        $criteria->setSort("mailinglist_id ASC, mailinglist_email");
        $criteria->setOrder("ASC");
        $numrows = $xnewsletter->getHandler('xNewsletter_mailinglist')->getCount();
        $start = xNewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $mailinglist_arr = $xnewsletter->getHandler('xNewsletter_mailinglist')->getall($criteria);
        if ($numrows > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($numrows>0) {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_MAILINGLIST_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_LISTNAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>
                ";
            $class = "odd";
            foreach (array_keys($mailinglist_arr) as $i) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $i."</td>";
                echo "<td class='center'>" . $mailinglist_arr[$i]->getVar("mailinglist_name") . "</td>";
                echo "<td class='center'>" . $mailinglist_arr[$i]->getVar("mailinglist_email") . "</td>";
                echo "<td class='center'>" . $mailinglist_arr[$i]->getVar("mailinglist_listname") . "</td>";
                echo "<td class='center'>" . $mailinglist_arr[$i]->getVar("mailinglist_subscribe") . "</td>";
                echo "<td class='center'>" . $mailinglist_arr[$i]->getVar("mailinglist_unsubscribe") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($mailinglist_arr[$i]->getVar("mailinglist_created"), "S") . "</td>";
                echo "<td class='center width5'>
                    <a href='mailinglist.php?op=edit_mailinglist&mailinglist_id=" . $i . "'><img src=".XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='"._EDIT."' title='"._EDIT . "' /></a>
                    <a href='mailinglist.php?op=delete_mailinglist&mailinglist_id=" . $i . "'><img src=".XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='"._DELETE."' title='" . _DELETE . "' /></a>
                    </td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_MAILINGLIST_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_LISTNAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_MAILINGLIST_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>
            </table><br /><br />";
        }
        break;

    case "new_mailinglist" :
        echo $indexAdmin->addNavigation("mailinglist.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, 'mailinglist.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $obj =& $xnewsletter->getHandler('xNewsletter_mailinglist')->create();
        $form = $obj->getForm();
        $form->display();
        break;

    case "save_mailinglist" :
        if (!$GLOBALS["xoopsSecurity"]->check()) {
            redirect_header("mailinglist.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if (isset($_REQUEST["mailinglist_id"])) {
            $obj =& $xnewsletter->getHandler('xNewsletter_mailinglist')->get($_REQUEST["mailinglist_id"]);
        } else {
            $obj =& $xnewsletter->getHandler('xNewsletter_mailinglist')->create();
        }
        //Form mailinglist_name
        $obj->setVar("mailinglist_name", $_REQUEST["mailinglist_name"]);
        //Form mailinglist_email
        $obj->setVar("mailinglist_email", $_REQUEST["mailinglist_email"]);
        //Form mailinglist_listname
        $obj->setVar("mailinglist_listname", $_REQUEST["mailinglist_listname"]);
        //Form mailinglist_subscribe
        $obj->setVar("mailinglist_subscribe", $_REQUEST["mailinglist_subscribe"]);
        //Form mailinglist_unsubscribe
        $obj->setVar("mailinglist_unsubscribe", $_REQUEST["mailinglist_unsubscribe"]);
        //Form mailinglist_submitter
        $obj->setVar("mailinglist_submitter", $_REQUEST["mailinglist_submitter"]);
        //Form mailinglist_created
        $obj->setVar("mailinglist_created", $_REQUEST["mailinglist_created"]);

        if ($xnewsletter->getHandler('xNewsletter_mailinglist')->insert($obj)) {
            redirect_header("mailinglist.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $obj->getHtmlErrors();
        $form =& $obj->getForm();
        $form->display();
        break;

    case "edit_mailinglist" :
        echo $indexAdmin->addNavigation("mailinglist.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, 'mailinglist.php?op=new_mailinglist', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, 'mailinglist.php?op=list', 'list');
        echo $indexAdmin->renderButton();
        $obj = $xnewsletter->getHandler('xNewsletter_mailinglist')->get($_REQUEST["mailinglist_id"]);
        $form = $obj->getForm();
        $form->display();
        break;

    case "delete_mailinglist" :
        $obj =& $xnewsletter->getHandler('xNewsletter_mailinglist')->get($_REQUEST["mailinglist_id"]);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
        if (!$GLOBALS["xoopsSecurity"]->check()) {
            redirect_header("mailinglist.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if ($xnewsletter->getHandler('xNewsletter_mailinglist')->delete($obj)) {
            redirect_header("mailinglist.php", 3, _AM_XNEWSLETTER_FORMDELOK);
        } else {
            echo $obj->getHtmlErrors();
        }
        } else {
            xoops_confirm(array("ok" => 1, "mailinglist_id" => $_REQUEST["mailinglist_id"], "op" => "delete_mailinglist"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("mailinglist_email")));
        }
        break;
}
include "admin_footer.php";