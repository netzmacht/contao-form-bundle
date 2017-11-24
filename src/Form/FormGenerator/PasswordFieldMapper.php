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

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class PasswordFieldMapper
 */
class PasswordFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'password';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = PasswordType::class;
}
