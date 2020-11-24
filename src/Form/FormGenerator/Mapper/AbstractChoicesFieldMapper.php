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
    protected $multiple;

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
        $options                = parent::getOptions($model, $typeBuilder, $next);
        $options['multiple']    = $this->multiple === null ? (bool) $model->multiple : $this->multiple;
        $options['expanded']    = $this->expanded;
        $options['placeholder'] = false;

        $options = $this->parseOptionsConfig($options, $model->options);

        return $options;
    }

    /**
     * Build the choices.
     *
     * @param array $options Form type options.
     * @param mixed $values  Given options.
     *
     * @return array
     */
    private function parseOptionsConfig(array $options, $values): array
    {
        $values             = StringUtil::deserialize($values);
        $options['choices'] = [];

        if (empty($values) || !is_array($values)) {
            return $options;
        }

        $group = null;

        foreach ($values as $option) {
            if ($option['group']) {
                $group = $option['label'];
                continue;
            }

            if ($group) {
                $options['choices'][$group][$option['label']] = $option['value'];
            } else {
                $options['choices'][$option['label']] = $option['value'];
            }

            if ($option['default']) {
                if ($options['multiple']) {
                    $options['data'][] = $option['value'];
                } else {
                    $options['data'] = $option['value'];
                }
            }
        }

        return $options;
    }
}
