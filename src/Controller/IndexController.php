<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;

class IndexController extends AbstractController
{

    /**
     * @var MessageRepository
     */
    private MessageRepository $messageRepository;


    public function __construct(MessageRepository $messageRepository) {
        $this->messageRepository = $messageRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    #[Route('public/message/add', name: 'message_add', methods: 'post')]
    public function add(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $user = $this->getUser();

            $message = new Message();
            $message->setUser($user);
            $message->setMessage($data['message']);
            $message->setUploadedAt(new \DateTime());
            $message->setIsPublic(false);

            $this->messageRepository->save($message, true);

        }

        return new JsonResponse(['status' => true]);
    }
}
