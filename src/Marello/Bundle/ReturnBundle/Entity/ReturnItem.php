<?php

namespace Marello\Bundle\ReturnBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ReturnBundle\Model\ExtendReturnItem;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

/**
 * @ORM\Entity()
 * @ORM\Table(name="marello_return_item")
 * @ORM\HasLifecycleCallbacks()
 * @Oro\Config()
 */
class ReturnItem extends ExtendReturnItem implements CurrencyAwareInterface
{

    use EntityCreatedUpdatedAtTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ReturnEntity
     *
     * @ORM\ManyToOne(targetEntity="ReturnEntity", inversedBy="returnItems")
     * @ORM\JoinColumn(name="return_id", onDelete="CASCADE")
     */
    protected $return;

    /**
     * @var OrderItem
     *
     * @ORM\ManyToOne(targetEntity="Marello\Bundle\OrderBundle\Entity\OrderItem", inversedBy="returnItems")
     * @ORM\JoinColumn(name="order_item_id")
     */
    protected $orderItem;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var string
     */
    protected $status;

    /**
     * ReturnItem constructor.
     *
     * @param OrderItem $orderItem
     */
    public function __construct(OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ReturnEntity
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * @param ReturnEntity $return
     *
     * @return $this
     */
    public function setReturn($return)
    {
        $this->return = $return;

        return $this;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     *
     * @return $this
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get currency for returnItem from "sibling" orderItem
     */
    public function getCurrency()
    {
        return $this->orderItem->getCurrency();
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return $this
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
