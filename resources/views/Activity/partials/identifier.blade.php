@if(!emptyOrHasEmptyTemplate(getVal($activityDataList, ['identifier'], [])))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label col-md-4">@lang('element.activity_identifier')</div>
            <div class="activity-element-info">
                {{ getVal($activityDataList, ['identifier', 'iati_identifier_text'])}}
            </div>
            <a href="{{route('activity.iati-identifier.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        </div>
    </div>
@endif
