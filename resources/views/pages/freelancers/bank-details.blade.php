@extends('layouts.master')

@section('title', __('bank_details'))

@section('content')

<div class="content">
<div class="main-content">

<div class="block justify-between page-header md:flex">
    <div>
        <h3 class="text-lg font-semibold">{{ __('bank_details') }}</h3>
    </div>

    <a href="{{ url()->previous() }}"
       class="px-4 py-2 bg-secondary text-white rounded-lg">
       {{ __('back') }}
    </a>
</div>

<div class="container">

<div class="grid grid-cols-12 gap-6">

<div class="col-span-12 lg:col-span-6">

<div class="box">

<div class="box-header">
<h5 class="box-title">{{ __('freelancer_information') }}</h5>
</div>

<div class="box-body">

<table class="table">
<tr>
<td class="font-semibold">{{ __('name') }}</td>
<td>{{ $user->username }}</td>
</tr>

<tr>
<td class="font-semibold">{{ __('email') }}</td>
<td>{{ $user->email }}</td>
</tr>

<tr>
<td class="font-semibold">{{ __('phone') }}</td>
<td>{{ $user->full_phone }}</td>
</tr>

</table>

</div>

</div>

</div>


<div class="col-span-12 lg:col-span-6">

<div class="box">

<div class="box-header">
<h5 class="box-title">{{ __('bank_details') }}</h5>
</div>

<div class="box-body">

<table class="table">

<tr>
<td class="font-semibold">{{ __('bank_name') }}</td>
<td>{{ $bank->bank_name ?? '-' }}</td>
</tr>

<tr>
<td class="font-semibold">{{ __('account_number') }}</td>
<td>{{ $bank->account_number ?? '-' }}</td>
</tr>

<tr>
<td class="font-semibold">{{ __('iban') }}</td>
<td>{{ $bank->iban ?? '-' }}</td>
</tr>

<tr>
<td class="font-semibold">{{ __('swift_code') }}</td>
<td>{{ $bank->swift_code ?? '-' }}</td>
</tr>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

@endsection