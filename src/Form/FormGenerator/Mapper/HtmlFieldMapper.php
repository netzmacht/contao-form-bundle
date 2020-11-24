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
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Netzmacht\ContaoFormBundle\Form\HtmlType;

/**
 * Class HtmlFieldMapper
 */
final class HtmlFieldMapper implements FormFieldMapper
{
    /**
     * {@inheritdoc}
     */
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'html';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(FormFieldModel $model): ?string
    {
        return 'field_' . $model->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        return HtmlType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        return [
            'html'  => $model->html,
            'class' => $model->class ?: ('tl_' . $model->type),
        ];
    }
}
