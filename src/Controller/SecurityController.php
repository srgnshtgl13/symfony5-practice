<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Service\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends MainController
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->passwordEncoder = $userPasswordEncoder;
        
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register/{slug?}", name="app_register")
     */
    public function register(Request $request, $slug = ""){
        $user = new User();
        $msg = 'User Created!';
        $isSlug = $slug;
        $title = 'Create User';
        if($slug){
            $repo = $this->getDoctrine()->getRepository(User::class);
            $find_user = $repo->findBySlug($slug);
            if(!$find_user){
                $this->addFlash('error', 'User Not Found!');
                return $this->redirectToRoute('app_register');
            }
            $user = $find_user[0];
            $msg = 'User Updated!';
            $title = 'Update User';
        }
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $data = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('app_register', ['slug' => $isSlug]);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'title' => $title
        ]);
    }

}
