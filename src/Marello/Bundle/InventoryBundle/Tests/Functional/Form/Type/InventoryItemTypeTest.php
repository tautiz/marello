<?php

namespace Marello\Bundle\InventoryBundle\Tests\Functional\Form\Type;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Form\Type\InventoryItemType;
use Marello\Bundle\InventoryBundle\Model\InventoryItemModify;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;
use Marello\Bundle\InventoryBundle\Manager\InventoryManager;

/**
 * @dbIsolation
 */
class InventoryItemTypeTest extends WebTestCase
{
    /** @var InventoryManager $manager */
    protected $manager;

    public function setUp()
    {
        $this->initClient();
        $this->manager = $this->getContainer()->get('marello_inventory.manager.inventory_manager');
    }

    /**
     * Creates tested form.
     *
     * @param $data
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    protected function createForm($data)
    {
        return $this->getContainer()
            ->get('form.factory')
            ->create(InventoryItemType::NAME, $data, [
                'csrf_protection' => false,
            ]);
    }

    /**
     * Test increase from 0 by 10.
     */
    public function testIncreaseFromZero()
    {
        $product   = $this->prophesize(Product::class);
        $warehouse = $this->prophesize(Warehouse::class);

        $item = new InventoryItem($warehouse->reveal(), $product->reveal());

        $form = $this->createForm($item);

        $form->submit([
            'stockOperator' => InventoryItemModify::OPERATOR_INCREASE,
            'stock'         => 10,
        ]);

        $this->assertTrue($form->isValid());
        $this->assertEquals(10, $item->getStock());
    }

    /**
     * Test increase from 25 by 10.
     */
    public function testIncrease()
    {
        $product   = $this->prophesize(Product::class);
        $warehouse = $this->prophesize(Warehouse::class);

        $inventoryItem = new InventoryItem($warehouse->reveal(), $product->reveal());
        $data = $this->getContextData($inventoryItem, 'import', 25, 0);
        $context = InventoryUpdateContext::createUpdateContext($data);
        $this->manager->updateInventoryItems($context);

        $form = $this->createForm($inventoryItem);

        $form->submit([
            'stockOperator' => InventoryItemModify::OPERATOR_INCREASE,
            'stock'   => 10,
        ]);

        $this->assertTrue($form->isValid());
        $this->assertEquals(35, $inventoryItem->getStock());
    }

    /**
     * Test decrease from 25 by 10.
     */
    public function testDecrease()
    {
        $product   = $this->prophesize(Product::class);
        $warehouse = $this->prophesize(Warehouse::class);
        $inventoryItem = new InventoryItem($warehouse->reveal(), $product->reveal());
        $data = $this->getContextData($inventoryItem, 'import', 25, 0);
        $context = InventoryUpdateContext::createUpdateContext($data);
        $this->manager->updateInventoryItems($context);

        $form = $this->createForm($inventoryItem);

        $form->submit([
            'stockOperator' => InventoryItemModify::OPERATOR_DECREASE,
            'stock'   => 10,
        ]);

        $this->assertTrue($form->isValid());
        $this->assertEquals(15, $inventoryItem->getStock());
    }

    /**
     * Test decrease from 10 by 20.
     */
    public function testDecreaseUnderZero()
    {
        $product   = $this->prophesize(Product::class);
        $warehouse = $this->prophesize(Warehouse::class);
        $inventoryItem = new InventoryItem($warehouse->reveal(), $product->reveal());
        $data = $this->getContextData($inventoryItem, 'import', 10, 0);
        $context = InventoryUpdateContext::createUpdateContext($data);
        $this->manager->updateInventoryItems($context);

        $form = $this->createForm($inventoryItem);

        $form->submit([
            'stockOperator' => InventoryItemModify::OPERATOR_DECREASE,
            'stock'   => 20,
        ]);

        $this->assertTrue($form->isValid());
        $this->assertEquals(-10, (int)$inventoryItem->getStock());
    }

    /**
     * Get Inventory Update context data
     * @param $item                 InventoryItem
     * @param $trigger              // change trigger
     * @param $inventory            // inventory to update
     * @param $allocatedInventory   // allocated inventory to update
     * @return array
     */
    protected function getContextData($item, $trigger, $inventory, $allocatedInventory)
    {
        $data = [
            'stock'             => $inventory,
            'allocatedStock'    => $allocatedInventory,
            'trigger'           => $trigger,
            'items'             => [
                [
                    'item'          => $item,
                    'qty'           => $inventory,
                    'allocatedQty'  => $allocatedInventory
                ]
            ]
        ];

        return $data;
    }
}
