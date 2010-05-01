<?php

class RemoveInactiveSubscribersTask extends sfBaseTask
{
  const INACTIVE_DAYS = 7;

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));

    $this->namespace        = 'sfNewsletterPlugin';
    $this->name             = 'RemoveInactiveSubscribers';
    $this->briefDescription = sprintf('Remove Inactive Subscribers after %d days.', self::INACTIVE_DAYS);
    $this->detailedDescription = <<<EOF
The [RemoveInactiveSubscribers|INFO] task removed subscribers that have not been activated after a set amount of days.
Call it with:

  [php symfony RemoveInactiveSubscribers|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $criteria = new Criteria(SubscriberPeer::DATABASE_NAME);
    $criteria->add(SubscriberPeer::CREATED_AT, new DateTime(sprintf('-%d days', self::INACTIVE_DAYS)), Criteria::LESS_EQUAL);

    $subscribers = SubscriberPeer::retrievePendingActivation($criteria);
    if (!empty($subscribers))
    {
      $this->logSection($this->name, sprintf('Removing %d subscribers.', count($subscribers)));
      /* @var $eachSubscriber Subscriber */
      foreach ($subscribers as $eachSubscriber)
      {
        if ($eachSubscriber->delete($connection))
        {
          $this->logSection($this->name, sprintf('Could not remove subscriber with id %d.', $eachSubscriber->getId()), 'ERROR');
        }
      }
    }
    else
    {
      $this->logSection($this->name, 'No subscribers found that require deletion.');
    }
  }
}
