<?php
use Illuminate\Support\Facades\Route;
?>
<script>window.location = "{{ route("admin.settings") }}";</script>
<meta http-equiv="refresh" content="0; url={{ route('admin.settings') }}">
<p class="p-8">Redirecting to <a href="{{ route('admin.settings') }}" class="underline">Pengaturan</a>...</p>
