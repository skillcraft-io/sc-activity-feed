<div class="col-md-6 mb-2">
    <a class="card card-link" href="{!! $history->getOwnerLink() !!}">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <span class="avatar rounded" style="background-image: url({!! $history->getOwnerAvatarLink() !!})">@if(!$history->getOwnerAvatarLink()) AF @endif</span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">{!! $history->owner->name !!}</div>
                    <div class="text-muted">{!! $history->title !!}</div>
                </div>
            </div>
        </div>
    </a>
</div>