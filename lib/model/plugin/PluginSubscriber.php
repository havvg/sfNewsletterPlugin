<?php

class PluginSubscriber extends BaseSubscriber
{
  /**
   * Activate the subscription.
   *
   * @return Subscriber (this)
   */
  public function activate()
  {
    $this->setActivatedAt(new DateTime())->setIsActive(true)->save();

    return $this;
  }

  /**
   * Unsubscribe from Newsletters.
   *
   * @return Subscriber (this)
   */
  public function unsubscribe()
  {
    $this->setIsActive(false)->save();

    return $this;
  }

  /**
   * Check whether this Subscriber is subscribed.
   *
   * @return bool
   */
  public function isSubscribed()
  {
    return (bool) $this->getIsActive();
  }

  /**
   * Returns an address object of SwiftMailer for the current Subscriber.
   *
   * @return Swift_Address
   */
  public function getSwiftAddress()
  {
    static $address = array();

    if (empty($address[$this->getId()]))
    {
      $address[$this->getId()] = new Swift_Address($this->getEmail(), $this->getName());
    }

    if ($this->isColumnModified(SubscriberPeer::EMAIL) || $this->isColumnModified(SubscriberPeer::NAME))
    {
      $address[$this->getId()]->setAddress($this->getEmail());
      $address[$this->getId()]->setName($this->getName());
    }

    return $address[$this->getId()];
  }
}
