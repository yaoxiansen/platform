<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Processor\Subresource\Shared;

use Oro\Bundle\ApiBundle\Metadata\EntityMetadata;
use Oro\Bundle\ApiBundle\Model\Error;
use Oro\Bundle\ApiBundle\Processor\Subresource\Shared\NormalizeParentEntityId;
use Oro\Bundle\ApiBundle\Request\EntityIdTransformerInterface;
use Oro\Bundle\ApiBundle\Request\EntityIdTransformerRegistry;
use Oro\Bundle\ApiBundle\Tests\Unit\Processor\Subresource\GetSubresourceProcessorTestCase;

class NormalizeParentEntityIdTest extends GetSubresourceProcessorTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|EntityIdTransformerInterface */
    private $entityIdTransformer;

    /** @var NormalizeParentEntityId */
    private $processor;

    protected function setUp()
    {
        parent::setUp();

        $this->entityIdTransformer = $this->createMock(EntityIdTransformerInterface::class);
        $entityIdTransformerRegistry = $this->createMock(EntityIdTransformerRegistry::class);
        $entityIdTransformerRegistry->expects(self::any())
            ->method('getEntityIdTransformer')
            ->with($this->context->getRequestType())
            ->willReturn($this->entityIdTransformer);

        $this->processor = new NormalizeParentEntityId($entityIdTransformerRegistry);
    }

    public function testProcessWhenParentIdAlreadyNormalized()
    {
        $this->context->setParentClassName('Test\Class');
        $this->context->setParentId(123);

        $this->entityIdTransformer->expects($this->never())
            ->method('reverseTransform');

        $this->processor->process($this->context);
    }

    public function testConvertStringParentIdToConcreteDataType()
    {
        $this->context->setParentClassName('Test\Class');
        $this->context->setParentId('123');
        $this->context->setParentMetadata(new EntityMetadata());

        $this->entityIdTransformer->expects($this->once())
            ->method('reverseTransform')
            ->with($this->context->getParentId(), self::identicalTo($this->context->getParentMetadata()))
            ->willReturn(123);

        $this->processor->process($this->context);

        $this->assertSame(123, $this->context->getParentId());
    }

    public function testProcessForInvalidParentId()
    {
        $this->context->setParentClassName('Test\Class');
        $this->context->setParentId('123');
        $this->context->setParentMetadata(new EntityMetadata());

        $this->entityIdTransformer->expects($this->once())
            ->method('reverseTransform')
            ->with($this->context->getParentId(), self::identicalTo($this->context->getParentMetadata()))
            ->willThrowException(new \Exception('some error'));

        $this->processor->process($this->context);

        $this->assertSame('123', $this->context->getParentId());
        $this->assertEquals(
            [
                Error::createValidationError('entity identifier constraint')
                    ->setInnerException(new \Exception('some error'))
            ],
            $this->context->getErrors()
        );
    }
}
