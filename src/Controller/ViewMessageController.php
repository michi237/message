<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ViewMessageController extends AbstractController
{

    /**
     * @var MessageRepository
     */
    private MessageRepository $messageRepository;


    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;

    }

    #[Route('/view', name: 'app_view_message')]
    public function index(): Response
    {
        $messages = $this->messageRepository->setIsPublic('is_public');

        if ($messages == null) {
            throw new \Exception('Brak wiadomości do wyświetlenia');
        }

        if (count($messages) > 2) {
            throw new \Exception('Za dużo wiadomości');
        }

        $result = [];
        foreach($messages as $message) {
            $result[] = $message->toArray();
        }


        return $this->render('view_message/index.html.twig', [
            'messages' => $result,
        ]);
    }
}
