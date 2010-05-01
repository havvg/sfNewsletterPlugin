<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$limeTest = new lime_test(12, new lime_output_color());

$subscriber = SubscriberPeer::retrieveSubscribed();
$limeTest->is(count($subscriber), 1, 'Count of active subscriptions.');

$pendingActivation = SubscriberPeer::retrievePendingActivation();
$limeTest->is(count($pendingActivation), 2, 'Count of subscribers that have to activate themselves.');

$criteria = new Criteria();
$criteria->add(SubscriberPeer::EMAIL, '%example.net', Criteria::LIKE);

$pendingActivation = SubscriberPeer::retrievePendingActivation($criteria);
$limeTest->is(count($pendingActivation), 1, 'Count pending activation from example.net.');

$criteria = new Criteria();
$criteria->add(SubscriberPeer::EMAIL, '%example%', Criteria::LIKE);

$pendingActivation = SubscriberPeer::retrievePendingActivation($criteria);
$limeTest->is(count($pendingActivation), 2, 'Count pending activation from example (any TLD).');

$subscriber = SubscriberPeer::retrieveByEmail('subscriber-three@example.net');
$limeTest->isa_ok($subscriber, 'Subscriber', 'Retrieved correct class.');
$limeTest->is($subscriber->getName(), 'third subscriber', 'Retrieved by email.');

try
{
  SubscriberPeer::retrieveByEmail(1);
  $limeTest->error('Invalid email provided.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught on invalid email.');
}

$subscriber = SubscriberPeer::retrieveByEmail('subscriber-one@example.net');
$limeTest->isa_ok($subscriber, 'Subscriber', 'Found Subscriber.');
$limeTest->is($subscriber->getName(), 'first subscriber', 'Found correct Subscriber.');
$limeTest->isnt($subscriber->isSubscribed(), true, 'Is not subscribed.');
$subscriber->activate();
$limeTest->ok($subscriber->isSubscribed(), 'Is now subscribed.');
$subscriber->unsubscribe();
$limeTest->isnt($subscriber->isSubscribed(), true, 'Is now unsubscribed.');