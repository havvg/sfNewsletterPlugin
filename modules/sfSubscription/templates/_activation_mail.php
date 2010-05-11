<?php
use_helper('I18n', 'Url');

$activationLink = url_for('@sf_newsletter_plugin_subscription_activate?id=' . $subscriber->getId() . '&' . activateAction::PARAMETER_NAME . '=' . $subscriber->getActivateHash());

echo __('<p>You subscribed to the newsletter. Please, <a href="%1">verify your email address</a>.</p>', array('%1%' => $activationLink));