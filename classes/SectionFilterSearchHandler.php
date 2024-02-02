<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters\classes;

use APP\search\ArticleSearch;
use APP\template\TemplateManager;
use APP\facades\Repo;
use PKP\core\VirtualArrayIterator;
use APP\pages\search\SearchHandler;

class SectionFilterSearchHandler extends SearchHandler
{
    public function index($args, $request)
    {
        $this->validate(null, $request);
        $this->search($args, $request);
    }

    public function search($args, $request)
    {

        $this->validate(null, $request);

        // Get and transform active filters.
        $articleSearch = new ArticleSearch();
        $searchFilters = $articleSearch->getSearchFilters($request);
        $keywords = $articleSearch->getKeywordsFromSearchFilters($searchFilters);

        // Get the range info.
        $rangeInfo = $this->getRangeInfo($request, 'search');

        // Retrieve results.
        $error = '';
        $results = $articleSearch->retrieveResults(
            $request,
            $searchFilters['searchJournal'],
            $keywords,
            $error,
            $searchFilters['fromDate'],
            $searchFilters['toDate'],
            $rangeInfo
        );

        $sectionId = $request->getUserVar('sections');
        if ($sectionId) {
            $results = $this->filterResultsBySection($results, $sectionId);
        }

        $this->setupTemplate($request);

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->setCacheability(TemplateManager::CACHEABILITY_NO_STORE);

        $this->_assignSearchFilters($request, $templateMgr, $searchFilters);
        [$orderBy, $orderDir] = $articleSearch->getResultSetOrdering($request);

        $templateMgr->assign([
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'simDocsEnabled' => true,
            'results' => $results,
            'error' => $error,
            'authorUserGroups' => Repo::userGroup()->getCollector()
                ->filterByRoleIds([\PKP\security\Role::ROLE_ID_AUTHOR])
                ->filterByContextIds($searchFilters['searchJournal'] ? [$searchFilters['searchJournal']->getId()] : null)
                ->getMany()->remember(),
            'searchResultOrderOptions' => $articleSearch->getResultSetOrderingOptions($request),
            'searchResultOrderDirOptions' => $articleSearch->getResultSetOrderingDirectionOptions(),
        ]);

        if (!$request->getContext()) {
            $templateMgr->assign([
                'searchableContexts' => $this->getSearchableContexts(),
            ]);
        }

        $templateMgr->display('frontend/pages/search.tpl');
    }

    private function filterResultsBySection($results, $sectionId)
    {
        $filteredResults = [];

        while ($item = $results->next()) {
            $submission = $item['article'];

            if ($submission->getSectionId() == $sectionId) {
                $filteredResults[] = $item;
            }
        }

        $newResults = new VirtualArrayIterator($filteredResults, count($filteredResults), $results->page, $results->itemsPerPage);

        return $newResults;
    }
}
