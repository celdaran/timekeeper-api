<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JournalImportController extends AbstractController
{
    #[Route('/api/v1/journal/import', name: 'journal_import', methods: ['POST'])]
    public function import(Request $request): Response
    {
        $someOtherField = $request->request->get('first-element-name');
        $anotherField = $request->request->get('second-element-name');

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('data');

        if (!$uploadedFile) {
            return new Response('No file was uploaded.', Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->request->get('userId');

        // At this point, you have access to the file and the user ID.
        // The file is stored in a temporary location.
        $originalFilename = $uploadedFile->getClientOriginalName();
        $temporaryPath = $uploadedFile->getRealPath();

        // -----------------------------------------------------------
        // Now you would start the parsing and validation logic here.
        // For example:
        $fileHandle = fopen($temporaryPath, 'r');
        $allRows = [];
        while (($row = fgetcsv($fileHandle)) !== false) {
            $a = $row[0];
            $b = $row[1];
            $allRows[] = $row;
        }
        fclose($fileHandle);
        // -----------------------------------------------------------

        $message = sprintf(
            'File "%s" (temporary path: %s) received for user ID "%s".',
            $originalFilename,
            $temporaryPath,
            $userId
        );

        return new Response($message);
    }
}