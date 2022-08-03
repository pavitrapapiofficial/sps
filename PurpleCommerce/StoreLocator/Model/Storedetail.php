<?php
namespace PurpleCommerce\StoreLocator\Model;

use PurpleCommerce\StoreLocator\Api\Data\StoredetailInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Storedetail extends AbstractModel implements StoredetailInterface, IdentityInterface
{
    const CACHE_TAG = 'storelocator_storedetail';
    /**
     * @var string
     */
    protected $_cacheTag = 'storelocator_storedetail';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'storelocator_storedetail';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\PurpleCommerce\StoreLocator\Model\ResourceModel\Storedetail::class);
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getStorename()
    {
        return $this->getData(self::STORENAME);
    }

    public function setStorename($storename)
    {
        return $this->setData(self::STORENAME, $storename);
    }

    public function getAdd1()
    {
        return $this->getData(self::ADD1);
    }

    public function setAdd1($add1)
    {
        return $this->setData(self::ADD1, $add1);
    }

    public function getAdd2()
    {
        return $this->getData(self::ADD2);
    }

    public function setAdd2($add2)
    {
        return $this->setData(self::ADD2, $add2);
    }

    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }

    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    public function setLink($link)
    {
        return $this->setData(self::LINK, $link);
    }
    public function getLat()
    {
        return $this->getData(self::LAT);
    }

    public function setLat($lat)
    {
        return $this->setData(self::LAT, $lat);
    }
    public function getLng()
    {
        return $this->getData(self::LNG);
    }
    public function setLng($lng)
    {
        return $this->setData(self::LNG, $lng);
    }
    public function getMiscl()
    {
        return $this->getData(self::MISCL);
    }
    public function setMiscl($miscl)
    {
        return $this->setData(self::MISCL, $miscl);
    }
    public function getLocatorid()
    {
        return $this->getData(self::LOCATORID);
    }
    public function setLocatorid($locatorid)
    {
        return $this->setData(self::LOCATORID, $locatorid);
    }
    
}
