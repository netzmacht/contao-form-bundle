<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Validator\Constraints;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Date;
use Contao\Idna;
use Contao\StringUtil;
use Contao\Validator;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function array_shift;
use function explode;
use function html_entity_decode;
use function in_array;
use function sprintf;
use function str_replace;
use function strncmp;
use function strpos;
use function substr_count;

final class RgxpValidator extends ConstraintValidator
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var Invoker */
    private $invoker;

    /** @var ContaoFrameworkInterface */
    private $framework;

    /**
     * RgxpValidator constructor.
     *
     * @param TranslatorInterface      $translator
     * @param Invoker                  $invoker
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(TranslatorInterface $translator, Invoker $invoker, ContaoFrameworkInterface $framework)
    {
        $this->translator = $translator;
        $this->invoker    = $invoker;
        $this->framework = $framework;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Rgxp) {
            throw new UnexpectedTypeException($constraint, Rgxp::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        try {
            $this->doValidate($value, $constraint);
        } catch (InvalidArgumentException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }

    private function doValidate($value, Rgxp $constraint): void
    {
        $rgxp = $constraint->getRgxp();

        switch ($rgxp) {
            case strncmp($rgxp, 'digit_', 6) === 0:
                // Special validation rule for style sheets
                $textual = explode('_', $rgxp);
                array_shift($textual);

                if (in_array($value, $textual, true) || strncmp($value, '$', 1) === 0) {
                    break;
                }
            // no break

            case 'digit':
                // Support decimal commas and convert them automatically (see #3488)
                if (substr_count($value, ',') === 1 && strpos($value, '.') === false) {
                    $value = str_replace(',', '.', $value);
                }

                if (!Validator::isNumeric($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'natural':
                if (!Validator::isNatural($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'alpha':
                if (!Validator::isAlphabetic($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'alnum':
                if (!Validator::isAlphanumeric($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'extnd':
                if (!Validator::isExtendedAlphanumeric(html_entity_decode($value))) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'date':
                // Date::getInputFormat and Date::getNumericDateFormat() expects that the framework is initialized
                $this->framework->initialize();

                if (!Validator::isDate($value)) {
                    throw new InvalidArgumentException(
                        $this->translateError('date'),
                        Date::getInputFormat(Date::getNumericDateFormat())
                    );
                }
                // Validate the date (see #5086)
                try {
                    new Date($value, Date::getNumericDateFormat());
                } catch (\OutOfBoundsException $e) {
                    throw new InvalidArgumentException($this->translateError('invalidDate'), $value);
                }
                break;

            case 'time':
                // Date::getInputFormat and Date::getNumericTimeFormat() expects that the framework is initialized
                $this->framework->initialize();

                if (!Validator::isTime($value)) {
                    throw new InvalidArgumentException(
                        $this->translateError('time'),
                        Date::getInputFormat(Date::getNumericTimeFormat())
                    );
                }
                break;

            case 'datim':
                // Date::getInputFormat and Date::getNumericDatimFormat() expects that the framework is initialized
                $this->framework->initialize();

                if (!Validator::isDatim($value)) {
                    throw new InvalidArgumentException(
                        $this->translateError('dateTime'),
                        Date::getInputFormat(Date::getNumericDatimFormat())
                    );
                }

                // Validate the date (see #5086)
                try {
                    new Date($value, Date::getNumericDatimFormat());
                } catch (\OutOfBoundsException $e) {
                    throw new InvalidArgumentException($this->translateError('invalidDate'), $value);
                }
                break;

            case 'friendly':
                [$name, $value] = StringUtil::splitFriendlyEmail($value);
            // no break

            case 'email':
                if (!Validator::isEmail($value)) {
                    $this->invalidValue($constraint);
                }

                if ($rgxp === 'friendly' && !empty($name)) {
                    $value = $name . ' [' . $value . ']';
                }
                break;

            case 'emails':
                // Check whether the current value is list of valid e-mail addresses
                $arrEmails = StringUtil::trimsplit(',', $value);

                foreach ($arrEmails as $strEmail) {
                    $strEmail = Idna::encodeEmail($strEmail);

                    if (!Validator::isEmail($strEmail)) {
                        $this->invalidValue($constraint);
                        break;
                    }
                }
                break;

            case 'url':
                if (!Validator::isUrl($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'alias':
                if (!Validator::isAlias($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'folderalias':
                if (!Validator::isFolderAlias($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'phone':
                if (!Validator::isPhone(html_entity_decode($value))) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'prcnt':
                if (!Validator::isPercent($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'locale':
                if (!Validator::isLocale($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'language':
                if (!Validator::isLanguage($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'google+':
                if (!Validator::isGooglePlusId($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            case 'fieldname':
                if (!Validator::isFieldName($value)) {
                    $this->invalidValue($constraint);
                }
                break;

            // HOOK: pass unknown tags to callback functions
            default:
                // Only invoke hook if widget is given
                $widget = $constraint->getWidget();
                if ($widget === null) {
                    break;
                }

                $widget = clone $widget;

                // Make sure that the framework is initialized, otherwise the hooks are not loaded
                $this->framework->initialize();

                if (isset($GLOBALS['TL_HOOKS']['addCustomRegexp'])
                    && \is_array($GLOBALS['TL_HOOKS']['addCustomRegexp'])) {
                    foreach ($GLOBALS['TL_HOOKS']['addCustomRegexp'] as $callback) {
                        $break = $this->invoker->invoke($callback, [$rgxp, $value, $widget]);

                        // Stop the loop if a callback returned true
                        if ($break === true) {
                            break;
                        }
                    }

                    if ($widget->hasErrors()) {
                        throw new InvalidArgumentException($widget->getErrorsAsString());
                    }
                }
                break;
        }
    }

    /**
     * Create an invalid argument exception with translated error message.
     *
     * @return void
     *
     * @throws InvalidArgumentException With the translated error message.
     */
    private function invalidValue(Rgxp $constraint): void
    {
        throw new InvalidArgumentException(
            sprintf($this->translateError($constraint->getRgxp()), $constraint->getLabel())
        );
    }

    private function translateError(string $key): string
    {
        return $this->translator->trans('ERR.' . $key, [], 'contao_default');
    }
}
