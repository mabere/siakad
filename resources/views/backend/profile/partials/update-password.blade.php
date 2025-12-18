<form method="POST" action="{{ $passwordRoute }}#tab-password" class="gy-3 form-settings">
    @csrf
    @method('PUT')
    <div class="row g-3 align-center">
        <div class="col-lg-5">
            <label class="form-label" for="current_password">Password Saat Ini</label>
        </div>
        <div class="col-lg-7">
            <div class="form-control-wrap">
                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="current_password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" class="form-control @error('current_password') error @enderror"
                    id="current_password" name="current_password" placeholder="••••••••">
                <x-custom.input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>
        </div>
    </div>
    <div class="row g-3 align-center">
        <div class="col-lg-5">
            <label class="form-label" for="password">Password Baru</label>
        </div>
        <div class="col-lg-7">
            <div class="form-control-wrap">
                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" class="form-control @error('password') error @enderror" id="password"
                    name="password" placeholder="••••••••">
                <x-custom.input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>
        </div>
    </div>
    <div class="row g-3 align-center">
        <div class="col-lg-5">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        </div>
        <div class="col-lg-7">
            <div class="form-control-wrap">
                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" class="form-control @error('password_confirmation') error @enderror"
                    id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                <x-custom.input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-lg-7 offset-lg-5">
            <button type="submit" class="btn btn-primary">
                <em class="icon ni ni-lock-alt"></em>
                <span>Perbarui Password</span>
            </button>
        </div>
    </div>
</form>