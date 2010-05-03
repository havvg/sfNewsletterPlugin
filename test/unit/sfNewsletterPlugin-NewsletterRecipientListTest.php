<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$limeTest = new lime_test(4, new lime_output_color());

$recipientList = NewsletterRecipientList::createInstanceActiveSubscribers();

$limeTest->isa_ok($recipientList, 'NewsletterRecipientList', 'NewsletterRecipientList is valid.');
$limeTest->is(count($recipientList), 1, 'Found only valid subscribers.');

$subscriber = new Swift_Address('subscriber-four@example.com', 'fourth subscriber');
/* @var $eachRecipient Swift_Address */
foreach ($recipientList->getBcc() as $eachRecipient)
{
  $limeTest->is($eachRecipient->getName(), $subscriber->getName(), 'Subscriber name ok.');
  $limeTest->is($eachRecipient->getAddress(), $subscriber->getAddress(), 'Subscriber email ok.');
}