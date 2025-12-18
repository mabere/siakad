<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('https://surat-siakad.test/images/bg.png') no-repeat center center/cover;
        }

        .login-container {
            max-width: 900px;
            width: 100%;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="row align-items-center">
            <div class="col-md-6 text-white text-center bg-info p-5">
                <h2>Selamat Datang di SIAKAD</h2>
                <p>Platform akademik untuk mengelola data mahasiswa, dosen, dan administrasi secara efisien.</p>
            </div>
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Silakan Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <input type="hidden" name="role" value="{{ $role }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" required autofocus>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login sebagai {{ ucfirst($role)
                                }}</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
