@extends('layouts.app')

@section('title', 'Gestión de Usuarios - MovieHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Películas</th>
                            <th>Valoraciones</th>
                            <th>Reseñas</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm" 
                                                onchange="this.form.submit()"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                                Usuario
                                            </option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $user->movies_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $user->ratings_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user->reviews_count }}</span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" 
                                              onsubmit="return confirm('¿Eliminar usuario {{ $user->name }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Tú</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
