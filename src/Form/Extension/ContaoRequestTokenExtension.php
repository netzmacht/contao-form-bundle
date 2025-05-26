<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoFormBundle\Form\ContaoRequestTokenType;
use Override;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContaoRequestTokenExtension allows to configure if the contao_request_token is added to a form
 *
 * It recognizes route settings to auto activate / deactivate the request token.
 */
final class ContaoRequestTokenExtension extends AbstractTypeExtension
{
    /**
     * @param RequestScopeMatcher $requestTokenMatcher Request scope matcher.
     * @param RequestStack        $requestStack        Request stack.
     */
    public function __construct(
        private readonly RequestScopeMatcher $requestTokenMatcher,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getExtendedType(): string
    {
        return FormType::class;
    }

    /** {@inheritDoc} */
    #[Override]
    public static function getExtendedTypes(): array
    {
        return [FormType::class];
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $checkToken = $this->isCheckRequestTokenEnabled();

        $resolver->setDefault('contao_request_token', $checkToken);
        $resolver->setDefault('csrf_protection', ! $checkToken);
        $resolver->setAllowedTypes('contao_request_token', 'bool');
    }

    /** {@inheritDoc} */
    #[Override]
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (! $options['contao_request_token']) {
            return;
        }

        if ($view->parent) {
            return;
        }

        if (! $options['compound']) {
            return;
        }

        $factory = $form->getConfig()->getFormFactory();

        $csrfForm = $factory->createNamed(
            'REQUEST_TOKEN',
            ContaoRequestTokenType::class,
            null,
            ['mapped' => false],
        );

        $view->children['REQUEST_TOKEN'] = $csrfForm->createView($view);
    }

    /**
     * Check if contao request token checking is disabled.
     */
    private function isCheckRequestTokenEnabled(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return false;
        }

        if (! $this->requestTokenMatcher->isContaoRequest($request)) {
            return false;
        }

        return $request->attributes->getBoolean('_token_check', true);
    }
}
