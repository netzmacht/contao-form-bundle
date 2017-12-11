<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Filter;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

/**
 * Class ContaoInputFilter
 */
class ContaoInputFilter
{
    /**
     * Contao input adapter.
     *
     * @var Adapter|Input
     */
    private $inputAdapter;

    /**
     * Config adapter.
     *
     * @var Adapter|Config
     */
    private $configAdapter;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * ContaoInputFilter constructor.
     *
     * @param Adapter|Input       $inputAdapter The input adapter.
     * @param Adapter|Config      $configAdapter The config adapter.
     * @param RequestScopeMatcher $scopeMatcher
     */
    public function __construct($inputAdapter, $configAdapter, RequestScopeMatcher $scopeMatcher)
    {
        $this->inputAdapter = $inputAdapter;
        $this->scopeMatcher = $scopeMatcher;
        $this->configAdapter = $configAdapter;
    }

    /**
     * Filter the given data.
     *
     * @param mixed $data           The data.
     * @param bool  $decodeEntities If true entities will be encoded.
     * @param bool  $allowHtml
     * @param bool  $raw
     *
     * @return mixed
     */
    public function filter($data, bool $decodeEntities = false, bool $allowHtml = false, bool $raw = false)
    {
        if ($raw) {
            $data = $this->inputAdapter->preserveBasicEntities($data);
        } else {
            $data = $this->inputAdapter->decodeEntities($data);
        }

        $data = $this->inputAdapter->xssClean($data, true);

        if (!$raw) {
            $data = $this->inputAdapter->stripTags($data, $this->getAllowedTags($allowHtml));

            if (!$decodeEntities) {
                $data = $this->inputAdapter->encodeSpecialChars($data);
            }
        }

        if (!$this->scopeMatcher->isBackendRequest()) {
            $data = $this->inputAdapter->encodeInsertTags($data);
        }

        return $data;
    }

    /**
     * @param bool $allowHtml
     *
     * @return string
     */
    private function getAllowedTags(bool $allowHtml): string
    {
        if ($allowHtml) {
            return (string) $this->configAdapter->get('allowedTags');
        }

        return '';
    }
}
