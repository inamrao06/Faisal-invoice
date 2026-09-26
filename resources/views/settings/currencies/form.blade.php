@extends('layouts.app')
@section('title', $currency->exists ? 'Edit Currency' : 'Add Currency')
@section('page_title', $currency->exists ? 'Edit Currency' : 'Add Currency')

@section('content')
<div class="currency-form-wrap">
    <div class="page-actions">
        <div>
            <h2>{{ $currency->exists ? 'Edit Currency' : 'Add Currency' }}</h2>
            <p>{{ $currency->exists ? 'Update currency details and availability.' : 'Add a currency for company accounts and transactions.' }}</p>
        </div>
        <a class="btn btn-light" href="{{ route('settings.currencies.index') }}"><i class="ti ti-arrow-left me-1"></i>Back to Currencies</a>
    </div>

    <form class="card currency-form-card" method="POST" action="{{ $currency->exists ? route('settings.currencies.update', $currency) : route('settings.currencies.store') }}">
        @csrf
        @if($currency->exists) @method('PUT') @endif

        <div class="card-header">
            <h3 class="card-title mb-0"><i class="ti ti-currency-dollar me-2 text-primary"></i>Currency Details</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="currency-name">Name <span class="text-danger">*</span></label>
                    <input id="currency-name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $currency->name) }}" maxlength="80" placeholder="e.g. Pakistani Rupee" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="currency-code">Code <span class="text-danger">*</span></label>
                    <input id="currency-code" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $currency->code) }}" maxlength="10" placeholder="e.g. PKR" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="currency-symbol">Symbol</label>
                    <input id="currency-symbol" class="form-control @error('symbol') is-invalid @enderror" name="symbol" value="{{ old('symbol', $currency->symbol) }}" maxlength="10" placeholder="e.g. Rs">
                    @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="card-body currency-form-options">
            <div class="form-check form-switch">
                <input type="hidden" name="is_default" value="0">
                <input id="currency-default" class="form-check-input" type="checkbox" name="is_default" value="1" @checked(old('is_default', $currency->is_default))>
                <label class="form-check-label" for="currency-default">Default currency</label>
                <p class="text-muted fs-12 mb-0">Only one currency can be marked as default.</p>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input id="currency-active" class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $currency->exists ? $currency->is_active : true))>
                <label class="form-check-label" for="currency-active">Active</label>
                <p class="text-muted fs-12 mb-0">Available when creating or editing companies.</p>
            </div>
        </div>
        <div class="card-footer currency-form-footer">
            <a class="btn btn-light" href="{{ route('settings.currencies.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>{{ $currency->exists ? 'Update Currency' : 'Create Currency' }}</button>
        </div>
    </form>
</div>
@endsection
