<?php
/**
 * @filesource modules/dms/views/write.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Dms\Write;

use Kotchasan\Html;
use Kotchasan\Language;
use Kotchasan\Text;

/**
 * module=dms-write
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มสร้าง/แก้ไข เอกสาร
     *
     * @param object $index
     * @param array $login
     *
     * @return string
     */
    public function render($index, $login)
    {
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/dms/model/write/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_Document}',
        ));
        // document_no
        $fieldset->add('text', array(
            'id' => 'document_no',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'item',
            'label' => '{LNG_Document number}',
            'placeholder' => '{LNG_Leave empty for generate auto}',
            'maxlength' => 20,
            'value' => isset($index->document_no) ? $index->document_no : '',
        ));
        // create_date
        $fieldset->add('date', array(
            'id' => 'create_date',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'item',
            'label' => '{LNG_Date}',
            'value' => isset($index->create_date) ? $index->create_date : date('Y-m-d'),
        ));
        $category = \Dms\Category\Model::init();
        foreach (Language::get('DMS_CATEGORIES') as $k => $label) {
            if ($k != 'department') {
                $fieldset->add('text', array(
                    'id' => $k,
                    'labelClass' => 'g-input icon-valid',
                    'itemClass' => 'item',
                    'label' => $label,
                    'datalist' => $category->toSelect($k),
                    'value' => isset($index->{$k}) ? $index->{$k} : '',
                    'text' => '',
                ));
            }
        }
        // department
        if ($login['status'] == 1 || empty($login['department']) || empty(self::$cfg->dms_upload_options)) {
            $department = $category->toSelect('department');
        } else {
            $department = array($login['department'], $category->get('department', $login['department']));
        }
        $fieldset->add('checkboxgroups', array(
            'id' => 'department',
            'name' => 'department[]',
            'labelClass' => 'g-input icon-group',
            'itemClass' => 'item',
            'label' => '{LNG_Department}',
            'options' => $department,
            'value' => isset($index->department) ? explode(',', $index->department) : array(),
        ));
        // topic
        $fieldset->add('text', array(
            'id' => 'topic',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'item',
            'label' => '{LNG_Document title}',
            'maxlength' => 255,
            'value' => isset($index->topic) ? $index->topic : '',
        ));
        // file
        $fieldset->add('file', array(
            'id' => 'file',
            'name' => 'file[]',
            'labelClass' => 'g-input icon-upload',
            'itemClass' => 'item',
            'label' => '{LNG_Browse file}',
            'comment' => '{LNG_Upload :type files no larger than :size} ({LNG_Can select multiple files})',
            'accept' => self::$cfg->dms_file_typies,
            'dataPreview' => 'filePreview',
            'multiple' => true,
        ));
        // url
        $fieldset->add('url', array(
            'id' => 'url',
            'labelClass' => 'g-input icon-world',
            'itemClass' => 'item',
            'label' => '{LNG_URL}',
            'maxlength' => 255,
            'comment' => '{LNG_Select the file or specify the URL of the document}',
            'placeholder' => '{LNG_URL must begin with http:// or https://}',
            'value' => isset($index->url) ? $index->url : '',
        ));
        // detail
        $fieldset->add('textarea', array(
            'id' => 'detail',
            'labelClass' => 'g-input icon-file',
            'itemClass' => 'item',
            'label' => '{LNG_Description}',
            'comment' => '{LNG_Note or additional notes}',
            'rows' => 5,
            'value' => isset($index->detail) ? $index->detail : '',
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button ok large icon-save',
            'value' => '{LNG_Save}',
        ));
        // id
        $fieldset->add('hidden', array(
            'id' => 'id',
            'value' => $index->id,
        ));
        \Gcms\Controller::$view->setContentsAfter(array(
            '/:type/' => implode(', ', self::$cfg->dms_file_typies),
            '/:size/' => Text::formatFileSize(self::$cfg->dms_upload_size),
        ));
        // คืนค่า HTML
        return $form->render();
    }
}
