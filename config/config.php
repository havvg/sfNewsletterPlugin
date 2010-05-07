<?php

if (sfConfig::get('app_newsletterplugin_routes_register', true) && in_array('sfSubscription', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('sfNewsletterPluginRouting', 'prependSubscriptionRouting'));
}

if (sfConfig::get('app_newsletterplugin_routes_register', true) && in_array('sfNewsletter', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('sfNewsletterPluginRouting', 'prependNewsletterRouting'));
}