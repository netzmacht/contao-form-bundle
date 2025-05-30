<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Override;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class TextFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'text';

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

    #[Override]
    public function getTypeClass(FormFieldModel $model): string
    {
        return self::$mapping[$model->rgxp] ?? parent::getTypeClass($model);
    }
}
