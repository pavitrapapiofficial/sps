<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Api\Data;

interface GridInterface
{
    const ENTITY_ID = 'entity_id';
    const TITLE = 'reason';
    const CONTENT = 'status';
    const PUBLISH_DATE = 'sort_order';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getReason();

    public function setReason($reason);

    public function getStatus();

    public function setStatus($status);

    public function getSortOrder();

    public function setSortOrder($sortOrder);
}
