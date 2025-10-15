<!-- resources/views/chatbots/preview.blade.php -->
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Chatbot Preview</title>

  {{-- 1) Make the path start with the Router basename so <Router basename="/chatbots"> can match.
         This runs BEFORE any scripts so React sees the adjusted path. --}}
  <script>
    (function(){
      try {
        if (!location.pathname.startsWith('/chatbots')) {
          const newPath = '/chatbots' + location.pathname;
          history.replaceState({}, '', newPath + location.search + location.hash);
          // continue — React will now see a pathname that begins with /chatbots
        }
      } catch (e) {
        console.warn('preview: pathname rewrite failed', e);
      }
    })();
  </script>

  {{-- 2) Load vendor jQuery (local build path) first, then load fallback CDN if local not available.
         We include both synchronously so they execute before the app bundle below. --}}
  <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
  <script>
    // If local jquery didn't load (404 or blocked), add CDN fallback immediately.
    (function(){
      if (typeof window.jQuery === 'undefined') {
        var s = document.createElement('script');
        s.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        s.async = false;
        document.head.appendChild(s);
      }
    })();
  </script>

  {{-- Load other vendor scripts that depend on jQuery (after jQuery) --}}
  <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ URL::asset('build/libs/metismenu/metisMenu.min.js') }}"></script>
  <script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>

  {{-- 3) Now load the Vite landbot entry (module). It will run after the above scripts executed. --}}
  @vite('resources/js/landbot/main.jsx')
</head>
<body>
  <div id="root"></div>

  <script>
    // expose preview id & query to the client app
    window.__BOT_PREVIEW_ID = "{{ request()->route('id') }}";
    window.__BOT_PREVIEW_QUERY = {!! json_encode(request()->query()) !!};
  </script>
</body>
</html>
