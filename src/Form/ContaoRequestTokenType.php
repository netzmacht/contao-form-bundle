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

namespace Netzmacht\ContaoFormBundle\Form;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Hidden input type for the Contao request token
 */
final class ContaoRequestTokenType extends HiddenType
{
    /**
     * The csrf token storage.
     *
     * @var CsrfTokenProvider
     */
    private $tokenProvider;

    /**
     * RequestTokenType constructor.
     *
     * @param CsrfTokenProvider $csrfTokenProvider The csrf token provider.
     */
    public function __construct(CsrfTokenProvider $csrfTokenProvider)
    {
        $this->tokenProvider = $csrfTokenProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options) : void
    {
        parent::buildView($view, $form, $options);

        $view->vars['full_name'] = 'REQUEST_TOKEN';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults(['data' => $this->tokenProvider->getTokenValue()]);
    }
}
