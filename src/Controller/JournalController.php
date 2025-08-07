<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\JournalCreateRequest;
use App\Service\JournalService;

#[OA\Tag(name: 'Journal Management')]
final class JournalController extends BaseController
{
    private JournalService $tagService;

    public function __construct(JournalService $tagService) {
        $this->journalService = $tagService;
    }

    #[Route('/api/v1/journal', name: 'journal_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] JournalCreateRequest $journal): JsonResponse
    {
        $journalId = $this->journalService->create($journal);
        return $this->json(ApiResponse::success(['journal' => $journalId]));
    }

    #[Route('/api/v1/journal/{id}', name: 'journal_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->journalService, $id, $request);
    }

    #[Route('/api/v1/journal/{id}', name: 'journal_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->journalService, $id, $request);
    }

}
