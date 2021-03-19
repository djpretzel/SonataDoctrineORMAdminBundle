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

namespace Sonata\DoctrineORMAdminBundle\Tests\Builder;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\FieldDescription\TypeGuesserInterface;
use Sonata\DoctrineORMAdminBundle\Builder\ListBuilder;
use Sonata\DoctrineORMAdminBundle\FieldDescription\FieldDescription;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * @author Andrew Mor-Yaroslavtsev <andrejs@gmail.com>
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
class ListBuilderTest extends TestCase
{
    private $typeGuesser;
    private $listBuilder;
    private $admin;

    protected function setUp(): void
    {
        $this->typeGuesser = $this->createStub(TypeGuesserInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);

        $this->admin->method('getClass')->willReturn('Foo');

        $this->listBuilder = new ListBuilder($this->typeGuesser);
    }

    public function testAddListActionField(): void
    {
        $this->admin->expects($this->once())->method('addListFieldDescription');

        $fieldDescription = new FieldDescription('foo');

        $list = $this->listBuilder->getBaseList();
        $this->listBuilder->addField($list, 'actions', $fieldDescription, $this->admin);

        $this->assertSame(
            '@SonataAdmin/CRUD/list__action.html.twig',
            $list->get('foo')->getTemplate(),
            'Custom list action field has a default list action template assigned'
        );
    }

    public function testCorrectFixedActionsFieldType(): void
    {
        $this->admin->expects($this->once())->method('addListFieldDescription');
        $this->typeGuesser->method('guess')->willReturn(new TypeGuess('_action', [], Guess::LOW_CONFIDENCE));

        $fieldDescription = new FieldDescription('_action');
        $fieldDescription->setOption('actions', ['test' => []]);

        $list = $this->listBuilder->getBaseList();
        $this->listBuilder->addField($list, null, $fieldDescription, $this->admin);

        $this->assertSame(
            'actions',
            $list->get('_action')->getType(),
            'Standard list _action field has "actions" type'
        );

        $this->assertSame(
            '@SonataAdmin/CRUD/list__action_test.html.twig',
            $fieldDescription->getOption('actions')['test']['template']
        );
    }

    /**
     * @dataProvider fixFieldDescriptionData
     */
    public function testFixFieldDescription($type, $template): void
    {
        $this->admin->expects($this->once())->method('attachAdminClass');

        $fieldDescription = new FieldDescription('test', [], ['type' => $type]);
        $fieldDescription->setOption('sortable', true);
        $fieldDescription->setType('fakeType');

        $this->listBuilder->fixFieldDescription($this->admin, $fieldDescription);

        $this->assertSame($template, $fieldDescription->getTemplate());
    }

    public function fixFieldDescriptionData(): array
    {
        return [
            'one-to-one' => [
                ClassMetadata::ONE_TO_ONE,
                '@SonataAdmin/CRUD/Association/list_one_to_one.html.twig',
            ],
            'many-to-one' => [
                ClassMetadata::MANY_TO_ONE,
                '@SonataAdmin/CRUD/Association/list_many_to_one.html.twig',
            ],
            'one-to-many' => [
                ClassMetadata::ONE_TO_MANY,
                '@SonataAdmin/CRUD/Association/list_one_to_many.html.twig',
            ],
            'many-to-many' => [
                ClassMetadata::MANY_TO_MANY,
                '@SonataAdmin/CRUD/Association/list_many_to_many.html.twig',
            ],
        ];
    }

    public function testFixFieldDescriptionException(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->listBuilder->fixFieldDescription($this->admin, new FieldDescription('name'));
    }
}
