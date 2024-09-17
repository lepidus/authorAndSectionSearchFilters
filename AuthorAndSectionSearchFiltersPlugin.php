<?php

namespace APP\plugins\generic\authorAndSectionSearchFilters;

use PKP\plugins\Hook;
use PKP\plugins\GenericPlugin;
use APP\core\Application;
use APP\facades\Repo;
use PKP\security\Role;
use APP\template\TemplateManager;
use APP\submission\Submission;
use APP\plugins\generic\authorAndSectionSearchFilters\classes\SearchBySectionDAO;

class AuthorAndSectionSearchFiltersPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);

        if (Application::isUnderMaintenance()) {
            return $success;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            Hook::add('TemplateManager::display', [$this, 'replaceAuthorsFilter']);
            Hook::add('Templates::Search::SearchResults::AdditionalFilters', [$this, 'createSectionsSearchFilter']);
            Hook::add('LoadHandler', [$this, 'replaceSearchHandler']);
        }

        return $success;
    }

    public function getDisplayName()
    {
        return __('plugins.generic.authorAndSectionSearchFilters.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.authorAndSectionSearchFilters.description');
    }

    public function replaceAuthorsFilter($hookName, $params)
    {
        $templateMgr = $params[0];
        $template = $params[1];

        if ($template !== 'frontend/pages/search.tpl') {
            return false;
        }

        $request = Application::get()->getRequest();
        $styleUrl = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/sectionFilter.css';

        $templateMgr->assign('sections', $request->getUserVar('sections'));
        $templateMgr->addStyleSheet('sectionSearchFilter', $styleUrl, ['contexts' => 'frontend']);

        $templateMgr->registerFilter("output", array($this, 'replaceAuthorsInputFieldFilter'));
    }

    public function replaceAuthorsInputFieldFilter($output, $templateMgr): string
    {
        $context = Application::get()->getRequest()->getContext();
        $pattern = '/<input type="text".*name="authors" [^>]+>/';
        $templateName = $context->getData('themePluginPath') == "classic" ?
            'newAuthorsFilterClassicTheme.tpl' : 'newAuthorsFilter.tpl';

        if (preg_match($pattern, $output, $matches, PREG_OFFSET_CAPTURE)) {
            $match = $matches[0][0];
            $offset = $matches[0][1];

            $templateMgr->assign('authorsList', $this->loadAuthors());
            $newOutput = substr($output, 0, $offset);
            $newOutput .= $templateMgr->fetch($this->getTemplateResource($templateName));
            $newOutput .= substr($output, $offset + strlen($match));
            $output = $newOutput;

            $templateMgr->unregisterFilter('output', array($this, 'replaceAuthorsInputFieldFilter'));
        }
        return $output;
    }

    private function loadAuthors(): array
    {
        $context = Application::get()->getRequest()->getContext();

        $authors = Repo::author()->getCollector()
            ->filterByContextIds([$context->getId()])
            ->getMany();

        return $this->filterContributingAuthors($authors);
    }

    private function filterContributingAuthors(\Illuminate\Support\LazyCollection $authors): array
    {
        $authorNames = [];

        foreach ($authors as $author) {
            $fullName = $author->getFullName();

            if (!isset($authorNames[$fullName])) {
                $dao = new SearchBySectionDAO();
                $publicationId = $author->getData('publicationId');

                if ($dao->publicationIsPublished($publicationId)) {
                    $authorNames[$fullName] = $author->getLocalizedFamilyName() . ', ' . $author->getLocalizedGivenName();
                }
            }
        }
        asort($authorNames);
        return $authorNames;
    }

    public function createSectionsSearchFilter($hookName, $params): bool
    {
        $templateMgr = $params[1];
        $output = &$params[2];

        $styleUrl = Application::get()->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/sectionFilter.css';
        $templateMgr->addStyleSheet('sectionSearchFilter', $styleUrl, ['contexts' => 'frontend']);

        $templateMgr->assign('sectionsList', $this->loadSections());

        $context = Application::get()->getRequest()->getContext();
        $templateName = $context->getData('themePluginPath') == "classic" ?
            'sectionSearchFilterClassicTheme.tpl' : 'sectionSearchFilter.tpl';

        $output .= $templateMgr->fetch($this->getTemplateResource($templateName));

        return false;
    }

    private function loadSections(): array
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $sections = Repo::section()->getSectionList($contextId, true);

        $sectionsTitles = [];
        foreach ($sections as $section) {
            $sectionsTitles[$section['id']] = $section['title'];
        }
        asort($sectionsTitles);
        return $sectionsTitles;
    }

    public function replaceSearchHandler($hookName, $params)
    {
        $page = $params[0];
        if ($page === 'search') {
            define('HANDLER_CLASS', 'APP\plugins\generic\authorAndSectionSearchFilters\classes\SectionFilterSearchHandler');
            return true;
        }
        return false;
    }
}
