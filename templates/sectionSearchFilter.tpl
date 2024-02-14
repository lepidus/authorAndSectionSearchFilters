<div class="section">
    <label class="label" for="sections">
        {translate key="plugins.generic.authorAndSectionSearchFilters.search.sections"}
    </label>
    {block name=searchSections}
        {fbvElement type="select" id="sections" name="sections" defaultLabel="{translate key="plugins.generic.authorAndSectionSearchFilters.search.noneSelected"}" defaultValue="" selected=$sections from=$sectionsList translate="0" size=$fbvStyles.size.MEDIUM}
    {/block}
</div>
