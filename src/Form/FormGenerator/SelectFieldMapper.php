<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

/**
 * Class SelectFieldMapper
 */
class SelectFieldMapper extends AbstractChoicesFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'select';

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = false;
}
