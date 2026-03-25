# Generador de Números Aleatorios

Aplicación PHP orientada a objetos que solicita al usuario N elementos y muestra N números aleatorios en una tabla con estadísticas.

## Requisitos

- Docker
- docker-compose
- Puerto 8082 disponible

## Instalación

1. Colocar esta carpeta en `./html/noo2/` del proyecto que contiene `docker-compose.yml`

2. Asegurar que el volumen en docker-compose.yml monte la carpeta html:
   ```yaml
   volumes:
     - ./html:/var/www/html
   ```

3. Ejecutar:
   ```bash
   docker-compose up -d
   ```

4. Acceder a: http://localhost:8082/noo2/

## Uso

1. Ingresar la cantidad de números a generar (n) entre 1 y 1000
2. (Opcional) Definir rango mínimo y máximo
3. Click en "Generar"

La aplicación muestra una tabla con los números generados y estadísticas: suma, promedio, mínimo y máximo.

## Características

- PHP 7.4 compatible
- Sin dependencias externas (require_once)
- Validación servidor
- Protección XSS (htmlspecialchars)
- Patrón PRG (Post/Redirect/Get)
- Sin bucles infinitos, switch o break
