<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HelpMessageExtension adds help text option to form types.
 */
final class HelpMessageExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return FormType::class;
    }

    /** @inheritDoc */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    /** @inheritDoc */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['help'] = ($options['help'] ?? '');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['help' => null]);
    }
}
