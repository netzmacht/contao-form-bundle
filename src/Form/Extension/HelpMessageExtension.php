<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */
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
    /** @inheritDoc */
    public function getExtendedType(): string
    {
        return FormType::class;
    }

    /** @inheritDoc */
    public function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    /** @inheritDoc */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['help'] = ($options['help'] ?? '');
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['help' => null]);
    }
}
