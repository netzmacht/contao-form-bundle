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

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class HiddenFieldMapper
 */
class HiddenFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'hidden';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = HiddenType::class;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->options['label']     = false;
        $this->options['maxlength'] = false;
        $this->options['minlength'] = false;

        $this->attributes = ['value' => 'data'];
    }
}
