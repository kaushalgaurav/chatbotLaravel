{{-- resources/views/layouts/head-css.blade.php --}}

@yield('css')

<link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

{{-- Vite will inject the correct built CSS and JS (hashed filenames from manifest.json) --}}
@viteReactRefresh
@vite([
  'resources/js/app.jsx',
  'resources/scss/bootstrap.scss',
  'resources/scss/icons.scss',
  'resources/scss/app.scss',
  'resources/scss/custom.scss',
])
