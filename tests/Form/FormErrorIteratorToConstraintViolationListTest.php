<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Tests\Form;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Form\FormErrorIteratorToConstraintViolationList;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class FormErrorIteratorToConstraintViolationListTest extends TestCase
{
    /**
     * @param FormErrorIterator<FormError> $formErrors
     *
     * @dataProvider provideFormErrorIterators
     */
    public function testTransform(int $expectedCount, FormErrorIterator $formErrors): void
    {
        $violationList = FormErrorIteratorToConstraintViolationList::transform($formErrors);

        static::assertInstanceOf(ConstraintViolationList::class, $violationList);
        static::assertCount($expectedCount, $violationList);
    }

    /**
     * @phpstan-return iterable<array{int, FormErrorIterator<FormError>}>
     * @psalm-return iterable<array{int, FormErrorIterator<FormError|FormErrorIterator>}>
     */
    public function provideFormErrorIterators(): iterable
    {
        $form = $this->createStub(FormInterface::class);
        $form->method('getName')->willReturn('name');

        yield [0, new FormErrorIterator($form, [])];

        yield [0, new FormErrorIterator($form, [
            new FormError('error'),
        ])];

        yield [1, new FormErrorIterator($form, [
            new FormError(
                'error',
                null,
                [],
                null,
                new ConstraintViolation('error', null, [], $form, 'path', 'invalid value')
            ),
        ])];
    }
}
