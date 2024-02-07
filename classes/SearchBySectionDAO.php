<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters\classes;

use PKP\db\DAO;
use Illuminate\Support\Facades\DB;
use APP\facades\Repo;
use APP\submission\Submission;
use APP\submission\Collector as SubmissionCollector;

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

    public function retrieveSubmissionsBySection(int $contextId, int $sectionId, ?string $fromDate, ?string $toDate)
    {
        $query = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByStatus([Submission::STATUS_PUBLISHED])
            ->filterBySectionIds([$sectionId])
            ->orderBy(SubmissionCollector::ORDERBY_DATE_PUBLISHED, SubmissionCollector::ORDER_DIR_ASC)
            ->getQueryBuilder();

        if (!empty($fromDate)) {
            $query->where('po.date_published', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->where('po.date_published', '<=', $toDate);
        }

        $submissions = [];
        foreach ($query->get() as $row) {
            $row = get_object_vars($row);

            $submissions[] = [
                'submissionId' => $row['submission_id'],
                'contextId' => $row['context_id'],
                'datePublished' => $row['date_published']
            ];
        }

        return $submissions;
    }

    public function publicationIsPublished(int $publicationId): bool
    {
        $result = DB::table('publications')
            ->where('publication_id', $publicationId)
            ->select('status')
            ->first();
        $status = get_object_vars($result)['status'];

        return $status == Submission::STATUS_PUBLISHED;
    }
}
