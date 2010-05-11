<?php

class sfNewsletterPluginConfiguration extends sfPluginConfiguration
{
  const EXCEPTION_NO_SENDER = 'There is no sender email configured.';

  const EXCEPTION_INVALID_SENDER = 'The configured sender email is invalid.';

  const EXCEPTION_SWIFT_ERROR = 'An error occured sending the email.';

  /**
   * Initialize the plugins configuration.
   *
   * @return void
   */
  public function initialize()
  {
    if (sfConfig::get('app_newsletterplugin_routes_register', true) && in_array('sfSubscription', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('sfNewsletterPluginRouting', 'prependSubscriptionRouting'));
    }

    if (sfConfig::get('app_newsletterplugin_routes_register', true) && in_array('sfNewsletter', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('sfNewsletterPluginRouting', 'prependNewsletterRouting'));
    }
  }

  /**
   * Returns the email from which all emails are sent.
   *
   * @return string
   */
  public static function getFromEmail()
  {
    $from = sfConfig::get('sf_newsletterplugin_from', false);

    if ($from === false || empty($from))
    {
      throw new InvalidArgumentException(self::EXCEPTION_NO_SENDER);
    }

    $validator = new sfEmailValidator(null);
    $error = false;
    if (!$validator->execute($from, $error))
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_SENDER);
    }

    return $from;
  }
}