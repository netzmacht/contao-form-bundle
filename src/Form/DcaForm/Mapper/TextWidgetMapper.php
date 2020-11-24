<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class TextWidgetMapper maps the text widget to the TextType
 */
final class TextWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $widgetType = 'text';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = TextType::class;

    /**
     * Mapping of rgxp to form type.
     *
     * @var array
     */
    private static $mapping = [
        'digit' => NumberType::class,
        'date'  => DateType::class,
        'time'  => TimeType::class,
        'datim' => DateTimeType::class,
        'email' => EmailType::class,
        'url'   => UrlType::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function getTypeClass(string $name, array $config): string
    {
        $rgxp = ($config['eval']['rgxp'] ?? null);
        if (isset(static::$mapping[$rgxp])) {
            return static::$mapping[$rgxp];
        }

        return parent::getTypeClass($name, $config);
    }
}
