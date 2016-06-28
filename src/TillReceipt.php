<?php

$data = ['Baked Beans' => 0.50, 'Washing Up Liquid' => 0.72, 'Rubber Gloves' => 1.50, 'Bread' => 0.72, 'Butter' => 0.83];
$discount = 0.50;

/**
 * Class Items
 */
class Items
{
    protected $items;

    public function getItems()
    {
        return $this->items;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }
}

/**
 * Class TillReceipt
 */
class TillReceipt
{
    protected $discount = 0.50;

    protected $sub_total = 0;

    protected $grand_total = 0;

    public function __construct(Items $items, $discount = null)
    {
        if (null !== $discount) {
            $this->discount = $discount;
        }

        $this->setSubTotal($this->calculateSubTotal($items));
        $this->setGrandTotal($this->calculateGrandTotal(
            $this->getSubTotal(),
            $this->discount
        ));
    }

    public function calculateSubTotal(Items $items)
    {
        if (empty($items)) {
            throw new \Exception('Item cannot be empty');
        }

        $total = 0;
        foreach ($items->getItems() as $item) {
            $total += $item;
        }

        return $total;
    }

    public function calculateGrandTotal($subTotal, $discount)
    {
        return $subTotal - $discount;
    }

    public function setSubTotal($value)
    {
        $this->sub_total = $value;
    }

    public function getSubTotal()
    {
        return $this->sub_total;
    }

    public function setGrandTotal($value)
    {
        $this->grand_total = $value;
    }

    public function getGrandTotal()
    {
        return $this->grand_total;
    }
}

$items = new Items();
$items->setItems($data);
$mask = "|%5.20s |%-30.30s \n";
printf($mask, 'Item', 'Price');
foreach ($items->getItems() as $item => $price) {
    printf($mask, $item, $price);
}

$reciept = new TillReceipt($items);
echo '-----------------' . "\n";
printf($mask, 'sub total', $reciept->getSubTotal());
echo '-----------------' . "\n";
printf($mask, 'grand total', $reciept->getGrandTotal());
