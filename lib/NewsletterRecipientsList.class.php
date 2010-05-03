<?php

class NewsletterRecipientList extends Swift_RecipientList implements Countable
{
  /**
   * Returns the amount of registered recipients.
   * Implements Countable Interface.
   *
   * @return int
   */
  public function count()
  {
    return count($this->bcc) + count($this->cc) + count($this->to);
  }

  /**
   * Creates a list of recipients containing all active subscribers.
   *
   * @throws RuntimeExcpetion
   *
   * @return NewsletterRecipientList
   */
  public static function createInstanceActiveSubscribers()
  {
    $recipientList = new self();

    $activeSubscribers = SubscriberPeer::retrieveSubscribed();
    if (!empty($activeSubscribers))
    {
      /* @var $eachSubscriber Subscriber */
      foreach ($activeSubscribers as $eachSubscriber)
      {
        $recipientList->addBcc($eachSubscriber->getSwiftAddress());
      }

      return $recipientList;
    }
    else
    {
      throw new RuntimeException('There are no active subscribers.');
    }
  }
}