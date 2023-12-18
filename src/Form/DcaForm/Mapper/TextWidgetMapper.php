<?php

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
     */
    protected string $widgetType = 'text';

    /**
     * The type class.
     */
    protected string $typeClass = TextType::class;

    /**
     * Mapping of rgxp to form type.
     *
     * @var array<string,class-string>
     */
    private static array $mapping = [
        'digit' => NumberType::class,
        'date'  => DateType::class,
        'time'  => TimeType::class,
        'datim' => DateTimeType::class,
        'email' => EmailType::class,
        'url'   => UrlType::class,
    ];

    /** {@inheritDoc} */
    public function getTypeClass(string $name, array $config): string
    {
        $rgxp = ($config['eval']['rgxp'] ?? null);
        if (isset(static::$mapping[$rgxp])) {
            return static::$mapping[$rgxp];
        }

        return parent::getTypeClass($name, $config);
    }
}
