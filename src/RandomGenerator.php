<?php

class RandomGenerator
{
    private $n;
    private $min;
    private $max;
    private $numbers = [];

    public function __construct($n, $min = 1, $max = 10000)
    {
        $this->n = $n;
        $this->min = $min;
        $this->max = $max;
    }

    public function generate()
    {
        $this->numbers = [];
        for ($i = 0; $i < $this->n; $i++) {
            $this->numbers[] = random_int($this->min, $this->max);
        }
        return $this->numbers;
    }

    public function getSum()
    {
        return array_sum($this->numbers);
    }

    public function getAverage()
    {
        if (empty($this->numbers)) {
            return 0.0;
        }
        return $this->getSum() / count($this->numbers);
    }

    public function getMin()
    {
        if (empty($this->numbers)) {
            return 0;
        }
        return min($this->numbers);
    }

    public function getMax()
    {
        if (empty($this->numbers)) {
            return 0;
        }
        return max($this->numbers);
    }
}
