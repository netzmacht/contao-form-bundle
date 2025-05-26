<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HtmlType extends AbstractType
{
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['class' => '']);
        $resolver->setRequired(['html']);
    }

    /** {@inheritDoc} */
    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['html']  = ($options['html'] ?? null);
        $view->vars['class'] = ($options['class'] ?? '');
    }
}
