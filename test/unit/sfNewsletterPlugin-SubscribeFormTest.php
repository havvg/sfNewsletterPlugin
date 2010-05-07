<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfNewsletterPlugin/data/fixtures');

$limeTest = new lime_test(19, new lime_output_color());

$form = new sfSubscribeForm();
$requestData = array('name' => 'Too short', 'email' => 'invalid email');
$form->bind($requestData);
$limeTest->ok($form->hasErrors(), 'The form contains errors.');
$limeTest->is(count($form->getErrorSchema()->getNamedErrors()), 2, 'Contains two errors.');
/* @var $eachError sfValidatorError */
foreach ($form->getErrorSchema()->getNamedErrors() as $field => $eachError)
{
  switch ($field)
  {
    case 'name':
      $limeTest->like($eachError->getMessage(), '/too short/', 'Name is too short.');
      break;
    case 'email':
      $limeTest->like($eachError->getMessage(), '/Invalid/', 'Email is invalid.');
      break;
  }
}

$form = new sfSubscribeForm();
$requestData = array('name' => 'This is a valid name', 'email' => 'invalid email');
$form->bind($requestData);
$limeTest->ok($form->hasErrors(), 'The form contains errors.');
$limeTest->is(count($form->getErrorSchema()->getNamedErrors()), 1, 'Contains one error.');
/* @var $eachError sfValidatorError */
foreach ($form->getErrorSchema()->getNamedErrors() as $field => $eachError)
{
  switch ($field)
  {
    case 'email':
      $limeTest->like($eachError->getMessage(), '/Invalid/', 'Email is invalid.');
      break;
  }
}

$form = new sfSubscribeForm();
$requestData = array('name' => 'Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long Too long', 'email' => 'email@example.com');
$form->bind($requestData);
$limeTest->ok($form->hasErrors(), 'The form contains errors.');
$limeTest->is(count($form->getErrorSchema()->getNamedErrors()), 1, 'Contains one error.');
/* @var $eachError sfValidatorError */
foreach ($form->getErrorSchema()->getNamedErrors() as $field => $eachError)
{
  switch ($field)
  {
    case 'name':
      $limeTest->like($eachError->getMessage(), '/too long/', 'Name is too long.');
      break;
  }
}

$form = new sfSubscribeForm();
$requestData = array('name' => 'Too short', 'email' => 'email@example.com');
$form->bind($requestData);
$limeTest->ok($form->hasErrors(), 'The form contains errors.');
$limeTest->is(count($form->getErrorSchema()->getNamedErrors()), 1, 'Contains one error.');
/* @var $eachError sfValidatorError */
foreach ($form->getErrorSchema()->getNamedErrors() as $field => $eachError)
{
  switch ($field)
  {
    case 'name':
      $limeTest->like($eachError->getMessage(), '/too short/', 'Name is too short.');
      break;
  }
}

$form = new sfSubscribeForm();
$requestData = array('name' => 'One valid name', 'email' => 'email@example.com');
$form->bind($requestData);
$limeTest->ok($form->isValid(), 'The form contains no errors.');
$limeTest->ok($form->save(), 'The form has been saved.');

$subscriber = SubscriberPeer::retrieveByEmail('email@example.com');
$limeTest->is($subscriber->getName(), 'One valid name', 'Subscriber found.');

$form = new sfSubscribeForm();
$requestData = array('name' => 'Another valid name', 'email' => 'email@example.com');
$form->bind($requestData);
$limeTest->ok($form->hasErrors(), 'The form contains errors.');
try
{
  $form->save();
  $limeTest->fail('The second form should not be saved.');
}
catch (Exception $e)
{
  $limeTest->is($e->getCode(), 'email [invalid]', 'Error code matches.');
}

$subscriber = SubscriberPeer::retrieveByEmail('email@example.com');
$limeTest->is($subscriber->getName(), 'One valid name', 'Subscriber found.');