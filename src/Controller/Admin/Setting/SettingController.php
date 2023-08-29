<?php

namespace App\Controller\Admin\Setting;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    #[Route('/admin/setting', name: 'admin.setting.index')]
    public function index(): Response
    {
        
        return $this->render('pages/admin/setting/index.html.twig');
    }
}
