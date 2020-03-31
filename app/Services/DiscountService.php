<?php

namespace App\Services;

/**
 * Расчет скидки
 *
 * Class DiscountService
 */
class DiscountService
{
    /**
     * @var float
     */
    private $total = 0;

    /**
     * Скидка
     * @var float;
     */
    private $discount = 0;

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Расчет скидки
     *
     * @return $this
     */
    public function calculation()
    {
        // TODO individual

        return $this;
    }

    /**
     * Сумма с вычетом скидки
     * @return float|int
     */
    public function getTotal()
    {
        return $this->total * (1 - $this->discount);
    }

    /**
     * Скидка
     * @return float
     */
    public function getDiscountTotal()
    {
        return $this->total * $this->discount;
    }

    /**
     * Скидка
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
