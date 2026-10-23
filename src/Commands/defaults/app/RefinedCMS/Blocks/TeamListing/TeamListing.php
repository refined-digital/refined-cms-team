<?php

namespace App\RefinedCMS\Content\Blocks\TeamListing;

use RefinedDigital\CMS\Modules\Content\BaseContent;
use RefinedDigital\CMS\Modules\Content\Contracts\ContentInterface;
use RefinedDigital\CMS\Modules\Core\Aggregates\AssetAggregate;

class TeamListing extends BaseContent implements ContentInterface
{
    protected string $name = 'Team Listing';

    public function __construct()
    {
        app(AssetAggregate::class)->addStyle('team.css');
    }

    public function fields(): array
    {
        return [
            $this->getField('background'),
            $this->getField('heading'),
            $this->getField('content'),
        ];
    }
}
