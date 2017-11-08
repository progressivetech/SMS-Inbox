<?php
use CRM_Smsinbox_ExtensionUtil as E;

class CRM_Smsinbox_Page_SmsInbox extends CRM_Core_Page {

  public function run() {
    $inboundSmsMessages = civicrm_api3('activity', 'get', array(
      'activity_type_id' => 'Inbound SMS',
      'options' => array('limit' => 0),
    ));

    foreach ($inboundSmsMessages['values'] as &$eachInboundSmsMessage) {

      $eachInboundSmsMessage['from'] = 'test';
      $eachInboundSmsMessage['to'] = 'test';

      if (empty($eachInboundSmsMessage['source_contact_id'])) {
        continue;
      }

      $eachInboundSmsMessage['from'] = civicrm_api3('contact', 'getvalue', array(
        'return' => 'display_name',
        'id' => $eachInboundSmsMessage['source_contact_id'],
      ));

      $fromContactId = civicrm_api3('activity_contact', 'getvalue', array(
        'return' => 'contact_id',
        'activity_id' => $eachInboundSmsMessage['id'],
        'record_type_id' => 3,
      ));


      $eachInboundSmsMessage['to'] =  civicrm_api3('contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $fromContactId,
      ));
    }

    $this->assign('inboundSmsMessages', $inboundSmsMessages['values']);

    parent::run();
  }

}
