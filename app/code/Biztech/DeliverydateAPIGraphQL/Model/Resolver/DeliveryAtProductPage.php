<?php
declare(strict_types=1);

namespace Biztech\DeliverydateAPIGraphQL\Model\Resolver;

use Biztech\Deliverydate\Helper\Data as DeliveryHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * DeliveryDate resolver for specific product, used for GraphQL request processing
 */
class DeliveryAtProductPage implements ResolverInterface
{
    /**
     * @var DeliveryHelper
     */
    private $deliveryHelper;

    /**
     * @param \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @param \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     *
     * @param deliveryHelper $deliveryHelper
     */
    public function __construct(
        DeliveryHelper $deliveryHelper,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Customer\Model\CustomerFactory $customer
    ) {
        $this->deliveryHelper = $deliveryHelper;
        $this->product = $product;
        $this->customer = $customer;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"Product id should be specified'));
        }

        try {
            $userId = $context->getUserId();
            if($userId){
                $groupId = $this->customer->create()->load($userId)->getGroupId();
            }else{
                $groupId = 0;
            }
            $product = $this->product->create()->load((int)$args['id']);
            if(is_null($product->getId())){
                throw new GraphQlInputException(__('"Product id was\'t available.'));
            }

            $isCustomerApplicable = $this->deliveryHelper->checkCurrentUserAllowed($groupId);
            $isAppliableWithCategory = $this->deliveryHelper->isAppliableWithCategory($product);

            $enable = ($isAppliableWithCategory && $isCustomerApplicable && $this->deliveryHelper->isEnableAtProductPage() && $product->getTypeId() !== \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL && $product->getTypeId() !== 'downloadable');
            $deliveryDateInformation = $this->deliveryHelper->getProductLevelConfig();
            $deliveryDateInformation['productId'] = $args['id'];
            $deliveryDateInformation['templateConfig']['useTemplate'] = $this->deliveryHelper->getConfigValue('deliverydate/deliverydate_general/delivery_method') == 1 ? 'calender' : 'timeslot';
            $deliveryDateInformation['general']['enabled'] = ($enable && $deliveryDateInformation['general']['enabled']);
            return $deliveryDateInformation;

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
