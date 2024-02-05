<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters\classes;

use PKP\db\DAO;
use Illuminate\Support\Facades\DB;

class SearchBySectionDAO extends DAO
{
    public function submissionIsInSection(int $submissionId, int $sectionId): bool
    {
        $result = DB::table('submissions')
            ->where('submission_id', $submissionId)
            ->select('current_publication_id')
            ->first();
        $currentPublicationId = get_object_vars($result)['current_publication_id'];

        $result = DB::table('publications')
            ->where('publication_id', $currentPublicationId)
            ->select('section_id')
            ->first();
        $publicationSectionId = get_object_vars($result)['section_id'];

        return $publicationSectionId == $sectionId;
    }
}
