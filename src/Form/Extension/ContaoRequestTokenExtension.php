<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoFormBundle\Form\ContaoRequestTokenType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContaoRequestTokenExtension allows to configure if the contao_request_token is added to a form
 *
 * It recognize route settings to auto activate / deactivate the request token.
 */
final class ContaoRequestTokenExtension extends AbstractTypeExtension
{
    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $requestTokenMatcher;

    /**
     * Request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ContaoRequestTokenExtension constructor.
     *
     * @param RequestScopeMatcher $requestTokenMatcher Request scope matcher.
     * @param RequestStack        $requestStack        Request stack.
     */
    public function __construct(RequestScopeMatcher $requestTokenMatcher, RequestStack $requestStack)
    {
        $this->requestTokenMatcher = $requestTokenMatcher;
        $this->requestStack        = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType() : string
    {
        return FormType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedTypes() : array
    {
        return [FormType::class];
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $checkToken = $this->isCheckRequestTokenEnabled();

        $resolver->setDefault('contao_request_token', $checkToken);
        $resolver->setDefault('csrf_protection', !$checkToken);

        $resolver->setAllowedTypes('contao_request_token', 'bool');
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['contao_request_token'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();

            $csrfForm = $factory->createNamed(
                'REQUEST_TOKEN',
                ContaoRequestTokenType::class,
                null,
                ['mapped' => false,]
            );

            $view->children['REQUEST_TOKEN'] = $csrfForm->createView($view);
        }
    }

    /**
     * Check if contao request token checking is disabled.
     *
     * @return bool
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
