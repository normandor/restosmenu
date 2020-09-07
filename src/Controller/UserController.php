<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $key
     *
     * @return string|int|bool|null
     */
    private function getCookie($key)
    {
        if (isset($_COOKIE[$key]))
        {
            return $_COOKIE[$key];
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function show(Request $request)
    {
        return $this->render('user/user_list.html.twig', [
            'user' => '$this->getUser()->getName()',
            'label' => 'Gestión de usuarios',
            'p' => 0,
            'route' => $request->get('_route'),
        ]);
    }

    /**
     * @param string      $dbuser
     * @param string      $dbpw
     * @param string      $dbname
     * @param string      $dbhost
     * @param Request     $request
     * @param UserService $userService
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function getTableData($dbuser, $dbpw, $dbname, $dbhost, Request $request, UserService $userService)
    {
        $resp = new Response(json_encode($userService->getTableData($dbuser, $dbpw, $dbname, $dbhost, $request->query->all())));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    /**
     * @param int     $zone
     * @param string  $dbuser
     * @param string  $dbpw
     * @param string  $dbname
     * @param string  $dbhost
     *
     * @return Response
     */
    public function modalShowEditUser($id, UserService $userService)
    {
        $userData = $userService->getUserData($id);

        return $this->render('user/modals/modal_user_edit_content.twig', [
                    'id' => $id,
                    'verified' => $userData['verified'],
                    'access_panel' => $userData['access_panel'],
                    'username' => $userData['username'],
                    'lastname' => $userData['lastname'],
                    'firstname' => $userData['firstname'],
                    'email' => $userData['email'],
                    'options' => [
                        'yes_no' => [
                            [
                                'value' => 0, // fix
                                'name' => "no",
                            ],
                            [
                                'value' => 1,
                                'name' => "yes",
                            ],
                        ],
                        'roles' => [],
                        'zones' => [],
                    ],
        ]);
    }

    /**
     * @param UserService $userService
     * @param Request     $request
     *
     * @return Response
     */
    public function updateUser(UserService $userService, Request $request)
    {
        $lastname = filter_var($request->get('lastname'), FILTER_SANITIZE_STRING);
        $firstname = filter_var($request->get('firstname'), FILTER_SANITIZE_STRING);
        $email = filter_var($request->get('email'), FILTER_SANITIZE_STRING);
        $role = filter_var($request->get('role'), FILTER_SANITIZE_STRING);
        $zonas = filter_var($request->get('zonas'), FILTER_SANITIZE_STRING);
        $acceso_tablero = filter_var($request->get('acceso_tablero'), FILTER_SANITIZE_NUMBER_INT);
        $usuario_activo = filter_var($request->get('usuario_activo'), FILTER_SANITIZE_NUMBER_INT);
        $userid = filter_var($request->get('userid'), FILTER_SANITIZE_NUMBER_INT);

        $success = $userService->updateUser($lastname, $firstname, $email, $role, $zonas, $acceso_tablero, $usuario_activo, $userid);

        return new Response(json_encode([
                    'message' => ($success) ? 'success' : 'error',
        ]));
    }

    /**
     * @param UserService $userService
     * @param Request     $request
     *
     * @return Response
     */
    public function deleteUser(UserService $userService, Request $request)
    {
        $userid = filter_var($request->get('user_id'), FILTER_SANITIZE_NUMBER_INT);

        $success = $userService->deleteUser($userid);

        return new Response(json_encode([
                    'message' => ($success) ? 'success' : 'error',
        ]));
    }

    /**
     * @param UserService                  $userService
     * @param Request                      $request
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function addUser(UserService $userService, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $username = filter_var($request->get('username'), FILTER_SANITIZE_STRING);
        $lastname = filter_var($request->get('lastname'), FILTER_SANITIZE_STRING);
        $firstname = filter_var($request->get('firstname'), FILTER_SANITIZE_STRING);
        $email = filter_var($request->get('email'), FILTER_SANITIZE_STRING);
        $password = filter_var($request->get('password1'), FILTER_SANITIZE_STRING);
        $roles = [];

        if ($userService->usernameExists($username))
        {
            return new Response(json_encode([
                        'message' => 'Usuario ya existe',
            ]));
        }

        $user = new \App\Entity\User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setUsername($username);
        $user->setAvatarPath('/images/avatars/avatar_gen.png');
        $user->setRestaurantId($this->getUser()->getRestaurantId());
        $manager->persist($user);
        $manager->flush();
        $success = true;
        // $success = $userService->addUser($username, $password, $firstname, $lastname, $email);

        return new Response(json_encode([
                    'message' => ($success) ? 'success' : 'error',
        ]));
    }

    /**
     * @return Response
     */
    public function modalShowAddUser()
    {
        return $this->render('user/modals/modal_user_new_content.twig', [
                    'label' => 'Gestión de usuarios',
        ]);
    }

    /**
     * @param UserService  $userService
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function profileAction(UserService $userService, Request $request, FileUploader $fileUploader): Response
    {
        $userEntity = $this->getUser();

        $originalPassword = $userEntity->getPassword();

        $form = $this->createForm(UserType::class, $userEntity, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEntity = $form->getData();

            $plainPassword = $form->get('password')->getData();

            if (!empty($plainPassword))  {

                // logout_on_user_change
                // causing issues with ValidatorInterface
                // todo: fix the auto logout when validation fails and remove following workaround
//            $pattern = '/^(?=.*[0-9])(?=.*[a-z].*[A-Z]).{8,20}$/';
//            if(!preg_match($pattern, $plainPassword)){
                if (strlen($plainPassword) < 8) {
                    $form->addError(new FormError('La clave debe tener al menos 8 letras'));
                    $userEntity->setPassword($originalPassword);
                } else {
                    $tempPassword = $this->passwordEncoder->encodePassword($userEntity, $userEntity->getPassword());
                    $userEntity->setPassword($tempPassword);
                }
            }
            else {
                $userEntity->setPassword($originalPassword);
            }

            $uploadedFile = $form['avatar_path']->getData();
            if ($uploadedFile) {
                $uploadedFilename = $fileUploader->upload($uploadedFile, 'images/avatars', md5($userEntity->getId()));
                $userEntity->setAvatarpath('images/avatars/'.$uploadedFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userEntity);
            $entityManager->flush();
            $this->redirectToRoute('profile');
        }

        return $this->render('user/profile.html.twig', [
            'label' => 'Perfil',
            'route' => $request->get('_route'),
            'user' => DashboardController::$user,
            'form' => $form->createView(),
        ]);
    }
}
