<?php
require_once(dirname(__FILE__) . '/../../bootstrap/functional.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$browser = new sfTestFunctional(new sfBrowser());
$limeTest = $browser->test();

$limeTest->plan += 1;
$newsletter = NewsletterPeer::retrieveByName('second newsletter');
$limeTest->isa_ok($newsletter, 'Newsletter', 'Newsletter found.');

$browser->getAndCheck('sfNewsletter', 'read', '/newsletter/read/' . 0, 404);
$browser->getAndCheck('sfNewsletter', 'read', '/newsletter/read/' . $newsletter->getId(), 200);
$browser->responseContains($newsletter->getContent());