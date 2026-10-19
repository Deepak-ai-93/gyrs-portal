<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdministratorRequest;
use App\Models\User;
use App\Services\ApplicationService;
use App\Support\AdminPermissions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    public function __construct(private readonly ApplicationService $audit) {}

    public function index(Request $request): View
    {
        $keyword = trim((string) $request->input('keyword'));

        $administrators = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->when($keyword !== '', function ($query) use ($keyword) {
                $like = '%'.str_replace('%', '\%', $keyword).'%';
                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)->orWhere('email', 'like', $like);
                });
            })
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.administrators.index', [
            'administrators' => $administrators,
            'keyword' => $keyword,
            'permissionLabels' => AdminPermissions::labels(),
            'permissionCount' => count(AdminPermissions::all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.administrators.create', [
            'administrator' => new User(['role' => User::ROLE_ADMIN]),
            'permissionGroups' => AdminPermissions::groups(),
        ]);
    }

    public function store(AdministratorRequest $request): RedirectResponse
    {
        $administrator = User::create([
            'name' => $request->string('name')->trim()->value(),
            'email' => $request->string('email')->trim()->lower()->value(),
            'phone' => $request->input('phone'),
            'password' => $request->string('password')->value(),
            'role' => User::ROLE_ADMIN,
            'is_super_admin' => false,
            'permissions' => $request->input('permissions', []),
            'status' => User::STATUS_VERIFIED,
            'approved_at' => now(),
        ]);

        $this->audit->audit($request->user(), 'administrator.created', $administrator, [
            'permissions' => $administrator->adminPermissions(),
        ]);

        return redirect()->route('admin.administrators.index')
            ->with('status', 'Administrator account created for '.$administrator->name.'.');
    }

    public function edit(User $administrator): View
    {
        abort_unless($administrator->isAdmin(), 404);

        return view('admin.administrators.edit', [
            'administrator' => $administrator,
            'permissionGroups' => AdminPermissions::groups(),
        ]);
    }

    public function update(AdministratorRequest $request, User $administrator): RedirectResponse
    {
        abort_unless($administrator->isAdmin(), 404);

        $data = [
            'name' => $request->string('name')->trim()->value(),
            'email' => $request->string('email')->trim()->lower()->value(),
            'phone' => $request->input('phone'),
        ];

        // A super administrator's access cannot be narrowed from this screen.
        if (! $administrator->isSuperAdmin()) {
            $data['permissions'] = $request->input('permissions', []);
        }

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->value();
        }

        $administrator->update($data);

        $this->audit->audit($request->user(), 'administrator.updated', $administrator, [
            'permissions' => $administrator->adminPermissions(),
        ]);

        return redirect()->route('admin.administrators.index')
            ->with('status', 'Administrator account updated.');
    }

    public function destroy(Request $request, User $administrator): RedirectResponse
    {
        abort_unless($administrator->isAdmin(), 404);

        if ($administrator->is($request->user())) {
            return redirect()->route('admin.administrators.index')
                ->with('error', 'You cannot remove your own administrator account.');
        }

        if ($administrator->isSuperAdmin()) {
            return redirect()->route('admin.administrators.index')
                ->with('error', 'Super administrator accounts cannot be removed.');
        }

        $email = $administrator->email;
        $administrator->delete();

        $this->audit->audit($request->user(), 'administrator.removed', null, [
            'administrator' => $email,
        ]);

        return redirect()->route('admin.administrators.index')
            ->with('status', 'Administrator account removed.');
    }
}
