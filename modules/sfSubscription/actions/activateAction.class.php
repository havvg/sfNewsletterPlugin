<?php

class activateAction extends sfAction
{
  /**
   * The name of the parameter passing the activation hash.
   *
   * @var string
   */
  const PARAMETER_NAME = 'activate';

  /**
   * Execute the activate action.
   * Requires routing to the Subscriber model.
   *
   * @param sfWebRequest $request
   *
   * @return string
   */
  public function execute($request)
  {
    /* @var $subscriber Subscriber */
    $subscriber = $this->getRoute()->getObject();
    if (!($subscriber instanceof Subscriber))
    {
      return sfView::ERROR;
    }
    else
    {
      if ($request->getParameter(self::PARAMETER_NAME) === $subscriber->getActivateHash() and $subscriber->activate())
      {
        return sfView::SUCCESS;
      }
      else
      {
        return sfView::ERROR;
      }
    }
  }
}