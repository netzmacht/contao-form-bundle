<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class TextareaWidgetMapper maps the textarea widget to the TextareaType
 */
final class TextareaWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The type class.
     */
    protected string $widgetType = 'textarea';

    /**
     * The type class.
     */
    protected string $typeClass = TextareaType::class;

    /**
     * {@inheritDoc}
     */
    public function __construct(ContaoFramework $framework)
    {
        parent::__construct($framework);

        $this->attributes['rows'] = true;
        $this->attributes['cols'] = true;
    }
}
