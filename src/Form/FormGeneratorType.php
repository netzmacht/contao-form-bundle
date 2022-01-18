<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Model;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_values;
use function current;
use function in_array;
use function next;
use function sprintf;

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
     * @param RepositoryManager $repositoryManager Contao model repository manager.
     * @param FieldTypeBuilder  $fieldTypeBuilder  Contao form type builder.
     */
    public function __construct(RepositoryManager $repositoryManager, FieldTypeBuilder $fieldTypeBuilder)
    {
        $this->repositoryManager = $repositoryManager;
        $this->fieldTypeBuilder  = $fieldTypeBuilder;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'ignoreUnsupported' => true,
                'ignore'            => [],
            ]
        );

        $resolver->setRequired(['formId']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options): void
    {
        $formId    = (int) $options['formId'];
        $formModel = $this->loadFormModel($formId);

        $this->applyFormModelSettings($formModel, $builder);

        $formFields = $this->loadFormFields($formId);
        $next       = $this->createNextCallback($formFields);

        while (($formField = $next())) {
            if (in_array($formField->type, $options['ignore'], true)) {
                continue;
            }

            $config = $this->fieldTypeBuilder->build($formField, $next);
            if ($config === null) {
                continue;
            }

            $builder->add(...array_values($config));
        }
    }

    /**
     * Apply the form model settings.
     *
     * @param FormModel   $formModel The form model.
     * @param FormBuilder $builder   The form builder.
     */
    private function applyFormModelSettings(FormModel $formModel, FormBuilder $builder): void
    {
        $builder->setMethod($formModel->method);

        if ($formModel->novalidate) {
            $builder->setAttribute('novalidate', 'novalidate');
        }

        $attributes = StringUtil::deserialize($formModel->attributes, true);

        if (isset($attributes[0]) && $attributes[0] !== '') {
            $builder->setAttribute('id', $attributes[0]);
        }

        if (! isset($attributes[1]) || $attributes[1] === '') {
            return;
        }

        $builder->setAttribute('class', $attributes[1]);
    }

    /**
     * Load the form model.
     *
     * @param int $formId The form id.
     *
     * @throws RuntimeException When form could not be found.
     */
    private function loadFormModel(int $formId): FormModel
    {
        $formModel = $this->repositoryManager->getRepository(FormModel::class)->find($formId);

        if (! $formModel instanceof FormModel) {
            throw new RuntimeException(sprintf('Form ID "%s" does not exist.', $formId));
        }

        return $formModel;
    }

    /**
     * Find all form fields of the form.
     *
     * @param int $formId The form id.
     *
     * @return FormFieldModel[]|Model[]|array
     */
    private function loadFormFields(int $formId): array
    {
        $repository = $this->repositoryManager->getRepository(FormFieldModel::class);
        $collection = $repository->findBy(['.pid=?', '.invisible=?'], [$formId, ''], ['order' => '.sorting ASC']);

        if ($collection) {
            return $collection->getModels();
        }

        return [];
    }

    /**
     * Crate the next callback.
     *
     * @param FormFieldModel[]|array $formFields Form fields array.
     */
    private function createNextCallback(&$formFields): callable
    {
        return static function (?callable $condition = null) use (&$formFields) {
            $current = current($formFields);

            if ($current === false) {
                return null;
            }

            if (! $condition || $condition($current)) {
                next($formFields);

                return $current;
            }

            return null;
        };
    }
}
