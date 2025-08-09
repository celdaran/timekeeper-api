<?php namespace App\Service;

use Exception;

class JournalImportParserService
{
    private array $data = [];

    public function __construct(string $input)
    {
        $input = trim($input);
        $pairs = explode(' | ', $input);

        // 2. Loop through each pair and populate a data array.
        foreach ($pairs as $pair) {
            $parts = explode(' - ', $pair, 2); // Split only on the first ` - `
            if (count($parts) === 2) {
                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $this->data[$key] = $value;
            }
        }
    }

    public function extractTag(): ?string
    {
        return $this->data['tag'] ?? null;
    }

    public function extractLocation(): ?string
    {
        return $this->data['location'] ?? null;
    }

    public function extractProject(): ?string
    {
        return $this->data['project'] ?? null;
    }

    public function extractActivity(): ?string
    {
        return $this->data['activity'] ?? null;
    }
}