{{-- resources/views/layouts/head-css.blade.php --}}

@yield('css')

{{-- Vite will inject the correct built CSS and JS (hashed filenames from manifest.json) --}}
@viteReactRefresh
@vite([
  'resources/js/app.jsx',
  'resources/scss/bootstrap.scss',
  'resources/scss/icons.scss',
  'resources/scss/app.scss',
  'resources/css/custom.css',
])
