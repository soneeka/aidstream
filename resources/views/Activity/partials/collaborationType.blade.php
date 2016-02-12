@if(!empty($collaborationType))
    <div class="panel panel-default">
        <div class="panel-heading">Collaboration Type
            <a href="{{route('activity.collaboration-type.index', $id)}}" class="edit-element">edit</a>
        </div>
        <div class="panel-element-body row">
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-sm-4">Code: </div>
                <div class="col-xs-12 col-sm-8">{{$getCode->getActivityCodeName('CollaborationType', $collaborationType)}}</div>
            </div>
        </div>
    </div>
@endif
