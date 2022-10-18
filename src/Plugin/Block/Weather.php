<?php

namespace Drupal\weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Weather' Block.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("Weather block"),
 *   category = @Translation("Weather World"),
 * )
 */
class Weather extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactory $configFactory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * Building block for getting city and temperature.
   */
  public function build() {
    $config = $this->getConfiguration();
    $city = $config['city'];
    $key = $this->getMySetting();
    $res = file_get_contents('https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&units=metric&appid=' . $key);
    $res = json_decode($res, TRUE);
    $tempCel = intval(round($res['main']['temp']));
    $temp = sprintf("%s %s °C", $city, $tempCel);

    return [
      '#theme' => 'weather',
      '#temp' => $temp,
    ];
  }

  /**
   * Gets my setting.
   */
  public function getMySetting() {
    $config = $this->configFactory->get('weather.settings');
    return $config->get('api_key');
  }

  /**
   * Creating form for city name in the block.
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $config = $this->getConfiguration();
    $getIp = json_decode(file_get_contents('http://ip-api.com/json/'), TRUE);
    $ip = $getIp['city'];

    $form['city'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#description' => $this->t("The name of the city for which the weather will be displayed. Recommended city is ") . $ip,
      "#default_value" => $config['city'] ?? $this->t('Lutsk'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $pattern = "/^([a-zA-Z-' ]+)$/";
    $city = $form_state->getValue('city');
    $key = $this->getMySetting();
    $res = @file_get_contents('https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&units=metric&appid=' . $key);
    if (empty(
    $form_state->getValue('city'))) {
      $form_state->setErrorByName('city', $this->t('Fields should not be empty.'));
    }
    if (!preg_match($pattern, $form_state->getValue('city'))) {
      $form_state->setErrorByName('city', $this->t('City name invalid.'));
    }
    if (!$res) {
      $form_state->setErrorByName('city',
        $this->t('OpenWeather Api does not support this city. Please, write the other city name.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['city'] = $form_state->getValue('city');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
