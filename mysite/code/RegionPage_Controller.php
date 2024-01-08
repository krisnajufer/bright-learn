<?php

class RegionPage_Controller extends Page_Controller
{
    private static $allowed_actions = array(
        'show'
    );

    public function show(SS_HTTPRequest $request)
    {
        $region = Region::get()->byID($request->param('ID'));

        if (!$region) {
            return $this->httpError(404, 'That region could not be found');
        }

        return array(
            'Region' => $region,
            'Title' => $region->Title
        );
    }
}
