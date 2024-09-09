<aside class="main-sidebar" id="sidebar-wrapper">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php $ruta='/upload/photo/'.Auth::user()->photo; ?>
                <img class="img-circle"  src="{!!url($ruta)!!}" alt="img-circle">
            </div>
            <div class="pull-left info">
                @if (Auth::guest())
                <p>CAN-UGPFDMPS</p>
                @else
                    <small>{{ Auth::user()->nombres}} {{ Auth::user()->apellido_paterno}}</small><br/>
                @endif
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- Sidebar Menu -->

        <ul class="sidebar-menu">
            @include('layouts.menu')
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>