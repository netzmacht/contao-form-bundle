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
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_filter;
use function implode;

/**
 * This extension adds three options to every form type: widget_class, be_widget_class and fe_widget_class.
 *
 * The widget class is compiled to view variable widget class.
 */
final class WidgetClassExtension extends AbstractTypeExtension
{
    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * WidgetClassExtension constructor.
     *
     * @param RequestScopeMatcher $scopeMatcher
     */
    public function __construct(RequestScopeMatcher $scopeMatcher)
    {
        $this->scopeMatcher = $scopeMatcher;
    }

    /** @inheritDoc */
    public function buildView(FormView $view, FormInterface $form, array $options) : void
    {
        $classes = [$options['widget']['class']];

        if ($this->scopeMatcher->isBackendRequest()) {
            $classes[] = $options['widget']['be_class'];
        }

        if ($this->scopeMatcher->isFrontendRequest()) {
            $classes[] = $options['widget']['fe_class'];
        }

        $view->vars['widget']['class'] = implode(' ', array_filter($classes));
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefault(
            'widget',
            // @codingStandardsIgnoreStart - static function is not supported
            static function (OptionsResolver $widgetResolver) : void {
                $widgetResolver->setDefaults(
                    [
                        'class'    => '',
                        'be_class' => '',
                        'fe_class' => ''
                    ]
                );
            }
        // @codingStandardsIgnoreStop
        );
    }

    /** @inheritDoc */
    public function getExtendedType() : string
    {
        return FormType::class;
    }

    /** @inheritDoc */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
