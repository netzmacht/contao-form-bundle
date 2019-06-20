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

namespace Netzmacht\ContaoFormBundle\Form\Extension;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CoreBundle\Picker\PickerBuilderInterface;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function explode;

/**
 * Class RichTextExtension enables RTE support in Contao backend scope
 */
final class RichTextExtension extends AbstractTypeExtension
{
    /**
     * Contao picker builder.
     *
     * @var PickerBuilderInterface
     */
    private $pickerBuilder;

    /**
     * Request Scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * RichTextExtension constructor.
     *
     * @param PickerBuilderInterface $pickerBuilder Picker builder.
     * @param RequestScopeMatcher    $scopeMatcher  Scope matcher.
     */
    public function __construct(PickerBuilderInterface $pickerBuilder, RequestScopeMatcher $scopeMatcher)
    {
        $this->pickerBuilder = $pickerBuilder;
        $this->scopeMatcher  = $scopeMatcher;
    }

    /** @inheritDoc */
    public function buildView(FormView $view, FormInterface $form, array $options) : void
    {
        if ($options['rte'] === false || !$this->scopeMatcher->isBackendRequest()) {
            $view->vars['rte'] = '';

            return;
        }

        [$file, $type] = explode('|', $options['rte'], 2);

        $fileBrowserTypes = [];
        foreach (array('file' => 'image', 'link' => 'file') as $context => $fileBrowserType) {
            if ($this->pickerBuilder->supportsContext($context)) {
                $fileBrowserTypes[] = $fileBrowserType;
            }
        }

        $template = new BackendTemplate('be_' . $file);
        $template->setData(
            [
                'selector' => $view->vars['id'],
                'type'     => $type,
                'fileBrowserTypes' => $fileBrowserTypes,
                'source' => $options['rte_source'],
                'language' => Backend::getTinyMceLanguage()
            ]
        );

        $view->vars['rte'] = $template->parse();
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefault('rte', false);
        $resolver->setDefault('rte_source', '');
    }

    /** @inheritDoc */
    public function getExtendedType() : string
    {
        return TextareaType::class;
    }

    /** @inheritDoc */
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
    }
}
