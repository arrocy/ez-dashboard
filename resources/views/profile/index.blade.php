@extends('layouts.main')

@section('title', __('Profile'))
@section('page-heading', __('User Profile'))

@section('content')
<div>
    <h1 class="h3 fw-bold mb-1">@lang('User Profile')</h1>
    <p class="text-body-secondary mb-0">@lang('Update the name and email address associated with your account.')</p>
</div>

<x-card class="my-3">
    <div class="d-flex align-items-center gap-3">
        <x-avatar :src="$user->avatar" :name="$user->name" size="xl" />
        <div><h2 class="h5 fw-bold mb-1">{{ $user->name }}</h2><p class="text-body-secondary mb-0">@lang('Account profile')</p></div>
    </div>
    <form method="POST" action="{{ route('profile.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="row row-cols-md-2 row-cols-xl-3 row-cols-xxl-4 g-3 py-4">
            <x-input name="name" :value="$user->name" icon="fa-solid fa-user" autocomplete="name" required />
            <x-input name="email" type="email" hint="{{ $user->hasVerifiedEmail() ? '' : 'Your email address is not verified.' }}" :value="$user->email" icon="fa-solid fa-envelope" autocomplete="email" required />
            <x-input name="phone" type="tel" :value="$user->phone" icon="fa-solid fa-phone" />
            <x-input name="avatar" :value="$user->avatar" icon="fa-solid fa-image" />
            <x-input name="password" type="password" icon="fa-solid fa-lock" placeholder="{{ __('Leave blank to keep current') }}" />
            <x-input name="password_confirmation" type="password" icon="fa-solid fa-lock" />
            <x-select name="timezone" icon="fa-solid fa-clock">
                @foreach (config('app.timezones') as $key => $value)
                <option value="{{ $key }}" {{ $user->timezone === $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </x-select>
        </div>
        <x-button variant="dark" type="submit"><i class="fa-regular fa-floppy-disk me-2"></i>@lang('Save changes')</x-button>
    </form>
</x-card>

@if (class_exists(\Arrocy\Saas\SaasServiceProvider::class))
    <x-card class="my-3">
        <div class="d-flex align-items-center gap-3 mb-3">
            <x-avatar :src="$subscription->pricing->avatar" :name="$subscription->pricing->name" size="xl" />
            <div>
                <h2 class="h5 fw-bold mb-1">{{ strtoupper($subscription->pricing->name) }}</h2>
                <p class="text-body-secondary mb-0">@lang('Subscription status: :status', ['status' => ucfirst($subscription->status)])</p>
                <p class="text-body-secondary mb-0">@lang('Valid from :start to :end', ['start' => $subscription->starts_at->format('M d, Y'), 'end' => $subscription->ends_at->format('M d, Y')])</p>
                <ul class="mb-0 ps-3">
                    @foreach ($subscription->pricing->features as $key => $value)
                    <li>{{ $key }}: {{ short_num($value) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
            @if ($subscription->pricing->name !== 'free' && !$subscription->gateway_subscription_id)
            <x-button variant="dark" type="button" data-bs-toggle="modal" data-bs-target="#extendSubscriptionModal-{{ $subscription->id }}">
                <i class="fa-solid fa-forward me-2"></i>@lang('Extend Subscription')
            </x-button>
            @endif
            @if ($subscription->gateway_subscription_id && !$subscription->cancel_at_period_end)
            <form method="POST" action="{{ route('saas.subscription.cancel', $subscription) }}" onsubmit="return confirm('{{ __('Cancel automatic renewal?') }}')">
                @csrf
                <x-button variant="danger" type="submit"><i class="fa-solid fa-ban me-2"></i>@lang('Cancel Subscription')</x-button>
            </form>
            @endif
            <x-button variant="dark" type="button" data-bs-toggle="modal" data-bs-target="#changeSubscriptionModal-{{ $subscription->id }}">
                <i class="fa-solid fa-retweet me-2"></i>@lang('Change Subscription')
            </x-button>
        </div>
    </x-card>

    @if ($subscription->pricing->name !== 'free')
    <x-modal id="extendSubscriptionModal-{{ $subscription->id }}" title="Extend Subscription">
        <form id="extend-subscription-form" method="POST" action="{{ route('saas.subscription.extend', $subscription) }}">
            @csrf
            <x-select name="paygate_id" label="Payment Gateways" class="mb-3" required>
                @forelse($paygates as $paygate)
                    <option value="{{ $paygate->id }}">{{ strtoupper($paygate->name) }}</option>
                @empty
                    <option value="" disabled selected>No payment gateway available</option>
                @endforelse
            </x-select>
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="dark" type="submit" form="extend-subscription-form">@lang('Extend')</x-button>
        </x-slot:footer>
    </x-modal>
    @endif

    <x-modal id="changeSubscriptionModal-{{ $subscription->id }}" title="Change Subscription">
        <form id="change-subscription-form" method="POST" action="{{ route('saas.subscription.change', $subscription) }}">
            @csrf
            <x-select name="pricing_id" label="Plan" class="mb-3">
                @foreach($pricings as $pricing)
                    @if ($pricing->id === $subscription->pricing->id) @continue @endif
                    <option value="{{ $pricing->id }}"><x-avatar :src="$pricing->avatar" :name="$pricing->name" />{{ strtoupper($pricing->name) }} [{{ setting('currency_code') }} {{ number_format($pricing->price) }}] [{{ short_num($pricing->credit) }} @lang('credits')] [{{ $pricing->duration }} @lang('days')]</option>
                @endforeach
            </x-select>
            <x-select name="paygate_id" label="Payment Gateways" class="mb-3" required>
                @forelse($paygates as $paygate)
                    <option value="{{ $paygate->id }}">{{ strtoupper($paygate->name) }}</option>
                @empty
                    <option value="" disabled selected>No payment gateway available</option>
                @endforelse
            </x-select>
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="dark" type="submit" form="change-subscription-form">@lang('Change')</x-button>
        </x-slot:footer>
    </x-modal>

    <x-card class="my-3">
        <div class="d-flex align-items-center gap-3">
            <x-avatar :src="$user->avatar" :name="$user->name" size="xl" />
            <div><h2 class="h5 fw-bold mb-1">{{ $user->name }}</h2><p class="text-body-secondary mb-0">@lang('Orders')</p></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-body-secondary">{{ $orders->total() }} @lang('orders found')</span>
            <x-button variant="primary" outline size="sm" type="submit"><i class="fa-solid fa-file-export me-1"></i>@lang('Export')</x-button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col">@lang('Reference')</th>
                        <th scope="col">@lang('Name')</th>
                        <th scope="col">@lang('Plan')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $recordId = $order->getKey();
                        @endphp
                        <tr>
                            <td>{{ $order->reference }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ strtoupper($order->pricing->name) }}</td>
                            <td>{{ number_format($order->amount) }}</td>
                            <td>{{ strtoupper($order->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty-state title="No orders found" description="Try another search or add a new order." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-top pt-3 mt-2">
            <span class="small text-body-secondary">@lang('Showing') {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} @lang('of') {{ $orders->total() }}</span>
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </x-card>
@endif

@if (! $user->hasRole('admin') && setting('user_delete_account_enabled'))
<x-card class="my-3 border-danger">
    <div class="d-flex align-items-center gap-2 text-danger mb-3">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <h2 class="h5 fw-bold mb-0">@lang('Delete Account')</h2>
    </div>
    <p class="mb-3">
        @lang('Deleting your account permanently removes your subscription, wallet or credit balance, orders and payment history, and other data associated with your account from our database.')
        @lang('Any active automatic renewal will be canceled with its payment provider before deletion.')
        <strong>@lang('This action cannot be undone, and the data cannot be restored.')</strong>
    </p>
    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <x-input name="password" type="password" autocomplete="current-password" required />
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input @error('confirm_deletion') is-invalid @enderror" type="checkbox" name="confirm_deletion" id="confirm-deletion" value="1" required>
                    <label class="form-check-label" for="confirm-deletion">
                        @lang('I understand that this deletion is permanent and cannot be reversed.')
                    </label>
                    @error('confirm_deletion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <x-button variant="danger" type="submit">
                    <i class="fa-solid fa-trash-can me-2"></i>@lang('Permanently delete my account')
                </x-button>
            </div>
        </div>
    </form>
</x-card>
@endif
@endsection
