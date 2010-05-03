<?php

class PluginNewsletterPeer extends BaseNewsletterPeer
{
  /**
   * Retrieve all Newsletters that are scheduled since a given DateTime and have not been sent yet.
   *
   * @param DateTime $datetime
   * @param Criteria $criteria
   * @param PropelPDO $con
   *
   * @return array of Newsletter
   */
  public static function retrieveScheduled(DateTime $datetime, Criteria $criteria = null, PropelPDO $con = null)
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

    $criteria->add(self::SCHEDULED_AT, $datetime, Criteria::LESS_EQUAL);
    $criteria->add(self::SENT_AT, null, Criteria::EQUAL);

    return self::doSelectJoinContentType($criteria, $con, Criteria::INNER_JOIN);
  }

  /**
   * Retrieve a Newsletter by its name.
   *
   * @throws InvalidArgumentException
   *
   * @param string $name
   * @param Criteria $criteria
   * @param PropelPDO $con
   *
   * @return Newsletter
   */
  public static function retrieveByName($name, Criteria $criteria = null, PropelPDO $con = null)
  {
    if (!is_string($name))
    {
      throw new InvalidArgumentException('The given name is invalid.');
    }

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

    $criteria->add(self::NAME, $name, Criteria::EQUAL);

    return self::doSelectOne($criteria, $con);
  }
}
