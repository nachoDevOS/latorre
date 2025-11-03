<footer class="app-footer">
    <div class="site-footer-right">
        <a style="color: black; font-size: 13px" href="https://www.soluciondigital.dev/" target="_blank">Copyright <small style="font-size: 13px">SolucionDigital {{date('Y')}}</small>
            {{-- <br>Todos los derechos reservados. --}}
        </a>
        @php $version = Voyager::getVersion(); @endphp
        {{-- @if (!empty($version))
            - {{ $version }}
        @endif --}}
    </div>
</footer>
