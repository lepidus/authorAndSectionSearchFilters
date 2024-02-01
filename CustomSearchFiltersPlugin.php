<?php

namespace APP\plugins\generic\customSearchFilters;

use PKP\plugins\Hook;
use PKP\plugins\GenericPlugin;
use APP\core\Application;
use APP\facades\Repo;
use PKP\security\Role;

class CustomSearchFiltersPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);

        if (Application::isUnderMaintenance()) {
            return $success;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            Hook::add('TemplateManager::display', [$this, 'replaceAuthorsFilter']);
        }

        return $success;
    }

    public function getDisplayName()
    {
        return __('plugins.generic.customSearchFilters.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.customSearchFilters.description');
    }

    public function replaceAuthorsFilter($hookName, $params)
    {
        $templateMgr = $params[0];
        $template = $params[1];

        if ($template !== 'frontend/pages/search.tpl') {
            return false;
        }

        $templateMgr->registerFilter("output", array($this, 'replaceAuthorsInputFieldFilter'));
    }

    public function replaceAuthorsInputFieldFilter($output, $templateMgr)
    {
        $pattern = '/<input type="text" id="authors" [^>]+>/';

        if (preg_match($pattern, $output, $matches, PREG_OFFSET_CAPTURE)) {
            $match = $matches[0][0];
            $offset = $matches[0][1];

            $templateMgr->assign('authors', $this->loadAuthors());
            $newOutput = substr($output, 0, $offset);
            $newOutput .= $templateMgr->fetch($this->getTemplateResource('newAuthorsFilter.tpl'));
            $newOutput .= substr($output, $offset + strlen($match));
            $output = $newOutput;

            $templateMgr->unregisterFilter('output', array($this, 'replaceAuthorsInputFieldFilter'));
        }
        return $output;
    }

    public function loadAuthors(): array
    {
        $context = Application::get()->getRequest()->getContext();

        $authorNames = ['' => ''];
        $authors = Repo::user()->getCollector()
            ->filterByContextIds([$context->getId()])
            ->filterByRoleIds([Role::ROLE_ID_AUTHOR])
            ->getMany();

        foreach ($authors as $author) {
            $fullName = $author->getFullName();
            $authorNames[$fullName] = $fullName;
        }

        return $authorNames;
    }
}
