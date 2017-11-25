<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;
use Contao\StringUtil;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class TextareaFieldMapper
 */
class TextareaFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'textarea';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = TextareaType::class;

    /**
     * {@inheritDoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $typeBuilder, $next);
        $size    = StringUtil::deserialize($model->size);

        if ($size[0]) {
            $options['attr']['rows'] = $size[0];
        }

        if ($size[1]) {
            $options['attr']['cols'] = $size[1];
        }

        return $options;
    }
}
