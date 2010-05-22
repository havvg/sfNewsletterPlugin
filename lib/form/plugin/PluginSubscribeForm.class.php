<?php

class PluginSubscribeForm extends BaseFormPropel
{
  const PARAMETER_NAME = 'newsletter_subscribe_form';

  public function configure()
  {
    $this->setWidgets(array(
      'name' => new sfWidgetFormInput(),
      'email' => new sfWidgetFormInput(),
    ));

    // check for valid input
    $this->setValidators(array(
      'name' => new sfValidatorString(array('required' => true, 'min_length' => 10, 'max_length' => 255)),
      'email' => new sfValidatorEmail(),
    ));

    $this->validatorSchema->setPostValidator(new sfValidatorPropelUnique(array('model' => 'Subscriber', 'column' => 'email', 'required' => true)));

    $this->widgetSchema->setNameFormat(self::PARAMETER_NAME . '[%s]');
  }

  /**
   * Returns the name of the related model.
   *
   * @return string
   */
  public function getModelName()
  {
    return 'Subscriber';
  }
}