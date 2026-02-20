<form action="{{ url()->current() }}" method="GET" class="d-inline">
    <select name="loja_codigo" class="form-select d-inline w-auto" onchange="this.form.submit()">
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
            <option value="">Visão Global</option>
        @endif
        @foreach($lojasPermitidas as $l)
            <option value="{{ $l->codigo }}" {{ (isset($loja) && $loja && $loja->codigo == $l->codigo) ? 'selected' : '' }}>
                {{ $l->nome }}
            </option>
        @endforeach
    </select>
</form>