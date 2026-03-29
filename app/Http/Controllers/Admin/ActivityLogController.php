<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    const VIEW_PERMISSION = "admin.logs.read";
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index(Request $request)
    {
        $this->checkPermission(self::VIEW_PERMISSION);

        $cronLogs = null;
        if (Storage::disk('logs')->exists('cron.log')) {
            $trimmed = $this->readCronLogTail('cron.log', 100000);
            $lines = preg_split('/\r\n|\r|\n/', (string) $trimmed) ?: [];
            $cronLogs = implode(PHP_EOL, array_slice($lines, -500));
        }

        $query = Activity::query()->with('causer')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = mb_strtolower((string) $request->input('search'));
            $searchLike = '%' . $search . '%';

            $query->where(function ($builder) use ($searchLike) {
                $builder->whereRaw('LOWER(description) LIKE ?', [$searchLike])
                    ->orWhereRaw('LOWER(properties) LIKE ?', [$searchLike])
                    ->orWhereHas('causer', function ($causerQuery) use ($searchLike) {
                        $causerQuery->whereRaw('LOWER(name) LIKE ?', [$searchLike]);
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.activitylogs.index')->with([
            'logs' => $logs,
            'cronlogs' => $cronLogs,
        ]);


    }

    private function readCronLogTail(string $path, int $maxBytes): string
    {
        $disk = Storage::disk('logs');
        $stream = $disk->readStream($path);

        if (!is_resource($stream)) {
            return (string) $disk->get($path);
        }

        try {
            $metadata = stream_get_meta_data($stream);
            $isSeekable = (bool) ($metadata['seekable'] ?? false);
            $size = $disk->size($path);

            if ($isSeekable && is_int($size) && $size > $maxBytes) {
                fseek($stream, max(0, $size - $maxBytes));
            }

            $contents = stream_get_contents($stream);

            if (!is_string($contents)) {
                return '';
            }

            if (!$isSeekable && strlen($contents) > $maxBytes) {
                return substr($contents, -$maxBytes);
            }

            return $contents;
        } finally {
            fclose($stream);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        abort(403, __('User does not have the right permissions.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        abort(403, __('User does not have the right permissions.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
