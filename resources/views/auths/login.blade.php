<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Patrol System</title>
    <link rel="icon" href="{{ asset('assets/favicon.png') }}">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">



    <style>
        .login-container {
            transition: all 0.4s ease;
        }

        .login-form {
            display: none;
        }

        .login-form.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-switch.active {
            background-color: #4e73df;
            color: #fff;
        }

        .bg-login-image {
            background: url('{{ asset(' assets/img/login-bg.jpg') }}') center center;
            background-size: cover;
        }

        #reader {
            width: 100%;
            height: 300px;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0 d-flex flex-wrap">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6 p-5 login-container">
                            <div class="text-center mb-4">
                                <h1 class="h4 text-gray-900">Patrol 5S</h1>
                            </div>

                            {{-- Error message --}}
                            @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                            @endif

                            {{-- ADMIN LOGIN FORM --}}
                            <form id="formAdmin" class="user login-form active" method="POST"
                                action="{{ route('login.admin') }}">
                                @csrf
                                <div class="form-group">
                                    <input name="Username_User" type="text" class="form-control form-control-user"
                                        placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input name="Password_User" type="password" class="form-control form-control-user"
                                        placeholder="Password">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


    <script>
        // document.addEventListener("DOMContentLoaded", function() {
        //     const qrDiv = document.createElement('div');
        //     qrDiv.id = "reader";
        //     qrDiv.style.display = "none";
        //     qrDiv.style.marginTop = "10px";
        //     document.getElementById("formMember").appendChild(qrDiv);

        //     const btnScan = document.createElement("button");
        //     btnScan.type = "button";
        //     btnScan.className = "btn btn-secondary btn-user btn-block mt-2";
        //     btnScan.textContent = "Scan Barcode NIK";
        //     document.getElementById("formMember").appendChild(btnScan);

        //     let html5QrCode = new Html5Qrcode("reader");

        //     btnScan.addEventListener("click", function() {
        //         qrDiv.style.display = "block";
        //         Html5Qrcode.getCameras().then(devices => {
        //             if (devices && devices.length) {
        //                 const rearCamera = devices.find(c => /back|rear|environment/i.test(c.label));
        //                 const camId = rearCamera ? rearCamera.id : devices[0].id;

        //                 html5QrCode.start(
        //                     camId, {
        //                         fps: 10,
        //                         qrbox: {
        //                             width: 250,
        //                             height: 250
        //                         }
        //                     },
        //                     qrCodeMessage => {
        //                         document.querySelector('[name="nik"]').value = qrCodeMessage;
        //                         html5QrCode.stop();
        //                         qrDiv.style.display = "none";
        //                     },
        //                     errorMessage => {}
        //                 );
        //             }
        //         }).catch(err => {
        //             alert("Tidak bisa membuka kamera: " + err);
        //         });
        //     });
        // });
    </script>

</body>

</html>