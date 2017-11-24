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

namespace Netzmacht\ContaoFormBundle\Event;

use Contao\FormModel;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Event is triggered when a form model is generated
 */
class BuildFormGeneratorFormEvent extends Event
{
    /**
     * The form model.
     *
     * @var FormModel
     */
    private $formModel;

    /**
     * The form builder.
     *
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * Form type options.
     *
     * @var array
     */
    private $options;

    /**
     * BuildFormGeneratorFormEvent constructor.
     *
     * @param FormModel   $formModel   The form model.
     * @param array       $options     Form type options.
     * @param FormBuilder $formBuilder Form builder
     */
    public function __construct(FormModel $formModel, array $options, FormBuilder $formBuilder)
    {
        $this->formModel   = $formModel;
        $this->options     = $options;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Get form model.
     *
     * @return FormModel
     */
    public function getFormModel(): FormModel
    {
        return $this->formModel;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get form builder.
     *
     * @return FormBuilder
     */
    public function getFormBuilder(): FormBuilder
    {
        return $this->formBuilder;
    }
}
