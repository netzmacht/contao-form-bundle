<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function is_array;
use function is_callable;

/**
 * Originally a part of https://github.com/adamquaile/AdamQuaileFieldsetBundle by Adam Quaile
 */
class FieldsetType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'legend' => '',
                'inherit_data' => true,
                'options' => [],
                'fields' => [],
                'label' => false,
            ])
            ->addAllowedTypes('fields', ['array', 'callable']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (empty($options['fields'])) {
            return;
        }

        if (is_callable($options['fields'])) {
            $options['fields']($builder);
        } elseif (is_array($options['fields'])) {
            foreach ($options['fields'] as $field) {
                $builder->add($field['name'], $field['type'], $field['attr']);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['legend'] === false) {
            return;
        }

        $view->vars['legend'] = $options['legend'];
    }

    public function getName(): string
    {
        return 'fieldset';
    }
}
