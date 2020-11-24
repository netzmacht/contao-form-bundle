<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;


use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextareaWidgetMapper extends AbstractWidgetMapper
{
    protected $widgetType = 'textarea';

    protected $typeClass = TextareaType::class;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        parent::__construct($framework);

        $this->attributes['rows'] = true;
        $this->attributes['cols'] = true;
    }
}
