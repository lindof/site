<?php
namespace Biztech\DeliverydateAPIGraphQL\Model\Resolver;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class to resolve custom Delivery date data field's in CustomerOrders GraphQL query
 */
class Orders implements ResolverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $setRepository;

    public function __construct(OrderRepositoryInterface $setRepository)
    {
        $this->setRepository = $setRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        return $this->setRepository->get($value['id'])->getData($field->getName());
    }
}
