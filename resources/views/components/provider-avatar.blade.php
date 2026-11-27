@props(['userId' => null, 'name' => 'Provider', 'size' => 44])
@php
    $photo = null;
    try {
        $u = $userId ? \App\Models\User::find($userId) : null;
        $p = $u ? \App\Models\Provider::where('email', $u->email)->first() : null;
        if ($p && $p->image_path) {
            $photo = \Illuminate\Support\Facades\Storage::disk('s3')->url($p->image_path);
        }
    } catch (\Throwable $e) {
        $photo = null; // no photo available: the letter shows instead
    }
    $letter = strtoupper(mb_substr(trim($name) ?: 'P', 0, 1));
@endphp
<div class="av" style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.38) }}px; overflow: hidden; position: relative;">
    @if($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 50%;" onerror="this.remove()">
    @endif
    @unless($photo)
        {{ $letter }}
    @endunless
</div>
