@extends('layouts.app')

@section('title', 'Mailing | ForjaLab')

@section('content')
<section class="admin-section"><div class="container">
    @include('admin.partials.menu')
    <div class="admin-header"><div><div class="eyebrow">Comunicación</div><h1 class="fw-bold mt-2 mb-0">Campañas de mailing</h1></div><a class="btn btn-dark" href="{{ route('admin.mailing.create') }}"><i class="bi bi-plus-circle me-2"></i>Nueva campaña</a></div>
    <div class="panel-card table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Campaña</th><th>Estado</th><th>Destinatarios</th><th>Resultados</th><th>Fecha</th><th></th></tr></thead><tbody>
    @forelse($campaigns as $campaign)<tr><td><strong>{{ $campaign->name }}</strong><small class="d-block text-secondary">{{ $campaign->subject }}</small></td><td><span class="badge text-bg-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'partial' ? 'warning' : 'secondary') }}">{{ ucfirst($campaign->status) }}</span></td><td>{{ $campaign->recipient_count }}</td><td><span class="text-success">{{ $campaign->sent_count }} enviados</span>@if($campaign->failed_count)<small class="d-block text-danger">{{ $campaign->failed_count }} fallidos</small>@endif</td><td>{{ $campaign->sent_at?->format('d/m/Y H:i') ?: $campaign->updated_at->format('d/m/Y H:i') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.mailing.edit',$campaign) }}"><i class="bi bi-pencil"></i></a></td></tr>
    @empty<tr><td colspan="6" class="text-center py-5"><i class="bi bi-envelope-paper fs-1 d-block mb-2"></i>Aún no hay campañas.</td></tr>@endforelse
    </tbody></table></div>{{ $campaigns->links() }}
</div></section>
@endsection
