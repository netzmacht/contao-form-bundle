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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
    protected $typeClass = RepeatedType::class;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param Translator $translator Translator.
     *
     * @throws \Assert\AssertionFailedException When configuration is broken.
     */
    public function __construct(Translator $translator)
    {
        parent::__construct();

        $this->translator = $translator;

        $this->options['label'] = false;
        $this->options['value'] = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $typeBuilder, $next);

        $options['type'] = PasswordType::class;

        $options['first_options'] = [
            'label' => $model->label
        ];

        $options['second_options'] = [
            'label' => $this->translator->trans('MSC.confirmation', [$model->label], 'contao_default')
        ];

        return $options;
    }
}
