<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

class ColoresController extends AbstractController
{
    /**
     * @Route("/colores", name="app_colores")
     */
    public function index(): Response
    {
        return $this->render('colores/index.html.twig', [
            'controller_name' => 'ColoresController',
        ]);
    }

    public function calcularCalidad(Request $request)
    {
        if ($request->isMethod('POST')) {
            $color1 = $request->request->get('color1');
            $color2 = $request->request->get('color2');
            $estilo = $request->request->get('estilo');

            $calidad = $this->calcularCalidadCombinacion($color1, $color2, $estilo);

            return $this->json([
                'calidad' => $calidad,
            ]);
        }

        throw new \Exception('Método no permitido');
    }

    private function calcularCalidadCombinacion($color1, $color2, $estilo)
    {
        $rgb1 = $this->convertirHexARGB($color1);
        $rgb2 = $this->convertirHexARGB($color2);

        $diferenciaRojo = abs($rgb1['r'] - $rgb2['r']);
        $diferenciaVerde = abs($rgb1['g'] - $rgb2['g']);
        $diferenciaAzul = abs($rgb1['b'] - $rgb2['b']);

        $maxDiferencia = max($diferenciaRojo, $diferenciaVerde, $diferenciaAzul);

        if ($estilo == 'formal') {
            $calidad = $maxDiferencia / 255;
        } elseif ($estilo == 'casual') {
            $calidad = 1 - ($maxDiferencia / 255);
        } else {
            throw new \Exception("Estilo no válido: $estilo");
        }

        $calidad = number_format($calidad, 2, '.', '');

        return $calidad;
    }

    private function convertirHexARGB($color)
    {
        $color = ltrim($color, '#');
        $length = strlen($color);

        if ($length == 6) {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        } elseif ($length == 3) {
            $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
            $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
            $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
        } else {
            throw new \Exception("Formato de color no válido: $color");
        }

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

}
