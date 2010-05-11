<?php

class readAction extends sfAction
{
  /**
   * Execute the read action.
   * Requires routing to the Newsletter model.
   *
   * @param sfWebRequest $request
   *
   * @return string
   */
  public function execute($request)
  {
    $newsletter = $this->getRoute()->getObject();
    if (!($newsletter instanceof Newsletter))
    {
      return sfView::ERROR;
    }
    else
    {
      $this->newsletter = $newsletter;
      return sfView::SUCCESS;
    }
  }
}