<?php

declare(strict_types=1);

namespace Kartalit\Controllers;

use Kartalit\Enums\Entity;
use Kartalit\Errors\EntityNotFoundException;
use Kartalit\Errors\UnauthorizedException;
use Kartalit\Models\Llegit;
use Kartalit\Models\Usuari;
use Kartalit\Schemas\TwigContext;
use Kartalit\Services\LlegitService;
use Kartalit\Services\TwigService;
use Kartalit\Services\UsuariService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class PerfilController extends WebController
{
    public function __construct(
        private TwigService $twigService,
        private UsuariService $usuariService,
        private LlegitService $llegitService,
    ) {}
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /** @var ?Usuari $reqUsuari */
        $reqUsuari = $request->getAttribute("usuari");
        $perfilUsuariId = isset($args["id"]) ? (int)$args["id"] : $reqUsuari?->getId();
        if (!$perfilUsuariId) {
            throw new UnauthorizedException();
        }
        /** @var ?Usuari $perfilUsuari */
        $perfilUsuari = $this->usuariService->getById($perfilUsuariId);
        if (!$perfilUsuari) {
            throw new EntityNotFoundException(Entity::USUARI, $perfilUsuariId);
        }
        $llegits = $this->llegitService->getByUsuari($perfilUsuari, $reqUsuari);
        $llegitsJson = array_map(fn(Llegit $llegit) => $llegit->getArray(), $llegits);
        $twigContextData = [
            "llegits" => $llegitsJson,
            "perfilUsuari" => $perfilUsuari->getArray($reqUsuari)
        ];
        $twigContext = new TwigContext($request, "Perfil", $twigContextData);
        return $this->twigService->render($response, "Pages/perfil.html.twig", $twigContext);
    }
}
