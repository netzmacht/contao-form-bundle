<?php

/**
 * contao-form-bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\EventListener;

use Netzmacht\ContaoFormBundle\Event\BuildFormGeneratorFormFieldEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class MapDefaultFormTypesListener
 *
 * @package Netzmacht\ContaoFormBundle\EventListener\FormGenerator
 */
class MapFormGeneratorFieldsListener
{
    private static $mapping = [
        'textarea' => TextareaType::class,
        'password' => PasswordType::class,
        'select'   => ChoiceType::class,
        'radio'    => RadioType::class,
        'checkbox' => CheckboxType::class,
        'upload'   => FileType::class,
        'hidden'   => HiddenType::class,
        'submit'   => SubmitType::class,

//        'explanation' => 'FormExplanation',
//        'html'        => 'FormHtml',
//        'fieldset'    => 'FormFieldset',
//        'captcha'     => 'FormCaptcha',
    ];

    private static $textTypeMapping = [
        'digit' => NumberType::class,
        'date'  => DateType::class,
        'time'  => TimeType::class,
        'datim' => DateTimeType::class,
        'email' => EmailType::class,
        'url'   => UrlType::class
    ];

    /**
     * Map the form generator form field types.
     *
     * @param BuildFormGeneratorFormFieldEvent $event The event.
     *
     * @return void
     */
    public function mapFormTypes(BuildFormGeneratorFormFieldEvent $event): void
    {
        $formField = $event->getFormFieldModel();
        $type      = null;

        if (isset(static::$mapping[$formField->type])) {
            $event->setType(static::$mapping[$formField->type]);
        }

        if ($formField->type !== 'text') {
            return;
        }

        if (isset(static::$textTypeMapping[$formField->rgxp])) {
            $event->setType(static::$textTypeMapping[$formField->rgxp]);
        } else {
            $event->setType(TextType::class);
        }
    }
}
