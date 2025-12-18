<x-main-layout>
    @section('title', 'Edit User Pelaksana Teknis')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.users.update', $user->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>

                        <!-- Field Name -->
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name', $user->name) }}" readonly>
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Field Roles (Checkbox) -->
                        <div class="form-group row">
                            <label for="roles" class="col-sm-3 text-end control-label col-form-label">Peran</label>
                            <div class="col-sm-9">
                                @foreach ($roles as $role)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="roles[]"
                                        value="{{ $role->name }}" {{ $user->roles->pluck('name')->contains($role->name)
                                    ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ ucfirst($role->name) }}</label>
                                </div>
                                @endforeach
                                @error('roles')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Field Email -->
                        <div class="form-group row">
                            <label for="email" class="col-sm-3 text-end control-label col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group row">
                            <span class="col-sm-3"></span>
                            <div class="col-sm-9">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-warning">Back</a>
                                <button type="submit" class="btn btn-sm btn-success">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
