<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Api\Data;

interface GoogleFeedCategoryInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const LEVEL0 = 'level0';
    const LEVEL1 = 'level1';
    const LEVEL2 = 'level2';
    const LEVEL3 = 'level3';
    const LEVEL4 = 'level4';
    const LEVEL5 = 'level5';
    const LEVEL6 = 'level6';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * set ID.
     *
     * @return $this
     */
    public function setId($entityId);

    /**
     * Get Level0.
     * @return string
     */
    public function getLevel0();

    /**
     * set Level0.
     * @return $this
     */
    public function setLevel0($level0);

    /**
     * Get Level1.
     * @return string
     */
    public function getLevel1();

    /**
     * set Level1.
     * @return $this
     */
    public function setLevel1($level1);

    /**
     * Get Level2.
     * @return string
     */
    public function getLevel2();

    /**
     * set Level2.
     * @return $this
     */
    public function setLevel2($level2);

    /**
     * Get Level3.
     * @return string
     */
    public function getLevel3();

    /**
     * set Level3.
     * @return $this
     */
    public function setLevel3($level3);

    /**
     * Get Level4.
     * @return string
     */
    public function getLevel4();

    /**
     * set Level4.
     * @return $this
     */
    public function setLevel4($level4);

    /**
     * Get Level5.
     * @return string
     */
    public function getLevel5();

    /**
     * set Level5.
     * @return $this
     */
    public function setLevel5($level5);

    /**
     * Get Level6.
     * @return string
     */
    public function getLevel6();

    /**
     * set Level6.
     * @return $this
     */
    public function setLevel6($level6);
}
