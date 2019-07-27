<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;

/**
 * Class SelectFieldMapper
 */
class SelectFieldMapper extends AbstractChoicesFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'select';

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = false;

    /**
     * {@inheritdoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $typeBuilder, $next);

        if ((bool) ($options['multiple'] ?? false) && $model->mSize > 0) {
            $options['attr']['size'] = $model->mSize;
        }

        return $options;
    }
}
