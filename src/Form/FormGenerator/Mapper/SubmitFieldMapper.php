<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class SubmitFieldMapper implements FormFieldMapper
{
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'submit';
    }

    public function getName(FormFieldModel $model): ?string
    {
        return 'submit_' . $model->id;
    }

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

        return $options;
    }
}
