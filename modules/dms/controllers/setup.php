<?php
/**
 * @filesource modules/dms/controllers/setup.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Dms\Setup;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=dms-setup
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * แสดงรายการเอกสาร
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_List of} {LNG_Document}');
        // เลือกเมนู
        $this->menu = 'dms';
        // สามารถอัปโหลดได้
        if (Login::checkPermission(Login::isMember(), 'can_upload_dms')) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-edocument">{LNG_Document management system}</span></li>');
            $ul->appendChild('<li><span>{LNG_Upload} {LNG_Document}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-documents">'.$this->title.'</h2>',
            ));
            // ตารางรายการเอกสาร
            $section->appendChild(createClass('Dms\Setup\View')->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
