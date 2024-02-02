<div class="section">
    <label class="label" for="sections">
        {translate key="plugins.generic.authorAndSectionSearchFilters.search.sections"}
    </label>
    {block name=searchSections}
        {fbvElement type="select" id="sections" name="sections" from=$sections translate="0" size=$fbvStyles.size.MEDIUM}
    {/block}
</div>
