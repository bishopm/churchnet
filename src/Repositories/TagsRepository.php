<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Cviebrock\EloquentTaggable\Models\Tag;

class TagsRepository extends EloquentBaseRepository
{
    public function checktag($record, $tagname)
    {
        return $record->tags->contains(function (Tag $tag) use ($tagname) {
            return $tag->name === $tagname;
        });
    }

    public function all()
    {
        $tags = Tag::all();
        $data=array();
        foreach ($tags as $tag) {
            $dum['id']=$tag->tag_id;
            $dum['name']=$tag->name;
            $data[$tag->type][]=$dum;
        }
        return $data;
    }
}
