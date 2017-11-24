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

use Contao\FormFieldModel;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildFormGeneratorFormFieldEvent
 */
class BuildFormGeneratorFormFieldEvent extends Event
{
    /**
     * The form field model.
     *
     * @var FormFieldModel
     */
    private $formFieldModel;

    /**
     * Parent form type options.
     *
     * @var array
     */
    private $parentFormTypeOptions;

    /**
     * The form field type.
     *
     * @var string|null
     */
    private $type;

    /**
     * Form field options.
     *
     * @var \ArrayObject
     */
    private $options;

    /**
     * Store if field is supported.
     *
     * @var bool
     */
    private $supported = false;

    /**
     * BuildFormGeneratorFormFieldEvent constructor.
     *
     * @param FormFieldModel $formFieldModel The form field model.
     * @param array          $options        The parent form type options.
     */
    public function __construct(FormFieldModel $formFieldModel, array $options)
    {
        $this->formFieldModel        = $formFieldModel;
        $this->parentFormTypeOptions = $options;
        $this->options               = new \ArrayObject();
    }

    /**
     * Get formFieldModel.
     *
     * @return FormFieldModel
     */
    public function getFormFieldModel(): FormFieldModel
    {
        return $this->formFieldModel;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getParentFormTypeOptions(): array
    {
        return $this->parentFormTypeOptions;
    }

    /**
     * Get type.
     *
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param null|string $type Type.
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->supported = true;
        $this->type      = $type;

        return $this;
    }

    /**
     * Get options.
     *
     * @return \ArrayObject
     */
    public function getOptions(): \ArrayObject
    {
        return $this->options;
    }

    /**
     * Check if field is supported as mapped field.
     *
     * @return bool
     */
    public function isSupported(): bool
    {
        return $this->supported;
    }
}
