<?php
require_once(dirname(__FILE__) . '/../../bootstrap/functional.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$browser = new sfTestFunctional(new sfBrowser());
$subscriber = SubscriberPeer::retrieveByEmail('subscriber-two@example.com');
$newsletter = NewsletterPeer::retrieveByName('first newsletter');

$browser->getAndCheck('sfNewsletter', 'read', '/newsletter/read/' . $newsletter->getId(), 200);
$browser->isRequestParameter('id', $newsletter->getId());

$browser->getAndCheck('sfSubscription', 'subscribe', '/newsletter/subscription', 200);

$browser->getAndCheck('sfSubscription', 'activate', '/newsletter/subscription/activate/' . $subscriber->getId() . '?' . activateAction::PARAMETER_NAME . '=5uhj32l4', 200);
$browser->isRequestParameter('id', $subscriber->getId());
$browser->isRequestParameter(activateAction::PARAMETER_NAME, '5uhj32l4');

$browser->getAndCheck('sfSubscription', 'unsubscribe', '/newsletter/subscription/unsubscribe/' . urlencode($subscriber->getEmail()) . '?' . unsubscribeAction::PARAMETER_NAME . '=asd87324', 200);
$browser->isRequestParameter('email', $subscriber->getEmail());
$browser->isRequestParameter(unsubscribeAction::PARAMETER_NAME, 'asd87324');