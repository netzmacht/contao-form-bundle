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

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ToggleableFieldsetExtension
 */
final class ToggleableFieldsetExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function getExtendedType(): string
    {
        return FieldsetType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedTypes(): iterable
    {
        return [FieldsetType::class];
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['root_id']    = $form->getRoot()->getName();
        $view->vars['toggleable'] = ($options['toggleable'] ?? false);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['toggleable' => false]);
    }
}
