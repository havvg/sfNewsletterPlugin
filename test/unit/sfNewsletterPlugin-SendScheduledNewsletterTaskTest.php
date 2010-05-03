<?php
require_once(dirname(__FILE__) . '/../bootstrap/task.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$limeTest = new lime_test(6, new lime_output_color());

$task = new SendScheduledNewsletterTask($dispatcher, $formatter);
try
{
  $task->run(array(), array());
  $limeTest->fail('InvalidArgumentException not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->is($e->getMessage(), SendScheduledNewsletterTask::EXCEPTION_NO_SENDER, 'Caught correct Exception.');
}

sfConfig::set('sf_newsletterplugin_from', 'invalid-email');
try
{
  $task->run(array(), array());
  $limeTest->fail('InvalidArgumentException not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->like($e->getMessage(), '/' . SendScheduledNewsletterTask::EXCEPTION_INVALID_SENDER . '/', 'Caught correct Exception.');
}

sfConfig::set('sf_newsletterplugin_from', 'email@example.com');
$task->run(array(), array());

$logs = $logger->getLogEntries();
$limeTest->like($logs[2], '/There are no newsletters on schedule./', 'Task exits while no newsletter are given.');

$newsletter = NewsletterPeer::retrieveByName('first newsletter');
$limeTest->ok($newsletter->setScheduledAt(new DateTime('-7 hours'))->save(), 'Scheduled Newsletter.');
$limeTest->is(count(NewsletterPeer::retrieveScheduled(new DateTime('-6 hours'))), 1, 'Found scheduled Newsletter.');

$task->run(array(), array('schedule="-6 hours"'));
$logs = $logger->getLogEntries();
$limeTest->unlike($logs[3], '/' . SendScheduledNewsletterTask::EXCEPTION_SWIFT_ERROR . '/', 'Email sent successfully.');

// @todo Add test checking POP3 to verify the email really got there!