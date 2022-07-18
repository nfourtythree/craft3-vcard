<?php
/**
 * vCard plugin for Craft CMS 4.x
 *
 * vCard generator plugin for Craft CMS 4
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2019 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard;

use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;

use craft\web\UrlManager;
use nfourtythree\vcard\models\VCardSettings;
use nfourtythree\vcard\services\VCardService;
use nfourtythree\vcard\variables\VCardVariable;

use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Vcard
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 *
 * @method VCardSettings getSettings()
 * @property-read VCardService $service
 */
class VCard extends Plugin
{
    /**
     * @var VCardVariable;
     */
    public static VCardVariable $vcardVariable;

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();

        $this->setComponents([
            'service' => VCardService::class,
        ]);

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            static function(RegisterUrlRulesEvent $event) {
                $event->rules['vcard/download'] = 'vcard/default';
            }
        );

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, static function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('vCard', VCardVariable::class);
        });
    }

    /**
     * Returns the vCard service
     *
     * @return VCardService
     * @throws InvalidConfigException
     */
    public function getService(): VCardService
    {
        return $this->get('service');
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new VCardSettings();
    }
}
