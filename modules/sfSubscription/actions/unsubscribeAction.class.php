<?php

class unsubscribeAction extends sfAction
{
  /**
   * The name of the parameter passing the unsubscribe hash.
   *
   * @var string
   */
  const PARAMETER_NAME = 'unsubscribe';

  /**
   * Execute the unsubscribe action.
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
      if ($request->getParameter(self::PARAMETER_NAME) === $subscriber->getUnsubscribeHash() and $subscriber->unsubscribe())
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