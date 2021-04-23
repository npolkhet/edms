<?php
/**
 * @filesource modules/dms/models/settings.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Dms\Settings;

use Gcms\Login;
use Kotchasan\Config;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=dms-settings
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{
    /**
     * ตั้งค่าโมดูล (settings.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, can_config, ไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::notDemoMode($login) && Login::checkPermission($login, 'can_config')) {
                try {
                    // รับค่าจากการ POST
                    $typies = array();
                    foreach (explode(',', strtolower($request->post('dms_file_typies')->filter('a-zA-Z0-9,'))) as $typ) {
                        if ($typ != '') {
                            $typies[$typ] = $typ;
                        }
                    }
                    // โหลด config
                    $config = Config::load(ROOT_PATH.'settings/config.php');
                    $config->dms_format_no = $request->post('dms_format_no')->topic();
                    $config->dms_user_permission = $request->post('dms_user_permission', array())->password();
                    $config->dms_file_typies = array_keys($typies);
                    $config->dms_upload_size = $request->post('dms_upload_size')->toInt();
                    $config->dms_download_action = $request->post('dms_download_action')->toInt();
                    if (empty($config->dms_file_typies)) {
                        // คืนค่า input ที่ error
                        $ret['ret_dms_file_typies'] = 'this';
                    } else {
                        // save config
                        if (Config::save($config, ROOT_PATH.'settings/config.php')) {
                            // คืนค่า
                            $ret['alert'] = Language::get('Saved successfully');
                            $ret['location'] = 'reload';
                            // เคลียร์
                            $request->removeToken();
                        } else {
                            // ไม่สามารถบันทึก config ได้
                            $ret['alert'] = sprintf(Language::get('File %s cannot be created or is read-only.'), 'settings/config.php');
                        }
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}
