<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Channel\UpdateRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Services\TagSyncService;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\NameSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use App\Support\QueryBuilder\Sorts\UpdatedSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ChannelController extends Controller
{
    /**
     * @var TagSyncService
     */
    protected $tagSyncService;

    public function __construct(TagSyncService $tagSyncService)
    {
        $this->authorizeResource(Channel::class, 'channel');

        $this->tagSyncService = $tagSyncService;
    }

    /**
     * @return ChannelResource
     */
    public function index()
    {
        $query = Channel::currentStatus(['published']);

        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $channels = QueryBuilder::for($query)
            ->allowedAppends(['items', 'thumbnail_url'])
            ->allowedIncludes(['media', 'model', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new NameSorter())->defaultDirection('asc'),
                AllowedSort::custom('recent', new RecentSorter())->defaultDirection('desc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated', new UpdatedSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return ChannelResource::collection($channels);
    }

    /**
     * @param Channel $channel
     *
     * @return ChannelResource
     */
    public function show(Channel $channel)
    {
        // Tracking
        $channel->recordActivity('viewed');
        $channel->recordView('view_count', now()->addYear());

        return new ChannelResource(
            $channel->load(['model', 'tags'])
                    ->append(['thumbnail', 'items'])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Channel       $channel
     *
     * @return ChannelResource
     */
    public function update(UpdateRequest $request, Channel $channel)
    {
        // Set attributes
        $channel->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        // Set status
        if ($request->has('status')) {
            $channel->setStatus($request->input('status'), 'user request');
        }

        // Sync tags
        $this->tagSyncService->sync(
            $channel,
            $request->input('tags')
        );

        return new ChannelResource($channel);
    }

    /**
     * @param Channel $channel
     *
     * @return ChannelResource|JsonResponse
     */
    public function destroy(Channel $channel)
    {
        if ($channel->delete()) {
            return new ChannelResource($channel);
        }

        return response()->json('Unable to delete channel', 500);
    }
}
