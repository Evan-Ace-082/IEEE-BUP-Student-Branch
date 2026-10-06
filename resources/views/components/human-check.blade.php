@php($prompt = \App\Support\HumanCheck::issue())
<div class="mb-3">
    <label class="form-label" for="human_answer">Security check: what is {{ $prompt }}?</label>
    <input class="form-control @error('human_answer') is-invalid @enderror" id="human_answer" name="human_answer" inputmode="numeric" autocomplete="off" required>
    @error('human_answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="hp">
    <label for="company_website">Company website</label>
    <input id="company_website" name="company_website" tabindex="-1" autocomplete="off" value="">
</div>
