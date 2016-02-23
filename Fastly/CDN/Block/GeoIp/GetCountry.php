<?php
/**
 * Fastly CDN for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Fastly CDN for Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_FASTLY_CDN.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fastly CDN to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fastly
 * @package     Fastly_CDN
 * @copyright   Copyright (c) 2016 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */

namespace Fastly\CDN\Block\GeoIp;

/**
 * This is a just a place holder to insert the ESI tag for GeoIP lookup.
 */
class GetCountry extends \Magento\Framework\View\Element\AbstractBlock
{
    protected function _toHtml()
    {
        return sprintf('<esi:include src="%s" />', $this->getUrl('fastlyCdn/geoip/getcountry'));
    }
}