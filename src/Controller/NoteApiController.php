<?php

namespace App\Controller;

use App\Entity\Note;
use App\Enum\Color;
use App\Enum\Priority;
use App\Http\ApiResponseFactory;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use DateTime;

#[Route(path: '/api/v1/notes', name: 'notes_')]
class NoteApiController extends AbstractController
{
    public function __construct(
        private readonly NoteRepository $noteRepository,
        private readonly ApiResponseFactory $api,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(path: '', name: 'get_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $notes = $this->noteRepository->findBy(['deletedAt' => null], ['updatedAt' => 'DESC', 'createdAt' => 'DESC']);

        $data = $this->serializer->normalize($notes, null, [
            'serialize_null' => true,
        ]);

        return $this->api->ok($data);
    }

    #[Route(path: '/{uid}', name: 'get_single', methods: ['GET'])]
    public function getSingle(string $uid): JsonResponse
    {
        if (!Uuid::isValid($uid))
        {
            return $this->api->error('Uid is not valid.', 403);
        }

        $note = $this->noteRepository->findOneBy(['uid' => Uuid::fromString($uid)->toBinary(), 'deletedAt' => null]);

        if (!$note) {
            return $this->api->error('Note not found.', 404);
        }

        $data = $this->serializer->normalize($note, null, [
            'serialize_null' => true,
        ]);

        return $this->api->ok($data);
    }

    #[Route(path: '', name: 'create', methods: ['POST'])]
    public function createNote(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || !isset($data['content'])) {
            return $this->api->error('Title and content are required.');
        }

        $note = new Note($data['title'], $data['content'], isset($data['priority']) ? Priority::tryFrom($data['priority']) : null, isset($data['color']) ? Color::tryFrom($data['color']) : null);

        $this->noteRepository->save($note);

        $responseData = $this->serializer->normalize($note, null, [
            'serialize_null' => true,
        ]);

        return $this->api->ok($responseData, 201);
    }

    #[Route(path: '/{uid}', name: 'update', methods: ['PUT'])]
    public function updateNote(string $uid, Request $request): JsonResponse
    {
        if (!Uuid::isValid($uid)) {
            return $this->api->error('Uid is not valid.', 403);
        }

        $note = $this->noteRepository->findOneBy(['uid' => Uuid::fromString($uid)->toBinary(), 'deletedAt' => null]);

        if (!$note) {
            return $this->api->error('Note not found.', 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $note->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $note->setContent($data['content']);
        }

        if (isset($data['priority'])) {
            $note->setPriority(Priority::tryFrom($data['priority']));
        }

        if (isset($data['color'])) {
            $note->setColor(Color::tryFrom($data['color']));
        }

        $note->setUpdatedAt(new DateTime());
        $this->noteRepository->save($note);

        $responseData = $this->serializer->normalize($note, null, [
            'serialize_null' => true,
        ]);

        return $this->api->ok($responseData);
    }

    #[Route(path: '/{uid}', name: 'delete', methods: ['DELETE'])]
    public function deleteNote(string $uid): JsonResponse
    {
        if (!Uuid::isValid($uid)) {
            return $this->api->error('Uid is not valid.', 403);
        }

        $note = $this->noteRepository->findOneBy(['uid' => Uuid::fromString($uid)->toBinary(), 'deletedAt' => null]);

        if (!$note) {
            return $this->api->error('Note not found.', 404);
        }

        $this->noteRepository->delete($note);

        return $this->api->ok(null, 204);
    }
}
