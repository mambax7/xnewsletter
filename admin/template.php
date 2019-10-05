<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
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
 * @copyright  Goffy ( wedega.com )
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op          = \Xmf\Request::getString('op', 'list');
$template_id = \Xmf\Request::getInt('template_id', 0);

switch ($op) {
    case 'list':
    case 'list_templates':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $adminObject->displayButton('left');

        $limit            = $helper->getConfig('adminperpage');
        $templateCriteria = new \CriteriaCompo();
        $templateCriteria->setSort('template_title DESC, template_id');
        $templateCriteria->setOrder('DESC');
        $templatesCount = $helper->getHandler('Template')->getCount();
        $start          = \Xmf\Request::getInt('start', 0);
        $templateCriteria->setStart($start);
        $templateCriteria->setLimit($limit);
        $templateObjs = $helper->getHandler('Template')->getAll($templateCriteria);
        if ($templatesCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($templatesCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "<table class='outer width100' cellspacing='1'>";
        echo '<tr>';
        echo '    <th>' . _AM_XNEWSLETTER_TEMPLATE_ID . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_TEMPLATE_TITLE . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_TEMPLATE_DESCRIPTION . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_TEMPLATE_SUBMITTER . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_TEMPLATE_CREATED . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>';
        echo '</tr>';

        if ($templatesCount > 0) {
            $class = 'odd';
            foreach ($templateObjs as $template_id => $templateObj) {
                echo "<tr class='{$class}'>";
                $class = ('even' === $class) ? 'odd' : 'even';
                echo '<td>' . $template_id . '</td>';
                echo '<td>' . $templateObj->getVar('template_title') . '</td>';
                echo '<td>' . $templateObj->getVar('template_description') . '</td>';
                echo "<td class='center'>" . \XoopsUser::getUnameFromId($templateObj->getVar('template_submitter'), 'S') . '</td>';
                echo "<td class='center'>" . formatTimestamp($templateObj->getVar('template_created'), 'S') . '</td>';
                echo "<td class='center' nowrap='nowrap'>";
                echo "    <a href='?op=edit_template&template_id=" . $template_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>";
                echo '    &nbsp;';
                echo "    <a href='?op=delete_template&template_id=" . $template_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>";
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<br>';
        echo "<div>{$pagenav}</div>";
        echo '<br>';
        break;
    case 'new_template':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $templateObj = $helper->getHandler('Template')->create();
        $form        = $templateObj->getForm();
        $form->display();
        break;
    case 'save_template':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $templateObj = $helper->getHandler('Template')->get($template_id);
        $templateObj->setVar('template_title', \Xmf\Request::getString('template_title', ''));
        $templateObj->setVar('template_description', $_REQUEST['template_description']);
        $templateObj->setVar('template_content', $_REQUEST['template_content']);
        $templateObj->setVar('template_submitter', \Xmf\Request::getInt('template_submitter', 0));
        $templateObj->setVar('template_created', \Xmf\Request::getInt('template_created', time()));

        if ($helper->getHandler('Template')->insert($templateObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        echo $templateObj->getHtmlErrors();
        $form = $templateObj->getForm();
        $form->display();
        break;
    case 'edit_template':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $templateObj = $helper->getHandler('Template')->get($template_id);
        $form        = $templateObj->getForm();
        $form->display();
        break;
    case 'delete_template':
        $templateObj = $helper->getHandler('Template')->get($template_id);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Template')->delete($templateObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'template_id' => $template_id, 'op' => 'delete_template'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $templateObj->getVar('template_title')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
