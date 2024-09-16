<div class="search-form-label">
    <span>{translate key="plugins.generic.authorAndSectionSearchFilters.search.sections"}</span>
</div>
{block name=searchSections}
    {fbvElement type="select" id="sectionsClassicTheme" name="sections" defaultLabel="{translate key="plugins.generic.authorAndSectionSearchFilters.search.noneSelected"}" defaultValue="" selected=$sections from=$sectionsList translate="0" size=$fbvStyles.size.MEDIUM}
{/block}
