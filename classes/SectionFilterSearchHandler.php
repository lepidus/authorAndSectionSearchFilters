<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters\classes;

use APP\template\TemplateManager;
use APP\facades\Repo;
use APP\pages\search\SearchHandler;
use APP\plugins\generic\authorAndSectionSearchFilters\classes\ArticleSearchBySection;

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

        $articleSearch = new ArticleSearchBySection();
        $searchFilters = $articleSearch->getSearchFilters($request);
        $keywords = $articleSearch->getKeywordsFromSearchFilters($searchFilters);

        $rangeInfo = $this->getRangeInfo($request, 'search');

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
}
