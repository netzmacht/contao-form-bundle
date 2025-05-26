<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CoreBundle\Picker\PickerBuilderInterface;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Override;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_pad;
use function explode;

/**
 * Class RichTextExtension enables RTE support in Contao backend scope
 */
final class RichTextExtension extends AbstractTypeExtension
{
    /**
     * @param PickerBuilderInterface $pickerBuilder Picker builder.
     * @param RequestScopeMatcher    $scopeMatcher  Scope matcher.
     */
    public function __construct(
        private readonly PickerBuilderInterface $pickerBuilder,
        private readonly RequestScopeMatcher $scopeMatcher,
    ) {
    }

    /** {@inheritDoc} */
    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['rte'] === false || ! $this->scopeMatcher->isBackendRequest()) {
            $view->vars['rte'] = '';

            return;
        }

        [$file, $type] = array_pad(explode('|', $options['rte'], 2), 2, '');

        $fileBrowserTypes = [];
        foreach (['file' => 'image', 'link' => 'file'] as $context => $fileBrowserType) {
            if (! $this->pickerBuilder->supportsContext($context)) {
                continue;
            }

            $fileBrowserTypes[] = $fileBrowserType;
        }

        $template = new BackendTemplate('be_' . $file);
        $template->setData(
            [
                'selector' => $view->vars['id'],
                'type'     => $type,
                'fileBrowserTypes' => $fileBrowserTypes,
                'source' => $options['rte_source'],
                'language' => Backend::getTinyMceLanguage(),
            ],
        );

        $view->vars['rte'] = $template->parse();
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('rte', false);
        $resolver->setDefault('rte_source', '');
    }

    public function getExtendedType(): string
    {
        return TextareaType::class;
    }

    /** @inheritDoc */
    #[Override]
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
    }
}
