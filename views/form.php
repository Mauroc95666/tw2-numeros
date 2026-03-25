<?php
$n = isset($data['n']) ? $data['n'] : '';
$min = isset($data['min']) ? $data['min'] : '';
$max = isset($data['max']) ? $data['max'] : '';
?>
<form method="POST" action="./index.php">
    <label for="n">Cantidad de números (n):</label><br>
    <input type="number" id="n" name="n" value="<?php echo htmlspecialchars($n, ENT_QUOTES, 'UTF-8'); ?>" min="1" max="1000" required><br>
    
    <label for="min">Mínimo (opcional):</label><br>
    <input type="number" id="min" name="min" value="<?php echo htmlspecialchars($min, ENT_QUOTES, 'UTF-8'); ?>"><br>
    
    <label for="max">Máximo (opcional):</label><br>
    <input type="number" id="max" name="max" value="<?php echo htmlspecialchars($max, ENT_QUOTES, 'UTF-8'); ?>"><br>
    
    <button type="submit">Generar</button>
</form>
