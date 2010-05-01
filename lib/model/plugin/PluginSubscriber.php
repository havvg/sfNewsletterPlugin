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
}
