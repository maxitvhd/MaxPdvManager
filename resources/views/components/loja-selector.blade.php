<form action="{{ url()->current() }}" method="GET" class="d-inline">
    <select name="loja_codigo" class="form-select d-inline w-auto" onchange="this.form.submit()">
        @foreach($lojasPermitidas as $l)
            <option value="{{ $l->codigo }}" {{ $loja->codigo == $l->codigo ? 'selected' : '' }}>
                {{ $l->nome }}
            </option>
        @endforeach
    </select>
</form>