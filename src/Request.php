<?php

class Request
{
    private $get;
    private $post;

    public function __construct(array $get, array $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function getInt($key, $default = null)
    {
        if (!isset($this->post[$key]) && !isset($this->get[$key])) {
            return $default;
        }
        $value = isset($this->post[$key]) ? $this->post[$key] : $this->get[$key];
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public function validate()
    {
        $errors = [];
        $data = [];

        $n = $this->getInt('n');
        if ($n === null) {
            $errors[] = 'El campo "n" es requerido.';
        } elseif ($n < 1 || $n > 1000) {
            $errors[] = 'El valor de "n" debe estar entre 1 y 1000.';
        } else {
            $data['n'] = $n;
        }

        $min = $this->getInt('min', 1);
        $max = $this->getInt('max', 10000);

        if ($min !== null && $max !== null) {
            if ($min >= $max) {
                $errors[] = 'El valor mínimo debe ser menor que el máximo.';
            } else {
                $data['min'] = $min;
                $data['max'] = $max;
            }
        } else {
            $data['min'] = 1;
            $data['max'] = 10000;
        }

        return ['errors' => $errors, 'data' => $data];
    }

    public function all()
    {
        return array_merge($this->get, $this->post);
    }
}
