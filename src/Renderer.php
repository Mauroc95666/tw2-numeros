<?php

class Renderer
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../views/';
    }

    public function renderForm($data = [])
    {
        ob_start();
        include $this->viewsPath . 'form.php';
        return ob_get_clean();
    }

    public function renderResults($numbers, $stats, $previousInput = [])
    {
        ob_start();
        include $this->viewsPath . 'results.php';
        return ob_get_clean();
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
