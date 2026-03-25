<?php

class App
{
    private $request;
    private $renderer;

    public function __construct(Request $request, Renderer $renderer)
    {
        $this->request = $request;
        $this->renderer = $renderer;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            $this->handlePost();
        } else {
            $this->handleGet();
        }
    }

    private function handlePost()
    {
        $validation = $this->request->validate();

        if (!empty($validation['errors'])) {
            $_SESSION['errors'] = $validation['errors'];
            $_SESSION['previous_input'] = $_POST;
            $this->redirectToGet();
        }

        $data = $validation['data'];
        $generator = new RandomGenerator($data['n'], $data['min'], $data['max']);
        $numbers = $generator->generate();

        $_SESSION['results'] = [
            'numbers' => $numbers,
            'stats' => [
                'sum' => $generator->getSum(),
                'average' => $generator->getAverage(),
                'min' => $generator->getMin(),
                'max' => $generator->getMax()
            ]
        ];
        $_SESSION['previous_input'] = $_POST;

        $this->redirectToGet();
    }

    private function handleGet()
    {
        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
        $results = isset($_SESSION['results']) ? $_SESSION['results'] : null;
        $previousInput = isset($_SESSION['previous_input']) ? $_SESSION['previous_input'] : [];

        unset($_SESSION['errors'], $_SESSION['results'], $_SESSION['previous_input']);

        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Generador de Números Aleatorios</title>';
        echo '<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; max-width: 400px; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
            .error { color: red; margin: 10px 0; }
            input[type="number"] { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
            button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; margin-top: 10px; }
            button:hover { background-color: #45a049; }
            .stats-row { font-weight: bold; background-color: #f2f2f2; }
        </style>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Generador de Números Aleatorios</h1>';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<p class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }

        echo $this->renderer->renderForm($previousInput);

        if ($results !== null) {
            echo $this->renderer->renderResults($results['numbers'], $results['stats'], $previousInput);
        }

        echo '</body>';
        echo '</html>';
    }

    private function redirectToGet()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $path = str_replace('/index.php', '/index.php', $uri);
        
        header('Location: ' . $scheme . '://' . $host . $path);
        exit;
    }
}
