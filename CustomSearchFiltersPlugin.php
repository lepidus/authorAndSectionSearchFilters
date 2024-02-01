<?php

namespace APP\plugins\generic\customSearchFilters;

use PKP\plugins\GenericPlugin;
use APP\core\Application;

class CustomSearchFiltersPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);

        if (Application::isUnderMaintenance()) {
            return $success;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            //hooks and events
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
}
