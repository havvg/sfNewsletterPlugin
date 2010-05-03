<?php

class SendScheduledNewsletterTask extends sfBaseTask
{
  const EXCEPTION_NO_SENDER = 'There is no sender email configured.';

  const EXCEPTION_INVALID_SENDER = 'The configured sender email is invalid.';

  const EXCEPTION_SWIFT_ERROR = 'An error occured sending the email.';

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),

      new sfCommandOption('schedule', date('Y-m-d 00:00:00'), sfCommandOption::PARAMETER_OPTIONAL, 'The datetime since when scheduled should be checked.'),
    ));

    $this->namespace        = 'sfNewsletterPlugin';
    $this->name             = 'SendScheduledNewsletter';
    $this->briefDescription = 'Sends all newsletters that are scheduled for the given datetime (using ISO or textual).';
    $this->detailedDescription = <<<EOF
The [SendScheduledNewsletter|INFO] task sends all newsletters that are scheduled since the given datetime and have not been sent yet.

The datetime may be provided using a valid ISO date or a textual represenation of the date, which is strtotime compatible.

Call it with:

  [php symfony RemoveInactiveSubscribers|INFO]
EOF;
  }

  /**
   * Send scheduled newsletters to all active subscribers.
   *
   * @todo Add event listener to swift in order to try resending the newsletter.
   *
   * @throws InvalidArgumentException
   *
   * @param array $arguments
   * @param array $options
   *
   * @return void
   */
  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $from = sfConfig::get('sf_newsletterplugin_from', false);

    if ($from === false)
    {
      $this->logSection($this->name, self::EXCEPTION_NO_SENDER, 30, 'ERROR');
      throw new InvalidArgumentException(self::EXCEPTION_NO_SENDER);
    }

    $validator = new sfEmailValidator(null);
    $error = false;
    if (!$validator->execute($from, $error))
    {
      $this->logSection($this->name, self::EXCEPTION_INVALID_SENDER, null, 'ERROR');
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_SENDER);
    }

    $newsletters = NewsletterPeer::retrieveScheduled(new DateTime($options['schedule']));
    if (empty($newsletters))
    {
      $this->logSection($this->name, 'There are no newsletters on schedule.');
      return;
    }

    /* @var $eachNewsletter Newsletter */
    foreach ($newsletters as $eachNewsletter)
    {
      try
      {
        // get recipient list
        $recipientList = NewsletterRecipientList::createInstanceActiveSubscribers();
        $recipientList->addTo($from);

        // send the mail using swift
        try
        {
          $mailer = new Swift(new Swift_Connection_NativeMail());
          $message = new Swift_Message($eachNewsletter->getSubject(), $eachNewsletter->getContent(), $eachNewsletter->getContentType()->getMimeType());

          $sent = $mailer->send($message, $recipientList, $from);
          $mailer->disconnect();

          if ($sent < count($recipientList))
          {
            $this->logSection($this->name, sprintf(self::EXCEPTION_SWIFT_ERROR . ' Error: Email has not reached all recipients. Successfully sent to %d of %d recipients.', $sent, count($recipientList)), null, 'ERROR');
          }
        }
        catch (Exception $e)
        {
          $mailer->disconnect();
          $this->logSection($this->name, self::EXCEPTION_SWIFT_ERROR . ' Error: ' . $e->getMessage(), null, 'ERROR');
        }
      }
      catch (RuntimeException $e)
      {
        $this->logSection($this->name, $e->getMessage());
      }
    }
  }
}
