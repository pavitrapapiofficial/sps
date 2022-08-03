<?php

namespace PurpleCommerce\StoreLocator\Api\Data;

interface StorerecordInterface
{
    const ID                 = 'id';
    const STORENAME         = 'storename';
    const ADD1               = 'add1';
    const ADD2               = 'add2';
    const CITY               = 'city';
    const REGION             = 'region';
    const COUNTRY            = 'country';
    const POSTCODE           = 'postcode';
    const PHONE              = 'phone';
    const LINK               = 'link';
    const LAT                = 'lat';
    const LNG                = 'lng';
    const MISCL                = 'misc1';
    const LOCATORID          = 'locatorid';
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Store Name
     *
     * @return int|null
     */
    public function getStorename();

    /**
     * Get Add1
     *
     * @return int|null
     */
    public function getAdd1();
    /** 
     * Get allowed order
     *
     * @return int|null
     */
    public function getLocatorid();
    /**
     * Get Add2
     *
     * @return int|null
     */
    public function getAdd2();

    /**
     * Get Customer ID
     *
     * @return int|null
     */
    public function getCity();

    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getRegion();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getCountry();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getPostcode();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getPhone();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getLink();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getLat();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getLng();
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getMiscl();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function setId($id);

    /**
     * Get Store Name
     *
     * @return int|null
     */
    public function setStorename($storename);

    /**
     * Get Add1
     *
     * @return int|null
     */
    public function setAdd1($add1);

    /**
     * Get Add2
     *
     * @return int|null
     */
    public function setAdd2($add2);

    /**
     * Get Customer ID
     *
     * @return int|null
     */
    public function setCity($city);

    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setRegion($region);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setCountry($country);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setPostcode($postcode);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setPhone($phone);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setLink($link);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setLat($lat);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setLng($lng);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setMiscl($miscl);
    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function setLocatorid($locatorid);
}
