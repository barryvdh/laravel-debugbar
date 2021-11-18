<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\Clockwork\Converter;
use DebugBar\OpenHandler;
use Illuminate\Http\Response;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Storage\EntryQueryOptions;
use Laravel\Telescope\Telescope;

class TelescopeController extends BaseController
{

    public function show(EntriesRepository $storage, $uuid)
    {

        $entry = $storage->find($uuid);
        $result = $storage->get('request', (new EntryQueryOptions())->batchId($entry->batchId))->first();

        return redirect(config('telescope.path') . '/requests/' . $result->id);
    }
}
