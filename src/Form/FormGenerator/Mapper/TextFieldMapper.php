<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class TextFieldMapper
 */
class TextFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'text';

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
        'url'   => UrlType::class
    ];

    /**
     * {@inheritDoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        if (isset(static::$mapping[$model->rgxp])) {
            return static::$mapping[$model->rgxp];
        }

        return parent::getTypeClass($model);
    }
}
