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

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Hidden input type for the Contao request token
 */
final class ContaoRequestTokenType extends HiddenType
{
    /**
     * The csrf token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenManager;

    /**
     * The csrf token name.
     *
     * @var string
     */
    private $tokenName;

    /**
     * RequestTokenType constructor.
     *
     * @param TokenStorageInterface $tokenStorage The csrf token storage.
     * @param string                $tokenName    The csrf token name.
     */
    public function __construct(TokenStorageInterface $tokenStorage, string $tokenName)
    {
        $this->tokenManager = $tokenStorage;
        $this->tokenName    = $tokenName;
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
        $resolver->setDefaults(['data' => $this->tokenManager->getToken($this->tokenName)]);
    }
}
