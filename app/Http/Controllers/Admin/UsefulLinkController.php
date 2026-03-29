<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UsefulLinkLocation;
use App\Http\Controllers\Controller;
use App\Models\UsefulLink;
use App\Settings\LocaleSettings;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsefulLinkController extends Controller
{
    const READ_PERMISSION = "admin.useful_links.read";
    const WRITE_PERMISSION = "admin.useful_links.write";
    private const ICON_CLASS_REGEX = '/^(fa[srldb]?|fab)(\s+fa-[a-z0-9-]+){1,6}$/i';
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index(LocaleSettings $locale_settings)
    {
        $this->checkAnyPermission([self::READ_PERMISSION, self::WRITE_PERMISSION]);
        return view('admin.usefullinks.index', [
            'locale_datatables' => $locale_settings->datatables
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {
        $this->checkPermission(self::WRITE_PERMISSION);
        $positions = UsefulLinkLocation::cases();
        return view('admin.usefullinks.create')->with('positions', $positions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $positions = array_map(static fn (UsefulLinkLocation $location) => $location->value, UsefulLinkLocation::cases());

        $validated = $request->validate([
            'icon' => ['required', 'string', 'max:120', 'regex:' . self::ICON_CLASS_REGEX],
            'title' => 'required|string|max:60',
            'link' => 'required|url|string|max:191',
            'description' => 'required|string|max:2000',
            'position' => ['required', 'array', 'min:1'],
            'position.*' => ['required', Rule::in($positions)],
        ]);


        UsefulLink::create([
            'icon' => $this->normalizeIconClass($validated['icon']),
            'title' => $validated['title'],
            'link' => $validated['link'],
            'description' => $validated['description'],
            'position' => implode(",", $validated['position']),
        ]);

        return redirect()->route('admin.usefullinks.index')->with('success', __('link has been created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  UsefulLink  $usefullink
     * @return Response
     */
    public function show(UsefulLink $usefullink)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UsefulLink  $usefullink
     * @return Application|Factory|View
     */
    public function edit(UsefulLink $usefullink)
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $positions = UsefulLinkLocation::cases();
        return view('admin.usefullinks.edit', [
            'link' => $usefullink,
            'positions' => $positions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  UsefulLink  $usefullink
     * @return RedirectResponse
     */
    public function update(Request $request, UsefulLink $usefullink)
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $positions = array_map(static fn (UsefulLinkLocation $location) => $location->value, UsefulLinkLocation::cases());

        $validated = $request->validate([
            'icon' => ['required', 'string', 'max:120', 'regex:' . self::ICON_CLASS_REGEX],
            'title' => 'required|string|max:60',
            'link' => 'required|url|string|max:191',
            'description' => 'required|string|max:2000',
            'position' => ['required', 'array', 'min:1'],
            'position.*' => ['required', Rule::in($positions)],
        ]);

        $usefullink->update([
            'icon' => $this->normalizeIconClass($validated['icon']),
            'title' => $validated['title'],
            'link' => $validated['link'],
            'description' => $validated['description'],
            'position' => implode(",", $validated['position']),
        ]);

        return redirect()->route('admin.usefullinks.index')->with('success', __('link has been updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  UsefulLink  $usefullink
     * @return Response
     */
    public function destroy(UsefulLink $usefullink)
    {
        $this->checkPermission(self::WRITE_PERMISSION);
        $usefullink->delete();

        return redirect()->back()->with('success', __('product has been removed!'));
    }

    public function dataTable()
    {
        $this->checkAnyPermission([self::READ_PERMISSION, self::WRITE_PERMISSION]);

        $query = UsefulLink::query();

        return datatables($query)
            ->addColumn('actions', function (UsefulLink $link) {
                return '
                            <a data-content="'.__('Edit').'" data-toggle="popover" data-trigger="hover" data-placement="top" href="'.route('admin.usefullinks.edit', $link->id).'" class="btn btn-sm btn-info mr-1"><i class="fas fa-pen"></i></a>

                           <form class="d-inline" onsubmit="return submitResult();" method="post" action="'.route('admin.usefullinks.destroy', $link->id).'">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                           <button data-content="'.__('Delete').'" data-toggle="popover" data-trigger="hover" data-placement="top" class="btn btn-sm btn-danger mr-1"><i class="fas fa-trash"></i></button>
                       </form>
                ';
            })
            ->editColumn('created_at', function (UsefulLink $link) {
                return $link->created_at ? $link->created_at->diffForHumans() : '';
            })
            ->editColumn('icon', function (UsefulLink $link) {
                $iconClass = $this->normalizeIconClass($link->icon);

                return '<i class="' . e($iconClass) . '"></i>';
            })
            ->rawColumns(['actions', 'icon'])
            ->make();
    }

    private function normalizeIconClass(string $icon): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($icon)) ?? '';
        if ($normalized === '' || !preg_match(self::ICON_CLASS_REGEX, $normalized)) {
            return 'fas fa-link';
        }

        $parts = explode(' ', Str::lower($normalized));
        $parts = array_values(array_unique(array_filter($parts, static fn (string $part) => preg_match('/^fa[srldb]?$|^fab$|^fa-[a-z0-9-]+$/', $part))));

        if (empty($parts)) {
            return 'fas fa-link';
        }

        return implode(' ', $parts);
    }
}
