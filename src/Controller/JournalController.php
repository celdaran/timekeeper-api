<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

use App\Dto\JournalCreateRequest;
use App\Service\JournalService;

#[OA\Tag(name: 'Journal Management')]
final class JournalController extends BaseController
{
    private JournalService $journalService;

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

    #[Route('/api/v1/journal/import/{profileId}', name: 'journal_import', methods: ['POST'])]
    public function import(Request $request, int $profileId): JsonResponse
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('data');

        if (!$uploadedFile) {
            return $this->json(ApiResponse::error(['error' => 'Could not find file to import']));
        }

        $temporaryPath = $uploadedFile->getRealPath();
        $originalFilename = $uploadedFile->getClientOriginalName();

        $rowCount = $this->journalService->import($temporaryPath, $originalFilename, $profileId);

        if ($rowCount) {
            return $this->json(ApiResponse::success(['import' => "successfully imported $rowCount rows"]));
        } else {
            return $this->json(ApiResponse::error(['error' => 'Could not import']));
        }
    }

}
