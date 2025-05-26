<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Override;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Hidden input type for the Contao request token
 */
final class ContaoRequestTokenType extends HiddenType
{
    /** @param CsrfTokenProvider $tokenProvider The csrf token provider. */
    public function __construct(private readonly CsrfTokenProvider $tokenProvider)
    {
    }

    /** {@inheritDoc} */
    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['full_name'] = 'REQUEST_TOKEN';
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data' => $this->tokenProvider->getTokenValue()]);
    }
}
