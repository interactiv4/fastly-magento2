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
 * @package     Fastly_Cdn
 * @copyright   Copyright (c) 2016 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */
namespace Fastly\Cdn\Helper;

use Fastly\Cdn\Model\Api;
use Fastly\Cdn\Helper\Vcl;
use Fastly\Cdn\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class AutomaticCompression
 *
 * @package Fastly\Cdn\Helper
 */
class AutomaticCompression extends AbstractHelper
{
    /**
     * @var Api
     */
    private $api;
    /**
     * @var \Fastly\Cdn\Helper\Vcl
     */
    private $vcl;

    /**
     * @param Context $context
     * @param Api $api
     * @param \Fastly\Cdn\Helper\Vcl $vcl
     */
    public function __construct(
        Context $context,
        Api $api,
        Vcl $vcl
    ) {
        parent::__construct($context);
        $this->api = $api;
        $this->vcl = $vcl;
    }

    public function updateVclSnippet($value)
    {
        $service = $this->api->checkServiceDetails();
        $activeVersion = $this->vcl->getCurrentVersion($service->versions);

        $snippet = $this->api->getSnippet($activeVersion, Config::IMAGE_SETTING_NAME);
        if (!$snippet) {
            return;
        }
        $snippetData = $this->buildSnippetData($snippet, $value);

        $clone = $this->api->cloneVersion($activeVersion);
        $this->api->uploadSnippet($clone->number, $snippetData);
        $this->api->activateVersion($clone->number);
        $this->api->addComment($clone->number, ['comment' => 'Magento Module updated the Image Optimization snippet']);
    }

    protected function buildSnippetData($snippet, $value)
    {
        $pattern = '/set req\.url = querystring\.set\(req\.url, "optimize", "(.*?)"\);/';
        preg_match($pattern, $snippet->content, $match);
        $replacement = str_replace('{value}', $value, 'set req.url = querystring.set(req.url, "optimize", "{value}");');

        if (isset($match[1])) {
            if ($value === 'off') {
                $replacement = '';
            }
            $content = preg_replace($pattern, $replacement, $snippet->content);
        } else {
            if ($value !== 'off') {
                $content = $snippet->content . $replacement;
            }
        }

        return [
            'name' => $snippet->name,
            'type' => $snippet->type,
            'dynamic' => $snippet->dynamic,
            'content' => $content,
            'priority' => $snippet->priority,
        ];
    }
}
