<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Tests\Validator\Constraints;

use Netzmacht\ContaoFormBundle\Tests\Fixtures\TestWidget;
use Netzmacht\ContaoFormBundle\Validator\Constraints\Rgxp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

final class RgxpTest extends TestCase
{
    public function testConstraintBelongsToTheDefaultGroup(): void
    {
        $constraint = new Rgxp(rgxp: 'digit');

        self::assertSame([Constraint::DEFAULT_GROUP], $constraint->groups);
    }

    public function testConstraintBelongsToGivenGroups(): void
    {
        $constraint = new Rgxp(rgxp: 'digit', groups: ['foo', 'bar']);

        self::assertSame(['foo', 'bar'], $constraint->groups);
    }

    public function testNamedArguments(): void
    {
        $widget     = new TestWidget('Widget label');
        $constraint = new Rgxp(rgxp: 'email', widget: $widget, label: 'Label', payload: 'payload');

        self::assertSame('email', $constraint->getRgxp());
        self::assertSame($widget, $constraint->getWidget());
        self::assertSame('Label', $constraint->getLabel());
        self::assertSame('payload', $constraint->payload);
    }

    public function testOptionsArrayIsSupported(): void
    {
        $widget     = new TestWidget();
        $constraint = new Rgxp(['rgxp' => 'alias', 'label' => 'Label', 'widget' => $widget, 'groups' => ['foo']]);

        self::assertSame('alias', $constraint->getRgxp());
        self::assertSame('Label', $constraint->getLabel());
        self::assertSame($widget, $constraint->getWidget());
        self::assertSame(['foo'], $constraint->groups);
    }

    public function testLabelFallsBackToWidgetLabel(): void
    {
        $constraint = new Rgxp(rgxp: 'digit', widget: new TestWidget('Widget label'));

        self::assertSame('Widget label', $constraint->getLabel());
    }

    public function testRgxpIsRequired(): void
    {
        $this->expectException(MissingOptionsException::class);

        new Rgxp();
    }

    public function testRgxpIsRequiredInOptionsArray(): void
    {
        $this->expectException(MissingOptionsException::class);

        new Rgxp(['label' => 'Label']);
    }
}
