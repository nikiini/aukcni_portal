<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdminerController extends AbstractController
{
    //  Inicializuje cestu k souboru Admineru, který je dostupný přes interní route.
    public function __construct(
        private string $databaseUrl,
        private bool $adminerEnabled,
        private string $adminerBootFile
    ) {
    }
    //  Vrátí obsah Admineru jako odpověď v rámci aplikace.
    #[Route("/adminer", name: "app.adminer")]
    public function pageAdminer(): Response
    {
        if (!$this->adminerEnabled) {
            throw new NotFoundHttpException("File not found.");
        }

        return new StreamedResponse(
            function () {
                $httpAuth = null;
                $dbConf = $this->databaseUrl;
                include $this->adminerBootFile;
            }
        );
    }
}
