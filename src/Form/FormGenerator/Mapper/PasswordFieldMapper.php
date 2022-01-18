<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Assert\AssertionFailedException;
use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

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
     * @param Translator $translator Translator.
     *
     * @throws AssertionFailedException When configuration is broken.
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
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $fieldTypeBuilder, $next);

        $options['type'] = PasswordType::class;

        $options['first_options'] = [
            'label' => $model->label,
        ];

        $options['second_options'] = [
            'label' => $this->translator->trans('MSC.confirmation', [$model->label], 'contao_default'),
        ];

        return $options;
    }
}
