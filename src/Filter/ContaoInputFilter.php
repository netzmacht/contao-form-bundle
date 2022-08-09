<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Filter;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

class ContaoInputFilter
{
    /**
     * Contao input adapter.
     *
     * @var Adapter<Input>
     */
    private Adapter $inputAdapter;

    /**
     * Config adapter.
     *
     * @var Adapter<Config>
     */
    private Adapter $configAdapter;

    /**
     * Request scope matcher.
     */
    private RequestScopeMatcher $scopeMatcher;

    /**
     * @param Adapter<Input>      $inputAdapter  The input adapter.
     * @param Adapter<Config>     $configAdapter The config adapter.
     * @param RequestScopeMatcher $scopeMatcher  Scope matcher.
     */
    public function __construct(Adapter $inputAdapter, Adapter $configAdapter, RequestScopeMatcher $scopeMatcher)
    {
        $this->inputAdapter  = $inputAdapter;
        $this->scopeMatcher  = $scopeMatcher;
        $this->configAdapter = $configAdapter;
    }

    /**
     * Filter to get raw data.
     *
     * @param mixed $data Given data.
     *
     * @return mixed
     */
    public function filterRaw($data)
    {
        $data = $this->inputAdapter->__call('preserveBasicEntities', [$data]);
        $data = $this->inputAdapter->__call('xssClean', [$data, true]);

        if (! $this->scopeMatcher->isBackendRequest()) {
            $data = $this->inputAdapter->__call('encodeInsertTags', [$data]);
        }

        return $data;
    }

    /**
     * Filter the given data.
     *
     * @param mixed $data           The data.
     * @param bool  $decodeEntities If true entities will be encoded.
     * @param bool  $allowHtml      Allow html.
     *
     * @return mixed
     */
    public function filter($data, bool $decodeEntities = false, bool $allowHtml = false)
    {
        $data = $this->inputAdapter->__call('decodeEntities', [$data]);
        $data = $this->inputAdapter->__call('xssClean', [$data, true]);
        $data = $this->inputAdapter->__call('stripTags', [$data, $this->getAllowedTags($allowHtml)]);

        if (! $decodeEntities) {
            $data = $this->inputAdapter->__call('encodeSpecialChars', [$data]);
        }

        if (! $this->scopeMatcher->isBackendRequest()) {
            $data = $this->inputAdapter->__call('encodeInsertTags', [$data]);
        }

        return $data;
    }

    /**
     * Get all allowed tags as string.
     *
     * @param bool $allowHtml Allowed html.
     */
    private function getAllowedTags(bool $allowHtml): string
    {
        if ($allowHtml) {
            return (string) $this->configAdapter->__call('get', ['allowedTags']);
        }

        return '';
    }
}
