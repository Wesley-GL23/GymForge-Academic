<?php
require_once 'config/config.php';

// Configurações da imagem
$size = 32;
$image = imagecreatetruecolor($size, $size);

// Cores
$darkBlue = imagecolorallocate($image, 5, 28, 44); // #051C2C
$royalBlue = imagecolorallocate($image, 0, 127, 255); // #007FFF
$white = imagecolorallocate($image, 255, 255, 255);

// Fundo transparente
imagealphablending($image, false);
$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $transparent);
imagesavealpha($image, true);

// Desenhar círculo base
imagefilledellipse($image, $size/2, $size/2, $size-2, $size-2, $royalBlue);

// Desenhar "G" estilizado
$points = [
    $size/2 - 6, $size/2 - 6, // Ponto inicial
    $size/2 + 4, $size/2 - 6, // Topo
    $size/2 + 4, $size/2 + 6, // Direita
    $size/2 - 6, $size/2 + 6, // Base
    $size/2 - 6, $size/2,     // Esquerda
    $size/2, $size/2          // Centro
];
imagefilledpolygon($image, $points, 3, $white);

// Salvar a imagem
imagepng($image, 'assets/img/favicon.png');
imagedestroy($image);

echo "Favicon gerado com sucesso!";
?> 