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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

/**
 * Class SubmitFieldMapper
 */
class SubmitFieldMapper implements FormFieldMapper
{
    /**
     * {@inheritDoc}
     */
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'submit';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        return ButtonType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(
        FormFieldModel $model,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = [
            'label' => $model->slabel ?: $model->name,
            'attr'  => [],
        ];

        if ($model->class) {
            $options['attr']['class'] = $model->class;
        }

        // TODO: Maybe support image buttons as well.

        return $options;
    }
}
