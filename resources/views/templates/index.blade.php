@extends('layouts.app')
@section('title', 'Reply Templates')
@section('page_title', 'Reply Templates')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null }">
    <div class="flex justify-between items-center">
        <p class="text-slate-400">Save your best replies as reusable templates.</p>
        <button @click="showForm = !showForm; editId = null" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Template
        </button>
    </div>

    <!-- Create Form -->
    <x-card x-show="showForm" x-collapse>
        <form action="{{ route('templates.store') }}" method="POST" class="space-y-4">
            @csrf
            <x-input name="name" placeholder="Template name" required />
            <x-select name="tone" required>
                <option value="">Select tone...</option>
                @foreach($tones as $tone)
                    <option value="{{ $tone }}">{{ ucfirst($tone) }}</option>
                @endforeach
            </x-select>
            <x-textarea name="body" placeholder="Template content..." rows="4" required />
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Save Template</button>
                <button type="button" @click="showForm = false" class="btn-secondary">Cancel</button>
            </div>
        </form>
    </x-card>

    <!-- Templates List -->
    <div class="grid md:grid-cols-2 gap-4">
        @forelse($templates as $template)
            <x-card>
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-white">{{ $template->name }}</h3>
                        <x-badge type="{{ $template->tone === 'recovery' ? 'warning' : 'info' }}">{{ ucfirst($template->tone) }}</x-badge>
                    </div>
                    <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-slate-500 hover:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
                <p class="text-slate-400 text-sm">{{ Str::limit($template->body, 150) }}</p>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-card class="text-center py-8">
                    <p class="text-slate-500">No templates yet. Create your first one!</p>
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
