<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Iseki Fisrol - Register</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    {{-- <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"> --}}

    <!-- Custom styles for this template-->
    <link href="{{asset ('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" action="{{route('register.create')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                </div>
                                <div class="form-group">
                                    <input name="Username_User" type="text" class="form-control form-control-user" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input name="Name_User" type="text" class="form-control form-control-user" placeholder="Name">
                                </div>
                                <div class="form-group">
                                    <input name="Password_User" type="password" class="form-control form-control-user" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <label for="Id_Type_User">Type User</label>
                                    <select name="Id_Type_User" class="form-control form-control-user"
                                    style="display: block;
                                            width: 100%;
                                            heigth: calc(2.9em + .75em + 2px);
                                            padding: 0.375rem .75rem;">
                                        @foreach ($type_users as $type_user)
                                            <option value="{{$type_user->Id_Type_User}}">{{$type_user->Name_Type_User}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                                <hr>
                            </form>
                            <div class="text-center">
                                <a class="small" href="{{route('login')}}">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>

</body>

</html>