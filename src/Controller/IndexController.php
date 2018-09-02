<?php declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use Michelf\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @var Kernel */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Route("/")
     */
    public function index()
    {
        $readme = $this->kernel->getProjectDir() . '/README.md';

        $html =  Markdown::defaultTransform(file_get_contents($readme));

        return new Response($html);
    }

}