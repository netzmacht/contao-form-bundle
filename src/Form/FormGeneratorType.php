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

        $builder->setMethod($formModel->method);

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
            $config = $this->formTypeBuilder->build($formField, $next);
            $builder->add(...$config);
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
