<?php

class PluginSubscriberPeer extends BaseSubscriberPeer
{
  /**
   * Returns all active Subscribers.
   *
   * @param Criteria $criteria
   * @param PropelPDO $con
   *
   * @return array of Subscriber
   */
  public static function retrieveSubscribed(Criteria $criteria = null, PropelPDO $con = null)
  {
    if ($con === null)
    {
      $con = Propel::getConnection(SubscriberPeer::DATABASE_NAME, Propel::CONNECTION_READ);
    }

    if (empty($criteria))
    {
      $criteria = new Criteria();
    }
    else
    {
      $criteria = clone $criteria;
    }

    $criteria->add(self::IS_ACTIVE, true, Criteria::EQUAL);

    return self::doSelect($criteria, $con);
  }

  /**
   * Returns all Subscribers that are required to activate themselves.
   *
   * @param Criteria $criteria
   * @param PropelPDO $con
   *
   * @return array of Subscriber
   */
  public static function retrievePendingActivation(Criteria $criteria = null, PropelPDO $con = null)
  {
    if ($con === null)
    {
      $con = Propel::getConnection(SubscriberPeer::DATABASE_NAME, Propel::CONNECTION_READ);
    }

    if (empty($criteria))
    {
      $criteria = new Criteria();
    }
    else
    {
      $criteria = clone $criteria;
    }

    $criteria->add(self::IS_ACTIVE, false, Criteria::EQUAL);
    $criteria->add(self::ACTIVATED_AT, null, Criteria::EQUAL);

    return self::doSelect($criteria, $con);
  }

  /**
   * Retrieve a single Subscriber by the given E-Mail.
   *
   * @param string $email
   * @param PropelPDO $con
   *
   * @return Subscriber
   */
  public static function retrieveByEmail($email, PropelPDO $con = null)
  {
    if (!is_string($email))
    {
      throw new InvalidArgumentException('The given email is invalid.');
    }

    if ($con === null)
    {
      $con = Propel::getConnection(SubscriberPeer::DATABASE_NAME, Propel::CONNECTION_READ);
    }

    $criteria = new Criteria();
    $criteria->add(self::EMAIL, $email, Criteria::EQUAL);

    return self::doSelectOne($criteria, $con);
  }
}
