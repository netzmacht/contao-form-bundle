<?php declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Originally a part of https://github.com/adamquaile/AdamQuaileFieldsetBundle
 * @author Adam Quaile
 */
class FieldsetType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'legend' => '',
                'inherit_data' => true,
                'options' => array(),
                'fields' => array(),
                'label' => false,
            ])
            ->addAllowedTypes('fields', ['array', 'callable']);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!empty($options['fields'])) {
            if (is_callable($options['fields'])) {
                $options['fields']($builder);
            } elseif (is_array($options['fields'])) {
                foreach ($options['fields'] as $field) {
                    $builder->add($field['name'], $field['type'], $field['attr']);
                }
            }
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (false !== $options['legend']) {
            $view->vars['legend'] = $options['legend'];
        }
    }

    public function getName(): string
    {
        return 'fieldset';
    }
}
