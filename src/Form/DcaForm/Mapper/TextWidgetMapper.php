<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Netzmacht\ContaoFormBundle\Form\DcaForm\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\DcaForm\FormFieldMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextWidgetMapper implements FormFieldMapper
{
    public function supports(array $config): bool
    {
        $inputType = $config['inputType'] ?? null;

        return 'text' === $inputType;
    }

    public function getTypeClass(array $config): string
    {
        return TextType::class;
    }

    public function getOptions(array $config, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        return [
            'label' => $config['label'][0] ?? '',
            'help'  => $config['label'][1] ?? null,
            'required' => $config['mandatory'] ?? false,
            'widget' => [
                'be_class' => $config['eval']['tl_class']
            ],
        ];
    }
}
