<?php

declare(strict_types=1);

namespace Kartalit\Controllers;

use Kartalit\Models\Autor;
use Kartalit\Models\Cita;
use Kartalit\Services\CitaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CitaController
{
    public function __construct(private CitaService $citaService) {}

    public function getRandom(Request $_, Response $res): Response
    {
        /** @var Cita $cita */
        $cita = $this->citaService->getRandom();
        $obra = $cita->getObra();
        $citaJson = [
            "cita" => $cita->getCita(),
            "obra" => [
                "titolOriginal" => $obra?->getTitolOriginal(),
                "titolCatala" => $obra?->getTitolCatala(),
                "autors" => array_map(function (Autor $autor) {
                    return ["nomComplet" => $autor->getNomComplet()];
                }, $obra->getAutors()->toArray()),
            ]
        ];
        $res->getBody()->write(json_encode($citaJson));
        return $res->withStatus(200);
    }
}
