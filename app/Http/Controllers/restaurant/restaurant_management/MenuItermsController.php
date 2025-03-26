<?php

namespace App\Http\Controllers\restaurant\restaurant_management;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class MenuItermsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menuItems = MenuItem::all();
        return view('restaurant.management.menu_iterms.menu_list', compact('user', 'menuItems'));
    }

    public function create()
    {
        $user = Auth::user();
        return view('restaurant.management.menu_iterms.add', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
        ]);

        MenuItem::create($request->all());
        return redirect()->route('restaurant.management.menu_iterms.add')->with('success', 'Menu item created successfully!');
    }

    public function show(MenuItem $menuItem)
    {
        $user = Auth::user();
        return view('restaurant.management.menu_iterms.show', compact('user', 'menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $user = Auth::user();
        return view('management.menu-items.edit', compact('user', 'menuItem'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
        ]);

        $menuItem->update($request->all());
        return redirect()->route('management.menu-items.index')->with('success', 'Menu item updated successfully!');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return redirect()->route('management.menu-items.index')->with('success', 'Menu item deleted successfully!');
    }

}
