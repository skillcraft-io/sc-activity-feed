@if ($histories->isNotEmpty())
<div class="row py-3 px-2">
    @foreach ($histories as $history)
        @include('plugins/sc-activity-feed::feed-item', compact('history'))
    @endforeach
</div>

    @if ($histories instanceof Illuminate\Pagination\LengthAwarePaginator)
        <x-core::card.footer>
            {{ $histories->links('core/base::components.simple-pagination') }}
        </x-core::card.footer>
    @endif
@else
    <x-core::empty-state
        :title="__('No results found')"
        :subtitle="__('It looks as through there are no activities here.')"
    />
@endif
