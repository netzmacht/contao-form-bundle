<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm;

use Symfony\Component\Form\FormBuilderInterface;

class WidgetTypeBuilder
{
    /**
     * Form field mappers.
     *
     * @var WidgetMapper[]
     */
    private $mappers;

    /**
     * @param WidgetMapper[] $mappers Form field mappers.
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * Build the form field type.
     *
     * @param string               $name    Form field name.
     * @param array<string,mixed>  $config  Form field config.
     * @param Context              $context Data container context.
     * @param callable             $next    Callback to get the next form field model.
     * @param FormBuilderInterface $builder Form builder.
     */
    public function build(
        string $name,
        array $config,
        Context $context,
        callable $next,
        FormBuilderInterface $builder
    ): void {
        foreach ($this->mappers as $mapper) {
            if (! $mapper->supports($name, $config)) {
                continue;
            }

            $builder->add(
                $name,
                $mapper->getTypeClass($name, $config),
                $mapper->getOptions($name, $config, $context, $this, $next)
            );

            $mapper->configure($builder->get($name), $config, $context);
        }
    }
}
