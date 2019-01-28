<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

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
    }
}
