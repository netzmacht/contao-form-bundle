<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
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
     * Contao form generator field type builder.
     *
     * @var FieldTypeBuilder
     */
    private $fieldTypeBuilder;

    /**
     * FormGeneratorType constructor.
     *
     * @param RepositoryManager $repositoryManager Contao model repository manager.
     * @param FieldTypeBuilder  $fieldTypeBuilder  Contao form type builder.
     */
    public function __construct(RepositoryManager $repositoryManager, FieldTypeBuilder $fieldTypeBuilder)
    {
        $this->repositoryManager = $repositoryManager;
        $this->fieldTypeBuilder  = $fieldTypeBuilder;
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
    public function buildForm(FormBuilder $builder, array $options)
    {
        $formId    = (int) $options['formId'];
        $formModel = $this->loadFormModel($formId);

        $this->applyFormModelSettings($formModel, $builder);

        $formFields = $this->loadFormFields($formId);
        $next       = function (?callable $condition = null) use ($formFields) {
            if (($next = next($formFields)) === false) {
                return null;
            }

            if (!$condition || $condition($next)) {
                return $next;
            }

            prev($formFields);

            return null;
        };

        while (($formField = $next())) {
            $config = $this->fieldTypeBuilder->build($formField, $next);
            $builder->add(...$config);
        }
    }

    /**
     * Apply the form model settings.
     *
     * @param FormModel   $formModel The form model.
     * @param FormBuilder $builder   The form builder.
     *
     * @return void
     */
    private function applyFormModelSettings(FormModel $formModel, FormBuilder $builder): void
    {
        $builder->setMethod($formModel->method);

        if ($formModel->novalidate) {
            $builder->setAttribute('novalidate', 'novalidate');
        }

        $attributes = StringUtil::deserialize($formModel->attributes, true);

        if (!empty($attributes[0])) {
            $builder->setAttribute('id', $attributes[0]);
        }

        if (!empty($attributes[1])) {
            $builder->setAttribute('class', $attributes[1]);
        }
    }

    /**
     * Load the form model.
     *
     * @param int $formId The form id.
     *
     * @return FormModel
     *
     * @throws \RuntimeException When form could not be found.
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
     * @return FormFieldModel[]|array
     */
    private function loadFormFields(int $formId): array
    {
        $repository = $this->repositoryManager->getRepository(FormFieldModel::class);
        $collection = $repository->findBy(['.pid=?'], [$formId], ['order' => '.sorting ASC']);

        if ($collection) {
            return $collection->fetchAll();
        }

        return [];
    }
}
