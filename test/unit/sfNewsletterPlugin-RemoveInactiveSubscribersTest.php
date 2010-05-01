<?php
require_once(dirname(__FILE__) . '/../bootstrap/task.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$limeTest = new lime_test(2, new lime_output_color());

$task = new RemoveInactiveSubscribersTask($dispatcher, $formatter);
$task->run(array(), array());

$logs = $logger->getLogEntries();
$limeTest->like($logs[0], '/No subscribers found that require deletion./', 'No subscribers to delete.');

$subscribers = SubscriberPeer::retrievePendingActivation();
$datetime = new DateTime(sprintf('-%d days', (RemoveInactiveSubscribersTask::INACTIVE_DAYS + 2)));

/* @var $eachSubscriber Subscriber */
foreach ($subscribers as $eachSubscriber)
{
  $eachSubscriber->setCreatedAt($datetime)->save();
}

$task = new RemoveInactiveSubscribersTask($dispatcher, $formatter);
$task->run(array(), array());

$logs = $logger->getLogEntries();
$limeTest->like($logs[1], '/Removing 2 subscribers./', 'Deleted both subscribers.');