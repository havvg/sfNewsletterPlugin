<?php
require_once(dirname(__FILE__) . '/../../bootstrap/functional.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$browser = new sfTestFunctional(new sfBrowser());
$limeTest = $browser->test();

$browser->getAndCheck('sfSubscription', 'subscribe', '/newsletter/subscription', 200);
$browser->responseContains(sfSubscribeForm::PARAMETER_NAME);

$name = 'A valid username';
$email = 'valid-email@example.com';
$postData = array('name' => $name, 'email' => $email);
$browser->post('/newsletter/subscription', array(sfSubscribeForm::PARAMETER_NAME => $postData));
$browser->isStatusCode(200);
$browser->responseContains('An email has been sent to you, in order to verify your address.');

$limeTest->plan += 3;
$subscriber = SubscriberPeer::retrieveByEmail($email);
$limeTest->isa_ok($subscriber, 'Subscriber', 'Subscriber found.');
$limeTest->is($subscriber->getName(), $name, 'Name matches.');
$limeTest->is($subscriber->getEmail(), $email, 'Email matches.');

$limeTest->plan += 3;
$activationHash = $subscriber->getActivateHash();
$unsubscribeHash = $subscriber->getUnsubscribeHash();
$limeTest->isnt(empty($activationHash), true, 'Activation Hash set.');
$limeTest->isnt(empty($unsubscribeHash), true, 'Unsubscribe Hash set.');
$limeTest->isnt($subscriber->isSubscribed(), true, 'Subscriber is not activated, yet.');

# test activation and unsubscribe with fixtures
$limeTest->plan += 1;
$subscriber = SubscriberPeer::retrieveByEmail('subscriber-two@example.com');
$limeTest->ok(!$subscriber->isSubscribed(), 'Subscriber not activated, yet.');

$browser->getAndCheck('sfSubscription', 'activate', '/newsletter/subscription/activate/' . ($subscriber->getId() * 42) . '?' . activateAction::PARAMETER_NAME . '=invalid', 404);

$browser->getAndCheck('sfSubscription', 'activate', '/newsletter/subscription/activate/' . $subscriber->getId() . '?' . activateAction::PARAMETER_NAME . '=5uhj32l4', 200);
$browser->isRequestParameter('id', $subscriber->getId());
$browser->isRequestParameter(activateAction::PARAMETER_NAME, '5uhj32l4');
$browser->responseContains('Your email address has been verified.');

$limeTest->plan += 1;
$subscriber = SubscriberPeer::retrieveByEmail('subscriber-two@example.com');
$limeTest->ok($subscriber->isSubscribed(), 'Subscriber activated.');

$browser->getAndCheck('sfSubscription', 'unsubscribe', '/newsletter/subscription/unsubscribe/' . urlencode($subscriber->getEmail()) . '?' . unsubscribeAction::PARAMETER_NAME . '=asd87324', 200);
$browser->isRequestParameter('email', $subscriber->getEmail());
$browser->isRequestParameter(unsubscribeAction::PARAMETER_NAME, 'asd87324');
$browser->responseContains('Your email address has been unsubscribed from the newsletter.');

$limeTest->plan += 1;
$subscriber = SubscriberPeer::retrieveByEmail('subscriber-two@example.com');
$limeTest->ok(!$subscriber->isSubscribed(), 'Subscriber unsubscribed again.');