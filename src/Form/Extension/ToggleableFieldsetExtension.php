<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Netzmacht\ContaoFormBundle\Form\FieldsetType;
use Override;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ToggleableFieldsetExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return FieldsetType::class;
    }

    /** {@inheritDoc} */
    #[Override]
    public static function getExtendedTypes(): iterable
    {
        return [FieldsetType::class];
    }

    /** {@inheritDoc} */
    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['root_id']    = $form->getRoot()->getName();
        $view->vars['toggleable'] = ($options['toggleable'] ?? false);
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['toggleable' => false]);
    }
}
