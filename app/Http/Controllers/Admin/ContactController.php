<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('order')->get();
        return view('contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'  => 'required|string',
            'label' => 'nullable|string',
            'value' => 'required|string',
            'icon'  => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        Contact::create($request->all());

        return back()->with('success', 'تم إضافة معلومة التواصل بنجاح');
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        
        $request->validate([
            'type'  => 'required|string',
            'label' => 'nullable|string',
            'value' => 'required|string',
            'icon'  => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $contact->update($request->all());

        return back()->with('success', 'تم تحديث معلومة التواصل بنجاح');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'تم حذف معلومة التواصل');
    }

    public function toggleStatus($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_active = !$contact->is_active;
        $contact->save();

        return back()->with('success', 'تم تغيير حالة التفعيل');
    }
}
