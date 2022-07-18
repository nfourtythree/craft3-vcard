<?php
/**
 * vCard plugin for Craft CMS 4.x
 *
 * vCard generator plugin for Craft CMS 4
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2022 Nathaniel Hammond (nfourtythree)
 */
namespace nfourtythree\vcard\services;

use Craft;
use craft\base\Component;
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use JeroenDesloovere\VCard\VCard as VCardLib;
use nfourtythree\vcard\models\VCard_AddressModel;
use nfourtythree\vcard\models\VCard_EmailModel;

use nfourtythree\vcard\models\VCard_PhoneNumberModel;

use nfourtythree\vcard\models\VCard_UrlModel;
use nfourtythree\vcard\models\VCardModel;
use nfourtythree\vcard\VCard;
use RuntimeException;
use yii\base\ExitException;
use yii\web\HttpException;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * VCardService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCardService extends Component
{
    /**
     * @param array $options
     * @return string|null
     */
    public function generateLink(array $options = []): ?string
    {
        if ($this->_validateOptions($options)) {
            $encodedOptions = $this->encodeUrlParam($options);
            return UrlHelper::actionUrl('vcard/default/index', ['vcard' => $encodedOptions]);
        }

        return null;
    }

    /**
     * @param array $options
     * @return string|void
     * @throws ExitException
     * @throws HttpException
     * @throws RangeNotSatisfiableHttpException
     */
    public function generateOutput(array $options = [])
    {
        return $this->generateVcard($options, "output");
    }

    /**
     * @param array $options
     * @param string $action
     * @return string|void
     * @throws ExitException
     * @throws HttpException
     * @throws RangeNotSatisfiableHttpException
     */
    public function generateVcard(array $options = [], string $action = "download")
    {
        if ($this->_validateOptions($options)) {
            if (isset($options['address'])) {
                $options['address'] = $this->_populateAddressModel($options['address']);
            }

            if (isset($options['phoneNumber'])) {
                $options['phoneNumber'] = $this->_populatePhoneNumberModel($options['phoneNumber']);
            }

            if (isset($options['email'])) {
                $options['email'] = $this->_populateEmailModel($options['email']);
            }

            if (isset($options['url'])) {
                $options['url'] = $this->_populateUrlModel($options['url']);
            }

            $vcard = new VCardModel($options);

            if ($vcard->validate()) {
                $vcardData = $this->_createVcardData($vcard);

                switch ($action) {
                    case 'output':
                        return $vcardData->getOutput();
                    case 'download':
                    default:
                        $output = $vcardData->getOutput();

                        Craft::$app->getResponse()->sendContentAsFile($output, sprintf('%s.%s', $vcardData->getFilename(), $vcardData->getFileExtension()), [
                            'mimeType' => $vcardData->getContentType(),
                        ]);
                        Craft::$app->end();
                }
            } else {
                foreach ($vcard->getErrors() as $error) {
                    Craft::error($error, __METHOD__);
                }
            }
        }
    }

    /**
     * @param $options
     * @return bool
     */
    private function _validateOptions($options): bool
    {
        if (empty($options)) {
            Craft::error(Craft::t('vcard', 'vCard Parameters must be supplied'), __METHOD__);
        }

        return true;
    }

    /**
     * @param array $address
     * @return VCard_AddressModel[]|string
     */
    private function _populateAddressModel(array $address): array|string
    {
        if (!empty($address)) {
            // check to see if we are dealing with multiple addresses
            if (ArrayHelper::isAssociative($address)) {
                $address = [$address];
            }

            $tmp = [];
            foreach ($address as $a) {
                $tmp[] = new VCard_AddressModel($a);
            }
            return $tmp;
        }

        return '';
    }

    /**
     * @param $phoneNumber
     * @return VCard_PhoneNumberModel[]|string
     */
    private function _populatePhoneNumberModel($phoneNumber): string|array
    {
        if (is_array($phoneNumber) or (is_string($phoneNumber) and $phoneNumber)) {
            $phoneNumber = $this->_createArrayFromString("number", $phoneNumber);
            // check to see if we are dealing with multiple phoneNumbers
            if (count($phoneNumber) == count($phoneNumber, COUNT_RECURSIVE)) {
                return [new VCard_PhoneNumberModel($phoneNumber)];
            }

            $tmp = [];
            foreach ($phoneNumber as $row) {
                $tmp[] = new VCard_PhoneNumberModel($this->_createArrayFromString("number", $row));
            }

            return $tmp;
        }

        return '';
    }

    /**
     * @param $key
     * @param $value
     * @return string[]
     */
    private function _createArrayFromString($key, $value): array
    {
        if (is_string($value)) {
            return [$key => $value];
        }
        // only switch if it is a string
        return $value;
    }

    /**
     * @param $email
     * @return VCard_EmailModel[]
     */
    private function _populateEmailModel($email): array
    {
        if (is_array($email)) {
            if (ArrayHelper::isAssociative($email)) {
                $email = [$email];
            }

            $emailModels = [];
            foreach ($email as $row) {
                $emailModels[] = new VCard_EmailModel($this->_createArrayFromString("address", $row));
            }

            return $emailModels;
        }

        if (is_string($email) and $email) {
            $email = $this->_createArrayFromString("address", $email);
            return [new VCard_EmailModel($email)];
        }

        return [];
    }

    /**
     * @param $url
     * @return VCard_UrlModel[]
     */
    private function _populateUrlModel($url): array
    {
        if (is_array($url)) {
            if (ArrayHelper::isAssociative($url)) {
                $url = [$url];
            }

            $urlModels = [];
            foreach ($url as $row) {
                $urlModels[] = new VCard_UrlModel($this->_createArrayFromString("address", $row));
            }

            return $urlModels;
        }

        if (is_string($url) and $url) {
            $url = $this->_createArrayFromString("address", $url);
            return [new VCard_UrlModel($url)];
        }

        return [];
    }

    /**
     * @param VCardModel $vcardModel
     * @return VCardLib
     */
    private function _createVcardData(VCardModel $vcardModel): VCardLib
    {
        $vcard = new VCardLib();

        $vcard->addName($vcardModel->lastName, $vcardModel->firstName, $vcardModel->additional, $vcardModel->prefix, $vcardModel->suffix);

        if ($vcardModel->company) {
            $vcard->addCompany($vcardModel->company);
        }

        if ($vcardModel->jobTitle) {
            $vcard->addJobtitle($vcardModel->jobTitle);
        }

        if ($vcardModel->url and is_array($vcardModel->url)) {
            foreach ($vcardModel->url as $url) {
                if (($url instanceof VCard_UrlModel) && $url->validate()) {
                    $vcard->addUrl(
                        $url->address,
                        $this->_fixTypeString($url->type)
                    );
                }
            }
        }

        if ($vcardModel->address and is_array($vcardModel->address)) {
            foreach ($vcardModel->address as $address) {
                if (($address instanceof VCard_AddressModel) && $address->validate()) {
                    $vcard->addAddress(
                        $address->name,
                        $address->extended,
                        $address->street,
                        $address->city,
                        $address->region,
                        $address->zip,
                        $address->country,
                        $this->_fixTypeString($address->type)
                    );
                }
            }
        }

        if ($vcardModel->phoneNumber and is_array($vcardModel->phoneNumber)) {
            foreach ($vcardModel->phoneNumber as $phoneNumber) {
                if (($phoneNumber instanceof VCard_PhoneNumberModel) && $phoneNumber->validate()) {
                    $vcard->addPhoneNumber(
                        $phoneNumber->number,
                        $this->_fixTypeString($phoneNumber->type)
                    );
                }
            }
        }

        if ($vcardModel->email and is_array($vcardModel->email)) {
            foreach ($vcardModel->email as $email) {
                if (($email instanceof VCard_EmailModel) && $email->validate()) {
                    $vcard->addEmail(
                        $email->address,
                        $this->_fixTypeString($email->type)
                    );
                }
            }
        }

        if ($vcardModel->photo) {
            $vcard->addPhoto($vcardModel->photo);
        }

        if ($vcardModel->note) {
            $vcard->addNote($vcardModel->note);
        }

        return $vcard;
    }

    /**
     * @param string $type
     * @return string
     */
    private function _fixTypeString(string $type): string
    {
        if ($type && strpos($type, 'TYPE=') === false) {
            $type = 'TYPE=' . $type;
        }

        return $type;
    }

    /**
     * @param array $options
     * @return string
     */
    public function encodeUrlParam(array $options = []): string
    {
        $optionsString = serialize($options);

        return $this->encrypt($optionsString);
    }

    /**
     * @param string $optionsString
     * @return mixed
     */
    public function decodeUrlParam(string $optionsString = ""): mixed
    {
        $optionsString = $this->decrypt($optionsString);
        return unserialize($optionsString);
    }

    /**
     * @param $string
     * @return string
     */
    protected function encrypt($string): string
    {
        $key = VCard::getInstance()->getSettings()->salt;
        if (!$key || $key == 's34s4L7') {
            throw new RuntimeException('You must provide a valid salt key.');
        }

        $key = md5($key);
        $iv = substr(md5($key), 0, 16);

        return rtrim(
            strtr(
                base64_encode(
                    openssl_encrypt($string, 'aes128', md5($key), OPENSSL_RAW_DATA, $iv)
                ),
                '+/', '-_'
            ), '='
        );
    }

    /**
     * @param $string
     * @return string
     */
    public function decrypt($string): string
    {
        $key = VCard::getInstance()->getSettings()->salt;
        if (!$key || $key == 's34s4L7') {
            throw new RuntimeException('You must provide a valid salt key.');
        }

        $key = md5($key);
        $iv = substr(md5($key), 0, 16);

        return rtrim(
            openssl_decrypt(
                base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT)),
                'aes128',
                md5($key),
                OPENSSL_RAW_DATA,
                $iv
            ),
            "\0"
        );
    }
}
