<?php

namespace App\Controller\Admin\Setting;

use App\Entity\Setting;
use App\Form\EditSettingFormType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingController extends AbstractController
{
    #[Route('/admin/setting', name: 'admin.setting.index')]
    public function index(SettingRepository $settingRepository): Response
    {
        
        // $setting = $settingRepository->findOneBy(['id'=>1]);

        $data = $settingRepository->findAll();
        $setting = $data[0];

        return $this->render('pages/admin/setting/index.html.twig', compact('setting'));
    }


    #[Route('/admin/setting/{id}/edit', name: 'admin.setting.edit', methods:['GET','PUT'])]
    public function edit(Setting $setting, Request $request, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(EditSettingFormType::class, $setting, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $em->persist($setting);
            $em->flush();

            $this->addFlash('success', "Les paramètres du site ont été modifiés.");

            return $this->redirectToRoute('admin.setting.index');
        }

        return $this->render("pages/admin/setting/edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

}
