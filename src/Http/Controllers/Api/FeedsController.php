<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use SimplePie;

class FeedsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */


    public function ffdl()
    {
        $feed = new SimplePie();
        $feed->set_cache_location(storage_path() . '/simplepie_cache');
        $feed->handle_content_type();
        $feed->set_feed_url(array('http://faithfordailyliving.org'));
        $feed->init();
        $prayer=$feed->get_items()[0];
        $data['prayer']['title']=$prayer->get_title();
        $data['prayer']['content']=$prayer->get_content();
        $devotion=$feed->get_items()[1];
        $data['devotion']['title']=$devotion->get_title();
        $data['devotion']['content']=$devotion->get_content();
        return $data;
    }
}
