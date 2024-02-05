<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters\classes;

use APP\facades\Repo;
use PKP\plugins\Hook;
use APP\search\ArticleSearch;
use PKP\search\SubmissionSearch;
use APP\plugins\generic\authorAndSectionSearchFilters\classes\SearchBySectionDAO;
use PKP\core\VirtualArrayIterator;

class ArticleSearchBySection extends ArticleSearch
{
    public function retrieveResults($request, $context, $keywords, &$error, $publishedFrom = null, $publishedTo = null, $rangeInfo = null, $exclude = [])
    {
        if ($rangeInfo && $rangeInfo->isValid()) {
            $page = $rangeInfo->getPage();
            $itemsPerPage = $rangeInfo->getCount();
        } else {
            $page = 1;
            $itemsPerPage = SubmissionSearch::SUBMISSION_SEARCH_DEFAULT_RESULT_LIMIT;
        }

        [$orderBy, $orderDir] = $this->getResultSetOrdering($request);

        $totalResults = null;
        $results = null;
        $hookResult = Hook::call(
            'SubmissionSearch::retrieveResults',
            [&$context, &$keywords, $publishedFrom, $publishedTo, $orderBy, $orderDir, $exclude, $page, $itemsPerPage, &$totalResults, &$error, &$results]
        );

        if ($hookResult === false) {
            foreach ($keywords as $searchType => $query) {
                $keywords[$searchType] = $this->_parseQuery($query);
            }
            $sectionId = $request->getUserVar('sections');

            $mergedResults = [];
            if ($this->searchingForSomething($keywords)) {
                $mergedResults = $this->_getMergedArray($context, $keywords, $publishedFrom, $publishedTo);
                if ($sectionId) {
                    $dao = new SearchBySectionDAO();
                    foreach ($mergedResults as $submissionId => $data) {
                        if (!$dao->submissionIsInSection($submissionId, $sectionId)) {
                            unset($mergedResults[$submissionId]);
                        }
                    }
                }
            } elseif ($sectionId) {
                // WIP: To be implemented...
                // Searching using only section as parameter
            }

            $results = $this->getSparseArray($mergedResults, $orderBy, $orderDir, $exclude);
            $totalResults = count($results);

            $offset = $itemsPerPage * ($page - 1);
            $length = max($totalResults - $offset, 0);
            $length = min($itemsPerPage, $length);
            if ($length == 0) {
                $results = [];
            } else {
                $results = array_slice(
                    $results,
                    $offset,
                    $length
                );
            }
        }

        $results = $this->formatResults($results, $request->getUser());

        return new VirtualArrayIterator($results, $totalResults, $page, $itemsPerPage);
    }

    private function searchingForSomething($keywords): bool
    {
        if (count($keywords) > 1) {
            return true;
        }
        $queryKeywords = $keywords[''];
        if (!empty($queryKeywords['+']) || !empty($queryKeywords['']) || !empty($queryKeywords['-'])) {
            return true;
        }
        return false;
    }
}
