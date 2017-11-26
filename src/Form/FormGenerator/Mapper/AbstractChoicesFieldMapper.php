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

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class AbstractChoicesFieldMapper
 */
abstract class AbstractChoicesFieldMapper extends AbstractFieldMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = ChoiceType::class;

    /**
     * Multiple. If null, the value is read from the model.
     *
     * @var bool|null
     */
    protected $multiple = null;

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = true;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->options['maxlength'] = false;
        $this->options['minlength'] = false;
        $this->options['rgxp']      = false;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options             = parent::getOptions($model, $typeBuilder, $next);
        $options['multiple'] = $this->multiple === null ? (bool) $model->multiple : $this->multiple;
        $options['expanded'] = $this->expanded;
        $options['choices']  = $this->buildChoices($model->options);

        return $options;
    }

    /**
     * Build the choices.
     *
     * @param mixed $options Given options.
     *
     * @return array
     */
    private function buildChoices($options): array
    {
        $options = StringUtil::deserialize($options);
        $choices = [];

        if (empty($options) || !is_array($options)) {
            return $choices;
        }

        $group = null;

        foreach ($options as $option) {
            if ($option['group']) {
                $group = $option['label'];
            }

            if ($group) {
                $choices[$group][$option['label']] = $option['value'];
            } else {
                $choices[][$option['label']] = $option['value'];
            }
        }

        return $choices;
    }
}
