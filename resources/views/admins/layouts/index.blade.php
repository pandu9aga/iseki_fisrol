<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Fisrol Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">

    <!-- Custom fonts & template CSS -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet"> --}}
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .dataTables_filter input {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            width: 200px;
        }

        .dataTables_length select {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;

            /* Sidebar default: tampil di layar besar */
            #accordionSidebar {
                transition: all 0.3s ease-in-out;
            }

            /* Di layar kecil (<= 768px), sidebar auto hide */
            @media (max-width: 768px) {
                #accordionSidebar {
                    margin-left: -250px;
                    /* sembunyikan sidebar */
                }

                #accordionSidebar.active {
                    margin-left: 0;
                    /* muncul kalau toggle ditekan */
                }
            }
        }
    </style>

    @yield('style')


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
            <!-- Sidebar Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-text mx-3">Iseki_Fisrol Admin</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Nav Items -->
            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i
                        class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <hr class="sidebar-divider">
            <li class="nav-item"><a class="nav-link" href="{{ route('data_user') }}"><i
                        class="fas fa-fw fa-user-circle"></i><span>Data User</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('member') }}"><i
                        class="fas fa-fw fa-address-card"></i><span>Member</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patrol') }}"><i
                        class="fas fa-fw fa-table"></i><span>Patrol</span></a></li>
            <hr class="sidebar-divider my-0">
            {{-- <li class="nav-item"><a class="nav-link" href="{{ route('temuan') }}"><i class="fas fa-fw fa-folder"></i><span>Temuan</span></a></li> --}}
            <li class="nav-item"><a class="nav-link" href="{{ route('admins.reports.kontribusi') }}"><i
                        class="fas fa-fw fa-chart-bar"></i><span>Laporan Kontribusi</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}"><i
                        class="fas fa-fw fa-sign-out-alt"></i><span>Log Out</span></a></li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown no-arrow">
                            {{-- <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Username</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>Profile</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>Settings</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>Activity Log</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>Logout</a>
                            </div> --}}
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Page Content -->
                @yield('content')

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Iseki 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    @yield('modal')

    <!-- JS Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Core plugin JS -->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts -->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                paging: true,
                searching: true,
                info: true,
                ordering: true,
                lengthChange: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                dom: '<"d-flex justify-content-between mb-3"lf>t<"d-flex justify-content-between mt-3"ip>',
                "pageLength": -1,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
            });

            // Sidebar toggle
            $('#sidebarToggle, #sidebarToggleTop').on('click', function() {
                $('#accordionSidebar').toggleClass('active');
            });
        });
    </script>
    @yield('script')

</body>

</html>