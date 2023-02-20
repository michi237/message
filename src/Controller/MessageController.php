<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MessageRepository;
use App\Entity\Message;


/**
 * @IsGranted("ROLE_USER")
 */

class MessageController extends AbstractController
{

    /**
     * @var MessageRepository
     */
    private MessageRepository $messageRepository;

    /**
     * @var Message
     */
    private Message $message;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }


    #[Route('/message', name: 'app_message', methods: 'GET')]
    public function index(): Response
    {

        $messages = $this->messageRepository->findAll();

        $result = [];
        foreach($messages as $message) {
            $result[] = $message->toArray();
        }

        return $this->render('message/index.html.twig', [
            'messages' => $result,

        ]);
    }

    #[Route('public/message/{id}/is_public/{is_public}', name: 'config_message_is_public', methods: 'GET')]
    public function active(string $id, bool $is_public): JsonResponse
    {
        try {
            $message = $this->messageRepository->getByPublic($is_public);
            $this->messageRepository->setDeactive(0, $message);

            $message = $this->messageRepository->getById($id);
            $this->messageRepository->setActive($is_public, $message);

            return new JsonResponse([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    #[Route('public/message/{id}/delete', name: 'config_message_delete', methods: 'GET')]
    public function remove(string $id): JsonResponse
    {
        try {
            $message = $this->messageRepository->getById($id);

            if ($message === null) {
                print_r('brak wiadomości');
            }

            $this->messageRepository->remove($message, true);

            return new JsonResponse([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
