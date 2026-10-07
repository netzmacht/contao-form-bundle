<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Tests\Validator\Constraints;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Widget;
use DateTimeImmutable;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\ContaoFormBundle\Tests\Fixtures\TestWidget;
use Netzmacht\ContaoFormBundle\Validator\Constraints\Rgxp;
use Netzmacht\ContaoFormBundle\Validator\Constraints\RgxpValidator;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;
use function vsprintf;

/** @SuppressWarnings(PHPMD.Superglobals) */
final class RgxpValidatorTest extends TestCase
{
    private const string LABEL = 'My field';

    /** @var array<string,mixed> */
    private array $backup = [];

    #[Override]
    protected function setUp(): void
    {
        foreach (['TL_CONFIG', 'TL_LANG', 'TL_HOOKS'] as $key) {
            $this->backup[$key] = $GLOBALS[$key] ?? null;
        }

        $GLOBALS['TL_CONFIG']['dateFormat']     = 'Y-m-d';
        $GLOBALS['TL_CONFIG']['timeFormat']     = 'H:i';
        $GLOBALS['TL_CONFIG']['datimFormat']    = 'Y-m-d H:i';
        $GLOBALS['TL_LANG']['DATE']             = ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm'];
        $GLOBALS['TL_HOOKS']['addCustomRegexp'] = [];
    }

    #[Override]
    protected function tearDown(): void
    {
        foreach ($this->backup as $key => $value) {
            if ($value === null) {
                unset($GLOBALS[$key]);
                continue;
            }

            $GLOBALS[$key] = $value;
        }
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function validValues(): iterable
    {
        yield 'digit integer' => ['digit', '123'];
        yield 'digit negative decimal' => ['digit', '-1.5'];
        yield 'digit decimal comma' => ['digit', '1,5'];
        yield 'digit float value' => ['digit', 1.5];
        yield 'digit int value' => ['digit', 42];
        yield 'digit_ textual value' => ['digit_auto_inherit', 'auto'];
        yield 'digit_ variable' => ['digit_auto', '$foo'];
        yield 'digit_ number' => ['digit_auto', '12'];
        yield 'natural' => ['natural', '12'];
        yield 'natural int value' => ['natural', 12];
        yield 'alpha' => ['alpha', 'Foo bar-baz'];
        yield 'alnum' => ['alnum', 'Foo_1 bar'];
        yield 'extnd' => ['extnd', 'Foo & bar!'];
        yield 'date' => ['date', '2024-02-29'];
        yield 'date object' => ['date', new DateTimeImmutable()];
        yield 'time' => ['time', '13:45'];
        yield 'datim' => ['datim', '2024-02-29 13:45'];
        yield 'email' => ['email', 'foo@example.org'];
        yield 'friendly' => ['friendly', 'Foo Bar [foo@example.org]'];
        yield 'friendly plain email' => ['friendly', 'foo@example.org'];
        yield 'emails' => ['emails', 'foo@example.org, bar@example.org'];
        yield 'url' => ['url', 'https://example.org/foo?bar=baz'];
        yield 'alias' => ['alias', 'foo-bar.baz_1'];
        yield 'folderalias' => ['folderalias', 'foo/bar-baz'];
        yield 'phone' => ['phone', '+49 (0)123 / 456-789'];
        yield 'prcnt' => ['prcnt', '42'];
        yield 'locale' => ['locale', 'de_DE'];
        yield 'language' => ['language', 'de-DE'];
        yield 'fieldname' => ['fieldname', 'foo[bar]_baz-1'];
        yield 'unknown rgxp without hooks' => ['custom', 'anything'];
        yield 'empty string' => ['digit', ''];
        yield 'null' => ['digit', null];
        yield 'array' => ['digit', ['abc']];
    }

    /** @return iterable<string, array{string, mixed, string}> */
    public static function invalidValues(): iterable
    {
        yield 'digit' => ['digit', 'abc', 'digit: ' . self::LABEL];
        yield 'digit two commas' => ['digit', '1,000,5', 'digit: ' . self::LABEL];
        yield 'digit_' => ['digit_auto', 'inherit', 'digit: ' . self::LABEL];
        yield 'natural' => ['natural', '-1', 'natural: ' . self::LABEL];
        yield 'natural negative int value' => ['natural', -1, 'natural: ' . self::LABEL];
        yield 'alpha' => ['alpha', 'abc1', 'alpha: ' . self::LABEL];
        yield 'alnum' => ['alnum', 'abc!', 'alnum: ' . self::LABEL];
        yield 'extnd' => ['extnd', 'foo<bar>', 'extnd: ' . self::LABEL];
        yield 'extnd encoded' => ['extnd', 'foo&lt;bar&gt;', 'extnd: ' . self::LABEL];
        yield 'date' => ['date', '29.02.2024', 'date: YYYY-MM-DD'];
        yield 'invalid date' => ['date', '2023-02-29', 'invalidDate: 2023-02-29'];
        yield 'time' => ['time', '1:45 pm', 'time: hh:mm'];
        yield 'datim' => ['datim', '2024-02-29', 'dateTime: YYYY-MM-DD hh:mm'];
        yield 'invalid datim' => ['datim', '2023-02-29 13:45', 'invalidDate: 2023-02-29 13:45'];
        yield 'email' => ['email', 'foo', 'email: ' . self::LABEL];
        yield 'friendly' => ['friendly', 'Foo Bar [foo]', 'email: ' . self::LABEL];
        yield 'emails' => ['emails', 'foo@example.org, bar', 'emails: ' . self::LABEL];
        yield 'url' => ['url', 'https://example.org/foo bar', 'url: ' . self::LABEL];
        yield 'alias' => ['alias', 'foo/bar', 'alias: ' . self::LABEL];
        yield 'folderalias' => ['folderalias', '/foo/bar', 'folderalias: ' . self::LABEL];
        yield 'phone' => ['phone', 'call me', 'phone: ' . self::LABEL];
        yield 'prcnt' => ['prcnt', '101', 'prcnt: ' . self::LABEL];
        yield 'locale' => ['locale', 'de-DE', 'locale: ' . self::LABEL];
        yield 'language' => ['language', 'de_DE', 'language: ' . self::LABEL];
        yield 'fieldname' => ['fieldname', 'foo bar', 'invalidFieldName: ' . self::LABEL];
    }

    #[DataProvider('validValues')]
    public function testValidValue(string $rgxp, mixed $value): void
    {
        $violations = $this->validate($value, new Rgxp(rgxp: $rgxp, label: self::LABEL));

        self::assertCount(0, $violations);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValue(string $rgxp, mixed $value, string $message): void
    {
        $violations = $this->validate($value, new Rgxp(rgxp: $rgxp, label: self::LABEL));

        self::assertCount(1, $violations);
        self::assertSame($message, $violations->get(0)->getMessage());
    }

    public function testLabelOfWidgetIsUsedForErrorMessage(): void
    {
        $violations = $this->validate('abc', new Rgxp(rgxp: 'digit', widget: new TestWidget('Widget label')));

        self::assertCount(1, $violations);
        self::assertSame('digit: Widget label', $violations->get(0)->getMessage());
    }

    public function testCustomRegexpHookAddsError(): void
    {
        $GLOBALS['TL_HOOKS']['addCustomRegexp'][] = static function (string $rgxp, mixed $value, Widget $widget): bool {
            if ($rgxp !== 'custom') {
                return false;
            }

            if ($value !== 'valid') {
                $widget->addError('Custom error');
            }

            return true;
        };

        $constraint = new Rgxp(rgxp: 'custom', widget: new TestWidget(self::LABEL));

        self::assertCount(0, $this->validate('valid', $constraint));

        $violations = $this->validate('invalid', $constraint);
        self::assertCount(1, $violations);
        self::assertSame('Custom error', $violations->get(0)->getMessage());
    }

    public function testCustomRegexpHookErrorsDoNotLeakIntoWidget(): void
    {
        $GLOBALS['TL_HOOKS']['addCustomRegexp'][] = static function (string $rgxp, mixed $value, Widget $widget): bool {
            $widget->addError(sprintf('Invalid value "%s" for rgxp "%s"', (string) $value, $rgxp));

            return true;
        };

        $widget = new TestWidget(self::LABEL);
        $this->validate('invalid', new Rgxp(rgxp: 'custom', widget: $widget));

        self::assertFalse($widget->hasErrors());
    }

    public function testCustomRegexpHookStopsAfterFirstHookReturningTrue(): void
    {
        $calls = [];

        $GLOBALS['TL_HOOKS']['addCustomRegexp'][] = static function () use (&$calls): bool {
            $calls[] = 'first';

            return true;
        };
        $GLOBALS['TL_HOOKS']['addCustomRegexp'][] = static function () use (&$calls): bool {
            $calls[] = 'second';

            return true;
        };

        $this->validate('foo', new Rgxp(rgxp: 'custom', widget: new TestWidget(self::LABEL)));

        self::assertSame(['first'], $calls);
    }

    public function testCustomRegexpHookIsNotCalledWithoutWidget(): void
    {
        $calls = 0;

        $GLOBALS['TL_HOOKS']['addCustomRegexp'][] = static function () use (&$calls): bool {
            $calls++;

            return true;
        };

        self::assertCount(0, $this->validate('foo', new Rgxp(rgxp: 'custom', label: self::LABEL)));
        self::assertSame(0, $calls);
    }

    private function validate(mixed $value, Rgxp $constraint): ConstraintViolationListInterface
    {
        $messages = [
            'ERR.digit'            => 'digit: %s',
            'ERR.natural'          => 'natural: %s',
            'ERR.alpha'            => 'alpha: %s',
            'ERR.alnum'            => 'alnum: %s',
            'ERR.extnd'            => 'extnd: %s',
            'ERR.date'             => 'date: %s',
            'ERR.time'             => 'time: %s',
            'ERR.dateTime'         => 'dateTime: %s',
            'ERR.invalidDate'      => 'invalidDate: %s',
            'ERR.email'            => 'email: %s',
            'ERR.emails'           => 'emails: %s',
            'ERR.url'              => 'url: %s',
            'ERR.alias'            => 'alias: %s',
            'ERR.folderalias'      => 'folderalias: %s',
            'ERR.phone'            => 'phone: %s',
            'ERR.prcnt'            => 'prcnt: %s',
            'ERR.locale'           => 'locale: %s',
            'ERR.language'         => 'language: %s',
            'ERR.invalidFieldName' => 'invalidFieldName: %s',
        ];

        $translator = $this->createStub(TranslatorInterface::class);
        $translator
            ->method('trans')
            ->willReturnCallback(
                static function (string $key, array $parameters = []) use ($messages): string {
                    $message = $messages[$key] ?? $key;

                    /** @psalm-var list<string|int|float> $parameters */
                    return $parameters === [] ? $message : vsprintf($message, $parameters);
                },
            );

        $validator = new RgxpValidator(
            $translator,
            new Invoker($this->createStub(Adapter::class)),
            $this->createStub(ContaoFramework::class),
        );

        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ConstraintValidatorFactory([RgxpValidator::class => $validator]))
            ->getValidator()
            ->validate($value, $constraint);
    }
}
