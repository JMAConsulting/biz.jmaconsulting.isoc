<?php
define('MEMBERSHIP_PROFILE', 13);
define('MEMBERSHIP_SIGNUP', 1);
require_once 'isoc.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function isoc_civicrm_config(&$config) {
  _isoc_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function isoc_civicrm_xmlMenu(&$files) {
  _isoc_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function isoc_civicrm_install() {
  return _isoc_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function isoc_civicrm_uninstall() {
  return _isoc_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function isoc_civicrm_enable() {
  return _isoc_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function isoc_civicrm_disable() {
  return _isoc_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function isoc_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _isoc_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function isoc_civicrm_managed(&$entities) {
  return _isoc_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_postProcess
 */
function isoc_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Profile_Form_Edit' && $form->getVar('_gid') == MEMBERSHIP_PROFILE) {
    $contactId = $form->getVar('_id');
    if ($contactId) {
      $params = array(
        'custom_2' => 1,
        'entity_id' => $contactId,
      );
      civicrm_api3('custom_value', 'create', $params);
    }
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 */
function isoc_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main' && $form->getVar('_id') == MEMBERSHIP_SIGNUP) {
    $params = array(
      'id' => 2,
      'entity_id' => $form->getVar('_contactID'),
    );
    $flag = FALSE;
    $memberResult = civicrm_api3('membership', 'get', array());
    foreach ($memberResult as $id => $values) {
      if (in_array($values['status_id'], array(1,2))) {
        $flag = TRUE;
      }
    }
    $result = civicrm_api3('custom_value', 'get', $params);
    if ($result['values'][2][0] != 2 || $flag) {
      CRM_Core_Error::fatal(ts('Access Denied'));
    }
  }
}