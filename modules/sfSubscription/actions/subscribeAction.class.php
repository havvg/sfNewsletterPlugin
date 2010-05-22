<?php

class subscribeAction extends sfAction
{
  const ERROR_TYPE_FORM = 1;
  const ERROR_TYPE_DATABASE = 2;
  const ERROR_TYPE_CONFIG = 3;
  const ERROR_TYPE_MAIL = 4;

  /**
   * Execute the subscribe action.
   *
   * @param sfWebRequest $request
   *
   * @return string
   */
  public function execute($request)
  {
    $this->form = new sfSubscribeForm();
    if ($request->getMethod() === sfWebRequest::POST)
    {
      try
      {
        $this->form->bind($request->getPostParameter(sfSubscribeForm::PARAMETER_NAME));

        if ($this->form->isValid() and $this->form->save())
        {
          if ($this->sendActivationMail($this->form->getObject()))
          {
            return sfView::SUCCESS;
          }
          else
          {
            $this->errorType = self::ERROR_TYPE_MAIL;
            $this->form->getObject()->delete();
            return sfView::ERROR;
          }
        }
        else
        {
          $this->errorType = self::ERROR_TYPE_FORM;
          return sfView::INPUT;
        }
      }
      catch (InvalidArgumentException $e)
      {
        $this->errorType = self::ERROR_TYPE_CONFIG;
        $this->errorMessage = $e->getMessage();

        if ($this->form->getObject())
        {
          $this->form->getObject()->delete();
        }

        return sfView::ERROR;
      }
      catch (PropelException $e)
      {
        $this->errorType = self::ERROR_TYPE_DATABASE;
        $this->errorMessage = $e->getMessage();

        if ($this->form->getObject())
        {
          $this->form->getObject()->delete();
        }

        return sfView::ERROR;
      }
    }
    else
    {
      return sfView::INPUT;
    }
  }

  /**
   * Send an email with an activation link to verify the subscriber is the owner of the email address.
   *
   * @throws InvalidArgumentException
   * @throws Exception
   *
   * @return bool
   */
  protected function sendActivationMail(Subscriber $subscriber)
  {
    try
    {
      $from = sfNewsletterPluginConfiguration::getFromEmail();

      $mailer = new Swift(new Swift_Connection_NativeMail());
      $message = new Swift_Message(sfConfig::get('sf_newsletter_plugin_activation_mail_subject', 'Newsletter Subscription'), $this->getPartial('activation_mail', array('subscriber' => $subscriber)), 'text/html');

      $sent = $mailer->send($message, $subscriber->getEmail(), $from);
      $mailer->disconnect();

      return ($sent === 1);
    }
    catch (Exception $e)
    {
      if (!empty($mailer))
      {
        $mailer->disconnect();
      }

      throw $e;
    }
  }
}