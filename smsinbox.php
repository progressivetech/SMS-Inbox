<?php

require_once 'smsinbox.civix.php';
use CRM_Smsinbox_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function smsinbox_civicrm_config(&$config) {
  _smsinbox_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function smsinbox_civicrm_install() {
  _smsinbox_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function smsinbox_civicrm_enable() {
  _smsinbox_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function smsinbox_civicrm_navigationMenu(&$menu) {
  _smsinbox_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _smsinbox_civix_navigationMenu($menu);
} // */

/**
 * Implements hook_civicrm_navigationMenu().
 */
function smsinbox_civicrm_navigationMenu(&$params) {
  $sMailingMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Mailings', 'id', 'name');

  //  Get the maximum key of $params
  $maxKey = max(array_keys($params));

  $params[$sMailingMenuId]['child'][$maxKey + 1] = array(
    'attributes' => array(
      'label'      => 'SMS Inbox',
      'name'       => 'SMSInbox',
      'url'        => 'civicrm/smsinbox',
      'permission' => NULL,
      'operator'   => NULL,
      'separator'  => NULL,
      'parentID'   => $sMailingMenuId,
      'navID'      => $maxKey + 1,
      'active'     => 1,
    ),
  );
}

/**
 * Not every "page" is a CiviCRM page.
 *
 * @param type $page
 */
function smsinbox_civicrm_check(&$messages, $statusNames, $includeDisabled) {
  // Early return if $statusNames doesn't call for our check
  if ($statusNames && !in_array('smsinbox', $statusNames)) {
    return;
  }
  if (!$includeDisabled) {
    $disabled = \Civi\Api4\StatusPreference::get()
      ->setCheckPermissions(FALSE)
      ->addWhere('is_active', '=', FALSE)
      ->addWhere('domain_id', '=', 'current_domain')
      ->addWhere('name', '=', 'smsinbox')
      ->execute()->count();
    if ($disabled) {
      return;
    }
  }
  $messageText = CRM_Smsinbox_Utils::checkForUnreadMessageStatus();

  if ($messageText) {
    $message = new CRM_Utils_Check_Message(
      'smsinbox',
      $messageText,
      E::ts('Unread SMSInbox messages'),
      \Psr\Log\LogLevel::WARNING,
      'fa-flag'
    );
    $message->addHelp(E::ts('You can read these messages by clicking the SMSInbox menu item from the Mailings menu.'));
    $messages[] = $message;
  }
}

/**
 * Run on forms for when a "page" isn't a CiviCRM page.
 *
 * @param string $formName
 * @param type $form
 */
function smsinbox_civicrm_buildForm($formName, &$form) {
  if ('CRM_Smsinbox_Form_SendSms' == $formName) {
    return;
  }
  CRM_Smsinbox_Utils::checkForUnreadMessageStatus();
}
