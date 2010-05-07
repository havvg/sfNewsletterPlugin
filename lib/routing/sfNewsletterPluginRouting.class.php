<?php

require_once(dirname(__FILE__) . '/../../modules/sfSubscription/actions/activateAction.class.php');
require_once(dirname(__FILE__) . '/../../modules/sfSubscription/actions/subscribeAction.class.php');
require_once(dirname(__FILE__) . '/../../modules/sfSubscription/actions/unsubscribeAction.class.php');

class sfNewsletterPluginRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function prependSubscriptionRouting(sfEvent $event)
  {
    $subscribeRoute = new sfRoute('/newsletter/subscription', array(
      // sfRequestRoute
      'action' => 'subscribe',
      'module' => 'sfSubscription',
    ));

    $activateRoute = new sfPropelRoute('/newsletter/subscription/activate/:id',
      // defaults
      array(
        // sfRequestRoute
        'action' => 'activate',
        'module' => 'sfSubscription',
      ),
      // requirements
      array(
        'id' => '\d+'
      ),
      // options
      array(
        // sfObjectRoute
        'allow_empty' => false,
        'model' => 'Subscriber',
        'type' => 'object',
    ));

    $unsubscribeRoute = new sfPropelRoute('/newsletter/subscription/unsubscribe/:email',
      // defaults
      array(
        // sfRequestRoute
        'action' => 'unsubscribe',
        'module' => 'sfSubscription',
      ),
      // requirements
      array(
        'email' => '.+',
      ),
      // options
      array(
        // sfObjectRoute
        'allow_empty' => false,
        'model' => 'Subscriber',
        'type' => 'object',
    ));

    $r = $event->getSubject();
    $r->prependRoute('sf_newsletter_plugin_subscription_subscribe', $subscribeRoute);
    $r->prependRoute('sf_newsletter_plugin_subscription_activate', $activateRoute);
    $r->prependRoute('sf_newsletter_plugin_subscription_unsubscribe', $unsubscribeRoute);
  }

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function prependNewsletterRouting(sfEvent $event)
  {
    $readRoute = new sfPropelRoute('/newsletter/read/:id',
      // defaults
      array(
        // sfRequestRoute
        'action' => 'read',
        'module' => 'sfNewsletter',
      ),
      // requirements
      array(
        'id' => '\d+',
      ),
      // options
      array(
      // sfObjectRoute
      'allow_empty' => false,
      'model' => 'Newsletter',
      'type' => 'object',
    ));

    $r = $event->getSubject();
    $r->prependRoute('sf_newsletter_plugin_newsletter', $readRoute);
  }
}