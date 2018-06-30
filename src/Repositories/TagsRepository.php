<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Spatie\Tags\Tag;

class TagsRepository extends EloquentBaseRepository
{
    public function checktag($record, $tagname)
    {
        return $record->tags->contains(function (Tag $tag) use ($tagname) {
            return $tag->name === $tagname;
        });
    }
}
