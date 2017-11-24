<?php

/**
 * contao-form-bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Contao\FormFieldModel;
use Contao\FormModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type based on the form generator of Contao.
 */
class FormGeneratorType extends AbstractType
{
    /**
     * Contao model repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * @var FieldTypeBuilder
     */
    private $formTypeBuilder;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * FormGeneratorType constructor.
     *
     * @param RepositoryManager $repositoryManager Contao model repository manager.
     * @param EventDispatcher   $eventDispatcher   The event dispatcher.
     */
    public function __construct(RepositoryManager $repositoryManager, EventDispatcher $eventDispatcher)
    {
        $this->repositoryManager = $repositoryManager;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('ignoreUnsupported', true);
        $resolver->setRequired(['formId']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formId     = (int) $options['formId'];
        $formModel  = $this->loadFormModel($formId);
        $formFields = $this->loadFormFields($formId);

        $builder->setMethod($formModel->method);

        $groups  = [];
        $current = null;

        foreach ($formFields as $formField) {
            $config = $this->formTypeBuilder->build($formField);

            if ($config === null) {
                continue;
            }

            if ($config['group'] === 'start') {
                if ($current) {
                    array_push($groups, $current);
                    $current = null;
                }

                $config['model'] = $formField;
                $current         = $config;
            } elseif ($config['group'] === 'stop') {
                if ($current) {
                    $options = $this->formTypeBuilder->setChildrenAsOption($formField, $current);
                    $builder->add($current['name'], $current['type'], $options);
                    $current = array_pop($groups);
                }
            } elseif ($current === null) {
                $builder->add($config['name'], $config['type'], $config['options']);
            } else {
                $current['children'][] = [
                    'name'    => $config['name'],
                    'type'    => $config['type'],
                    'options' => $config['options']
                ];
            }
        }

        while ($current) {
            $options = $this->formTypeBuilder->setChildrenAsOption($current['model'], $current);
            $builder->add($current['name'], $current['type'], $options);

            $current = array_pop($groups);
        }
    }

    /**
     * Load the form model.
     *
     * @param int $formId The form id.
     *
     * @return FormModel
     */
    private function loadFormModel(int $formId): FormModel
    {
        $formModel = $this->repositoryManager->getRepository(FormModel::class)->find($formId);

        if (!$formModel instanceof FormModel) {
            throw new \RuntimeException(sprintf('Form ID "%s" does not exist.', $formId));
        }

        return $formModel;
    }

    /**
     * Find all form fields of the form.
     *
     * @param int $formId The form id.
     *
     * @return FormFieldModel[]|iterable
     */
    private function loadFormFields(int $formId): iterable
    {
        $repository = $this->repositoryManager->getRepository(FormFieldModel::class);
        $collection = $repository->findBy(['.pid=?'], [$formId], ['order' => '.sorting ASC']);

        if ($collection) {
            return $collection;
        }

        return [];
    }
}
