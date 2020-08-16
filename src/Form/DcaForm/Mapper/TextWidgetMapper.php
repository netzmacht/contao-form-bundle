<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoFormBundle\Form\DcaForm\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\DcaForm\FormFieldMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextWidgetMapper implements FormFieldMapper
{
    public function supports(string $name, array $config): bool
    {
        $inputType = $config['inputType'] ?? null;

        return 'text' === $inputType;
    }

    public function getTypeClass(string $name, array $config): string
    {
        return TextType::class;
    }

    public function getOptions(
        string $name,
        array $config,
        Definition $definition,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
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
