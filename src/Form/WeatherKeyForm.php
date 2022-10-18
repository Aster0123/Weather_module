<?php

namespace Drupal\weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provide class for creating form for api key.
 */
class WeatherKeyForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'key_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'weather.settings',
    ];
  }

  /**
   * Creating form for getting api key.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config          = $this->config('weather.settings');
    $form['api_key'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('OpenWeather Api Key'),
      '#description' => $this->t('The api key is required to get weather information in your weather block.'),
      '#default_value' => $config->get('api_key'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the api key.
   */
  public function validateForm(array &$form, FormStateInterface $form_state): bool {
    $key = $form_state->getValue('api_key');
    $res = @file_get_contents('https://api.openweathermap.org/data/2.5/weather?q=Rome&units=metric&appid=' . $key);
    if (!$res) {
      $form_state->setErrorByName('api_key', $this->t('Your API key is not valid.'));
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('weather.settings');
    $config
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
